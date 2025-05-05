<?php

namespace App\Http\Controllers;

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\InvalidArgumentException;
use App\Services\AmoCrmAuthService;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function redirect()
    {
        $authUrl = (new AmoCrmAuthService())->getAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        (new AmoCrmAuthService())->exchangeCodeForToken($code);
        return 'Authorization successful.';
    }

}