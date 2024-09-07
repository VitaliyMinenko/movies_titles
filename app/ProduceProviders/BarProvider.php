<?php

namespace App\ProduceProviders;

use App\IProvider;
use Exception;
use External\Bar\Auth\LoginService;
use External\Bar\Movies\MovieService;
use External\Bar\Exceptions\ServiceUnavailableException;
use Illuminate\Support\Facades\Cache;

class BarProvider implements IProvider
{
    public const CACHE_KEY  = 'bar_movie_titles';

    /**
     * @param LoginService $loginService
     * @param MovieService $movieService
     */
    public function __construct(
        private LoginService $loginService,
        private MovieService $movieService
    ) {
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login(string $login, string $password): bool
    {
        $this->loginService->login($login, $password);
    }

    /**
     * @throws ServiceUnavailableException
     * @throws Exception
     */
    public function getTitles(): array
    {
        try {
            $titles = $this->getTitlesFromService();

            return $this->convertToStandardView($titles);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param array $titles
     * @return array
     */
    private function toStandardView(array $titles): array
    {
        return array_column($titles['titles'], 'title');
    }

    /**
     * @throws ServiceUnavailableException
     */
    private function getTitlesFromService(): array
    {
        try {
            $titles = retry(5, function () {
                return $this->movieService->getTitles();
            }, 100);
            Cache::put(self::CACHE_KEY, $titles, 600);

            return $titles;
        } catch (ServiceUnavailableException $e) {
            if (Cache::has(self::CACHE_KEY)) {
                return Cache::get(self::CACHE_KEY);
            }
            throw $e;
        }
    }
}
