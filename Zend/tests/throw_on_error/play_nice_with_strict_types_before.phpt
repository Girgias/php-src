--TEST--
throw_on_error declare statement with strict_types declare before
--FILE--
<?php
declare(strict_types=1);
declare(throw_on_error=1);

echo "OK";
--EXPECT--
OK
