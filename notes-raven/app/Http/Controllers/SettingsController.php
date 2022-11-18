<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\NotesController;

class SettingsController extends Controller
{
    function LogLenght($newValue){
        if($newValue>=1){
            $ip = (new NotesController)->GetUserIp();
            Storage::append('public/main_notes.log', 'Update Log Lenght='.$newValue.' '.date('d.m.Y H:i:s').' '.$ip);
            SettingsController::SetEnvValue('LOG_LENGHT', $newValue);

            $pathsInFolder = Storage::directories('public/notes');
            foreach($pathsInFolder as $value){
                $LogText = Storage::get($value.'/log.log');
                $LogLenght = substr_count($LogText,PHP_EOL)+1;
                while($LogLenght > $newValue){
                    $LogLenght = substr_count($LogText,PHP_EOL)+1;
                    $LogText = substr($LogText, strpos($LogText,PHP_EOL) + 1);
                }
                Storage::put($value.'/log.log', $LogText);
            }

            $LogText = Storage::get('public/main_notes.log');
            $LogLenght = substr_count($LogText,PHP_EOL)+1;

            while($LogLenght > $newValue){
                $LogText = substr($LogText, strpos($LogText,PHP_EOL) + 1);
                $LogLenght = substr_count($LogText,PHP_EOL)+1;
            }

            Storage::put('public/main_notes.log', $LogText);
        }
    }
    
    function AutoSave($newValue){
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
