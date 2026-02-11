#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
tools/generate-font.py

Build x5 variable font that "snaps" between discrete powers (n1..n3) by using:
- fvar axis: opsz
- GSUB FeatureVariations (rvrn) to substitute base glyphs with p2/p3 glyphs

Input SVGs (rect + symbol + use only):
  dist/x5-n{power}-u{codepoint}.svg

Outputs:
  dist/fonts/x5.ttf
  dist/fonts/x5.woff2 (if brotli available)
  dist/fonts/x5.woff  (optional)

Run:
  python tools/generate-font.py
  python tools/generate-font.py --formats ttf,woff2
"""

from __future__ import annotations

import argparse
import json
import math
import re
import sys
import unicodedata
import xml.etree.ElementTree as ET
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, Iterable, List, Optional, Tuple

from fontTools.fontBuilder import FontBuilder
from fontTools.ttLib import TTFont
from fontTools.pens.ttGlyphPen import TTGlyphPen
from fontTools.varLib.featureVars import addFeatureVariations


# --- Your characters (as JSON-escaped string) ---
CHARS_JSON = r"""
{"chars": " !\"#$%'()+,-.\/0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_{|}~\u00b0\u00b1\u00b7\u00f7\u2021\u2026\u20bf\u2234\u2235\u221e\u22ee\u2302\u23cf\u23f8\u23fb\u25a0\u25a1\u24a3\u25ac\u25ad\u25ae\u25af\u25b2\u25b3\u25b4\u25b6\u25b7\u25bc\u25bd\u25f0\u25f1\u25f2\u25f3\u25c0\u25c1\u25c6\u25c7\u25e2\u25e3\u25e4\u25e5\u25eb\u25fb\u2609\u2630\u2631\u2632\u2633\u2634\u2635\u2636\u2637\u2661\u2665\u271d\ud80c\udcd1"}
""".strip()


# --- Font naming / metrics ---
FAMILY_NAME = "x5"
STYLE_NAME = "Regular"
POSTSCRIPT_NAME = "x5-Regular"

UPM = 1000

# opsz axis range
OPSZ_MIN = 8.0
OPSZ_DEFAULT = 16.0
OPSZ_MAX = 144.0

# Snap thresholds (opsz user units):
# n1 <= 24, n2 <= 64, else n3
SNAP_OPSZ_MAX_P1 = 24.0
SNAP_OPSZ_MAX_P2 = 64.0


@dataclass(frozen=True)
class Axis:
    tag: str
    min: float
    default: float
    max: float
    name: str


# ---------------------------
# Utilities
# ---------------------------

def project_root() -> Path:
    return Path(__file__).resolve().parents[1]


def fix_surrogates(s: str) -> str:
    """
    Python's json can yield surrogate pairs as two code units (e.g. \\ud80c\\udcd1).
    Convert them into real non-BMP characters.
    """
    return s.encode("utf-16-le", "surrogatepass").decode("utf-16-le")


def decode_chars(json_blob: str) -> List[str]:
    obj = json.loads(json_blob)
    s = fix_surrogates(obj["chars"])
    seen = set()
    out = []
    for ch in s:
        if ch not in seen:
            seen.add(ch)
            out.append(ch)
    return out


def glyph_name_for_codepoint(cp: int) -> str:
    if cp == 0x20:
        return "space"
    if cp <= 0xFFFF:
        return f"uni{cp:04X}"
    return f"u{cp:X}"


def find_svg_for_codepoint(dist_dir: Path, power: int, cp: int) -> Optional[Path]:
    """
    Tries several hex paddings/cases to match your filename scheme:
      dist/x5-n{power}-u{hex}.svg
    """
    candidates: List[str] = []
    if cp <= 0xFFFF:
        candidates += [f"{cp:04x}", f"{cp:04X}"]
    else:
        candidates += [f"{cp:05x}", f"{cp:05X}", f"{cp:06x}", f"{cp:06X}"]
    candidates += [f"{cp:x}", f"{cp:X}"]

    for hx in candidates:
        p = dist_dir / f"x5-n{power}-u{hx}.svg"
        if p.exists():
            return p

    prefix = f"x5-n{power}-u"
    cp_hex_upper = f"{cp:X}"
    cp_hex_lower = f"{cp:x}"
    for p in dist_dir.glob(f"{prefix}*.svg"):
        stem = p.stem
        if stem.startswith(prefix):
            tail = stem[len(prefix):]
            if tail in (cp_hex_upper, cp_hex_lower) or tail.upper() == cp_hex_upper or tail.lower() == cp_hex_lower:
                return p

    return None


def local_tag(tag: str) -> str:
    return tag.rsplit("}", 1)[-1]


def svg_ns_uri(tag: str) -> Optional[str]:
    if tag.startswith("{") and "}" in tag:
        return tag[1:].split("}", 1)[0]
    return None


def qname(ns: Optional[str], local: str) -> str:
    return f"{{{ns}}}{local}" if ns else local


def parse_svg_length(val: Optional[str]) -> Optional[float]:
    """
    Parses numeric SVG lengths like '12', '12px'. Returns None for '%' or missing/invalid.
    """
    if not val:
        return None
    v = val.strip()
    if not v or v.endswith("%"):
        return None
    for suf in ("px", "pt", "pc", "mm", "cm", "in", "em", "ex"):
        if v.endswith(suf):
            v = v[:-len(suf)].strip()
            break
    try:
        return float(v)
    except Exception:
        return None


def parse_viewbox(root: ET.Element, fallback_upm: int) -> Tuple[float, float, float, float]:
    vb = root.get("viewBox")
    if vb:
        parts = vb.replace(",", " ").split()
        if len(parts) == 4:
            try:
                minx, miny, w, h = map(float, parts)
                return minx, miny, w, h
            except Exception:
                pass
    w = parse_svg_length(root.get("width")) or float(fallback_upm)
    h = parse_svg_length(root.get("height")) or float(fallback_upm)
    return 0.0, 0.0, w, h


# ---------------------------
# 2D Affine transforms
# Represented as (a,b,c,d,e,f) for:
# x' = a*x + c*y + e
# y' = b*x + d*y + f
# ---------------------------

Affine = Tuple[float, float, float, float, float, float]

IDENT: Affine = (1.0, 0.0, 0.0, 1.0, 0.0, 0.0)

def mul(T1: Affine, T2: Affine) -> Affine:
    a1,b1,c1,d1,e1,f1 = T1
    a2,b2,c2,d2,e2,f2 = T2
    # T = T1 ∘ T2  (apply T2 then T1)
    return (
        a1*a2 + c1*b2,
        b1*a2 + d1*b2,
        a1*c2 + c1*d2,
        b1*c2 + d1*d2,
        a1*e2 + c1*f2 + e1,
        b1*e2 + d1*f2 + f1
    )

def apply(T: Affine, x: float, y: float) -> Tuple[float, float]:
    a,b,c,d,e,f = T
    return (a*x + c*y + e, b*x + d*y + f)

def translate(tx: float, ty: float) -> Affine:
    return (1.0, 0.0, 0.0, 1.0, tx, ty)

def scale(sx: float, sy: float) -> Affine:
    return (sx, 0.0, 0.0, sy, 0.0, 0.0)


_transform_cmd = re.compile(r"([a-zA-Z]+)\s*\(([^)]*)\)")

def parse_transform_attr(s: Optional[str]) -> Affine:
    """
    Minimal SVG transform parser: translate, scale, matrix.
    (Enough for rect+symbol+use based grids.)
    """
    if not s:
        return IDENT
    s = s.strip()
    if not s:
        return IDENT

    T = IDENT
    for m in _transform_cmd.finditer(s):
        name = m.group(1).strip().lower()
        nums = re.split(r"[,\s]+", m.group(2).strip())
        nums = [n for n in nums if n]
        vals: List[float] = []
        for n in nums:
            try:
                vals.append(float(n))
            except Exception:
                vals.append(0.0)

        if name == "translate":
            tx = vals[0] if len(vals) > 0 else 0.0
            ty = vals[1] if len(vals) > 1 else 0.0
            T = mul(T, translate(tx, ty))
        elif name == "scale":
            sx = vals[0] if len(vals) > 0 else 1.0
            sy = vals[1] if len(vals) > 1 else sx
            T = mul(T, scale(sx, sy))
        elif name == "matrix" and len(vals) >= 6:
            a,b,c,d,e,f = vals[:6]
            T = mul(T, (a,b,c,d,e,f))
        else:
            # rotate/skew not expected in your restricted SVG set; ignore safely
            continue

    return T


# ---------------------------
# SVG rect extraction with symbol/use expansion
# ---------------------------

def is_whiteish_fill(el: ET.Element) -> bool:
    fill = (el.get("fill") or "").strip().lower()
    style = (el.get("style") or "").strip().lower()
    white_literals = {"white", "#fff", "#ffffff", "rgb(255,255,255)", "rgb(255, 255, 255)"}
    if fill in white_literals:
        return True
    for token in ("fill:#fff", "fill:#ffffff", "fill:white", "fill:rgb(255,255,255)", "fill: rgb(255,255,255)"):
        if token in style:
            return True
    return False


def is_invisible_rect(el: ET.Element) -> bool:
    # skip rects that are non-glyph: fill="none" or opacity 0
    fill = (el.get("fill") or "").strip().lower()
    if fill == "none":
        return True
    op = parse_svg_length(el.get("opacity"))
    if op is not None and op <= 0.0:
        return True
    fop = parse_svg_length(el.get("fill-opacity"))
    if fop is not None and fop <= 0.0:
        return True
    style = (el.get("style") or "").strip().lower()
    if "fill:none" in style:
        return True
    if "opacity:0" in style or "opacity: 0" in style:
        return True
    return False


def get_href(el: ET.Element) -> Optional[str]:
    # both href and xlink:href
    return el.get("href") or el.get("{http://www.w3.org/1999/xlink}href")


Rect = Tuple[float, float, float, float]  # (x0,y0,x1,y1) in SVG coordinate space (after expansion transforms)


def build_id_index(root: ET.Element) -> Dict[str, ET.Element]:
    idx: Dict[str, ET.Element] = {}
    for el in root.iter():
        _id = el.get("id")
        if _id:
            idx[_id] = el
    return idx


def symbol_viewbox(sym: ET.Element, fallback: Tuple[float, float, float, float]) -> Tuple[float, float, float, float]:
    vb = sym.get("viewBox")
    if vb:
        parts = vb.replace(",", " ").split()
        if len(parts) == 4:
            try:
                return tuple(map(float, parts))  # type: ignore
            except Exception:
                pass
    # fallback to symbol width/height if present, else fallback passed in
    w = parse_svg_length(sym.get("width"))
    h = parse_svg_length(sym.get("height"))
    if w is not None and h is not None:
        return (0.0, 0.0, float(w), float(h))
    return fallback


def collect_rects_from_element(
    el: ET.Element,
    id_index: Dict[str, ET.Element],
    root_viewbox: Tuple[float, float, float, float],
    T: Affine,
    out: List[Rect],
    depth: int = 0
) -> None:
    """
    Recursively collect rects, expanding <use> and applying transforms.
    Assumes only rect/symbol/use/g/defs-ish structure.
    """
    if depth > 50:
        return

    tag = local_tag(el.tag)

    # Apply element transform if present
    T_el = mul(T, parse_transform_attr(el.get("transform")))

    if tag == "rect":
        w_raw = (el.get("width") or "").strip()
        h_raw = (el.get("height") or "").strip()

        # skip problematic percent rects (your white background)
        if "%" in w_raw or "%" in h_raw:
            return

        if is_invisible_rect(el):
            return

        w = parse_svg_length(w_raw)
        h = parse_svg_length(h_raw)
        if w is None or h is None or w <= 0 or h <= 0:
            return

        x = parse_svg_length(el.get("x")) or 0.0
        y = parse_svg_length(el.get("y")) or 0.0

        # Ignore full-canvas white background rects
        minx, miny, vbw, vbh = root_viewbox
        if is_whiteish_fill(el):
            if abs(x - minx) < 1e-6 and abs(y - miny) < 1e-6 and abs(w - vbw) < 1e-6 and abs(h - vbh) < 1e-6:
                return

        # Transform all 4 corners (still OK for translate/scale/matrix)
        p1 = apply(T_el, x, y)
        p2 = apply(T_el, x + w, y)
        p3 = apply(T_el, x + w, y + h)
        p4 = apply(T_el, x, y + h)

        xs = [p1[0], p2[0], p3[0], p4[0]]
        ys = [p1[1], p2[1], p3[1], p4[1]]
        x0, x1 = min(xs), max(xs)
        y0, y1 = min(ys), max(ys)

        # Degenerate?
        if (x1 - x0) <= 0 or (y1 - y0) <= 0:
            return

        out.append((x0, y0, x1, y1))
        return

    if tag == "use":
        href = get_href(el)
        if not href or not href.startswith("#"):
            return
        ref_id = href[1:]
        ref = id_index.get(ref_id)
        if ref is None:
            return

        # <use> implicit x/y translation
        ux = parse_svg_length(el.get("x")) or 0.0
        uy = parse_svg_length(el.get("y")) or 0.0

        T_use = mul(T_el, translate(ux, uy))

        # width/height matter when referencing <symbol> (viewport mapping)
        if local_tag(ref.tag) == "symbol":
            sym_vb = symbol_viewbox(ref, root_viewbox)
            sminx, sminy, svbw, svbh = sym_vb

            use_w = parse_svg_length(el.get("width"))
            use_h = parse_svg_length(el.get("height"))
            if use_w is None:
                use_w = svbw
            if use_h is None:
                use_h = svbh

            sx = (use_w / svbw) if svbw else 1.0
            sy = (use_h / svbh) if svbh else 1.0

            # Map symbol viewBox to the <use> viewport:
            # translate(-minx,-miny) then scale(sx,sy)
            T_sym_map = mul(T_use, scale(sx, sy))
            T_sym_map = mul(T_sym_map, translate(-sminx, -sminy))

            # Recurse into symbol children
            for ch in list(ref):
                collect_rects_from_element(ch, id_index, root_viewbox, T_sym_map, out, depth + 1)
        else:
            # Referencing e.g. a <g> or <rect> by id
            collect_rects_from_element(ref, id_index, root_viewbox, T_use, out, depth + 1)

        return

    # Skip defs/symbol definitions when traversing normally (they’re reached via <use>)
    if tag in ("defs", "symbol"):
        return

    # Recurse through children
    for ch in list(el):
        collect_rects_from_element(ch, id_index, root_viewbox, T_el, out, depth + 1)


def svg_rects(svg_path: Path, fallback_upm: int) -> Tuple[Tuple[float, float, float, float], List[Rect]]:
    root = ET.fromstring(svg_path.read_bytes())
    vb = parse_viewbox(root, fallback_upm)
    idx = build_id_index(root)

    rects: List[Rect] = []
    collect_rects_from_element(root, idx, vb, IDENT, rects)
    return vb, rects


# ---------------------------
# SVG rects -> TrueType glyph
# ---------------------------

def build_notdef_glyph(upm: int) -> object:
    pen = TTGlyphPen(None)
    m = upm * 0.08
    M = upm * 0.92
    pen.moveTo((m, m))
    pen.lineTo((M, m))
    pen.lineTo((M, M))
    pen.lineTo((m, M))
    pen.closePath()
    m2 = upm * 0.20
    M2 = upm * 0.80
    pen.moveTo((m2, m2))
    pen.lineTo((M2, m2))
    pen.lineTo((M2, M2))
    pen.lineTo((m2, M2))
    pen.closePath()
    return pen.glyph()


def rects_to_ttglyph(
    viewbox: Tuple[float, float, float, float],
    rects: List[Rect],
    upm: int
) -> object:
    minx, miny, w, h = viewbox
    if w <= 0 or h <= 0:
        return TTGlyphPen(None).glyph()

    # Global mapping: fit max(w,h) into UPM, center, and flip Y
    s = upm / max(w, h)
    xoff = (upm - (w * s)) / 2.0
    yoff = (upm - (h * s)) / 2.0

    def map_point(x: float, y: float) -> Tuple[int, int]:
        # x' = s*(x - minx) + xoff
        # y' = -s*(y - miny) + (yoff + h*s)
        xf = s * (x - minx) + xoff
        yf = -s * (y - miny) + (yoff + h * s)
        return (int(round(xf)), int(round(yf)))

    pen = TTGlyphPen(None)

    # Add one contour per rect (your system is rect-only so this is faithful)
    for (x0, y0, x1, y1) in rects:
        # ensure order
        if x1 < x0:
            x0, x1 = x1, x0
        if y1 < y0:
            y0, y1 = y1, y0

        p0 = map_point(x0, y0)
        p1 = map_point(x1, y0)
        p2 = map_point(x1, y1)
        p3 = map_point(x0, y1)

        # Skip degenerate after rounding
        if p0[0] == p1[0] or p0[1] == p3[1]:
            continue

        pen.moveTo(p0)
        pen.lineTo(p1)
        pen.lineTo(p2)
        pen.lineTo(p3)
        pen.closePath()

    return pen.glyph()


# ---------------------------
# Feature-variation snapping (opsz -> rvrn)
# ---------------------------

def normalize_value(v: float, axis_min: float, axis_default: float, axis_max: float) -> float:
    # OpenType normalized coords: min=-1, default=0, max=+1
    if v == axis_default:
        return 0.0
    if v < axis_default:
        denom = axis_default - axis_min
        return (v - axis_default) / denom if denom else -1.0
    denom = axis_max - axis_default
    return (v - axis_default) / denom if denom else 1.0


def clamp_norm(x: float) -> float:
    return -1.0 if x < -1.0 else (1.0 if x > 1.0 else x)


# ---------------------------
# Main
# ---------------------------

def main(argv: Optional[List[str]] = None) -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--formats", default="ttf,woff2,woff", help="Comma list: ttf,woff2,woff")
    ap.add_argument("--family", default=FAMILY_NAME)
    ap.add_argument("--style", default=STYLE_NAME)
    ap.add_argument("--postscript", default=POSTSCRIPT_NAME)
    ap.add_argument("--upm", type=int, default=UPM)
    ap.add_argument("--dist-dir", default="dist", help="Directory containing x5-n{power}-u{hex}.svg")
    ap.add_argument("--out-dir", default="dist/fonts", help="Output directory for font files")
    ap.add_argument("--powers", default="1,2,3", help="Comma list of powers to include (default 1,2,3)")
    args = ap.parse_args(argv)

    formats = [x.strip().lower() for x in args.formats.split(",") if x.strip()]
    powers = [int(x.strip()) for x in args.powers.split(",") if x.strip()]
    if powers != sorted(powers) or powers[0] != 1:
        print("Error: powers must start at 1 and be sorted (e.g. 1,2,3).", file=sys.stderr)
        return 2

    root = project_root()
    dist_dir = (root / args.dist_dir).resolve()
    out_dir = (root / args.out_dir).resolve()
    out_dir.mkdir(parents=True, exist_ok=True)

    chars = decode_chars(CHARS_JSON)
    cps = sorted({ord(ch) for ch in chars})

    # Build glyph set:
    # base glyphs are power 1
    # alternates: .p2, .p3
    glyphs: Dict[str, object] = {}
    cmap: Dict[int, str] = {}

    glyph_order: List[str] = [".notdef"]
    glyphs[".notdef"] = build_notdef_glyph(args.upm)

    missing: List[str] = []
    base_names: List[str] = []

    def build_glyph_for(power: int, cp: int) -> object:
        svg_path = find_svg_for_codepoint(dist_dir, power=power, cp=cp)
        if not svg_path:
            missing.append(f"power{power} U+{cp:04X} missing (expected dist/x5-n{power}-u*.svg)")
            return TTGlyphPen(None).glyph()

        vb, rects = svg_rects(svg_path, args.upm)
        return rects_to_ttglyph(vb, rects, args.upm)

    # Base glyphs (p1)
    for cp in cps:
        gname = glyph_name_for_codepoint(cp)
        base_names.append(gname)
        cmap[cp] = gname
        glyphs[gname] = build_glyph_for(1, cp)

    glyph_order.extend(base_names)

    # Alternate glyphs (p2/p3)
    for p in powers[1:]:
        for cp in cps:
            base = glyph_name_for_codepoint(cp)
            alt = f"{base}.p{p}"
            glyph_order.append(alt)
            glyphs[alt] = build_glyph_for(p, cp)

    if missing:
        print("Warning: some SVGs were not found. Missing glyphs will be blank:")
        for line in missing[:80]:
            print("  -", line)
        if len(missing) > 80:
            print(f"  ... and {len(missing) - 80} more")

    # Build font
    fb = FontBuilder(args.upm, isTTF=True)
    fb.setupGlyphOrder(glyph_order)
    fb.setupCharacterMap(cmap)
    fb.setupGlyf(glyphs)

    # Horizontal metrics: monospace square cell
    glyf_table = fb.font["glyf"]
    hmtx: Dict[str, Tuple[int, int]] = {}
    aw = args.upm
    for gn in glyph_order:
        g = glyf_table[gn]
        try:
            g.recalcBounds(glyf_table)
            x_min = int(getattr(g, "xMin", 0) or 0)
        except Exception:
            x_min = 0
        hmtx[gn] = (aw, x_min)

    fb.setupHorizontalMetrics(hmtx)
    fb.setupHorizontalHeader(ascent=args.upm, descent=0, lineGap=0)

    fb.setupNameTable(
        {
            "familyName": args.family,
            "styleName": args.style,
            "fullName": f"{args.family} {args.style}",
            "psName": args.postscript,
            "version": "Version 1.000",
        }
    )
    fb.setupOS2(
        sTypoAscender=args.upm,
        sTypoDescender=0,
        sTypoLineGap=0,
        usWinAscent=args.upm,
        usWinDescent=0,
        achVendID="ACJ ",
    )
    fb.setupPost()
    fb.setupMaxp()

    # fvar opsz axis (no gvar needed since we're using feature variations substitutions)
    axis = Axis("opsz", OPSZ_MIN, OPSZ_DEFAULT, OPSZ_MAX, "Optical Size")
    fb.setupFvar(
        axes=[(axis.tag, axis.min, axis.default, axis.max, axis.name)],
        instances=[
            {
                "location": {axis.tag: axis.default},
                "stylename": args.style,
                "postscriptfontname": args.postscript,
            }
        ],
    )

    # Build rvrn substitutions by opsz ranges
    eps = 1e-3  # avoid overlap at boundary
    p2_min_u = SNAP_OPSZ_MAX_P1
    p2_max_u = SNAP_OPSZ_MAX_P2 - eps
    p3_min_u = SNAP_OPSZ_MAX_P2
    p3_max_u = axis.max

    p2_min = clamp_norm(normalize_value(p2_min_u, axis.min, axis.default, axis.max))
    p2_max = clamp_norm(normalize_value(p2_max_u, axis.min, axis.default, axis.max))
    p3_min = clamp_norm(normalize_value(p3_min_u, axis.min, axis.default, axis.max))
    p3_max = clamp_norm(normalize_value(p3_max_u, axis.min, axis.default, axis.max))

    subs_p2: Dict[str, str] = {}
    subs_p3: Dict[str, str] = {}
    for cp in cps:
        base = glyph_name_for_codepoint(cp)
        if 2 in powers:
            subs_p2[base] = f"{base}.p2"
        if 3 in powers:
            subs_p3[base] = f"{base}.p3"

    conditional_subs = []
    if 2 in powers:
        conditional_subs.append(([{axis.tag: (p2_min, p2_max)}], subs_p2))
    if 3 in powers:
        conditional_subs.append(([{axis.tag: (p3_min, p3_max)}], subs_p3))

    addFeatureVariations(fb.font, conditional_subs, featureTag="rvrn")

    # fontTools version compatibility: recalcBBoxes may be a bool flag, not a method
    if callable(getattr(fb.font, "recalcBBoxes", None)):
        fb.font.recalcBBoxes()
    else:
        fb.font.recalcBBoxes = True
    fb.font.recalcTimestamp = True

    # Write outputs
    ttf_path = out_dir / "x5.ttf"
    fb.font.save(str(ttf_path))
    print(f"Wrote {ttf_path}")

    if "woff" in formats:
        try:
            w = TTFont(str(ttf_path))
            w.flavor = "woff"
            woff_path = out_dir / "x5.woff"
            w.save(str(woff_path))
            print(f"Wrote {woff_path}")
        except Exception as e:
            print(f"Skipping WOFF: {e}", file=sys.stderr)

    if "woff2" in formats:
        try:
            w = TTFont(str(ttf_path))
            w.flavor = "woff2"
            woff2_path = out_dir / "x5.woff2"
            w.save(str(woff2_path))
            print(f"Wrote {woff2_path}")
        except Exception as e:
            print(f"Skipping WOFF2 (often needs 'brotli'): {e}", file=sys.stderr)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
