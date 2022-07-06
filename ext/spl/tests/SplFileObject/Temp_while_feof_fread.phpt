--TEST--
Iterate over SplTempFileObject in a while loop
--FILE--
<?php

$file = new SplTempFileObject();

$file->fwrite("foo,bar\n");
$file->fwrite("\n");
$file->fwrite("baz,qux\n");

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
Using fread()
string(10) "foo,bar

b"
string(7) "az,qux
"
Using fgets()
string(8) "foo,bar
"
string(1) "
"
string(8) "baz,qux
"
