<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class TriggerNextJsBuild
{


    public function triggerBuild(): array
    {
        $nextJsPath = '/var/www/html/Une-Taupe-Chez-Vous_Next.js';

        exec('cd ' . $nextJsPath . ' && npm run build 2>&1', $output, $return_var);
    
        if ($return_var !== 0) {
            $error = 'Une erreur est survenue lors de l\'exécution de la commande : ' . implode("\n", $output);
            return ['error' => $error];
        } else {
            return ['success' => true];
        }

    }
}