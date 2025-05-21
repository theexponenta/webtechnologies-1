<?php

declare(strict_types=1);

class Utils {
    static function rrmdir(string $dir): void { 
        if (is_dir($dir)) { 
          $objects = scandir($dir);
          foreach ($objects as $object) { 
            if ($object !== "." && $object !== "..") { 
              if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                Utils::rrmdir($dir. DIRECTORY_SEPARATOR .$object);
              else
                unlink($dir. DIRECTORY_SEPARATOR .$object); 
            } 
          }
          rmdir($dir); 
        } 
    }

    public static function hashPassword(string $password, string $salt) : string {
      return hash('sha256', $password.':'.$salt);
    }

    public static function setUserSessionData(User $user): void {
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'token' => $user->getToken()
        ];
    }
}

