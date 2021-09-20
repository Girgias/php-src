--TEST--
Test finfo_open() function : error functionality
--EXTENSIONS--
fileinfo
--FILE--
<?php
$magicFile = __DIR__ . DIRECTORY_SEPARATOR . 'magic';

echo "*** Testing OO API of finfo: errors ***\n";

try {
    $o = new finfo('foobar');
    var_dump($o);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), "\n";
}

try {
    var_dump( new finfo(0x0000080) );
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), "\n";
}

try {
    $o = new finfo(FILEINFO_MIME, 'foobarfile');
    var_dump($o);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), "\n";
}

try {
    $o = new finfo(PHP_INT_MAX - 1, $magicFile);
    var_dump($o);
} catch (Throwable $e) {
    echo $e::class, ': ', $e->getMessage(), "\n";
}

?>
--EXPECTF--
*** Testing OO API of finfo: errors ***
TypeError: finfo::__construct(): Argument #1 ($flags) must be of type int, string given
object(finfo)#1 (0) {
}
Exception: finfo::__construct(%s/foobarfile): Failed to open stream: No such file or directory
Exception: finfo::__construct(): using regular magic file `%smagic'
