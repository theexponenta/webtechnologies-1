<?php


declare(strict_types=1);

require_once __DIR__.'/EntityRepository.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../models/Product.php';


class UserRepository extends EntityRepository {

    public function __construct(DBSession $dbSession) {
        parent::__construct($dbSession, "users", User::class);
    }

    public function getCart(int $id): array {
        $result = $this->dbSession->query("SELECT products.* FROM cart INNER JOIN products ON products.id = cart.product_id WHERE cart.user_id = ?", [$id]);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = Product::fromRow($row);
        }

        return $products;
    }
}