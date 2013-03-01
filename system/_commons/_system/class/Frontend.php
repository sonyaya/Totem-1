<?php

    /**
     * Version: 0.4a
     *
     * Changes::
     *     0.4a:
     *      - mINIT           : o método foi removido da classe, mVAR agora é capaz de incorporar todas as funcionalidade
     *                          que antes eram inerentes ao método mINIT
     *
     *     0.3a:
     *      - mStartVAR       : renomeado para mINIT
     *      - Agora é possivel informar como você deseja que a variavel seja retornada adicionando o prefixo 'tipo':, exempo:
     *        &m.init:json:VARIAVEL; &m.var:json:VARIAVEL; -- Atualmente os tipos aceitos são: json, print_r, int, integer,
     *        str, string, bool, boolean
     *
     *    0.2a:
     *      - criado mINCLUDE : permite incluir arquivos externos ao arquivo atual
     *
     *    0.1a:
     *      - crido mIMPORT   : permite importar arquivos jSon
     *      - crido mVAR      : permite imprimir variaveis importadas por mIMPORT
     *      - crido mStartVAR : permite imprimir variaveis iniciaveis como _GET, _POST e arrays passados para o método construtor
     *      - crido mIF       : permite criar condicionais if
     *      - crido mREPEAT   : permite criar laços de repetição
     *      - crido mFOREACH  : permite criar laços de repeticão para as variaveis importadas em mVAR
     */

    namespace backend;
    use backend\DOMCrawler;

    class Frontend{
        private $DOM       = "";
        private $mVARS     = Array();

        /**
         * metodo construtor
         *
         */
        public function __construct($htmlFile, $mInitVARS="", $getFile=true){
            $this->mVARS          = $mInitVARS;
            $this->mVARS['_GET']  = $_GET;
            $this->mVARS['_POST'] = $_POST;
            $this->mVARS['_NOW']  = getdate();

            $this->DOM = new DOMCrawler($htmlFile, $getFile);
        }

        /**
         * permite importar arquivos jSon
         *
         */
        public function mIMPORT(){
            $imports = "";

            foreach($this->DOM->find("m.import") as $key=>$TAG){
                $imports .= $TAG['innerHTML'];
                $this->DOM->removeTag($TAG);
            }
            
            $regex = "{
                m[.]var:(?P<var>.*?)
                \s*?=\s*?
                (?P<aspas>['\"])
                    (?P<link>.*?)
                (?P=aspas)\s*?;
            }six";
            
            preg_match_all($regex, $imports, $matches, PREG_SET_ORDER);
            foreach($matches as $match){
                $this->mVARS[$match['var']] = json_decode( @file_get_contents($match['link']), true);
            }
        }

        /**
         * permite incluir arquivos externos ao arquivo atual
         *
         */
        public function mINCLUDE(){
            while(count($this->DOM->findEComma("m.include")) > 0){ 
                $TAGS = $this->DOM->findEComma("m.include");
                foreach($TAGS as $TAG){
                    // variaveis
                    preg_match_all("/#(.*?)#/i", $TAG['value'], $vars, PREG_SET_ORDER);
                    foreach ($vars as $var) {
                        $file_path = str_replace($var[0], $this->getMVar($var[1]), $TAG['value']);
                    }

                    // tenta buscar e incluir o arquivo
                    if( $ret = file_get_contents( $file_path ) ){
                        $this->DOM->replaceEComma( $TAG, $ret );
                    }else{
                        $this->DOM->replaceEComma( $TAG, "<!-- file not found to include: {$TAG['value']} -->" );
                    }
                }
            }
        }

        /**
         * permite imprimir variaveis importadas por mIMPORT
         *
         */
        public function mVAR(){
            $TAGS = $this->DOM->findEComma("m.var");
            foreach($TAGS as $TAG){
                $ret = $this->getMVar($TAG['value']);
                $this->DOM->replaceEComma($TAG, $ret);
            }
        }

        /**
         * permite criar condicionais if
         *
         */
        public function mIF(){
            foreach($this->DOM->find("m.if") as $key=>$TAG){
                if(
                    isset($TAG['attr']['cond']) &&
                    !empty($TAG['attr']['cond'])
                ){
                    $cond = $TAG['attr']['cond'];

                    $i = 0;
                    $replace['from'][$i] = "-eq-";
                    $replace['to'  ][$i] = "===";

                    $i++;
                    $replace['from'][$i] = "-neq-";
                    $replace['to'  ][$i] = "!==";

                    $i++;
                    $replace['from'][$i] = "-ne-";
                    $replace['to'  ][$i] = "!==";

                    $i++;
                    $replace['from'][$i] = "-gt-";
                    $replace['to'  ][$i] = ">";

                    $i++;
                    $replace['from'][$i] = "-gte-";
                    $replace['to'  ][$i] = ">=";

                    $i++;
                    $replace['from'][$i] = "-ge-";
                    $replace['to'  ][$i] = ">=";

                    $i++;
                    $replace['from'][$i] = "-lt-";
                    $replace['to'  ][$i] = "<";

                    $i++;
                    $replace['from'][$i] = "-lte-";
                    $replace['to'  ][$i] = "<=";

                    $i++;
                    $replace['from'][$i] = "-le-";
                    $replace['to'  ][$i] = "<=";

                    $i++;
                    $replace['from'][$i] = "-not-";
                    $replace['to'  ][$i] = "!";

                    $i++;
                    $replace['from'][$i] = "mod";
                    $replace['to'  ][$i] = "%";

                    $cond = str_replace($replace['from'], $replace['to'], $cond);

                    if( !empty($cond) && eval("return ($cond);") ){
                        $this->DOM->replaceTagByOwnValue($TAG);
                    }else{
                        $this->DOM->removeTag($TAG);
                    }
                }else{
                    $this->DOM->removeTag($TAG);
                }
            }
        }
        
        /**
         * permite criar laços de repeticão para as variaveis importadas em mVAR
         *
         */
        public function mFOREACH(){
            while($TAGS = $this->DOM->find("m.foreach")){
                foreach($TAGS as $TAG){
                    $HTML = "";
                    $ATTR = $TAG['attr'];
                    $aKEY = @$ATTR['key'];
                    $var  = $this->getMVar($ATTR['var']);
                    
                    if(!empty($var) && is_array($var)){
                        foreach($var as $key=>$val){
                            $str = rtrim($TAG['innerHTML'], "\t ");
                            $str = preg_replace("/(.*?)[\r\n]$/i", "$1", $str);
                            $HTML .= str_replace("#{$aKEY}#", $key, $str);
                        }
                    }
                    
                    $this->DOM->replaceTag($TAG, $HTML);
                }
            }
        }
        
        
        /**
         * permite criar laços de repetição
         *
         */
        public function mREPEAT(){
            while($TAGS = $this->DOM->find("m.repeat")){
                foreach($TAGS as $TAG){
                    $HTML = "";

                    // busca a tags
                    $start = @$TAG['attr']['start'];
                    $stop  = @$TAG['attr']['stop' ];
                    $aKEY  = @$TAG['attr']['key'];
                    
                    // busca mvar caso start e stop não sejam valores numéricos
                    $start = (!is_numeric($start)) ? $this->getMVar("int:$start") : $start;
                    $stop  = (!is_numeric($stop) ) ? $this->getMVar("int:$stop")  : $stop ;

                    // executa o for em sua direção correta
                    if($start <= $stop){

                        for($i=$start; $i<=$stop; $i++){
                            $val = str_pad( $i, strlen($start), "0", STR_PAD_LEFT );
                            $HTML .= str_replace("#{$aKEY}#", $val, rtrim($TAG['innerHTML']) );
                        }

                    }elseif($start > $stop){

                        for($i=$start; $i>=$stop; $i--){
                            $val = str_pad( $i, strlen($stop), "0", STR_PAD_LEFT );
                            $HTML .= str_replace("#{$aKEY}#", $val, rtrim($TAG['innerHTML']) );
                        }

                    }
                    
                    // retorna pro layout
                    $this->DOM->replaceTag($TAG, $HTML);
                }
            }
        }
        
        /**
         * retorna variaveis comuns importadas por mIMPORT
         *
         */
        private function getMVar($path){
            return $this->getMVarInArray($path, $this->mVARS);
        }
        
        /**
         * retorna uma variaveis de um array passado por parametro
         *
         */
        private function getMVarInArray($path, $array){
            $oPath = $path;
            $path  = preg_replace("/^(.*?)\:/", "", $path);
            $path  = str_replace(".", "']['", $path);
            $path  = "['$path']";
            $var   = eval("return @\$array$path;");
            
            preg_match("/^(.*?)\:/", $oPath, $typeReturn);
            $typeReturn = 
                ( !empty($typeReturn[1]) )
                    ? $typeReturn[1] 
                    : ""
            ;

            switch($typeReturn){
                case "json";
                    return json_encode($var);
                    break;

                case "print_r";
                    return print_r($var, true);
                    break;
                
                case "int";
                case "integer";
                    return (integer)$var;
                    break;

                case "str";
                case "string";
                    return (string)$var;
                    break;

                case "bool";
                case "boolean";
                    return ( @$var ) ? 'true' : 'false';
                    break;

                default:
                    if(isset($var)){
                        return $var;
                    }else{
                        return "<!-- $oPath variable was not set -->";
                    }
                    break;
            };
        }

        /**
         * imprime o layout formatado
         *
         */
        public function __toString(){
            $this->mINCLUDE();
            $this->mIMPORT();
            $this->mFOREACH();
            $this->mREPEAT();
            $this->mVAR();
            $this->mIF();

            return $this->DOM->getFile();
        }
    }