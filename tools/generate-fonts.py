#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
tools/generate-font.py

Build an x5 variable font that "snaps" between discrete powers (n1..n3) by
using an `opsz` axis + GSUB FeatureVariations with the `rvrn` feature.

Input SVGs (per glyph per power):
  dist/x5-n{power}-u{codepoint}.svg

Example:
  dist/x5-n1-u0041.svg   (U+0041 'A', power 1)
  dist/x5-n2-u0041.svg
  dist/x5-n3-u0041.svg

Outputs:
  dist/fonts/x5.ttf
  dist/fonts/x5.woff2   (if brotli is available)
  dist/fonts/x5.woff    (optional)

Notes:
- Base glyphs are power 1; powers 2 and 3 are alternates substituted via rvrn.
- Snapping thresholds are controlled by SNAP_OPSZ_MAX_P1 and SNAP_OPSZ_MAX_P2.
- You can tweak OPSZ_MIN/DEFAULT/MAX and snap points to taste.

Run:
  python tools/generate-font.py
  python tools/generate-font.py --formats ttf,woff2
"""

from __future__ import annotations

import argparse
import json
import sys
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, Iterable, List, Optional, Tuple

from fontTools.fontBuilder import FontBuilder
from fontTools.misc.transform import Transform
from fontTools.pens.cu2quPen import Cu2QuPen
from fontTools.pens.roundingPen import RoundingPen
from fontTools.pens.ttGlyphPen import TTGlyphPen
from fontTools.svgLib.path import SVGPath
from fontTools.ttLib import TTFont
from fontTools.varLib.featureVars import addFeatureVariations


# --- Your characters (as JSON-escaped string) ---
CHARS_JSON = r"""
{"chars": " !\"#$%'()+,-.\/0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_{|}~\u00b0\u00b1\u00b7\u00f7\u2021\u2026\u20bf\u2234\u2235\u221e\u22ee\u2302\u23cf\u23f8\u23fb\u25a0\u25a1\u24a3\u25ac\u25ad\u25ae\u25af\u25b2\u25b3\u25b4\u25b6\u25b7\u25bc\u25bd\u25f0\u25f1\u25f2\u25f3\u25c0\u25c1\u25c6\u25c7\u25e2\u25e3\u25e4\u25e5\u25eb\u25fb\u2609\u2630\u2631\u2632\u2633\u2634\u2635\u2636\u2637\u2661\u2665\u271d\ud80c\udcd1"}
""".strip()


# --- Font / axis defaults ---
FAMILY_NAME = "x5"
STYLE_NAME = "Regular"
POSTSCRIPT_NAME = "x5-Regular"

UPM = 1000

# We use opsz as the driver for "which power should display".
OPSZ_MIN = 8.0
OPSZ_DEFAULT = 16.0
OPSZ_MAX = 144.0

# Snap thresholds in *opsz user units*:
# - power 1: opsz <= SNAP_OPSZ_MAX_P1
# - power 2: SNAP_OPSZ_MAX_P1 < opsz <= SNAP_OPSZ_MAX_P2
# - power 3: opsz > SNAP_OPSZ_MAX_P2
SNAP_OPSZ_MAX_P1 = 24.0
SNAP_OPSZ_MAX_P2 = 64.0

# Cubic->quadratic conversion error tolerance (in font units)
CU2QU_MAX_ERR = 0.5


@dataclass(frozen=True)
class Axis:
    tag: str
    min: float
    default: float
    max: float
    name: str


def project_root() -> Path:
    return Path(__file__).resolve().parents[1]


def decode_chars(json_blob: str) -> List[str]:
    obj = json.loads(json_blob)
    s = obj["chars"]
    # Preserve order, but remove duplicates if any
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
    Try multiple filename variants for robustness:
      dist/x5-n{power}-u{hex}.svg
    where {hex} might be padded/unpadded, lower/upper.
    """
    candidates: List[str] = []
    # padded forms (common)
    if cp <= 0xFFFF:
        candidates += [f"{cp:04x}", f"{cp:04X}"]
    else:
        # common widths for >BMP
        candidates += [f"{cp:05x}", f"{cp:05X}", f"{cp:06x}", f"{cp:06X}"]
    # unpadded forms
    candidates += [f"{cp:x}", f"{cp:X}"]

    for hx in candidates:
        p = dist_dir / f"x5-n{power}-u{hx}.svg"
        if p.exists():
            return p

    # last resort: scan for any matching suffix
    prefix = f"x5-n{power}-u"
    cp_hex_upper = f"{cp:X}"
    cp_hex_lower = f"{cp:x}"
    for p in dist_dir.glob(f"{prefix}*.svg"):
        stem = p.stem  # e.g. x5-n1-u0041
        if stem.startswith(prefix):
            tail = stem[len(prefix) :]
            if tail in (cp_hex_upper, cp_hex_lower) or tail.upper() == cp_hex_upper or tail.lower() == cp_hex_lower:
                return p

    return None


