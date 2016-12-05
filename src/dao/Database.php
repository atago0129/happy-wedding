<?php

namespace acolish\dao;

use acolish\config\DatabaseConfig;

abstract class Database
{
    private $pdo;

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
        if ($this->pdo) {
            return $this->pdo;
        }
		$this->pdo = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf8', $this->user, $this->password);
		$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		return $this->pdo;
	}
}