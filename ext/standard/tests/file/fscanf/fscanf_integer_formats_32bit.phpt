--TEST--
Test 32-bit integer boundaries with sscanf() and fscanf()
--SKIPIF--
<?php
if (PHP_INT_SIZE !== 4) {
    die('skip 32-bit only');
}
?>
--FILE--
<?php

function scan_integer_32bit(string $input, string $format): void
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
    ['2147483647', '%d'],
    ['2147483648', '%d'],
    ['-2147483648', '%d'],
    ['-2147483649', '%d'],
    ['2147483648', '%i'],
    ['0x80000000', '%i'],
    ['020000000000', '%i'],
    ['17777777777', '%o'],
    ['20000000000', '%o'],
    ['-20000000001', '%o'],
    ['7fffffff', '%x'],
    ['80000000', '%x'],
    ['-80000000', '%x'],
    ['-80000001', '%x'],
    ['2147483647', '%u'],
    ['2147483648', '%u'],
    ['4294967295', '%u'],
    ['4294967296', '%u'],
    ['-1', '%u'],
    ['-2147483648', '%u'],
    ['-2147483649', '%u'],
];

foreach ($cases as [$input, $format]) {
    scan_integer_32bit($input, $format);
}

?>
--EXPECT--
Format "%d", input "2147483647":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%d", input "2147483648":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%d", input "-2147483648":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%d", input "-2147483649":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%i", input "2147483648":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%i", input "0x80000000":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%i", input "020000000000":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%o", input "17777777777":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%o", input "20000000000":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%o", input "-20000000001":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%x", input "7fffffff":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%x", input "80000000":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%x", input "-80000000":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%x", input "-80000001":
array(1) {
  [0]=>
  int(-2147483648)
}
Format "%u", input "2147483647":
array(1) {
  [0]=>
  int(2147483647)
}
Format "%u", input "2147483648":
array(1) {
  [0]=>
  string(10) "2147483648"
}
Format "%u", input "4294967295":
array(1) {
  [0]=>
  string(10) "4294967295"
}
Format "%u", input "4294967296":
array(1) {
  [0]=>
  string(10) "4294967295"
}
Format "%u", input "-1":
array(1) {
  [0]=>
  string(10) "4294967295"
}
Format "%u", input "-2147483648":
array(1) {
  [0]=>
  string(10) "2147483648"
}
Format "%u", input "-2147483649":
array(1) {
  [0]=>
  int(2147483647)
}
