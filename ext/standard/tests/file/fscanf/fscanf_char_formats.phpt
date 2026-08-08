--TEST--
Test character parsing with sscanf() and fscanf()
--FILE--
<?php

function scan_character(string $description, string $input, string $format): void
{
    echo $description, ":\n";

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
    ['basic conversion', 'alpha', '%c'],
    ['leading whitespace without format whitespace', ' alpha', '%c'],
    ['leading whitespace with format whitespace', ' alpha', ' %c'],
    ['field width', 'alpha', '%4c'],
    ['field width stops at whitespace', 'alpha beta', '%30c'],
    ['assignment suppression', 'ab', '%*c%c'],
    ['whitespace input', "\n", '%c'],
    ['NUL at the start of input', "\0alpha", '%c'],
];

foreach ($cases as [$description, $input, $format]) {
    scan_character($description, $input, $format);
}

?>
--EXPECT--
basic conversion:
array(1) {
  [0]=>
  string(1) "a"
}
leading whitespace without format whitespace:
array(1) {
  [0]=>
  string(0) ""
}
leading whitespace with format whitespace:
array(1) {
  [0]=>
  string(1) "a"
}
field width:
array(1) {
  [0]=>
  string(4) "alph"
}
field width stops at whitespace:
array(1) {
  [0]=>
  string(5) "alpha"
}
assignment suppression:
array(1) {
  [0]=>
  string(1) "b"
}
whitespace input:
array(1) {
  [0]=>
  string(0) ""
}
NUL at the start of input:
NULL
