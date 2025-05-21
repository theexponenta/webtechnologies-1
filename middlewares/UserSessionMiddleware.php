<?php

declare(strict_types=1);


require_once __DIR__.'/../services/UserService.php';
require_once __DIR__.'/../repositories/UserRepository.php';
require_once __DIR__.'/../database/DBSession.php';
require_once __DIR__.'/../routing/Request.php';
require_once __DIR__.'/../utils/Utils.php';


class UserSessionMiddleware {
    private UserService $userService;

    public function __construct(DBSession $dbSession) {
        $this->userService = new UserService(new UserRepository($dbSession));
    }

    public function __invoke(Request $request, callable $next) {
        if (!isset($_SESSION['user']) && isset($_COOKIE['session_token'])) {
            $user = $this->userService->getByToken($_COOKIE['session_token']);
            if ($user) {
                Utils::setUserSessionData($user);
            }
        }

        return $next($request);
    }
}
