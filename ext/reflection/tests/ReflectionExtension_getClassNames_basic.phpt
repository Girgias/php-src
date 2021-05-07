--TEST--
ReflectionExtension::getClassNames() method on an extension which actually returns some information
--CREDITS--
Felix De Vliegher <felix.devliegher@gmail.com>
--FILE--
<?php
$standard = new ReflectionExtension('standard');
var_dump($standard->getClassNames());
?>
--EXPECT--
array(11) {
  [0]=>
  string(22) "__PHP_Incomplete_Class"
  [1]=>
  string(14) "AssertionError"
  [2]=>
  string(10) "FileSystem"
  [3]=>
  string(7) "Network"
  [4]=>
  string(15) "FileSystemError"
  [5]=>
  string(12) "FileNotFound"
  [6]=>
  string(12) "NotDirectory"
  [7]=>
  string(23) "InsufficientPermissions"
  [8]=>
  string(16) "TemporaryFailure"
  [9]=>
  string(15) "php_user_filter"
  [10]=>
  string(9) "Directory"
}
