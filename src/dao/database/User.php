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
		$sql = 'SELECT * FROM user WHERE id = :id';
		$pdo = $this->getPDO();
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_STR);

		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function insert($id, $name, $type)
	{
		$sql = 'INSERT INTO user (id, name, type) VALUES (:id, :name, :type)';
		$pdo = $this->getPDO();
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':type', $type);

		$stmt->execute();
	}

	public function updateStatus($id, $status)
    {
        $sql = 'UPDATE user SET status = :status WHERE id = :id';
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':status', $status);

        $stmt->execute();
    }
}