<?php
declare(strict_types=1);

$c = [];

$c[0x20] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];// Space (SP)
$c[0x21] = [0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0,0,1,0,0];// Exclamation Mark
$c[0x22] = [0,1,0,1,0,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];// Quotation Mark
$c[0x23] = [0,1,0,1,0,1,1,1,1,1,0,1,0,1,0,1,1,1,1,1,0,1,0,1,0];// Number Sign
$c[0x24] = [0,1,1,1,1,1,0,1,0,0,0,1,1,1,0,0,0,1,0,1,1,1,1,1,0];// Dollar Sign
$c[0x25] = [1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1];// Percentage Sign
#$c[0x26] = [0,0,1,0,0,0,1,0,1,0,0,0,1,0,1,0,1,0,1,0,0,1,1,1,0];// Ampersand
$c[0x27] = [0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];// Apostrophe
$c[0x28] = [0,0,0,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,1,0];// Left Parenthesis
$c[0x29] = [0,1,0,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,0,0,0];// Right Parenthesis
#// Asterisk
$c[0x2B] = [0,0,0,0,0,0,0,1,0,0,0,1,1,1,0,0,0,1,0,0,0,0,0,0,0];// Plus Sign
$c[0x2C] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0];// Comma
$c[0x2D] = [0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0];// Hyphen-Minus
$c[0x2E] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0];// Full Stop
$c[0x2F] = [0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0];// Solidus
$c[0x30] = [0,1,1,1,0,1,0,0,1,1,1,0,1,0,1,1,1,0,0,1,0,1,1,1,0];// Digit Zero
$c[0x31] = [0,0,1,0,0,0,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0];// Digit One
$c[0x32] = [1,1,1,1,1,0,0,0,0,1,0,1,1,1,1,1,0,0,0,0,1,1,1,1,1];// Digit Two
$c[0x33] = [1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0];// Digit Three
$c[0x34] = [1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1];// Digit Four
$c[0x35] = [1,1,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0];// Digit Five
$c[0x36] = [0,1,1,1,1,1,0,0,0,0,1,1,1,1,0,1,0,0,0,1,0,1,1,1,0];// Digit Six
$c[0x37] = [1,1,1,1,1,0,0,0,0,1,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0];// Digit Seven
$c[0x38] = [0,1,1,1,0,1,0,0,0,1,0,1,1,1,0,1,0,0,0,1,0,1,1,1,0];// Digit Eight
$c[0x39] = [0,1,1,1,0,1,0,0,0,1,0,1,1,1,1,0,0,0,0,1,1,1,1,1,0];// Digit Nine
$c[0x3A] = [0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0];// Colon
$c[0x3B] = [0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0];// Semicolon
$c[0x3C] = [0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,1,0];// Less-Than Sign
$c[0x3D] = [0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0];// Equals Sign
$c[0x3E] = [0,1,0,0,0,0,0,1,0,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0];// Greater-Than Sign
$c[0x3F] = [0,1,1,1,0,1,0,0,0,1,0,0,1,1,0,0,0,0,0,0,0,0,1,0,0];// Question Mark
$c[0x40] = [1,1,1,1,1,1,0,0,0,1,1,0,1,1,1,1,0,1,0,1,1,0,1,1,1];// Commercial At
$c[0x41] = [0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1];// Latin Capital Letter A
$c[0x42] = [1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,1,1,1,0];// Latin Capital Letter B
$c[0x43] = [0,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,1,1,1,1];// Latin Capital Letter C
$c[0x44] = [1,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,0];// Latin Capital Letter D
$c[0x45] = [1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,1,1,1,1];// Latin Capital Letter E
$c[0x46] = [1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0];// Latin Capital Letter F
$c[0x47] = [0,1,1,1,1,1,0,0,0,0,1,0,1,1,1,1,0,0,0,1,0,1,1,1,1];// Latin Capital Letter G
$c[0x48] = [1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1];// Latin Capital Letter H
$c[0x49] = [1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,1,1,1,1,1];// Latin Capital Letter I
$c[0x4A] = [0,0,1,1,1,0,0,0,0,1,0,0,0,0,1,1,0,0,0,1,0,1,1,1,0];// Latin Capital Letter J
$c[0x4B] = [1,0,0,1,0,1,0,1,0,0,1,1,0,0,0,1,0,1,0,0,1,0,0,1,0];// Latin Capital Letter K
$c[0x4C] = [1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,1,1,1,1];// Latin Capital Letter L
$c[0x4D] = [1,0,0,0,1,1,1,0,1,1,1,0,1,0,1,1,0,0,0,1,1,0,0,0,1];// Latin Capital Letter M
$c[0x4E] = [1,0,0,0,1,1,1,0,0,1,1,0,1,0,1,1,0,0,1,1,1,0,0,0,1];// Latin Capital Letter N
$c[0x4F] = [0,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0];// Latin Capital Letter O
$c[0x50] = [1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,0,1,0,0,0,0];// Latin Capital Letter P
$c[0x51] = [0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,1,0,0,1,1,0,1];// Latin Capital Letter Q
$c[0x52] = [1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,0,0,0,1];// Latin Capital Letter R
$c[0x53] = [0,1,1,1,1,1,0,0,0,0,0,1,1,1,0,0,0,0,0,1,1,1,1,1,0];// Latin Capital Letter S
$c[0x54] = [1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0];// Latin Capital Letter T
$c[0x55] = [1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0];// Latin Capital Letter U
$c[0x56] = [1,0,0,0,1,1,0,0,0,1,0,1,0,1,0,0,1,0,1,0,0,0,1,0,0];// Latin Capital Letter V
$c[0x57] = [1,0,0,0,1,1,0,0,0,1,1,0,1,0,1,1,1,0,1,1,1,0,0,0,1];// Latin Capital Letter W
$c[0x58] = [1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,0,0,1];// Latin Capital Letter X
$c[0x59] = [1,0,0,0,1,0,1,0,1,0,0,1,1,1,0,0,0,1,0,0,0,0,1,0,0];// Latin Capital Letter Y
$c[0x5A] = [1,1,1,1,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,1,1,1,1];// Latin Capital Letter Z
$c[0x5B] = [0,0,1,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,1,0];// Left Square Bracket
$c[0x5C] = [1,0,0,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,1];// Reverse Solidus
$c[0x5D] = [0,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,0,0];// Right Square Bracket
$c[0x5E] = [0,0,1,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];// Circumflex Accent
$c[0x5F] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1];// Low Line

