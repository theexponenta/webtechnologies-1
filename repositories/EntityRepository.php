<?php


declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';
require_once __DIR__.'/../models/Model.php';


abstract class EntityRepository {

    protected DBSession $dbSession;
    protected string $tableName;
    protected string $modelType;

    public function __construct(DBSession $dbSession, string $tableName, string $modelType) {
        $this->dbSession = $dbSession;
        $this->tableName = $tableName;
        $this->modelType = $modelType;
    }

    function getById(mixed $id): ?Model {
        $result = $this->dbSession->query("SELECT * FROM $this->tableName WHERE id = ?", [$id]);
        if ($result->num_rows === 0) {
            return null;
        }

        return $this->modelType::fromRow($result->fetch_assoc());
    }

    public function getAll(): array {
        $result = $this->dbSession->query("SELECT * FROM $this->tableName");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $this->modelType::fromRow($row);
        }

        return $products;
    }

    public function deleteById(mixed $id): int {
        $this->dbSession->query("DELETE FROM $this->tableName WHERE id = ?", [$id]);
        return $this->dbSession->affetedRows();
    }

    public function updateById(mixed $id, string $field, mixed $value): void {
        $this->dbSession->query("UPDATE $this->tableName SET $field = ? WHERE id = ?", [$value, $id]);
    }
}
