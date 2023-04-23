--TEST--
String assignment from $_GET in eval()
--XFAIL--
Any string from $_GET should not return true on an is_literal() check
--FILE--
$_GET['evil'] = 'hi';
eval('$foo = "'.$_GET['evil'].'";');

var_dump($foo);
var_dump(is_literal($foo));
--EXPECT--
string(2) "hi"
bool(false)
