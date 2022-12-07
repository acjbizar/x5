<?php
declare(strict_types=1);

$c = [];

$c[0x30] = array(0,1,1,1,0,1,0,0,1,1,1,0,1,0,1,1,1,0,0,1,0,1,1,1,0);//0
$c[0x31] = array(0,0,1,0,0,0,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0);//1
$c[0x32] = array(1,1,1,1,1,0,0,0,0,1,0,1,1,1,1,1,0,0,0,0,1,1,1,1,1);//2
$c[0x33] = array(1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0);//3
$c[0x34] = array(1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1);//4
$c[0x35] = array(1,1,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0);//5
$c[0x36] = array(0,1,1,1,1,1,0,0,0,0,1,1,1,1,0,1,0,0,0,1,0,1,1,1,0);//6
$c[0x37] = array(1,1,1,1,1,0,0,0,0,1,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0);//7
$c[0x38] = array(0,1,1,1,0,1,0,0,0,1,0,1,1,1,0,1,0,0,0,1,0,1,1,1,0);//8
$c[0x39] = array(0,1,1,1,0,1,0,0,0,1,0,1,1,1,1,0,0,0,0,1,1,1,1,1,0);//9

$c[0x41] = array(0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);//A
$c[0x42] = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,1,1,1,0);//B
$c[0x43] = array(0,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,1,1,1,1);//C
$c[0x44] = array(1,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,0);//D
$c[0x45] = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,1,1,1,1);//E
$c[0x46] = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0);//F
$c[0x47] = array(0,1,1,1,1,1,0,0,0,0,1,0,1,1,1,1,0,0,0,1,0,1,1,1,1);//G
$c[0x48] = array(1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);//H
$c[0x49] = array(1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,1,1,1,1,1);//I
$c[0x4A] = array(0,0,1,1,1,0,0,0,0,1,0,0,0,0,1,1,0,0,0,1,0,1,1,1,0);//J
$c[0x4B] = array(1,0,0,1,0,1,0,1,0,0,1,1,0,0,0,1,0,1,0,0,1,0,0,1,0);//K
$c[0x4C] = array(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,1,1,1,1);//L
$c[0x4D] = array(1,0,0,0,1,1,1,0,1,1,1,0,1,0,1,1,0,0,0,1,1,0,0,0,1);//M
$c[0x4E] = array(1,0,0,0,1,1,1,0,0,1,1,0,1,0,1,1,0,0,1,1,1,0,0,0,1);//N
$c[0x4F] = array(0,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0);//O
$c[0x50] = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,0,1,0,0,0,0);//P
$c[0x51] = array(0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,1,0,0,1,1,0,1);//Q
$c[0x52] = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,0,0,0,1);//R
$c[0x53] = array(0,1,1,1,1,1,0,0,0,0,0,1,1,1,0,0,0,0,0,1,1,1,1,1,0);//S
$c[0x54] = array(1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0);//T
$c[0x55] = array(1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0);//U
$c[0x56] = array(1,0,0,0,1,1,0,0,0,1,0,1,0,1,0,0,1,0,1,0,0,0,1,0,0);//V
$c[0x57] = array(1,0,0,0,1,1,0,0,0,1,1,0,1,0,1,1,1,0,1,1,1,0,0,0,1);//W
$c[0x58] = array(1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,0,0,1);//X
$c[0x59] = array(1,0,0,0,1,0,1,0,1,0,0,1,1,1,0,0,0,1,0,0,0,0,1,0,0);//Y
$c[0x5A] = array(1,1,1,1,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,1,1,1,1);//Z

$c[0x25A0] = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);// black square

$c[0x2609] = array(0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,0,1,0,1,1,1,0);// sun

$c[0x2630] = array(1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1);// trigram for heaven
$c[0x2631] = array(1,1,0,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1);// trigram for lake
$c[0x2632] = array(1,1,1,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,1,1,1);// trigram for fire
$c[0x2633] = array(1,1,0,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,1,1,1);// trigram for thunder
$c[0x2634] = array(1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,0,1,1);// trigram for wind
$c[0x2635] = array(1,1,0,1,1,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,1,0,1,1);// trigram for water
$c[0x2636] = array(1,1,1,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,0,1,1);// trigram for mountain
$c[0x2637] = array(1,1,0,1,1,0,0,0,0,0,1,1,0,1,1,0,0,0,0,0,1,1,0,1,1);// trigram for earth

$c[0x2665] = array(0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0,0,0,1,0,0);// black heart suit

return $c;
