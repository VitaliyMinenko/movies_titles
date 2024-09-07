<?php

namespace App\Http\Controllers;

use App\Serializer;
use App\Services\ProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class MovieController extends Controller
{
    /**
     * @param ProviderService $providerService
     */
    public function __construct(
        private ProviderService $providerService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * @throws \Exception
     */
    public function getTitles(Request $request): JsonResponse
    {
        try {
            $titleCollection = $this->providerService->getAllTitles();

            return response()->json($titleCollection->toJson());
        } catch (\Exception $exception) {
            return response()->json(['status' => 'failure']);
        }
    }
}
