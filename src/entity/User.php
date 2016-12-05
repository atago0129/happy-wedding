<?php

namespace acolish\entity;


class User
{
	const GIFT_TYPE_NORMAL = 0;
	const GIFT_TYPE_SPECIAL = 1;

    const STATUS_UNANSWERED = 0;
    const STATUS_NON_PARTICIPANT = 1;
    const STATUS_PARTICIPANT = 2;

	private $id;

	private $name;

    private $displayName;

	private $giftType;

    private $status;

    private $giftId;

	public function __construct($record)
	{
		$this->id = isset($record['id']) ? $record['id'] : null;
		$this->name = isset($record['name']) ? $record['name'] : null;
        $this->displayName = isset($record['displayName']) ? $record['displayName'] : null;
		$this->giftType = isset($record['gift_type']) ? intval($record['gift_type']) : null;
        $this->status = isset($record['status']) ? intval($record['status']) : null;
        $this->giftId = isset($record['gift_id']) ? $record['gift_id'] : null;
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
	public function getGiftType()
	{
		return $this->giftType;
	}

    /**
     * @return string
     */
    public function getGiftId()
    {
        return $this->giftId;
    }

    public function setGiftId($giftId)
    {
        $this->giftId = $giftId;
    }

    /**
     * @return int
     */
	public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusString()
    {
        switch ($this->status) {
            case self::STATUS_UNANSWERED:
                return '未回答';
            case self::STATUS_NON_PARTICIPANT:
                return '欠席';
            case self::STATUS_PARTICIPANT:
                return '出席';
            default:
                throw new \RuntimeException('出欠ステータスが不整合を起こしています');
        }
    }

	public function toAssoc()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->giftType,
		];
	}

	public function setStatus($status)
    {
        $this->status = intval($status);
    }

}