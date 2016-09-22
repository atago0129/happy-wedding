<?php

namespace acolish\dao\database;

use acolish\dao\Database;

class User extends Database
{
	/**
	 * @param string $id
	 * @return \acolish\entity\User
	 */
	public function fetchUserById($id)
	{
		$sql = 'SELECT * FROM user LEFT OUTER JOIN user_gift ON user.id = user_gift.user_id WHERE user.id = :id';
		$pdo = $this->getPDO();
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_STR);

		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function updateStatus($id, $status)
    {
        $sql = 'UPDATE user SET status = :status WHERE id = :id';
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':status', $status, \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function insertUserGift($userId, $giftId)
    {
        $sql = 'INSERT INTO user_gift (user_id, gift_id) VALUES (:user_id, :gift_id)';
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':gift_id', $giftId);

        $stmt->execute();
    }
}