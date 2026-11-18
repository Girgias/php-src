--TEST--
Static properties behaviour with static methods
--FILE--
<?php

echo 'Using static::$v++:', "\n";
class C1 {
	static $v = 5;

	static function foo() {
		static::$v++;
	}
}
class C2 extends C1 {}

var_dump(C1::$v, C2::$v);
C1::foo();
var_dump(C1::$v, C2::$v);
C2::foo();
var_dump(C1::$v, C2::$v);

echo 'Using self::$v++:', "\n";
class C3 {
	static $v = 5;

	static function foo() {
		self::$v++;
	}
}
class C4 extends C3 {}

var_dump(C3::$v, C4::$v);
C3::foo();
var_dump(C3::$v, C4::$v);
C4::foo();
var_dump(C3::$v, C4::$v);

echo 'Redefining static::$v with self::$v++:', "\n";
class C5 {
	static $v = 5;

	static function foo() {
		self::$v++;
	}
}
class C6 extends C5 {
	static $v = 10;
}

var_dump(C5::$v, C6::$v);
C5::foo();
var_dump(C5::$v, C6::$v);
C6::foo();
var_dump(C5::$v, C6::$v);

echo 'Redefining static::$v with static::$v++:', "\n";
class C7 {
	static $v = 5;

	static function foo() {
		static::$v++;
	}
}
class C8 extends C7 {
	static $v = 10;
}

var_dump(C7::$v, C8::$v);
C7::foo();
var_dump(C7::$v, C8::$v);
C8::foo();
var_dump(C7::$v, C8::$v);
?>
DONE
--EXPECT--
Using static::$v++:
int(5)
int(5)
int(6)
int(6)
int(7)
int(7)
Using self::$v++:
int(5)
int(5)
int(6)
int(6)
int(7)
int(7)
Redefining static::$v with self::$v++:
int(5)
int(10)
int(6)
int(10)
int(7)
int(10)
Redefining static::$v with static::$v++:
int(5)
int(10)
int(6)
int(10)
int(6)
int(11)
DONE
