<?php

namespace acolish\dao;

use acolish\config\DatabaseConfig;

abstract class Database
{
	private $host;

	private $name;

	private $user;

	private $password;

	/**
	 * Database constructor.
	 * @param DatabaseConfig $setting
	 */
	public function __construct(DatabaseConfig $setting)
	{
		$this->host = $setting->getHost();
		$this->name = $setting->getName();
		$this->user = $setting->getUser();
		$this->password = $setting->getPassword();
	}

	protected function getPDO()
	{
		$pdo = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf-8', $this->user, $this->password);
		$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		return $pdo;
	}
}