<?php

namespace App\MessageHandler;

use App\Message\TriggerNextJsBuild;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[AsMessageHandler]
class TriggerNextJsBuildHandler
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(TriggerNextJsBuild $message)
    {      
       
    }

}