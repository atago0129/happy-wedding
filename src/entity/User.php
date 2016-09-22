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

    private $displayName;

	private $type;

    private $status;

	public function __construct($record)
	{
		$this->id = isset($record['id']) ? $record['id'] : null;
		$this->name = isset($record['name']) ? $record['name'] : null;
        $this->displayName = isset($record['displayName']) ? $record['displayName'] : null;
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

	public function getDisplayName()
    {
        return $this->displayName;
    }

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

    /**
     * @return int
     */
	public function getStatus()
    {
        return $this->status;
    }

	public function toAssoc()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->type,
		];
	}

	public function setStatus($status)
    {
        $this->status = $status;
    }
}