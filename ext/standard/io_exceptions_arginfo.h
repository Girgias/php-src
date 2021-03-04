/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: a253c10f25a4e0cd8243881a3144a4f913c4b38f */




static const zend_function_entry class_FileSystem_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_Network_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_FileSystemError_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_FileNotFound_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_NotDirectory_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_InsufficientPermissions_methods[] = {
	ZEND_FE_END
};


static const zend_function_entry class_TemporaryFailure_methods[] = {
	ZEND_FE_END
};

static zend_class_entry *register_class_FileSystem(zend_class_entry *class_entry_IO)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "FileSystem", class_FileSystem_methods);
	class_entry = zend_register_internal_interface(&ce);
	zend_class_implements(class_entry, 1, class_entry_IO);

	return class_entry;
}

static zend_class_entry *register_class_Network(zend_class_entry *class_entry_IO)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "Network", class_Network_methods);
	class_entry = zend_register_internal_interface(&ce);
	zend_class_implements(class_entry, 1, class_entry_IO);

	return class_entry;
}

static zend_class_entry *register_class_FileSystemError(zend_class_entry *class_entry_Exception, zend_class_entry *class_entry_FileSystem)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "FileSystemError", class_FileSystemError_methods);
	class_entry = zend_register_internal_class_ex(&ce, class_entry_Exception);
	zend_class_implements(class_entry, 1, class_entry_FileSystem);

	return class_entry;
}

static zend_class_entry *register_class_FileNotFound(zend_class_entry *class_entry_Exception, zend_class_entry *class_entry_FileSystem)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "FileNotFound", class_FileNotFound_methods);
	class_entry = zend_register_internal_class_ex(&ce, class_entry_Exception);
	zend_class_implements(class_entry, 1, class_entry_FileSystem);

	return class_entry;
}

static zend_class_entry *register_class_NotDirectory(zend_class_entry *class_entry_Exception, zend_class_entry *class_entry_FileSystem)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "NotDirectory", class_NotDirectory_methods);
	class_entry = zend_register_internal_class_ex(&ce, class_entry_Exception);
	zend_class_implements(class_entry, 1, class_entry_FileSystem);

	return class_entry;
}

static zend_class_entry *register_class_InsufficientPermissions(zend_class_entry *class_entry_Exception, zend_class_entry *class_entry_FileSystem)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "InsufficientPermissions", class_InsufficientPermissions_methods);
	class_entry = zend_register_internal_class_ex(&ce, class_entry_Exception);
	zend_class_implements(class_entry, 1, class_entry_FileSystem);

	return class_entry;
}

static zend_class_entry *register_class_TemporaryFailure(zend_class_entry *class_entry_Exception, zend_class_entry *class_entry_Network)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "TemporaryFailure", class_TemporaryFailure_methods);
	class_entry = zend_register_internal_class_ex(&ce, class_entry_Exception);
	zend_class_implements(class_entry, 1, class_entry_Network);

	return class_entry;
}
