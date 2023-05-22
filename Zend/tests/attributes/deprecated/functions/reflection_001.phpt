--TEST--
#[\Deprecated]: ReflectionFunction::isDeprecated() returns true.
--FILE--
<?php

#[\Deprecated]
function test() {
}

$r = new ReflectionFunction('test');

var_dump($r->isDeprecated());

?>
--EXPECTF--
bool(true)
