<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\Migrations\Provider\Exception\ProviderException;
use Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RobloxApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getToken(string $code, string $code_verifier): array
    {
        $response = $this->client->request('POST', "https://apis.roblox.com/oauth/v1/token", [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',  // Définir le type de contenu
            ],
            'body' => [
                'code' => $code,
                'code_verifier' => $code_verifier,
                'client_id' => $_ENV['OAUTH_ROBLOX_CLIENT_ID'],
                'grant_type' => "authorization_code",
                'client_secret' => $_ENV['OAUTH_ROBLOX_CLIENT_SECRET'],
            ]
        ]);

        return [
            'token' => $response->toArray()["access_token"],
            'refresh_token' => $response->toArray()["refresh_token"]
        ];
    }

    public function getUserInfo(string $token): array
    {
        $response = $this->client->request('GET', "https://apis.roblox.com/oauth/v1/userinfo", [
            "headers" => [
                "Authorization" => "Bearer {$token}",
            ]
        ]);

        return $response->toArray();
    }

    public function getUserAvatars(array& $userArray): void
    {
        $response = $this->client->request('GET',
            "https://thumbnails.roblox.com/v1/users/avatar-headshot?format=png&size=48x48&isCircular=true"
            . "&userIds=" . urlencode(implode(",", array_keys($userArray))),
        );

        foreach (json_decode($response->getContent(), true)['data'] as $user) {
            $userArray[$user['targetId']] = $user['imageUrl'];
            if($user['imageUrl'] === "")
            {
                $userArray[$user['targetId']] = json_decode($this->client->request('GET',
                    "https://thumbnails.roblox.com/v1/users/avatar-headshot?format=png&size=48x48&isCircular=true"
                    . "&userIds=" . $user['targetId']
                )->getContent())['data'][0]['imageUrl'];
            }
        }
    }

    public function getLoginData(UserRepository $re): array
    {
        $loginData = [
            'logged' => false,
            'user' => null,
        ];
        if(isset($_COOKIE['TOKEN'])){
            $token = $_COOKIE['TOKEN'];
            if($token != null && $re->findByToken($token)){
                $loginData['logged'] = true;
                $loginData['user'] = $re->findByToken($token);
            }
        }
        return $loginData;
    }
}
