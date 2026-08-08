--TEST--
sscanf(): test %[] specifier with strings
--FILE--
<?php

$formats = [
	'%[aefgbcd]',
	'%[a-g]',
	'%[g-a]',
	'%[^uvwstxyz]',
	'%[^s-z]',
	'%[^z-s]',
	'%4[aefgbcd]',
	'%4[g-a]',
	'%4[^uvwstxyz]',
	'%4[^s-z]',
	'%4[^z-s]',
];

$str = "abcdefghijklmnopqrstuvwxyz";

foreach ($formats as $format) {
	echo "Using format string '$format':\n";
	$out = null;
	var_dump(sscanf($str, $format, $out));
	var_dump($out);
}
?>
--EXPECT--
Using format string '%[aefgbcd]':
int(1)
string(7) "abcdefg"
Using format string '%[a-g]':
int(1)
string(7) "abcdefg"
Using format string '%[g-a]':
int(1)
string(7) "abcdefg"
Using format string '%[^uvwstxyz]':
int(1)
string(18) "abcdefghijklmnopqr"
Using format string '%[^s-z]':
int(1)
string(18) "abcdefghijklmnopqr"
Using format string '%[^z-s]':
int(1)
string(18) "abcdefghijklmnopqr"
Using format string '%4[aefgbcd]':
int(1)
string(4) "abcd"
Using format string '%4[g-a]':
int(1)
string(4) "abcd"
Using format string '%4[^uvwstxyz]':
int(1)
string(4) "abcd"
Using format string '%4[^s-z]':
int(1)
string(4) "abcd"
Using format string '%4[^z-s]':
int(1)
string(4) "abcd"
