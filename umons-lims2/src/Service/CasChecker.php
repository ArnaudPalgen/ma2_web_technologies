<?php


namespace App\Service;


class CasChecker
{

    private string $regex = "#([0-9]{2,7})-([0-9]{2})-[0-9]#";

    private function getCheckDigit($cas){
        preg_match($this->regex,$cas,$match);
        $digits = array_reverse(str_split($match[1].$match[2]) );

        $sum = 0;
        for($i =0; $i < count($digits); $i++){
            $sum += ($i+1)*$digits[$i];
        }
        return $sum % 10;
    }

    public function isValid($cas){
        if(!preg_match($this->regex,$cas)){
            return false;
        }

        return $this->getCheckDigit($cas) ==  substr($cas,-1);
    }


}