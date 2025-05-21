<?php

declare(strict_types=1);


require_once 'ErrorCode.php';
require_once __DIR__.'/../services/UserService.php';
require_once __DIR__.'/../repositories/UserRepository.php';


class SessionController {

    private UserService $userService;
    private static int $REMEMBER_TIME = 60 * 60 * 24 * 30;
    private static string $CAPTHA_URL = "https://www.google.com/recaptcha/api/siteverify";
    
    public function __construct(DBSession $dbSession) {
        $this->userService = new UserService(new UserRepository($dbSession));
    }

    public function checkCaptcha(string $captchaResponse): bool {
        $config = Config::getConfig();
        $secretKey = $config["captcha"]["secret_key"];
        $url = SessionController::$CAPTHA_URL."?secret=$secretKey&response=$captchaResponse";
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);
        return $responseKeys["success"];
    }

    public function register(Request $request): string {
        $params = $request->getFormParams();
        $firstName = $params['first_name'] ?? null;
        $lastName = $params['last_name'] ?? null;
        $password = $params['password'] ?? null;
        $email = $params['email'] ?? null;
        $captchaResponse = $params['g-recaptcha-response'] ?? null;

        $response = ['success' => false];
        
        if ($captchaResponse === null || !$this->checkCaptcha($captchaResponse)) {
            $response['errorCode'] = ErrorCode::CAPTCHA_FAILED->value;
            return json_encode($response);
        }

        if ($firstName === null || $lastName === null || $password === null || $email === null) {
            $response['errorCode'] = ErrorCode::ALL_FIELDS_REQUIRED->value;
            return json_encode($response);
        }

        if ($this->userService->emailExists($email)) {
            $response['errorCode'] = ErrorCode::EMAIL_EXISTS->value;
            return json_encode($response);
        }

        $user = $this->userService->register($firstName, $lastName, $email, $password);
        $response['success'] = true;
        $response['redirect'] = '/';
        
        Utils::setUserSessionData($user);

        return json_encode($response);
    }

    public function logout(Request $request): string {
        $this->userService->unsetToken($_SESSION['user']['id']);
        session_destroy();
        setcookie('session_token', '', 0, '/');
        return json_encode(["redirect" => "/"]);
    }

    public function login(Request $request): string {
        $params = $request->getFormParams();
        $email = $params['email'] ?? null;
        $password = $params['password'] ?? null;
        $rememberMe = $params['remember_me'] ?? false;
        $captchaResponse = $params['g-recaptcha-response'] ?? null;

        $response = ['success' => false];

        if ($captchaResponse === null || !$this->checkCaptcha($captchaResponse)) {
            $response['errorCode'] = ErrorCode::CAPTCHA_FAILED->value;
            return json_encode($response);
        }

        if ($email == null || $password == null) {
            $response['errorCode'] = ErrorCode::ALL_FIELDS_REQUIRED->value;
            return json_encode($response);
        }

        $user = $this->userService->getByEmail($email);

        if (!$user) {
            $response['errorCode'] = ErrorCode::INCORRECT_EMAIL_OR_PASSWORD->value;
            return json_encode($response);
        }

        $hashedPassword = Utils::hashPassword($password, $user->getSalt());
        if ($hashedPassword !== $user->getPassword()) {
            $response['errorCode'] = ErrorCode::INCORRECT_EMAIL_OR_PASSWORD->value;
            return json_encode($response);
        }

        if ($rememberMe === 'on') {
            $token = $user->getToken();
            if ($token === null) {
                $token = $this->userService->generateNewToken($user->getId());
            }

            setcookie('session_token', $token, time() + SessionController::$REMEMBER_TIME, '/');
        }

        Utils::setUserSessionData($user);
        
        $response['success'] = true;
        $response['redirect'] = '/';

        return json_encode($response);
    } 
}

