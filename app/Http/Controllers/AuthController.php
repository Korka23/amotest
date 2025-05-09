<?php

namespace App\Http\Controllers;

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
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

    /**
     * @throws InvalidArgumentException
     * @throws AmoCRMMissedTokenException
     */
    public function accessToken()
    {
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient();
        $longLivedAccessToken = new LongLivedAccessToken(config('amocrm.access_token'));
        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain(config('amocrm.base_domain'));

        try {
            $apiClient->contacts()->get();
        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {
            return response(['error' => $e->getMessage()], 470);
        }
        return response()->noContent();
    }

}