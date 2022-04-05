<?php

namespace App\Http\Controllers;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    public function authenticate(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, true)) {
            return $this->respondError('wrong data');
        }

        $token = Auth::user()->createToken('api-token');

        return $this->respondWithSuccess([
            'token' => $token->plainTextToken,  // Приобразуем в текст
        ]);
    }
}
