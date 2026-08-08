--TEST--
Test string parsing with sscanf() and fscanf()
--FILE--
<?php

function scan_string(string $description, string $input, string $format): void
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
    ['basic conversion', 'alpha beta', '%s'],
    ['leading whitespace', " \talpha", '%s'],
    ['field width', 'alpha beta', '%4s'],
    ['field width longer than the word', 'alpha beta', '%30s'],
    ['assignment suppression', 'alpha beta', '%*s %s'],
    ['all assignments suppressed', 'alpha', '%*s'],
    ['literal text and format whitespace', 'name: alpha beta', 'name: %s %s'],
    ['character set', 'alpha-123', '%[a-z]'],
    ['character set with no match', '123-alpha', '%[a-z]'],
    ['whitespace-only input', " \t\v", '%s'],
    ['NUL at the start of input', "\0alpha", '%s'],
];

foreach ($cases as [$description, $input, $format]) {
    scan_string($description, $input, $format);
}

?>
--EXPECT--
basic conversion:
array(1) {
  [0]=>
  string(5) "alpha"
}
leading whitespace:
array(1) {
  [0]=>
  string(5) "alpha"
}
field width:
array(1) {
  [0]=>
  string(4) "alph"
}
field width longer than the word:
array(1) {
  [0]=>
  string(5) "alpha"
}
assignment suppression:
array(1) {
  [0]=>
  string(4) "beta"
}
all assignments suppressed:
array(0) {
}
literal text and format whitespace:
array(2) {
  [0]=>
  string(5) "alpha"
  [1]=>
  string(4) "beta"
}
character set:
array(1) {
  [0]=>
  string(5) "alpha"
}
character set with no match:
array(1) {
  [0]=>
  NULL
}
whitespace-only input:
NULL
NUL at the start of input:
NULL
