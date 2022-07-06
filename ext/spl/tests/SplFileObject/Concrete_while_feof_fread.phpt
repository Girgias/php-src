--TEST--
Iterate over SplFileObject in a while loop
--FILE--
<?php

$file = new SplFileObject(__DIR__ . '/test_data.txt');

echo 'Using fread()', \PHP_EOL;
$file->rewind();
while (!$file->eof()) {
    var_dump($file->fread(10));
}

echo 'Using fgets()', \PHP_EOL;
$file->rewind();
while (!$file->eof()) {
    var_dump($file->fgets());
}

?>
--EXPECT--
string(10) "foo,bar

b"
string(6) "az,qux"
string(8) "foo,bar
"
string(1) "
"
string(7) "baz,qux"
