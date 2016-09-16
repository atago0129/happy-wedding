<?php

namespace acolish\config;

class DatabaseConfig
{
	static private $instance;

	private $host;

	private $name;

	private $user;

	private $password;

	private function __construct()
	{
		$setting = require_once __DIR__ . '/../../conf/db.php';
		$this->host = isset($setting['host']) ? $setting['host'] : null;
		$this->name = isset($setting['name']) ? $setting['name'] : null;
		$this->user = isset($setting['user']) ? $setting['user'] : null;
		$this->password = isset($setting['password']) ? $setting['password'] : null;
	}

	static public function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getPassword()
	{
		return $this->password;
	}
}