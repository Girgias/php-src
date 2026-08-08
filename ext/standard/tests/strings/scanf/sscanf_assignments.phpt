--TEST--
sscanf(): assignment behaviour
--FILE--
<?php


$formats = [
	'%1$s %2$s %1$s',
	'%1$s %*s %3$s',
	'%s %*s %3$s',
];

$str = "Hello World";

foreach ($formats as $format) {
	echo "Using format string '$format':\n";
	try {
	    var_dump(sscanf($str, $format));
	} catch (Throwable $e) {
	    echo $e::class, ': ', $e->getMessage(), PHP_EOL;
	}
	try {
	    var_dump(sscanf($str, $format, $a, $b, $c));
	    var_dump($a, $b, $c);
	    $a = $b = $c = null;
	} catch (Throwable $e) {
	    echo $e::class, ': ', $e->getMessage(), PHP_EOL;
	}
}

$formats = [
	'%0$s',
	'%256$s',
	'%1$s %s',
];

foreach ($formats as $format) {
	echo "Using format string '$format':\n";
	try {
		var_dump(sscanf($str, $format));
	} catch (ValueError $exception) {
		echo $exception->getMessage(), PHP_EOL;
	}
}

echo "XPG argument beyond supplied references:\n";
$value = null;
try {
	sscanf('a', '%2$s', $value);
} catch (ValueError $exception) {
	echo $exception->getMessage(), PHP_EOL;
}

echo "Suppressed assignments:\n";
var_dump(sscanf('alpha', '%*n%s'));
var_dump(sscanf('alpha 42', '%*[a-z] %d'));

echo "More than 16 references:\n";
$input = implode(' ', range(1, 17));
$format = rtrim(str_repeat('%d ', 17));
$values = array_fill(0, 17, null);
var_dump(sscanf($input, $format, ...$values));
var_dump($values[0], $values[16]);

echo "Invalid format with more than 16 references:\n";
$values = array_fill(0, 18, null);
try {
	sscanf($input, $format . ' %Q', ...$values);
} catch (ValueError $exception) {
	echo $exception->getMessage(), PHP_EOL;
}

echo "More than 32 results:\n";
$input = implode(' ', range(1, 33));
$format = rtrim(str_repeat('%d ', 33));
$result = sscanf($input, $format);
var_dump(count($result), $result[0], $result[32]);

echo "XPG result beyond the static assignment buffer:\n";
$result = sscanf('alpha', '%17$s');
var_dump(count($result), $result[0], $result[16]);

echo "Typed property references:\n";
class ScanValues
{
	public int $position = 0;
	public int $integer = 0;
	public float $float = 0.0;
	public string $string = '';
	public string $range = '';
	public string $unsigned = '';
}

$values = new ScanValues();
var_dump(sscanf('alpha', '%n%s', $values->position, $values->string));
var_dump($values->position, $values->string);
var_dump(sscanf('42 1.5 alpha beta', '%d %f %s %[a-z]', $values->integer, $values->float, $values->string, $values->range));
var_dump($values->integer, $values->float, $values->string, $values->range);
$input = PHP_INT_SIZE === 8 ? '18446744073709551615' : '4294967295';
var_dump(sscanf($input, '%u', $values->unsigned));
var_dump($values->unsigned === $input);
?>
--EXPECT--
Using format string '%1$s %2$s %1$s':
ValueError: Variable is assigned by multiple "%n$" conversion specifiers
ValueError: Variable is assigned by multiple "%n$" conversion specifiers
Using format string '%1$s %*s %3$s':
array(3) {
  [0]=>
  string(5) "Hello"
  [1]=>
  NULL
  [2]=>
  NULL
}
ValueError: Variable is not assigned by any conversion specifiers
Using format string '%s %*s %3$s':
ValueError: cannot mix "%" and "%n$" conversion specifiers
ValueError: cannot mix "%" and "%n$" conversion specifiers
Using format string '%0$s':
"%n$" argument index out of range
Using format string '%256$s':
"%n$" argument index out of range
Using format string '%1$s %s':
cannot mix "%" and "%n$" conversion specifiers
XPG argument beyond supplied references:
"%n$" argument index out of range
Suppressed assignments:
array(1) {
  [0]=>
  string(5) "alpha"
}
array(1) {
  [0]=>
  int(42)
}
More than 16 references:
int(17)
int(1)
int(17)
Invalid format with more than 16 references:
Bad scan conversion character "Q"
More than 32 results:
int(33)
int(1)
int(33)
XPG result beyond the static assignment buffer:
int(17)
NULL
string(5) "alpha"
Typed property references:
int(2)
int(0)
string(5) "alpha"
int(4)
int(42)
float(1.5)
string(5) "alpha"
string(4) "beta"
int(1)
bool(true)
