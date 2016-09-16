<?php

namespace acolish\entity;


class User
{
	const TYPE_NORMAL = 0;
	const TYPE_SPECIAL = 1;

	private $id;

	private $name;

	private $type;

	public function __construct($record)
	{
		$this->id = isset($record['id']) ? $record['id'] : null;
		$this->name = isset($record['name']) ? $record['name'] : null;
		$this->type = isset($record['type']) ? $record['type'] : null;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	public function toAssoc()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->type,
		];
	}
}