$c[0x7B] = [0,0,1,0,0,0,0,1,0,0,0,1,0,0,0,0,0,1,0,0,0,0,1,0,0];// Left Curly Bracket
$c[0x7C] = [0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0];// Vertical Line
$c[0x7D] = [0,0,1,0,0,0,0,1,0,0,0,0,0,1,0,0,0,1,0,0,0,0,1,0,0];// Right Curly Bracket
$c[0x7E] = [0,0,0,0,0,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,0,0,0];// Tilde

$c[0xB7] = [0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0];// Middle Dot

$c[0xF7] = [0,0,1,0,0,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,0,0,1,0,0];// Division Sign

$c[0x2302] = [0,0,1,0,0,0,1,0,1,0,1,0,0,0,1,1,0,0,0,1,1,1,1,1,1];// House

$c[0x25A0] = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];// Black Square
$c[0x25A1] = [1,1,1,1,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,1];// White Square

$c[0x25C6] = [0,0,1,0,0,0,1,1,1,0,1,1,1,1,1,0,1,1,1,0,0,0,1,0,0];// Black Square
$c[0x25C7] = [0,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0];// White Diamond

$c[0x2609] = [0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,0,1,0,1,1,1,0];// sun

$c[0x2630] = [1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1];// trigram for heaven
$c[0x2631] = [1,1,0,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1];// trigram for lake
$c[0x2632] = [1,1,1,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,1,1,1];// trigram for fire
$c[0x2633] = [1,1,0,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,1,1,1];// trigram for thunder
$c[0x2634] = [1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,0,1,1];// trigram for wind
$c[0x2635] = [1,1,0,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,0,1,1];// trigram for water
$c[0x2636] = [1,1,1,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,0,1,1];// trigram for mountain
$c[0x2637] = [1,1,0,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,0,1,1];// trigram for earth

$c[0x2661] = [0,1,0,1,0,1,0,1,0,1,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0];// white heart suit
$c[0x2665] = [0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0,0,0,1,0,0];// black heart suit

$c[0x271D] = [0,0,1,0,0,0,1,1,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0];// Latin Cross

return $c;
