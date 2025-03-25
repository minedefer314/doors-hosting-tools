<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class WebsocketService
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }
    public function fireClients(): void
    {
        $update = new Update(
            'http://localhost:8000/api/messages',
            json_encode(['message' => 'update']),
            true
        );

        $this->hub->publish($update);
    }
}