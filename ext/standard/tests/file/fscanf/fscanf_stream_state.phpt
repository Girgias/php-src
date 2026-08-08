--TEST--
Test fscanf() with write-only, empty and exhausted streams
--FILE--
<?php

$filename = __DIR__ . '/fscanf_stream_state.tmp';

echo "Write-only stream:\n";
$stream = fopen($filename, 'wb');
var_dump(fscanf($stream, '%s'));
fclose($stream);

echo "Empty stream:\n";
$stream = fopen($filename, 'rb');
var_dump(ftell($stream));
var_dump(fscanf($stream, '%s'));
var_dump(ftell($stream));
fclose($stream);

file_put_contents($filename, "123 rest\nalpha\n45.5\n");
$stream = fopen($filename, 'rb');

echo "Read lines:\n";
var_dump(ftell($stream));
var_dump(fscanf($stream, '%d'));
var_dump(ftell($stream));
var_dump(fscanf($stream, '%c'));
var_dump(ftell($stream));
var_dump(fscanf($stream, '%f'));
var_dump(ftell($stream));

echo "Natural EOF:\n";
var_dump(feof($stream));
var_dump(fscanf($stream, '%s'));
var_dump(ftell($stream));
var_dump(feof($stream));

echo "Seek to EOF:\n";
var_dump(rewind($stream));
var_dump(ftell($stream));
var_dump(fseek($stream, 0, SEEK_END));
var_dump(ftell($stream));
var_dump(fscanf($stream, '%s'));

fclose($stream);

?>
--CLEAN--
<?php
@unlink(__DIR__ . '/fscanf_stream_state.tmp');
?>
--EXPECTF--
Write-only stream:

Notice: fscanf(): Read of 8192 bytes failed with errno=%d %s in %s on line %d
bool(false)
Empty stream:
int(0)
bool(false)
int(0)
Read lines:
int(0)
array(1) {
  [0]=>
  int(123)
}
int(9)
array(1) {
  [0]=>
  string(1) "a"
}
int(15)
array(1) {
  [0]=>
  float(45.5)
}
int(20)
Natural EOF:
bool(false)
bool(false)
int(20)
bool(true)
Seek to EOF:
bool(true)
int(0)
int(0)
int(20)
bool(false)
