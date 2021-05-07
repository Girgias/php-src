--TEST--
If throw_on_error declare statement is missing do not promote warning
--FILE--
<?php

echo $undef;

function foo() {
    echo $undef;
}

foo();

--EXPECTF--
Warning: Undefined variable $undef in %s on line %d

Warning: Undefined variable $undef in %s on line %d
