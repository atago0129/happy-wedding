<?php

namespace acolish\dao\database;


use acolish\dao\Database;

class Gift extends Database
{
    public function fetchById($id) {
        $sql = 'SELECT * FROM gift WHERE id = :id';
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAllByType($type) {
        $sql = 'SELECT * FROM gift WHERE type = :type';
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':type', $type, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}