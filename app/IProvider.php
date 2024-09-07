<?php
declare(strict_types=1);

namespace App;

interface IProvider
{
    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login(string $login, string $password): bool;

    /**
     * @return array
     */
    public function getTitles(): array;
}
