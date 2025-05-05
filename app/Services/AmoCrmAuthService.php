<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;

class AmoCrmAuthService
{
    protected $client;

    public function __construct()
    {
        $this->client = new AmoCRMApiClient(
            config('amocrm.client_id'),
            config('amocrm.client_secret'),
            config('amocrm.redirect_uri')
        );
    }

    public function getAuthUrl(): string
    {
        return $this->client->getOAuthClient()->getAuthorizeUrl([
            'redirect_uri' => config('amocrm.redirect_uri')
        ]);
    }

    public function exchangeCodeForToken(string $code): void
    {
        $token = $this->client->getOAuthClient()->getAccessTokenByCode($code);

        $this->client->setAccessToken($token)
            ->setAccountBaseDomain($token->getValues()['baseDomain'])
            ->onAccessTokenRefresh(function ($token, $domain) {
                $this->saveToken($token, $domain);
            });

        $this->saveToken($token, $token->getValues()['baseDomain']);
    }

    protected function saveToken($token, $domain)
    {
        file_put_contents(storage_path('app/tokens.json'), json_encode([
            'access_token' => $token->getToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires' => $token->getExpires(),
            'base_domain' => $domain
        ]));
    }

    public function loadToken()
    {
        $data = json_decode(file_get_contents(storage_path('app/tokens.json')), true);

        $accessToken = new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires' => $data['expires']
        ]);

        $this->client->setAccessToken($accessToken)
            ->setAccountBaseDomain($data['base_domain'])
            ->onAccessTokenRefresh(function ($token, $domain) {
                $this->saveToken($token, $domain);
            });

        return $this->client;
    }
}