def parse_viewbox(svg_path: Path) -> Tuple[float, float, float, float]:
    svg = SVGPath(str(svg_path))
    root = svg.root
    vb = root.get("viewBox")
    if vb:
        parts = vb.replace(",", " ").split()
        if len(parts) == 4:
            minx, miny, w, h = map(float, parts)
            return minx, miny, w, h

    # fallback to width/height attributes
    w_attr = root.get("width")
    h_attr = root.get("height")
    try:
        w = float(w_attr) if w_attr else float(UPM)
        h = float(h_attr) if h_attr else float(UPM)
    except Exception:
        w, h = float(UPM), float(UPM)
    return 0.0, 0.0, w, h


def svg_to_ttglyph(svg_path: Path, upm: int) -> object:
    """
    Convert an SVG into a TrueType glyf TTGlyph:
    - scales to fit a UPM×UPM square (preserving aspect ratio)
    - centers within that square
    - flips Y (SVG down -> font up)
    - converts cubics -> quadratics
    - rounds to integer coords
    """
    minx, miny, w, h = parse_viewbox(svg_path)

    # scale to fit the larger dimension into the em square
    s = upm / max(w, h) if max(w, h) > 0 else 1.0
    xoff = (upm - (w * s)) / 2.0
    yoff = (upm - (h * s)) / 2.0

    # x' =  s*x + e
    # y' = -s*y + f
    e = xoff - (minx * s)
    f = yoff + ((h + miny) * s)
    transform = Transform(s, 0, 0, -s, e, f)

    svg = SVGPath(str(svg_path), transform=transform)

    base_pen = TTGlyphPen(None)
    rounding_pen = RoundingPen(base_pen)
    quad_pen = Cu2QuPen(rounding_pen, max_err=CU2QU_MAX_ERR, all_quadratic=True)

    svg.draw(quad_pen)
    return base_pen.glyph()


def build_notdef_glyph(upm: int) -> object:
    pen = TTGlyphPen(None)
    # Simple box with a smaller inner box
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


def normalize_value(v: float, axis_min: float, axis_default: float, axis_max: float) -> float:
    # OpenType normalized coords: min=-1, default=0, max=+1
    if v == axis_default:
        return 0.0
    if v < axis_default:
        denom = axis_default - axis_min
        return (v - axis_default) / denom if denom else -1.0
    denom = axis_max - axis_default
    return (v - axis_default) / denom if denom else 1.0


def clamp01(x: float) -> float:
    return -1.0 if x < -1.0 else (1.0 if x > 1.0 else x)


