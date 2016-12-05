<?php

namespace acolish\model;


use acolish\config\DatabaseConfig;
use \acolish\dao\database\User as UserDatabase;
use \acolish\entity\User as UserEntity;

class User
{
	public function getUserById($id)
	{
	    $record = (new UserDatabase(DatabaseConfig::getInstance()))->fetchUserById($id);
        if (!$record) {
            return null;
        }
		return new UserEntity($record);
	}

	public function updateUserStatus(UserEntity $user)
    {
        (new UserDatabase(DatabaseConfig::getInstance()))->updateStatus($user->getId(), $user->getStatus());
    }

    public function decisionGift(UserEntity $user)
    {
        (new UserDatabase(DatabaseConfig::getInstance()))->insertUserGift($user->getId(), $user->getGiftId());
    }
}