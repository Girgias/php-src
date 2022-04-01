--TEST--
throw_on_error declare statement must be before anything else
--FILE--
<?php
echo "Before";

declare(throw_on_error=1);

--EXPECTF--
Fatal error: throw_on_error declaration must be the very first statement in the script in %s on line 4
