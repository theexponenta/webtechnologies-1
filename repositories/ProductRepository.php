<?php


declare(strict_types=1);

require_once __DIR__.'/EntityRepository.php';
require_once __DIR__.'/../models/Product.php';


class ProductRepository extends EntityRepository {

    public function __construct(DBSession $dbSession) {
        parent::__construct($dbSession, "products", Product::class);
    }
    
}
