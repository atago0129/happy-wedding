<?php

namespace acolish\model;

use acolish\config\DatabaseConfig;
use acolish\dao\database\Gift as GiftDatabase;
use acolish\entity\Gift as GiftEntity;

class Gift
{
    public function getById($id) {
        $record = (new GiftDatabase(DatabaseConfig::getInstance()))->fetchById($id);
        if (!$record) {
            return null;
        }
        return new GiftEntity($record);
    }

    public function getGiftsByType($type) {
        $records = (new GiftDatabase(DatabaseConfig::getInstance()))->fetchAllByType($type);

        $list = [];
        foreach ($records as $record) {
            $list[] = new GiftEntity($record);
        }

        return $list;
    }

}