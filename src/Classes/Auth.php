<?php

class Auth {
    public function authenticate($user) {
        if (!$this->isLoggedIn()) {
            $_SESSION["loggedIn"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["group"] = $user["group"];
        }

        return $_SESSION;
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['loggedIn'])) {
            return false;
        }

        return true;
    }

    public function logout() {
        return session_destroy();
    }
}
