<?php

    namespace backend;
    
    /**
     *
     */
    class DOMCrawler{
        private $file;
        
        /**
         *
         */
        public function __construct($htmlFile, $fileGet = true){
            if($fileGet){
                $this->file = file_get_contents($htmlFile);
            }else{
                $this->file = $htmlFile;
            }
        }

       /**
         * Econtra TAGs do tipo &TAG:VALOR;
         *
         */
        public function findEComma($TAG, $DOM=NULL){
            // DOCUMENTO DOM
            $DOM = ($DOM === NULL)? $this->file : $DOM;

            // TAG
            $TAG = preg_quote($TAG);

            // ENCONTRA AS TAGS
            preg_match_all("/(?P<indent>[ \t]*?)(?P<tag>(\/\*&|<\!--&|\"-&|'-&|&|E\/)$TAG:(?P<value>.*?)(;\*\/|;-->|;-\"|;-'|;))/im", $DOM, $TAGS, PREG_SET_ORDER);
            return $TAGS;
        }

        /**
         * Substitui a tag &TAG:VALOR; por uma nova string HTML
         *
         */
        public function replaceEComma($TAG, $newStrHTML, $DOM=NULL){
            // DOCUMENTO DOM
            $DOM = ($DOM === NULL)? $this->file : $DOM;

            // REPLACE
            $newStrHTML = preg_replace("/^/im", $TAG['indent'], $newStrHTML);
            if( is_string($newStrHTML) ){
                $newStrHTML = ( (string)$newStrHTML );
            }
            
            //
            $this->file = str_replace($TAG[0], $newStrHTML, $DOM);
        }

        /**
         *
         *
         * TO-DO: pode ser melhorado o desempenho do $findByATTR, pois da forma que esta, primeiro ele acha a tag, depois ele se preocupa se o ATTR é igual ou não
         * TO-DO: erro, somente se for passado todas os ATTR exatamente iguais aos da TAG ela não é encontrada
         */
        public function find($TAG, $findByATTR=NULL, $DOM=NULL){
            // DOCUMENTO DOM
            $DOM = ($DOM === NULL)? $this->file : $DOM;

            // TAG
            $TAG = preg_quote($TAG);

            // STRING REGEX
            $regexOpenTag  = "/[\r\n]{0,1}(?P<indent>[ \t]*?)(<!--<|\/\*<|<)\s*?$TAG\s*?(?P<attr>.*?)\s*?(>-->|>\*\/|>)/i";
            $regexCloseTag = "/(<!--<|\/\*<|<)\/\s*?$TAG\s*?(>-->|>\*\/|>)/i";

            // REGEX PARA ENCONTRAR TAGS DE ABERTURA E FECHAMENTO
            preg_match_all($regexOpenTag  , $DOM , $openTags  , PREG_OFFSET_CAPTURE);
            preg_match_all($regexCloseTag , $DOM , $closeTags , PREG_OFFSET_CAPTURE);

            // VARIAVEIS ÚTEIS
            $attr      = $openTags['attr'];
            $indent    = $openTags['indent'];
            $openTags  = $openTags[0];
            $closeTags = $closeTags[0];


            // MENSAGEM DE ERRO CASO EXISTAM NÚMEROS DIVERGENTES
            // ENTRE TAGS DE ABERTURAS E TAGS DE FECHAMENTO
            if(count($openTags) !== count($closeTags)){
                echo __METHOD__ . " -> Quantidades divergentes de tags de abertura e fechamento de elementos: $TAG";
                exit();
            }

            // VARIAVEL DE RETORNO DA STRING
            $return = Array();

            // VERIFICA SE EXISTE UMA TAG IGUAL DENTRO DELA MESMO SE EXISTIR INICIA UMA PROCESSO DE NAVEGAÇÃO
            // POR PROFUNDIDADE, AO SER ENCONTRADO UMA OU MAIS TAGS DE ABETURA ENTRE A TAG DE ABERTURA RAIZ E
            // A TAG DE FECHAMENTO, É SETADO UMA VARIAVEL COM O NUMERO DA PROFUNDIDADE (quatidade de tags de
            // aberturas encontradas) E ENTÃO ELA É DECRECIDA ATÉ RETORNAR AO VALOR ZERO, VEJA A EXPLICAÇÃO
            // NA TABELA A BAIXO:
            //  ------------------------------ ------------------------------- ------------------------------- ------------------------------- -------------------------------
            // | ECONTRA A DEEP 3             | DEEP 3                        | DEEP 2                        | DEEP 1                        | DEEP 0                        |
            //  ------------------------------ ------------------------------- ------------------------------- ------------------------------- -------------------------------
            // | <div cond="1">            <- |  <div cond="1">            <- |  <div cond="1">               |  <div cond="1">               |  <div cond="1">               |
            // |    TES 1                     |     TES 1                     |     TES 1                     |     TES 1                     |     TES 1                     |
            // |    <div cond="2">          1 |     <div cond="2">            |     <div cond="2">         <- |     <div cond="2">            |     <div cond="2">            |
            // |        TES 2                 |         TES 2                 |         TES 2                 |         TES 2                 |         TES 2                 |
            // |        <div cond="3">      2 |         <div cond="3">        |         <div cond="3">        |         <div cond="3">     <- |         <div cond="3">        |
            // |            TES 3             |             TES 3             |             TES 3             |             TES 3             |             TES 3             |
            // |            <div cond="4">  3 |             <div cond="4">    |             <div cond="4">    |             <div cond="4">    |             <div cond="4"> <- |
            // |                TES 4         |                 TES 4         |                 TES 4         |                 TES 4         |                 TES 4         |
            // |            </div>         <- |             </div>            |             </div>            |             </div>            |             </div>         <- |
            // |            TES 3             |             TES 3             |             TES 3             |             TES 3             |             TES 3             |
            // |        </div>                |         </div>                |         </div>                |         </div>             <- |         </div>                |
            // |        TES 2                 |         TES 2                 |         TES 2                 |         TES 2                 |         TES 2                 |
            // |    </div>                    |     </div>                    |     </div>                 <- |     </div>                    |     </div>                    |
            // |    TEST 1                    |     TEST 1                    |     TEST 1                    |     TEST 1                    |     TEST 1                    |
            // | </div>                       |  </div>                    <- |  </div>                       |  </div>                       |  </div>                       |
            //  ------------------------------ ------------------------------- ------------------------------- ------------------------------- -------------------------------
            $deep = 0;
            
            foreach($openTags as $key => $val){
                $innerHTML  = "";
                $outerHTML  = "";
                $openTag    = $openTags[$key][0];
                $openTagPos = $openTags[$key][1] + strlen($openTags[$key][0]);

                if($deep == 0){
                    $closeTag    = $closeTags[$key][0];
                    $closeTagPos = $closeTags[$key][1];
                    $innerHTML   = substr($DOM, $openTagPos, $closeTagPos-$openTagPos);
                    $deep        = preg_match_all($regexOpenTag, $innerHTML, $matches);

                    if($deep !== 0){
                        $closeTag    = $closeTags[$key+$deep][0];
                        $closeTagPos = $closeTags[$key+$deep][1];
                        $innerHTML   = substr($DOM, $openTagPos, $closeTagPos-$openTagPos);
                    }

                    $outerHTML = $openTag . $innerHTML . $closeTag;
                }else{
                    $deep        = $deep-2;
                    $closeTag    = $closeTags[$key+$deep][0];
                    $closeTagPos = $closeTags[$key+$deep][1];
                    $innerHTML   = substr($DOM, $openTagPos, $closeTagPos-$openTagPos);
                    $outerHTML   = $openTag . $innerHTML . $closeTag;
                }

                // TRANSFORMA O ATTR EM ERRAY
                $attrRet = "";
                
                $attrRet = $attr[$key][0];                                                             // Pega propriedades
                $attrRet = str_replace("&", "%26", $attrRet);                                          // Substitui & por %26
                $attrRet = preg_replace("/=[ \t]*?(?P<SEP>[\"'])(.*?)(?P=SEP)/i", "=$2", $attrRet);    // Remove aspas simples ou suplas sobresalientes
                $attrRet = preg_replace("/[ \t]*?(\w*?\=)/i", "&$1", $attrRet);                        // Adiciona & de separação
                
                parse_str($attrRet, $attrRet);

                // ARRAY RETURN CASO NÃO SEJA SETADO BUSCA POR ATRIBUTO OU O ATRINUTO SE ENCAIXA
                if( $findByATTR === NULL || $findByATTR === array_intersect($findByATTR, $attrRet) ){
                    $return[$key]['attr']      = $attrRet;         // Atributos da TAG
                    $return[$key]['innerHTML'] = $innerHTML;       // Conteúdo interno da TAG
                    $return[$key]['outerHTML'] = trim($outerHTML); // Conteúdo externo, considerando as tags de abertura e fechamento
                    $return[$key]['HTMLSpace'] = $outerHTML;       // Conteúdo externo, considerando as tags de abertura e fechamento mais o espaçamento a esquerda
                    $return[$key]['indent']    = $indent[$key][0]; // Identação da TAG
                }
            }

            // RETORNO
            return $return;
        }

        /**
         * Substitui a tag por uma nova string HTML
         *
         */
        public function replaceTag($TAG, $newStrHTML, $DOM=NULL){
            $DOM = ($DOM === NULL)? $this->file : $DOM;
            $this->file = str_replace($TAG['HTMLSpace'], $newStrHTML, $DOM);
        }

        /**
         * Remove a tag e seu valor
         *
         */
        public function removeTag($TAG, $DOM=NULL){
            $DOM = ($DOM === NULL)? $this->file : $DOM;
            $this->file = str_replace($TAG['HTMLSpace'], '', $DOM);
        }

        /**
         * Substitui o conteúdo da tag por uma nova sctring HTML
         *
         */
        public function replaceTagValue($TAG, $newStrHTML, $DOM=NULL){
            $DOM = ($DOM === NULL)? $this->file : $DOM;
            $this->file = str_replace($TAG['innerHTML'], $newStrHTML, $DOM);
        }

        /**
         * Remove a tag substituindo a mesma por seu proprio conteúdo
         *
         */
        public function replaceTagByOwnValue($TAG, $DOM=NULL){
            $DOM = ($DOM === NULL)? $this->file : $DOM;
            $ownValue = $TAG['innerHTML'];
            preg_match("/[\r\n](?P<space>[ \t]*?)\w/i", $TAG['innerHTML'], $indent);
            if(isset($indent['space'])){
                $indent = $indent['space'];
                $ownValue = $indent.trim($TAG['innerHTML']);
            }
            $this->file = str_replace($TAG['HTMLSpace'], $ownValue, $DOM);
        }
        
        /**
         *
         *
         */
        public function getFile(){
            return $this->file;
        }
        
        /**
         *
         *
         */
        public function __toString(){
            return $this->getFile();
        }
    }