<?php


require_once __DIR__.'/../database/DBSession.php';


class MainController {

    private DBSession $db_session;

    public function __construct($db_session) {
        $this->db_session = $db_session;
    }

    public function view() {
        include __DIR__.'/../views/main.html';    
    }

}
