<?php

namespace acolish\model;


use acolish\config\DatabaseConfig;

class User
{
	public function getUserById($id)
	{
		return new \acolish\entity\User(
			(new \acolish\dao\database\User(
				DatabaseConfig::getInstance()
			)
			)->fetchUserById($id)
		);
	}
}