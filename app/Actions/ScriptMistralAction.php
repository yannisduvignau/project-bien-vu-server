<?php

namespace App\Actions;

use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Log;

class ScriptMistralAction
{
    public function execute($script, string|array $data, $echapeCmd=false)
    {
        $venv_python = storage_path('scripts/venv/bin/python3');

        // if((!$echapeCmd) && (typeOf($data) === 'string')){
        if(!$echapeCmd){
            $command = escapeshellcmd("$venv_python $script " . escapeshellarg($data));
        }else{
            $command = "$venv_python $script " . escapeshellarg(json_encode($data));
        }

        Log::info("Commande exécutée : " . $command);
        return shell_exec($command);
    }
}
