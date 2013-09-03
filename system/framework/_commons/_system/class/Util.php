<?php

    namespace backend;

    class Util{
        /**
         * Mescla dois recursivamente, com dois tipos de cola, um para linhas e outro para colunas
         *
         * @param type $array
         * @param type $glue
         * @param type $lineGlues
         * @return type
         */
        public static function implode_recursive($array, $glue, $lineGlues=''){
            $ret = '';
            foreach ($array as $item) {
                if (is_array($item)) {
                    $lineGlues = (empty($lineGlues))? $glue : $lineGlues;
                    $ret .= Util::implode_recursive($item, $glue) . $lineGlues;
                } else {
                    $ret .= $item . $glue;
                }
            }
            $ret = substr($ret, 0, 0-mb_strlen($glue));
            return $ret;
        }

        /**
         *
         * @param type $string
         * @return type
         */
        public static function slug($string){
            $newstring =
                str_replace(
                    Array('á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü','ç','Á','À','Ã','Â','É','Ê','Í','Ó','Ô','Õ','Ú','Ü','Ç',' '),
                    Array('a','a','a','a','e','e','i','o','o','o','u','u','c','A','A','A','A','E','E','I','O','O','O','U','U','C','_'),
                    $string
                )
            ;
	    $newstring = preg_replace("/[^a-zA-Z0-9_]/", "", $newstring);
            return $newstring;
	}


        /**
         * UTF-8 aware alternative to str_pad.
         *
         * $pad_str may contain multi-byte characters.
         *
         * @author Oliver Saunders <oliver@osinternetservices.com>
         * @package php-utf8
         * @subpackage functions
         * @see http://www.php.net/str_pad https://github.com/fluxbb/utf8/tree/master/functions
         * @param string $input
         * @param int $length
         * @param string $pad_str
         * @param int $type ( same constants as str_pad )
         * @return string
         */
        public static function mb_str_pad($input, $length, $pad_str=' ', $type = STR_PAD_RIGHT){
                $input_len = mb_strlen($input);
                if ($length <= $input_len)
                        return $input;

                $pad_str_len = mb_strlen($pad_str);
                $pad_len = $length - $input_len;

                if ($type == STR_PAD_RIGHT){
                    $repeat_times = ceil($pad_len / $pad_str_len);
                    return mb_substr($input.str_repeat($pad_str, $repeat_times), 0, $length);
                }

                if ($type == STR_PAD_LEFT){
                    $repeat_times = ceil($pad_len / $pad_str_len);
                    return mb_substr(str_repeat($pad_str, $repeat_times), 0, floor($pad_len)).$input;
                }

                if ($type == STR_PAD_BOTH){
                    $pad_len /= 2;
                    $pad_amount_left = floor($pad_len);
                    $pad_amount_right = ceil($pad_len);
                    $repeat_times_left = ceil($pad_amount_left / $pad_str_len);
                    $repeat_times_right = ceil($pad_amount_right / $pad_str_len);

                    $padding_left = mb_substr(str_repeat($pad_str, $repeat_times_left), 0, $pad_amount_left);
                    $padding_right = mb_substr(str_repeat($pad_str, $repeat_times_right), 0, $pad_amount_right);

                    return $padding_left.$input.$padding_right;
                }

                trigger_error('mb_str_pad: Unknown padding type ('.$type.')', E_USER_ERROR);
        }
        
        /**
         * 
         * @global type $_M_LANGUAGE
         * @return type
         */
        public static function lng(){
            // Idioma global
            global $_M_LANGUAGE; 
            
            // Parametros
            $array = func_get_args();
            
            // Frase
            if(is_array($array[0])){
                $frase = $_M_LANGUAGE[ $array[0][0] ][ $array[0][1] ];
                array_shift($array);
            }else{
                $frase = array_shift($array);
            }
            
            // Retorno
            foreach ($array as $key=>$val){
                $frase = str_replace("%" . ($key+1), $val, $frase);
            }
            
            return $frase;
        }
    }