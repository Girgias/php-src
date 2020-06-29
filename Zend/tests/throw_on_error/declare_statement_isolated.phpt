--TEST--
Test throw_on_error declare statement is isolated to file
--FILE--
<?php
include 'fixture/no_declare.inc';

try {
    include 'fixture/declare_on.inc';
} catch (\Exception $e) {
    echo "Warning caught\n";
}
include 'fixture/declare_off.inc';
?>
DONE
--EXPECTF--
Warning: Undefined variable $undef in %sno_declare.inc on line 2
Warning caught

Warning: Undefined variable $undef in %sdeclare_off.inc on line 4
DONE
