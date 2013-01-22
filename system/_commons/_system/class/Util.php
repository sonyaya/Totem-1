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
            $ret = substr($ret, 0, 0-strlen($glue));
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
	    $newstring = preg_replace("/[^a-zA-Z0-9_.]/", "", $newstring);
            return $newstring;
	}
    }