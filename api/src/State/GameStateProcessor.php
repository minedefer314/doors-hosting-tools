<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\RobloxApiService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GameStateProcessor implements ProcessorInterface
{
    private GameRepository $gre;
    private RobloxApiService $api;
    private UserRepository $ure;

    public function __construct(
        GameRepository $gre,
        RobloxApiService $api,
        UserRepository $ure,
    ) {
        $this->gre = $gre;
        $this->api = $api;
        $this->ure = $ure;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Game
    {
        if (!$data instanceof Game) {
            throw new \RuntimeException('Invalid data');
        }

        $userData = $this->api->getLoginData($this->ure);
        if (!$userData['logged']) {
            throw new UnauthorizedHttpException('Bearer', 'Authentication required');
        }

        return $this->gre->addGame(
            title: $data->getTitle(),
            host: $userData['user'],
            description: $data->getDescription(),
            rules: $data->getRules(),
        );
    }
}