<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class TriggerNextJsBuild
{


    public function triggerBuild(): array
    {
        exec('npm run build-next  ../', $output, $return_var);
    
        if ($return_var !== 0) {
            $error = 'Une erreur est survenue lors de l\'exécution de la commande : ' . implode("\n", $output);
            return ['error' => $error];
        } else {
            return ['error' => null];
        }

    }
}