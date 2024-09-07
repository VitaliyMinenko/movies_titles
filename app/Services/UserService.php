<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\AuthRequestDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getCurrentUser(AuthRequestDto $authRequestDto): User
    {
        $user = User::where('login', $authRequestDto->getLogin())->first();
        if (!$user) {
            return User::create([
                'login' => $authRequestDto->getLogin(),
                'password' => Hash::make($authRequestDto->getPassword())
            ]);
        }

        return $user;
    }
}
