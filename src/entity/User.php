<?php

namespace acolish\entity;


class User
{
	const TYPE_NORMAL = 0;
	const TYPE_SPECIAL = 1;

    const STATUS_UNANSWERED = 0;
    const STATUS_NON_PARTICIPANT = 1;
    const STATUS_UNSELECTED_PRESENT = 2;
    const STATUS_SELECTED_PRESENT = 10;

	private $id;

	private $name;

	private $type;

    private $status;

	public function __construct($record)
	{
		$this->id = isset($record['id']) ? $record['id'] : null;
		$this->name = isset($record['name']) ? $record['name'] : null;
		$this->type = isset($record['type']) ? $record['type'] : null;
        $this->status = isset($record['status']) ? $record['status'] : null;
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