<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\AuthRequestDto;
use App\Dto\AuthResponseDto;
use Exception;
use Illuminate\Support\Facades\App;

class AuthServiceResolver
{
    /**
     * @param string $login
     * @param array $keys
     * @return string|null
     */
    public function extractPrefix(string $login, array $keys): ?string
    {
        foreach ($keys as $prefix) {
            if (str_starts_with($login, $prefix)) {
                return $prefix;
            }
        }

        return null;
    }


    /**
     * @throws Exception
     */
    public function resolve(AuthRequestDto $loginRequestDto): AuthResponseDto
    {
        $mappings = config('providers');
        $keys = array_keys($mappings);
        $loginPrefix = $this->extractPrefix($loginRequestDto->getLogin(), $keys);
        $isAuth = App::make($mappings[$loginPrefix])
                ->login($loginRequestDto
                ->getLogin(), $loginRequestDto
                ->getPassword());
        $authResponseDto = new AuthResponseDto();
        $authResponseDto->setIsAuth($isAuth);
        $authResponseDto->setProvider($loginPrefix);

        return $authResponseDto;
    }
}
