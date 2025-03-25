<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\RobloxApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends AbstractController
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[Route('/login', name: 'app_login')]
    public function connect(UserRepository $re): RedirectResponse
    {
        if(isset($_COOKIE['TOKEN']))
        {
            $user = $re->findByToken($_COOKIE['TOKEN']);
            if($user)
            {
                return $this->redirect('/');
            }
        }
        $session = $this->requestStack->getSession();
        $codeVerifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $session->set('code_verifier', $codeVerifier);

        $url = "https://apis.roblox.com/oauth/v1/authorize?"
            . "client_id=" . $_ENV['OAUTH_ROBLOX_CLIENT_ID']
            . "&code_challenge=" . rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=')
            . "&code_challenge_method=" . "S256"
            . "&redirect_uri=" . urlencode($_ENV['SERVER_ADDRESS'] . '/login/check')
            . "&scope=" . urlencode("openid profile")
            . "&response_type=code"
            . "&state=" . "login";
        return $this->redirect($url);
    }

    #[Route('/login/check', name: 'app_login_check', methods: ['GET'])]
    public function connectCheck(Request $request, UserRepository $re, RobloxApiService $api, EntityManagerInterface $en): Response
    {
        if(isset($_COOKIE['TOKEN']))
        {
            $user = $re->findByToken($_COOKIE['TOKEN']);
            if($user)
            {
                return $this->redirect('/');
            }
        }

        $session = $this->requestStack->getSession();
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        if(!$code || !$state)
        {
            return $this->redirect('/');
        }

        $tokenSet = $api->getToken(code: $code, code_verifier: $session->get('code_verifier'));

        $info = $api->getUserInfo($tokenSet['token']);
        $user = $re->findOneBy(['robloxId' => intval($info['sub'])]);

        if ($user === null) {
            $re->createUser(
                token: $tokenSet['refresh_token'],
                id: intval($info['sub']),
                username: $info['preferred_username'],
                displayName: $info['nickname'],
                profile: $info['profile'],
                picture: $info['picture']
            );
        } else {
            $user->setRobloxToken($tokenSet['refresh_token']);
            $user->setDisplayName($info['nickname']);
            $user->setUsername($info['preferred_username']);
            $user->setPicture($info['picture']);
            $en->persist($user);
            $en->flush();
        }

        $time = time() + 60 * 60 * 24 * 90;
        $cookie = Cookie::create(
            name: "TOKEN",
            value: $tokenSet['refresh_token'],
            expire: $time,
        );

        $response = new RedirectResponse('/');
        $response->headers->setCookie($cookie);
        return $response->send();
    }

    #[Route('/logout', name: 'app_logout')]
    public function disconnect(RobloxApiService $api, UserRepository $re, EntityManagerInterface $em): Response
    {
        $userData = $api->getLoginData($re);
        if($userData['logged'])
        {
            $user = $userData['user'];
            $user->setRobloxToken(null);
            $em->persist($user);
            $em->flush();
        }

        $response = new RedirectResponse('/');
        $response->headers->clearCookie('TOKEN');

        return $response->send();
    }
}
