--TEST--
throw_on_error declare statement
--FILE--
<?php
declare(throw_on_error=1);

echo "OK";
--EXPECT--
OK
