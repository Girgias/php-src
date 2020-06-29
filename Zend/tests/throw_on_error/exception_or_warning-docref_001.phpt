--TEST--
php_exception_or_warning_docref test when declare enabled
--SKIPIF--
<?php
if (!class_exists('_ZendTestClass')) die('skip zend_test extension required');
?>
--FILE--
<?php
declare(throw_on_error=1);

try {
    zend_throw_on_error_declare_exception_or_warning();
} catch (\Exception $e) {
    echo $e->getMessage() . \PHP_EOL;
}

echo "OK";
--EXPECT--
Context dependent
OK
