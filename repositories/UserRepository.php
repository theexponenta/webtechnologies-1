<?php


declare(strict_types=1);

require_once __DIR__.'/EntityRepository.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../models/Product.php';


class UserRepository extends EntityRepository {

    public function __construct(DBSession $dbSession) {
        parent::__construct($dbSession, "users", User::class);
    }

    public function add(string $firstName, string $lastName, string $email, string $password, string $salt, ?string $token, DateTime $registerTime, DateTime $lastLoginTime, bool $isVerified): User {
        $this->dbSession->query("INSERT INTO users (first_name, last_name, email, password, salt, token)  VALUES (?, ? , ?, ?, ?, ?)",
                                [$firstName, $lastName, $email, $password, $salt, $token]);

        return new User($this->dbSession->lastInsertId(), $firstName, $lastName, $email, $password, $salt, $token, $registerTime, $lastLoginTime, $isVerified);
    }

    public function emailExists(string $email): bool {
        $result = $this->dbSession->query("SELECT email FROM users WHERE email = ?", [$email]);
        return $result->num_rows > 0;
    }

    public function getByEmail(string $email): ?User {
        $result = $this->dbSession->query("SELECT * FROM users WHERE email = ?", [$email]);
        if ($result->num_rows == 0)
            return null;

        return User::fromRow($result->fetch_assoc());
    }

    public function getCart(int $id): array {
        $result = $this->dbSession->query("SELECT products.* FROM cart INNER JOIN products ON products.id = cart.product_id WHERE cart.user_id = ?", [$id]);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = Product::fromRow($row);
        }

        return $products;
    }

    public function addProductToCart(int $userId, int $productId): void {
        $this->dbSession->query("INSERT INTO cart (user_id, product_id) VALUES (?, ?)", [$userId, $productId]);
    }

    public function updateToken(int $userId, ?string $token): void {
        $this->dbSession->query("UPDATE users SET token = ? WHERE id = ?", [$token, $userId]);
    }

    public function getByToken(string $token): ?User {
        $result = $this->dbSession->query("SELECT * FROM users WHERE token = ?", [$token]);
        if ($result->num_rows == 0)
            return null;

        return User::fromRow($result->fetch_assoc());
    }
}