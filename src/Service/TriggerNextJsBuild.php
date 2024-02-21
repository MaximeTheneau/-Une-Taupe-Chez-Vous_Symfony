<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class TriggerNextJsBuild
{


    public function triggerBuild(): JsonResponse
    {
        $nextJsPath = '/var/www/html/Une-Taupe-Chez-Vous_Next.js';

        // exec('cd ' . $nextJsPath . ' && pnpm run build 2>&1', $output, $return_var);
        $output = [];
        $response = new JsonResponse(['error' => 'Une erreur est survenue lors de l\'exécution de la commande : ' . implode("\n", $output)], Response::HTTP_INTERNAL_SERVER_ERROR);
        // if ($return_var !== 0) {
        //     throw new \RuntimeException('Une erreur est survenue lors de l\'exécution de la commande : ' . implode("\n", $output));
            
        // }

        return $response;
   
    }
}