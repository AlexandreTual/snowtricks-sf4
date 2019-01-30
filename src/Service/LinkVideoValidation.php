<?php

namespace App\Service;

class LinkVideoValidation
{
    public static function isValid($link)
    {
        switch ($link) {
            case preg_match('#https://www\.youtube\.com/watch\?v=#', $link):
                return true;
            break;
            case preg_match('#https://youtu\.be/#', $link):
                return true;
            break;
            case preg_match('#https://dai\.ly/#', $link):
                return true;
            break;
            case preg_match('#https://www\.dailymotion\.com/video/#', $link):
                return true;
            break;
            case preg_match('#https://www\.youtube\.com/embed/#', $link):
                return true;
            break;
            default:
                return false;
            break;          
        }
    }
    
    public function formatLink($link)
    {
        $patternYoutube1 = '#https://www\.youtube\.com/watch\?v=#';
        $patternYoutube2 = '#https://youtu\.be/#';
        $patternYoutube3 = '#https://www\.youtube\.com/embed/#';
        $replacementYoutube = 'https://www.youtube.com/embed/';
        $patternDayli1 = '#https://dai\.ly/#';
        $patternDayli2 ='#https://www\.dailymotion\.com/video/#';
        $patternDayli3 ='#https://www.dailymotion.com/embed/video/#';
        $replacementDayli = 'https://www.dailymotion.com/embed/video/';
        
        if(preg_match($patternYoutube3, $link)) {
            return $link;
        } elseif (preg_match($patternDayli3, $link)){
            return $link;
        } elseif (preg_match($patternYoutube1, $link)) {
            return preg_replace($patternYoutube1, $replacementYoutube, $link);
        } elseif (preg_match($patternYoutube2, $link)) {
            return preg_replace($patternYoutube2, $replacementYoutube, $link);
        } elseif (preg_match($patternDayli1, $link)) {
            return preg_replace($patternDayli1, $replacementDayli, $link);
        } elseif (preg_match($patternDayli2, $link)) {
            return preg_replace($patternDayli2, $replacementDayli, $link);
        }
    }
}
