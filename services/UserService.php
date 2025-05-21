<?php

declare(strict_types=1);


require_once __DIR__.'/../repositories/UserRepository.php';
require_once __DIR__.'/../models/User.php';


class UserService {

    private UserRepository $userRepository;

    private static int $SALT_BYTES_LENGTH = 32;
    private static int $TOKEN_BYTES_LENGTH = 32;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function emailExists(string $email): bool {
        return $this->userRepository->emailExists($email);
    }

    public function getByEmail(string $email): ?User {
        return $this->userRepository->getByEmail($email);
    }

    public function register(string $firstName, string $lastName, string $email, string $password): User {
        $salt = bin2hex(random_bytes(UserService::$SALT_BYTES_LENGTH));
        $hashedPassword = Utils::hashPassword($password, $salt);
        $now = new DateTime(); 

        return $this->userRepository->add($firstName, $lastName, $email, $hashedPassword, $salt, null, $now, $now, true);
    }

    public function generateNewToken(int $userId): string {
        $token = bin2hex(random_bytes(UserService::$TOKEN_BYTES_LENGTH));
        $this->userRepository->updateToken($userId, $token);
        return $token;
    }

    public function unsetToken(int $userId): void {
        $this->userRepository->updateToken($userId, null);
    }

    public function getByToken(string $token): ?User {
        return $this->userRepository->getByToken($token);
    }
}
