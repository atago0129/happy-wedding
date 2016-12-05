<?php

spl_autoload_register(function ($class){
	if (strpos($class, 'acolish\\') !== 0) {
		return false;
	}

	$filePath = __DIR__ . '/' . strtr(substr($class, strlen('acolish\\')), '\\', '/') . '.php';

	if (!file_exists($filePath)) {
		return false;
	}

	require_once $filePath;
	return true;
});