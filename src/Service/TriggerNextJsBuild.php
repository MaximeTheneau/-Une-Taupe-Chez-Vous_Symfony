<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;

class TriggerNextJsBuild
{
    public function triggerBuild(): Response
    {
        $url = 'https://' . $_ENV['NGINX_DOMAIN'] . '/api/webhook';
        $data = [
            'name' => 'NextJsBuild',
            'project' => $_ENV['NGINX_DOMAIN'],
            'force' => true,
        ];

        $calculatedSignature =  hash_hmac('sha256', json_encode($data), $_ENV['APP_AUTHTOKEN']);
        $headers = [
            'Content-Type: application/json',
            'x-hub-signature-256: ' .'sha256=' . $calculatedSignature,
            'x-' . $_ENV['NGINX_DOMAIN'] . '-event' => 'build',
        ];

        $client = HttpClient::create();

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $data,
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        if ($statusCode === Response::HTTP_OK) {
            return new Response($content );
        }
            
        return new Response('Une erreur est survenue lors de la requête.', $statusCode);
    }
}
