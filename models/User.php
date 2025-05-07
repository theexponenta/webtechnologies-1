<?php

declare(strict_types=1);

require_once 'Model.php';


class User extends Model {
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $salt;
    private string $token;
    private DateTime $registerTime;
    private DateTime $lastLogin;
    private bool $isVerified;

    public function __construct(int $id, string $firstName, string $lastName, string $email, string $password, string $salt, string $token, DateTime $registerTime, DateTime $lastLogin, bool $isVerified) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;
        $this->token = $token;
        $this->registerTime = $registerTime;
        $this->lastLogin = $lastLogin;
        $this->isVerified = $isVerified;
    }

    public function getId() {
        return $this->id;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getToken() {
        return $this->token;
    }

    public function getRegisterTime() {
        return $this->registerTime;
    }

    public function getLastLogin() {
        return $this->lastLogin;
    }

    public function isVerified() {
        return $this->isVerified;
    }

    public static function fromRow(array $row): User {
        return new User(
            (int)$row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['password'],
            $row['salt'],
            $row['token'],
            new DateTime($row['register_time']),
            new DateTime($row['last_login']),
            (bool)$row['is_verified']
        );
    }
}
