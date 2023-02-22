<?php

class AuthGuard {
    public function __construct() {
        return $this->run();
    }

    public function run() {
        if (!(new Auth())->isLoggedIn()) {
            return ['message' => 'Authentication Failed', 'responseCode' => 403];
        }
        return true;
    }
}
