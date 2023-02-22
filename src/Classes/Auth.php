<?php

class Auth {
    public function authenticate($user) {
        session_start();
        $_SESSION["loggedIn"] = true;
        $_SESSION["username"] = $user["username"];
        $_SESSION["id"] = $user["id"];
        return $_SESSION;
    }
}
