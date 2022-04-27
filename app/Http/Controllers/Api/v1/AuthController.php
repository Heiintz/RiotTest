<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Utilities\Api\V1\ProxyRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Api\V1\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $proxy;

    public function __construct(ProxyRequest $proxy){
        $this->proxy = $proxy;
    }

    public function login(){
        $user = User::where('email', request('email'))->first();

        abort_unless($user, 403, "L' email ou le mot de passe est incorrect.");
        abort_unless(
            Hash::check(request('password'), $user->password),
            403,
            "L' email ou le mot de passe est incorrect."
        );

        $resp = $this->proxy
            ->grantPasswordToken(request('email'), request('password'));

        return response([
            'userEmail' => $user->email,
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'You have been logged in',
        ], 200);
    }

    public function refreshToken() {
        $resp = $this->proxy->refreshAccessToken();

        return response([
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'Token has been refreshed.',
        ], 200);
    }

    public function logout()
    {
        $token = request()->user()->token();
        $token->delete();

        // remove the httponly cookie
        cookie()->queue(cookie()->forget('refresh_token'));

        return response([
            'message' => 'You have been successfully logged out',
        ], 200);
    }
}