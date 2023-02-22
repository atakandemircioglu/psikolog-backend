<?php

class UserController {
    public function login($username, $password) {
        $user = (new UserModel())->getByUsername($username);

        if ($user["password"] !== $password) {
            return false;
        }

        session_start();
        $_SESSION["loggedIn"] = true;
        $_SESSION["username"] = $username;

        return $_SESSION;
    }
}
