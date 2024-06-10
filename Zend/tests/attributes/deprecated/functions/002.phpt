--TEST--
#[\Deprecated]: Abstract Methods.
--XFAIL--
https://github.com/php/php-src/pull/11293#discussion_r1632925617
--FILE--
<?php

abstract class P {
	#[\Deprecated]
	abstract function test();
}

class Clazz extends P {
	function test() {
	}
}


$c = new Clazz();
$c->test();

$r = new ReflectionMethod('P', 'test');
var_dump($r->isDeprecated());

$r = new ReflectionMethod('Clazz', 'test');
var_dump($r->isDeprecated());

?>
--EXPECTF--
bool(true)
bool(false)
