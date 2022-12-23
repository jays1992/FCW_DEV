<?php 

namespace App\Helpers;

use DateTime;

class Utils
{
    
    public static function clean($string) {
        $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower($string)); // Removes special chars.
    }

    public static function is_valid_date($date,$format='d/m/Y')
    {
      $f = DateTime::createFromFormat($format, $date);
      $valid = DateTime::getLastErrors();         
      return ($valid['warning_count']==0 && $valid['error_count']==0 && $f !== false);
    }

    public static function Dateformate($date,$seperator) {
        if(!empty($date) && !empty($seperator)){
            $ex = explode(''.$seperator.'',$date);
            return $ex[2].'-'.$ex[1].'-'.$ex[0];
        }
    }

    public static function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function array_empty($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $value) {
                if (!array_empty($value)) {
                    return false;
                }
            }
        }
        elseif (!empty($mixed)) {
            return false;
        }
        return true;
    }
    
    public static function CalculatePercent($amount, $percent){
        if($amount && $percent){
            $a = (float)$amount;
            //echo '<br>';
            $b = (float)$percent;
            $ddd = ($a * $b);
            return ($ddd / 100);
        }else{
            return 0;
        }
    }
    
    public static function makeslug($string){
       return strtolower(trim(str_replace(' ','-', $string)));
    }


}//class