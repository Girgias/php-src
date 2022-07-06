--TEST--
Iterate over SplFileObject with empty lines with CSV flags
--FILE--
<?php

$file = new SplFileObject(__DIR__ . '/test_data.txt');

$file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY /* | SplFileObject::DROP_NEW_LINE */);


foreach ($file as $line) {
    var_dump($line);
}
?>
--EXPECT--
array(2) {
  [0]=>
  string(3) "foo"
  [1]=>
  string(3) "bar"
}
array(1) {
  [0]=>
  NULL
}
array(2) {
  [0]=>
  string(3) "baz"
  [1]=>
  string(3) "qux"
}
