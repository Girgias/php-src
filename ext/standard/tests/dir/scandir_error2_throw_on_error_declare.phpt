--TEST--
Test scandir() function error conditions with throw on error declare enabled - Non-existent directory
--SKIPIF--
<?php
if (substr(PHP_OS, 0, 3) == 'WIN') {
    die('skip.. Not valid for Windows');
}
?>
--FILE--
<?php

declare(throw_on_error=1);

echo "*** Testing scandir() : error conditions ***\n";
$directory = __DIR__ . '/idonotexist';

echo "\n-- Pass scandir() an absolute path that does not exist --\n";
try {
    var_dump(scandir($directory));
} catch (\FileSystemError $e) {
    echo $e->getMessage() . \PHP_EOL;
}

echo "\n-- Pass scandir() a relative path that does not exist --\n";
try {
    var_dump(scandir('/idonotexist'));
} catch (\FileSystem $e) {
    echo $e->getMessage() . \PHP_EOL;
}
?>
--EXPECT--
*** Testing scandir() : error conditions ***

-- Pass scandir() an absolute path that does not exist --
(errno 2): No such file or directory

-- Pass scandir() a relative path that does not exist --
(errno 2): No such file or directory
