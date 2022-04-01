--TEST--
throw_on_error declare statement with strict_types declare after
--FILE--
<?php
declare(throw_on_error=1);
declare(strict_types=1);

echo "OK";
--EXPECT--
OK
