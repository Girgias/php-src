--TEST--
Test floating-point parsing with sscanf() and fscanf()
--FILE--
<?php

function scan_float(string $input, string $format): void
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
    ['0', '%f'],
    ['+12.5', '%f'],
    ['-.5', '%f'],
    ['.5', '%f'],
    ['12.', '%f'],
    ['  1.25', '%f'],
    ['12.5xyz', '%f'],
    ['12.34', '%4f'],
    ['1.5', '%hf'],
    ['1.5', '%lf'],
    ['1.5', '%Lf'],
    ['1e3', '%e'],
    ['-1E-3', '%E'],
    ['6.02e23', '%g'],
    ['1e', '%f'],
    ['1e+', '%f'],
    ['1E', '%f'],
    ['x', '%f'],
    ['.', '%f'],
    ['+', '%f'],
    ['1e309', '%f'],
    ['1e-4000', '%f'],
    ['1-2', '%f'],
    ['1.2.3', '%f'],
    ['e1', '%f'],
    ['1e2e3', '%f'],
    ['1.25', '%100f'],
    ['1.5 2.5', '%*f %f'],
    ['value=1.25', 'value=%f'],
];

foreach ($cases as [$input, $format]) {
    scan_float($input, $format);
}

?>
--EXPECT--
Format "%f", input "0":
array(1) {
  [0]=>
  float(0)
}
Format "%f", input "+12.5":
array(1) {
  [0]=>
  float(12.5)
}
Format "%f", input "-.5":
array(1) {
  [0]=>
  float(-0.5)
}
Format "%f", input ".5":
array(1) {
  [0]=>
  float(0.5)
}
Format "%f", input "12.":
array(1) {
  [0]=>
  float(12)
}
Format "%f", input "  1.25":
array(1) {
  [0]=>
  float(1.25)
}
Format "%f", input "12.5xyz":
array(1) {
  [0]=>
  float(12.5)
}
Format "%4f", input "12.34":
array(1) {
  [0]=>
  float(12.3)
}
Format "%hf", input "1.5":
array(1) {
  [0]=>
  float(1.5)
}
Format "%lf", input "1.5":
array(1) {
  [0]=>
  float(1.5)
}
Format "%Lf", input "1.5":
array(1) {
  [0]=>
  float(1.5)
}
Format "%e", input "1e3":
array(1) {
  [0]=>
  float(1000)
}
Format "%E", input "-1E-3":
array(1) {
  [0]=>
  float(-0.001)
}
Format "%g", input "6.02e23":
array(1) {
  [0]=>
  float(6.02E+23)
}
Format "%f", input "1e":
array(1) {
  [0]=>
  float(1)
}
Format "%f", input "1e+":
array(1) {
  [0]=>
  float(1)
}
Format "%f", input "1E":
array(1) {
  [0]=>
  float(1)
}
Format "%f", input "x":
array(1) {
  [0]=>
  NULL
}
Format "%f", input ".":
NULL
Format "%f", input "+":
NULL
Format "%f", input "1e309":
array(1) {
  [0]=>
  float(INF)
}
Format "%f", input "1e-4000":
array(1) {
  [0]=>
  float(0)
}
Format "%f", input "1-2":
array(1) {
  [0]=>
  float(1)
}
Format "%f", input "1.2.3":
array(1) {
  [0]=>
  float(1.2)
}
Format "%f", input "e1":
array(1) {
  [0]=>
  NULL
}
Format "%f", input "1e2e3":
array(1) {
  [0]=>
  float(100)
}
Format "%100f", input "1.25":
array(1) {
  [0]=>
  float(1.25)
}
Format "%*f %f", input "1.5 2.5":
array(1) {
  [0]=>
  float(2.5)
}
Format "value=%f", input "value=1.25":
array(1) {
  [0]=>
  float(1.25)
}
