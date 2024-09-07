<?php

namespace App\ProduceProviders;

use App\IProvider;
use Exception;
use External\Baz\Auth\Authenticator;
use External\Baz\Auth\Responses\Success;
use External\Baz\Movies\MovieService;
use External\Baz\Exceptions\ServiceUnavailableException;
use Illuminate\Support\Facades\Cache;

class BazProvider implements IProvider
{
    public const CACHE_KEY  = 'bar_movie_titles';
    public function __construct(
        private Authenticator $authenticator,
        private MovieService $movieService,
    ) {
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login(string $login, string $password): bool
    {
        $response = $this->authenticator->auth($login, $password);
        if ($response instanceof Success) {
            return true;
        } else {
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
            $titles = $this->getTitlesFromService();

            return $this->convertToStandardView($titles);
        } catch (ServiceUnavailableException $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    private function convertToStandardView(array $titles): array
    {
        return $this->getDeepestLevel($titles);
    }

    /**
     * @param array $array
     * @return array
     */
    private function getDeepestLevel(array $array): array
    {
        $result = [];
        foreach ($array as $element) {
            if (is_array($element)) {
                $result = array_merge($result, $this->getDeepestLevel($element));
            } else {
                $result[] = $element;
            }
        }
        return $result;
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
