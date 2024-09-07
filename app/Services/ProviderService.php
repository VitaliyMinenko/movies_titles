<?php
declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 *
 */
class ProviderService
{
    /**
     * @return Collection
     * @throws Exception
     */
    public function getAllTitles(): Collection
    {
        $providers = config('providers');
        $result = new Collection();
        try {
            foreach ($providers as $provider) {
                $titles = App::make($provider)->getTitles();
                $result->push(...$titles);
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }


        return $result;
    }
}
