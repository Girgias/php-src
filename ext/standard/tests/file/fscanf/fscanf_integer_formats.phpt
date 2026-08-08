--TEST--
Test integer parsing with sscanf() and fscanf()
--FILE--
<?php

function scan_integer(string $input, string $format): void
{
    echo 'Format ', json_encode($format), ', input ', json_encode($input), ":\n";

    $stringResult = sscanf($input, $format);

    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $input);
    rewind($stream);
    $streamResult = fscanf($stream, $format);
    fclose($stream);

    if ($stringResult !== $streamResult) {
        echo "sscanf()/fscanf() mismatch:\n";
        var_dump($stringResult, $streamResult);
        return;
    }

    var_dump($stringResult);
}

$cases = [
    ['0', '%d'],
    ['+42', '%d'],
    ['-42', '%d'],
    ['  42', '%d'],
    ['42xyz', '%d'],
    ['12345', '%3d'],
    ['abc', '%d'],
    ['+', '%d'],
    ['2147483647', '%d'],
    ['-2147483648', '%d'],
    ['42', '%hd'],
    ['42', '%ld'],
    ['42', '%Ld'],
    ['42', '%D'],
    ['42', '%i'],
    ['077', '%i'],
    ['0x2a', '%i'],
    ['08', '%i'],
    ['0x', '%i'],
    ['8', '%i'],
    ['1-2', '%d'],
    ['1x2', '%i'],
    ['00x2', '%i'],
    ['-0x2', '%i'],
    ['0X', '%i'],
    ['123', '%100d'],
    ['077', '%o'],
    ['128', '%o'],
    ['12345', '%4o'],
    ['2a', '%x'],
    ['2A', '%X'],
    ['0x2a', '%x'],
    ['0x', '%x'],
    ['42', '%u'],
    ['+42', '%u'],
    ['12 34', '%*d %d'],
    ['value=123', 'value=%d'],
];

foreach ($cases as [$input, $format]) {
    scan_integer($input, $format);
}

echo "Unsigned value beyond PHP_INT_MAX:\n";
$input = PHP_INT_SIZE === 8 ? '18446744073709551615' : '4294967295';
$stringResult = sscanf($input, '%u');
$stream = fopen('php://memory', 'r+');
fwrite($stream, $input);
rewind($stream);
$streamResult = fscanf($stream, '%u');
fclose($stream);
var_dump($stringResult === $streamResult);
var_dump($stringResult[0] === $input);

?>
--EXPECT--
Format "%d", input "0":
array(1) {
  [0]=>
  int(0)
}
Format "%d", input "+42":
array(1) {
  [0]=>
  int(42)
}
Format "%d", input "-42":
array(1) {
  [0]=>
  int(-42)
}
Format "%d", input "  42":
array(1) {
  [0]=>
  int(42)
}
Format "%d", input "42xyz":
array(1) {
  [0]=>
  int(42)
}
Format "%3d", input "12345":
array(1) {
  [0]=>
  int(123)
}
Format "%d", input "abc":
array(1) {
  [0]=>
  NULL
}
Format "%d", input "+":
NULL
Format "%d", input "2147483647":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%d", input "-2147483648":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%hd", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%ld", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%Ld", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%D", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%i", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%i", input "077":
array(1) {
  [0]=>
  int(63)
}
Format "%i", input "0x2a":
array(1) {
  [0]=>
  int(42)
}
Format "%i", input "08":
array(1) {
  [0]=>
  int(0)
}
Format "%i", input "0x":
array(1) {
  [0]=>
  int(0)
}
Format "%i", input "8":
array(1) {
  [0]=>
  int(8)
}
Format "%d", input "1-2":
array(1) {
  [0]=>
  int(1)
}
Format "%i", input "1x2":
array(1) {
  [0]=>
  int(1)
}
Format "%i", input "00x2":
array(1) {
  [0]=>
  int(0)
}
Format "%i", input "-0x2":
array(1) {
  [0]=>
  int(0)
}
Format "%i", input "0X":
array(1) {
  [0]=>
  int(0)
}
Format "%100d", input "123":
array(1) {
  [0]=>
  int(123)
}
Format "%o", input "077":
array(1) {
  [0]=>
  int(63)
}
Format "%o", input "128":
array(1) {
  [0]=>
  int(10)
}
Format "%4o", input "12345":
array(1) {
  [0]=>
  int(668)
}
Format "%x", input "2a":
array(1) {
  [0]=>
  int(42)
}
Format "%X", input "2A":
array(1) {
  [0]=>
  int(42)
}
Format "%x", input "0x2a":
array(1) {
  [0]=>
  int(42)
}
Format "%x", input "0x":
array(1) {
  [0]=>
  int(0)
}
Format "%u", input "42":
array(1) {
  [0]=>
  int(42)
}
Format "%u", input "+42":
array(1) {
  [0]=>
  int(42)
}
Format "%*d %d", input "12 34":
array(1) {
  [0]=>
  int(34)
}
Format "value=%d", input "value=123":
array(1) {
  [0]=>
  int(123)
}
Unsigned value beyond PHP_INT_MAX:
bool(true)
bool(true)
