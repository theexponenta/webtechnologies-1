<?php


declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';


abstract class EntityRepository {

    protected DBSession $dbSession;

    public function __construct($dbSession) {
        $this->dbSession = $dbSession;
    }

    abstract function getById($id);
    abstract function deleteById($id);
    abstract function updateById($id, $values);
}
