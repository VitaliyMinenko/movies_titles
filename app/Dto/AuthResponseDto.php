<?php

declare(strict_types=1);

namespace App\Dto;

/**
 *
 */
class AuthResponseDto
{
    private bool $isAuth;

    private string $provider;

    /**
     * @return bool
     */
    public function isAuth(): bool
    {
        return $this->isAuth;
    }

    /**
     * @param bool $isAuth
     */
    public function setIsAuth(bool $isAuth): void
    {
        $this->isAuth = $isAuth;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }
}
