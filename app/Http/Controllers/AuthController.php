<?php

namespace App\Http\Controllers;

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\InvalidArgumentException;
use App\Services\AmoCrmAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function redirect(Request $request)
    {
        Log::info(print_r($request->all(), true));
        $authUrl = (new AmoCrmAuthService())->getAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        Log::info(print_r($request->all(), true));
        $code = $request->get('code');
        (new AmoCrmAuthService())->exchangeCodeForToken($code);
        return 'Authorization successful.';
    }

}