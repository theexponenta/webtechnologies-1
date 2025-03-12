<?php


require_once __DIR__.'/EntityRepository.php';


class ProductRepository extends EntityRepository {

    public function getById($id) {
        throw new NotImplementedError();
    }

    public function deleteById($id) {
        throw new NotImplementedError();
    }

    public function updateById($id, $values) {
        throw new NotImplementedError();
    }

}
