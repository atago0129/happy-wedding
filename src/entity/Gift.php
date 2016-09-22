<?php

namespace acolish\entity;


class Gift
{
    const TYPE_NORMAL = 0;
    const TYPE_SPECIAL = 1;

    /** @var string */
    private $id;

    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $image;

    public function __construct($record)
    {
        $this->id = isset($record['id']) ? $record['id'] : null;
        $this->type = isset($record['type']) ? intval($record['type']) : null;
        $this->name = isset($record['name']) ? $record['name'] : null;
        $this->description = isset($record['description']) ? $record['description'] : null;
        $this->image = isset($record['image']) ? $record['image'] : null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getType()
    {
        return $this->type;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function toAssoc() {
        return [
            'key' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }

}