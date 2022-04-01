--TEST--
If throw_on_error declare statement is explicitly turned off do not promote warning
--FILE--
<?php
declare(throw_on_error=0);

echo $undef;

--EXPECTF--
Warning: Undefined variable $undef in %s on line %d
