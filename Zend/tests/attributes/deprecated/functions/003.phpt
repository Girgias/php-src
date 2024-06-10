--TEST--
#[\Deprecated]: Interfaces.
--XFAIL--
https://github.com/php/php-src/pull/11293#discussion_r1632925617
--FILE--
<?php

interface I {
	#[\Deprecated]
	function test();
}

class Clazz implements I {
	function test() {
	}
}


$c = new Clazz();
$c->test();

$r = new ReflectionMethod('I', 'test');
var_dump($r->isDeprecated());

$r = new ReflectionMethod('Clazz', 'test');
var_dump($r->isDeprecated());

?>
--EXPECTF--
bool(true)
bool(false)
