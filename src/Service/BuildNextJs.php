<?php 
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\CacheInterface;

class BuildNextJs
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function buildNextJs()
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
            'x-taupe-event: ' . 'build',
        ];

        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $data,
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        return $content;
    }
}