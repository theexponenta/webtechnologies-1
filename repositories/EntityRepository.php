<?php


require_once __DIR__.'/../database/DBSession.php';


abstract class EntityRespository {

    protected DBSession $session;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    abstract function getById($id);
    abstract function deleteById($id);
    abstract function updateById($id, $values);
}
