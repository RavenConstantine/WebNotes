<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotesController extends Controller
{
    function Load() {
        $pathsInFolder = Storage::directories('public/notes');
        $NotesList = array();
        foreach($pathsInFolder as $value){
            $path = pathinfo($value);
            $title = Storage::get($value.'/title.txt');
            $text = Storage::get($value.'/text.txt');
            NotesController::AddLog($path['filename'], 'Load');
            $NotesList[] = array(
                'id' => $path['filename'],
                'title' => $title,
                'text' => $text
            );
        }
        return $NotesList;
    }

    function Create() {
        do{
            $newID = NotesController::GenerateRandomString();
        } while (Storage::exists('public/deletedNotes/'.$newID));

        Storage::makeDirectory('public/notes/'.$newID);
        Storage::put('public/notes/'.$newID.'/text.txt','Новая заметка');
        Storage::put('public/notes/'.$newID.'/title.txt','Новая заметка');
        Storage::put('public/notes/'.$newID.'/log.log','');
        NotesController::AddLog($newID, 'Create');
    }
    
    function Open($id) {
        $title = Storage::get('public/notes/'.$id.'/title.txt');
        $text = Storage::get('public/notes/'.$id.'/text.txt');
        NotesController::AddLog($id, 'Open');
        $Note = array(
            'title' => $title,
            'text' => $text
        );
        return $Note;
    }

    function Save($id, $title, $text) {
        $title = base64_decode($title);
        $text = base64_decode($text);
        Storage::copy('public/notes/'.$id.'/text.txt', 'public/notes/'.$id.'/'.date('d.m.Y H:i:s').'.story');
        Storage::put('public/notes/'.$id.'/title.txt', $title);
        Storage::put('public/notes/'.$id.'/text.txt', $text);
        NotesController::AddLog($id, 'Save');
    }

    function Delete($id) {
        do{
            $newID = NotesController::GenerateRandomString();
        } while (Storage::exists('public/deletedNotes/'.$newID));

        NotesController::AddLog($id, 'Delete; NewID='.$newID);
        Storage::makeDirectory('public/deletedNotes/'.$newID);
        $filesInFolder = Storage::files('public/notes/'.$id);
        foreach($filesInFolder as $value){
            $file = pathinfo($value);
            echo($value);
            Storage::copy($value, 'public/deletedNotes/'.$newID.'/'.$file['basename']);
        }
        Storage::deleteDirectory('public/notes/'.$id);
    }

    function GenerateRandomString() {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $strength = 16;
        $chars_length = strlen($chars);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $chars[mt_rand(0, $chars_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    function GetUserIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }

    function AddLog($id, $text){
        $ip = NotesController::GetUserIp();
        Storage::append('public/notes/'.$id.'/log.log', $text.' '.date('d.m.Y H:i:s').' '.$ip);
        Storage::append('public/main_notes.log', $text.' '.$id.' '.date('d.m.Y H:i:s').' '.$ip);

        $nowLogText = Storage::get('public/notes/'.$id.'/log.log');
        $nowMainLogText = Storage::get('public/main_notes.log');

        $logLenght = env('LOG_LENGHT', 1000);
        $nowLogLenght = substr_count($nowLogText,PHP_EOL)+1;
        $nowMainLogLenght = substr_count($nowMainLogText,PHP_EOL)+1;

        if($nowLogLenght > $logLenght){
            Storage::put('public/notes/'.$id.'/log.log', substr($nowLogText, strpos($nowLogText,PHP_EOL) + 1));
        }
        if($nowMainLogLenght > $logLenght){
            Storage::put('public/main_notes.log', substr($nowMainLogText, strpos($nowMainLogText,PHP_EOL) + 1));
        }
    }

    function GetStoryCount($id){
        NotesController::AddLog($id, 'Get Story');
        $pathsInFolder = Storage::files('public/notes/'.$id);
        $storyCount = 0;
        foreach($pathsInFolder as $value){
            $path = pathinfo($value);
            if($path['extension'] == "story"){
                $storyCount = $storyCount + 1;
            }
        }
        return $storyCount;
    }

    function LoadStory($id){
        NotesController::AddLog($id, 'Load Story');
        $pathsInFolder = Storage::files('public/notes/'.$id);
        $StoryList = array();
        foreach($pathsInFolder as $value){
            $path = pathinfo($value);
            if($path['extension'] == "story"){
                $text = Storage::get($value);
                $StoryList[] = array(
                    'id' => $path['filename'],
                    'title' => $path['filename'],
                    'text' => $text
                );
            }
        }
        return $StoryList;
    }

    function OpenStory($id, $date){
        $text = Storage::get('public/notes/'.$id.'/'.$date.'.story');
        NotesController::AddLog($id, 'Open story '.$date);
        $Story = array(
            'title' => $date,
            'text' => $text
        );
        return $Story;
    }
}