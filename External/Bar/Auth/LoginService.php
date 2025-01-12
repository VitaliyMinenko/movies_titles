<?php

namespace External\Bar\Auth;

class LoginService
{
    /**
     * Authenticates user. On success it returns true otherwise false.
     */
    public function login(string $login, string $password): bool
    {
        if (preg_match("/^BAR_.*/", $login, $matches)) {
            return $password === "foo-bar-baz";
        }

        return false;
    }
}
