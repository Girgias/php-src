--TEST--
spl_autoload_unregister("spl_autoload_call") without registrations
--FILE--
<?php
var_dump(spl_autoload_unregister("spl_autoload_call"));
?>
Done
--EXPECTF--

Deprecated: Using autoload_call_class() as a callback for autoload_unregister_class() is deprecated, to remove all registered autoloaders, call autoload_unregister_class() for all values returned from autoload_list_class() in %s on line %d
bool(true)
Done
