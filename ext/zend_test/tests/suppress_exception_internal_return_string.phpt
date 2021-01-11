--TEST--
Suppresion operator internal functions can return things other than null: string
--SKIPIF--
<?php if (!extension_loaded('zend-test')) die('skip: zend-test extension required'); ?>
--FILE--
<?php

$value = @zend_suppress_exception_internal_return_string();

var_dump($value);

try {
    zend_suppress_exception_internal_return_string();
} catch (\Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), \PHP_EOL;
}

echo 'Done', PHP_EOL;
?>
--EXPECT--
string(6) "Before"
Error: Shouldn't be seen
Done
