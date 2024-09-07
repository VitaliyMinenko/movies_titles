<?php

namespace App\ProduceProviders;

use App\IProvider;
use Exception;
use External\Foo\Auth\AuthWS;
use External\Foo\Exceptions\ServiceUnavailableException;
use External\Foo\Movies\MovieService;
use Illuminate\Support\Facades\Cache;

class FooProvider implements IProvider
{
    private const CACHE_KEY = 'foo_movie_titles';
    public function __construct(
        private AuthWS $authWS,
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
        try {
            $this->authWS->authenticate($login, $password);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @throws ServiceUnavailableException
     * @throws Exception
     */
    public function getTitles(): array
    {
        try {
            return $this->getTitlesFromService();
        } catch (ServiceUnavailableException $exception) {
            throw new Exception($exception->getMessage());
        }
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
        } catch (\Exception $e) {
            throw new ServiceUnavailableException($e->getMessage());
        }
    }
}
