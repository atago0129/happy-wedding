<?php

class UserRecordGenerator
{
	const USER_ID_SALT = 'salt';

	public function exec()
	{
		while (($line = fgets(STDIN)) !== false) {
			$line = trim($line);
			list($name, $type) = explode("\t", $line);
		}
	}
}