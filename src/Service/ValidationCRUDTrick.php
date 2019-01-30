<?php

namespace App\Service;

use App\Service\LinkVideoValidation;

class ValidationCRUDTrick
{

    private $videoValidation;

    public function __construct(LinkVideoValidation $videoValidation)
    {
        $this->videoValidation = $videoValidation;
    }

    public function CoverImage($medias, $trick)
    {
        
            
    }

    public function video($medias, $route)
    {
        
    }
}