<?php

namespace App\Service;

class MakeSerialService
{
    public function makeSerial()
    {
        $chars = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $serial = '';
        $max = count($chars)-1;
        for($i=0;$i<9;$i++){
            $serial .= (!($i % 3) && $i ? '-' : '').$chars[rand(0, $max)];
        }
        return $serial;
    }
}