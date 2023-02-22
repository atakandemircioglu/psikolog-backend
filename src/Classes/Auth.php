<?php

class Auth {
    public function authenticate($user) {
        if (!$this->isLoggedIn()) {
            $_SESSION["loggedIn"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["id"] = $user["id"];
        }

        return $_SESSION;
    }

    public function isLoggedIn() {
        session_start();
        if (!isset($_SESSION['loggedIn'])) {
            return false;
        }

        return true;
    }
}
