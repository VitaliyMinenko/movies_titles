<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AuthRequestDto;
use App\Http\Requests\AuthRequest;
use App\Serializer;
use App\Services\AuthServiceResolver;
use App\Services\UserService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *
 */
class AuthController extends Controller
{
    /**
     * @param AuthServiceResolver $authServiceResolver
     * @param Serializer $JmsSerializer
     * @param UserService $userService
     */
    public function __construct(
        private AuthServiceResolver $authServiceResolver,
        private Serializer $JmsSerializer,
        private UserService $userService
    ) {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @throws Exception
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $loginRequest = $request->validated();
        $AuthRequestDto = $this->JmsSerializer->serializer->deserialize(json_encode($loginRequest), AuthRequestDto::class, 'json');
        $AuthResponseDto = $this->authServiceResolver->resolve($AuthRequestDto);
        if ($AuthResponseDto->isAuth()) {
            $user = $this->userService->getCurrentUser($AuthRequestDto);
            $token = JWTAuth::claims([
                'login' => $AuthRequestDto->getLogin(),
                'system' => $AuthResponseDto->getProvider(),
            ])->fromUser($user);

            return response()->json([
                'status' => 'success',
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status' => 'failure',
            ]);
        }
    }
}
