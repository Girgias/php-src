--TEST--
Suppresion operator internal functions can return things other than null: int
--SKIPIF--
<?php if (!extension_loaded('zend-test')) die('skip: zend-test extension required'); ?>
--FILE--
<?php

$value = @zend_suppress_exception_internal_return_int();

var_dump($value);

try {
    zend_suppress_exception_internal_return_int();
} catch (\Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), \PHP_EOL;
}

echo 'Done', PHP_EOL;
?>
--EXPECT--
int(20)
Error: Shouldn't be seen
Done
