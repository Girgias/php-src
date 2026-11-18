--TEST--
Static variables behaviour within static methods
--FILE--
<?php

class C1 {
	static function foo() {
		static $v = 5;
		$v++;
		var_dump($v);
	}
}
class C2 extends C1 {}

C1::foo();
C2::foo();

?>
--EXPECT--
int(6)
int(7)
