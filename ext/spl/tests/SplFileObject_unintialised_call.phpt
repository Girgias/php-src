--TEST--
SplFileObject child call parent object not inititialized
--FILE--
<?php

$o = new SplTempFileObject();
$methods = get_class_methods($o);

foreach ($methods as $method) {
    if ($method === '__construct') {
        continue;
    }
    echo 'Calling method: ', $method, \PHP_EOL;
    try {
        $o->$method();
    } catch (\Error $e) {
        echo $e::class, ': ', $e->getMessage(), \PHP_EOL;
    }
}
?>
--EXPECT--
Calling method: rewind
Error: The parent constructor was not called: the object is in an invalid state
Calling method: eof
Error: The parent constructor was not called: the object is in an invalid state
Calling method: valid
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fgets
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fread
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fgetcsv
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fputcsv
Error: The parent constructor was not called: the object is in an invalid state
Calling method: setCsvControl
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getCsvControl
Error: The parent constructor was not called: the object is in an invalid state
Calling method: flock
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fflush
Error: The parent constructor was not called: the object is in an invalid state
Calling method: ftell
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fseek
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fgetc
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fpassthru
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fscanf
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fwrite
Error: The parent constructor was not called: the object is in an invalid state
Calling method: fstat
Error: The parent constructor was not called: the object is in an invalid state
Calling method: ftruncate
Error: The parent constructor was not called: the object is in an invalid state
Calling method: current
Error: The parent constructor was not called: the object is in an invalid state
Calling method: key
Error: The parent constructor was not called: the object is in an invalid state
Calling method: next
Error: The parent constructor was not called: the object is in an invalid state
Calling method: setFlags
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getFlags
Error: The parent constructor was not called: the object is in an invalid state
Calling method: setMaxLineLen
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getMaxLineLen
Error: The parent constructor was not called: the object is in an invalid state
Calling method: hasChildren
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getChildren
Error: The parent constructor was not called: the object is in an invalid state
Calling method: seek
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getCurrentLine
Error: The parent constructor was not called: the object is in an invalid state
Calling method: __toString
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getPath
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getFilename
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getExtension
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getBasename
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getPathname
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getPerms
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getInode
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getSize
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getOwner
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getGroup
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getATime
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getMTime
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getCTime
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getType
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isWritable
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isReadable
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isExecutable
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isFile
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isDir
Error: The parent constructor was not called: the object is in an invalid state
Calling method: isLink
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getLinkTarget
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getRealPath
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getFileInfo
Error: The parent constructor was not called: the object is in an invalid state
Calling method: getPathInfo
Error: The parent constructor was not called: the object is in an invalid state
Calling method: openFile
Error: The parent constructor was not called: the object is in an invalid state
Calling method: setFileClass
Error: The parent constructor was not called: the object is in an invalid state
Calling method: setInfoClass
Error: The parent constructor was not called: the object is in an invalid state
Calling method: __debugInfo
Error: The parent constructor was not called: the object is in an invalid state
Calling method: _bad_state_ex
Error: The parent constructor was not called: the object is in an invalid state
