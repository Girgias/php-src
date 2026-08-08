--TEST--
sscanf(): test %[] specifier with edge cases
--FILE--
<?php

try {
	$out = null;
	var_dump(sscanf('[hello]', '%[][helo]', $out));
    var_dump($out);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), PHP_EOL;
}
try {
	$out = null;
	var_dump(sscanf('-in-', '%[-i-n]', $out));
    var_dump($out);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), PHP_EOL;
}
try {
	$out = null;
	var_dump(sscanf('-[in]-', '%[][-i-n-]', $out));
    var_dump($out);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), PHP_EOL;
}
?>
--EXPECT--
int(1)
string(7) "[hello]"
int(1)
string(4) "-in-"
int(1)
string(6) "-[in]-"
