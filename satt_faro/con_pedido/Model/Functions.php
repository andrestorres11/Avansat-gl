<?php
namespace Phppot;

class Functions
{

    function random_bytes($largura = 32){
        $chars = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        // separar a string acima com uma virgula após cada letra ou número;
        $chars = preg_replace("/([a-z0-9])/i", "$1,", $chars);
        $chars = explode(',', $chars);

        $string_generate = array();
        for($i = 0; $i < $largura; $i++){
            // $chars[random_int(0, 61) = largura da array $chars
            array_push($string_generate, $chars[$this->random_int(0, 61)]);
        }
        $string_ready = str_shuffle(implode($string_generate));
       
        for($i = 0; $i < $this->random_int(256,512); $i++){
            $random_string = str_shuffle($string_ready);
        }
        // se a largura for um número par o numero de caracteres da string for maior ou igual a 4
        if($largura % 2 === 0 && strlen($random_string) >= 4){
            $random_string_start = str_shuffle(substr($random_string, 0, $largura / 2));
            $random_string_end = str_shuffle(substr($random_string, $largura / 2, $largura));
            $new_random_string = str_shuffle($random_string_start . $random_string_end);
            return str_shuffle($new_random_string);
        }
        else {
            return str_shuffle($random_string);
        }
    }

    function random_int($ini = 0,$fin = 99999999){
        return rand($ini,$fin);
    }
}