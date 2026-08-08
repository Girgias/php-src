--TEST--
sscanf(): test %[] specifier with numbers
--FILE--
<?php

$formats = [
	'%[240531]',
	'%[0-5]',
	'%[5-0]',
	'%[^9687]',
	'%[^6-9]',
	'%[^9-6]',
	'%4[240531]',
	'%4[0-5]',
	'%4[5-0]',
	'%4[^9687]',
	'%4[^6-9]',
	'%4[^9-6]',
];

$str = "00112233445566778899";

foreach ($formats as $format) {
	echo "Using format string '$format':\n";
	$out = null;
	var_dump(sscanf($str, $format, $out));
	var_dump($out);
}
?>
--EXPECT--
Using format string '%[240531]':
int(1)
string(12) "001122334455"
Using format string '%[0-5]':
int(1)
string(12) "001122334455"
Using format string '%[5-0]':
int(1)
string(12) "001122334455"
Using format string '%[^9687]':
int(1)
string(12) "001122334455"
Using format string '%[^6-9]':
int(1)
string(12) "001122334455"
Using format string '%[^9-6]':
int(1)
string(12) "001122334455"
Using format string '%4[240531]':
int(1)
string(4) "0011"
Using format string '%4[0-5]':
int(1)
string(4) "0011"
Using format string '%4[5-0]':
int(1)
string(4) "0011"
Using format string '%4[^9687]':
int(1)
string(4) "0011"
Using format string '%4[^6-9]':
int(1)
string(4) "0011"
Using format string '%4[^9-6]':
int(1)
string(4) "0011"
