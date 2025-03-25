<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Service\WebsocketService;
use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use App\Service\RobloxApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiController extends AbstractController
{

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/api/search-user', name: 'api_search_user', methods: ['GET'])]
    public function searchUser(Request $request, RobloxApiService $api): Response
    {
        $username = $request->query->get('username');
        $url = "https://users.roblox.com/v1/users/search?keyword=" . urlencode($username);
        $key = $_ENV['ROBLOSECURITY'];

        $client = HttpClient::create([
            'headers' => [
                'Cookie' => $key,
            ]
        ]);

        $response = $client->request('GET', $url);

        $content = $response->getContent();
        $content = json_decode($content, true);
        $content = $content['data'];

        if (count($content) > 3) {
            $content = array_slice($content, 0, 3);
        } elseif (count($content) === 0) {
            return new JsonResponse("[]", 200, [], true);
        }

        $userAvatars = [];
        foreach ($content as $user) {
            $userAvatars[$user['id']] = "";
        }

        $api->getUserAvatars($userAvatars);

        foreach ($content as $user) {
            $index = array_search($user, $content);
            $content[$index]['avatar'] = $userAvatars[$user['id']];
        }

        $content = json_encode($content, JSON_PRETTY_PRINT);

        return new JsonResponse($content, 200, [], true);
    }

    #[Route('/api/games', name: 'api_post_games', methods: ['POST'])]
    public function postGame(Request $request, GameRepository $gre, RobloxApiService $api, UserRepository $ure): Response
    {
        $userData = $api->getLoginData($ure);
        if(!$userData['logged']) {
            return new JsonResponse("", Response::HTTP_UNAUTHORIZED, [], true);
        }
        $user = $userData['user'];

        $data = $request->toArray();
        $title = strip_tags($data['title']);
        $rules = array_map('strip_tags', $data['rules']);

        $gre->addGame($title, $user, $data['description'], $rules);
        return new JsonResponse("", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/login-data', name: 'api_get_logindata', methods: ['GET'])]
    public function getLoginData(RobloxApiService $api, UserRepository $ure, SerializerInterface $serializer): Response
    {
        $userData = $api->getLoginData($ure);
        $userData = $serializer->serialize($userData, 'json');
        return new JsonResponse($userData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/mercure-token', name: 'mercure_token')]
    public function generateToken(): JsonResponse
    {
        $secret = $_ENV['MERCURE_JWT_SECRET'];

        $payload = [
            "mercure" => [
                "subscribe" => ["http://localhost:8000/api/messages"]
            ]
        ];

        $jwt = JWT::encode($payload, $secret, 'HS256');

        return new JsonResponse(['token' => $jwt]);
    }

    #[Route('/api/send-update', name: 'send_update')]
    public function sendUpdate(WebsocketService $socket): JsonResponse
    {
        $socket->fireClients();
        return new JsonResponse(['status' => 'Update sent!']);
    }
}
