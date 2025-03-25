<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\RobloxApiService;
use App\Service\WebsocketService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MessageStateProcessor implements ProcessorInterface
{
    private MessageRepository $mre;
    private RobloxApiService $api;
    private UserRepository $ure;

    private WebsocketService $socket;

    public function __construct(
        MessageRepository $mre,
        RobloxApiService $api,
        UserRepository $ure,
        WebsocketService $socket
    ) {
        $this->mre = $mre;
        $this->api = $api;
        $this->ure = $ure;
        $this->socket = $socket;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Message
    {
        if (!$data instanceof Message) {
            throw new \RuntimeException('Invalid data');
        }

        $userData = $this->api->getLoginData($this->ure);
        if (!$userData['logged']) {
            throw new UnauthorizedHttpException('Bearer', 'Authentication required');
        }

        $message = $this->mre->addMessage(content: $data->getContent(), sender: $userData['user']);
        $this->socket->fireClients();
        return $message;
    }
}
