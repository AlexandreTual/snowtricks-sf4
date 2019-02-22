<?php

namespace App\Core;

abstract class Utils
{
    public static function slugMaker($data1, $data2)
    {
        $lowerData1 = strtolower($data1);
        $lowerData2 = strtolower($data2);
        $cleanData1 = str_replace("'", '-', $lowerData1);
        $cleanData2 = str_replace("'", '-', $lowerData2);

        return $slug = $cleanData1.'-'.$cleanData2;
    }
}
