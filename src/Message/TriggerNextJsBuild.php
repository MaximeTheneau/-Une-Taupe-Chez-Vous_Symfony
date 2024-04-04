<?php

namespace App\Message;

use App\MessageHandler\TriggerNextJsBuildHandler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageInterface;


class TriggerNextJsBuild
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

}
