<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\NotesController;

class SettingsController extends Controller
{
    function NewLogLenght($newValue){
        $ip = (new NotesController)->GetUserIp();
        Storage::append('public/main_notes.log', 'Update Log Lenght='.$newValue.' '.date('d.m.Y H:i:s').' '.$ip);
        SettingsController::SetEnvValue('LOG_LENGHT', $newValue);

        $pathsInFolder = Storage::directories('public/notes');
        foreach($pathsInFolder as $value){
            $nowLogText = Storage::get($value.'/log.log');
            $nowLogLenght = substr_count($nowLogText,PHP_EOL)+1;
            while($nowLogLenght > $newValue){
                $nowLogLenght = substr_count($nowLogText,PHP_EOL)+1;
                $nowLogText = substr($nowLogText, strpos($nowLogText,PHP_EOL) + 1);
            }
            Storage::put($value.'/log.log', $nowLogText);
        }

        $nowMainLogText = Storage::get('public/main_notes.log');
        $nowMainLogLenght = substr_count($nowMainLogText,PHP_EOL)+1;

        while($nowMainLogLenght > $newValue){
            $nowMainLogText = substr($nowMainLogText, strpos($nowMainLogText,PHP_EOL) + 1);
            $nowMainLogLenght = substr_count($nowMainLogText,PHP_EOL)+1;
        }

        Storage::put('public/main_notes.log', $nowMainLogText);
    }
    
    function NewAutoSave($newValue){
        $ip = (new NotesController)->GetUserIp();
        Storage::append('public/main_notes.log', 'Update Auto Save='.$newValue.' '.date('d.m.Y H:i:s').' '.$ip);
        SettingsController::SetEnvValue('AUTO_SAVE', $newValue);
    }

    function SetEnvValue($key, $value){
        $path = app()->environmentFilePath();
        $escaped = preg_quote('='.env($key), '/');
        echo(env($key));

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
