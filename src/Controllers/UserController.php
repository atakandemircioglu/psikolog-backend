<?php

class UserController {
    public function login($username, $password) {
        $user = (new UserModel())->getByUsername($username);
        if (!$user || $user["password"] !== $password) {
            return false;
        }

        return (new Auth())->authenticate($user);
    }

    public function auth() {
        if (!isset($_SESSION['loggedIn'])) {
            return false;
        }

        return $_SESSION;
    }
}