def main(argv: Optional[List[str]] = None) -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--formats", default="ttf,woff2,woff", help="Comma list: ttf,woff2,woff")
    ap.add_argument("--family", default=FAMILY_NAME)
    ap.add_argument("--style", default=STYLE_NAME)
    ap.add_argument("--postscript", default=POSTSCRIPT_NAME)
    ap.add_argument("--upm", type=int, default=UPM)
    ap.add_argument("--dist-dir", default="dist", help="Directory containing x5-n{power}-u{codepoint}.svg")
    ap.add_argument("--out-dir", default="dist/fonts", help="Output directory for font files")
    ap.add_argument("--powers", default="1,2,3", help="Comma list of powers to include (default 1,2,3)")
    args = ap.parse_args(argv)

    formats = [x.strip().lower() for x in args.formats.split(",") if x.strip()]
    powers = [int(x.strip()) for x in args.powers.split(",") if x.strip()]
    if powers != sorted(powers) or powers[0] != 1:
        print("Error: powers must start at 1 and be sorted (e.g. 1,2,3).", file=sys.stderr)
        return 2
    if len(powers) < 1:
        print("Error: need at least power 1.", file=sys.stderr)
        return 2

    root = project_root()
    dist_dir = (root / args.dist_dir).resolve()
    out_dir = (root / args.out_dir).resolve()
    out_dir.mkdir(parents=True, exist_ok=True)

    chars = decode_chars(CHARS_JSON)
    cps = sorted({ord(ch) for ch in chars})

    # Build glyphs: base (p1) + alternates (.p2/.p3)
    glyphs: Dict[str, object] = {}
    cmap: Dict[int, str] = {}

    glyph_order: List[str] = [".notdef"]
    glyphs[".notdef"] = build_notdef_glyph(args.upm)

    # Base glyphs (power 1)
    missing: List[str] = []
    base_names: List[str] = []

    for cp in cps:
        gname = glyph_name_for_codepoint(cp)
        base_names.append(gname)
        cmap[cp] = gname

        svg_path = find_svg_for_codepoint(dist_dir, power=1, cp=cp)
        if not svg_path:
            missing.append(f"power1 U+{cp:04X} -> expected dist/x5-n1-u*.svg")
            # still create an empty glyph so font builds
            glyphs[gname] = TTGlyphPen(None).glyph()
        else:
            glyphs[gname] = svg_to_ttglyph(svg_path, args.upm)

    glyph_order.extend(base_names)

    # Alternate glyphs for p2/p3
    alt_names_by_power: Dict[int, List[str]] = {}
    for p in powers[1:]:
        alt_names: List[str] = []
        for cp in cps:
            base = glyph_name_for_codepoint(cp)
            alt = f"{base}.p{p}"
            alt_names.append(alt)

            svg_path = find_svg_for_codepoint(dist_dir, power=p, cp=cp)
            if not svg_path:
                missing.append(f"power{p} U+{cp:04X} -> expected dist/x5-n{p}-u*.svg")
                glyphs[alt] = TTGlyphPen(None).glyph()
            else:
                glyphs[alt] = svg_to_ttglyph(svg_path, args.upm)
        alt_names_by_power[p] = alt_names
        glyph_order.extend(alt_names)

    if missing:
        print("Warning: some SVGs were not found. The font will still be generated, but missing glyphs will be blank:")
        for line in missing[:80]:
            print("  -", line)
        if len(missing) > 80:
            print(f"  ... and {len(missing) - 80} more")

    # Build font
    fb = FontBuilder(args.upm, isTTF=True)
    fb.setupGlyphOrder(glyph_order)
    fb.setupCharacterMap(cmap)
    fb.setupGlyf(glyphs)

    # Horizontal metrics: monospace (advanceWidth = UPM), but LSB must equal xMin for glyf
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

    # Add opsz axis (variable font container)
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

    # Add FeatureVariations substitutions for snapping (rvrn)
    # Convert snap thresholds to normalized coords.
    eps = 1e-3  # avoid overlap at boundaries
    p2_min_u = SNAP_OPSZ_MAX_P1
    p2_max_u = SNAP_OPSZ_MAX_P2 - eps
    p3_min_u = SNAP_OPSZ_MAX_P2
    p3_max_u = axis.max

    p2_min = clamp01(normalize_value(p2_min_u, axis.min, axis.default, axis.max))
    p2_max = clamp01(normalize_value(p2_max_u, axis.min, axis.default, axis.max))
    p3_min = clamp01(normalize_value(p3_min_u, axis.min, axis.default, axis.max))
    p3_max = clamp01(normalize_value(p3_max_u, axis.min, axis.default, axis.max))

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

    # Recalc bboxes after building everything
    fb.font.recalcBBoxes()

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
