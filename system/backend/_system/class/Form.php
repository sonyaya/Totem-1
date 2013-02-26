<?php
    namespace backend;
    
    use backend\MySQL;
    use backend\Frontend;
    use backend\Util;
    use vendor\Symfony\Component\Yaml\Yaml;
   

    class Form{
        
        /**
         *
         * @var type 
         */
        private $htmlLayout = "";
        
        /**
         *
         * @var type 
         */
        private $sendArrayToLayout = Array();
        
        
        /**
         * Gera HTML do formulário
         * 
         * @global array $_M_THIS_CONFIG
         * @param type $formFilename
         * @param type $updateId
         * @return \backend\Frontend
         */
        public function viewForm($formFilename, $updateId=null){
            global $_M_THIS_CONFIG;

            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "modules/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
            }

            // VERIFICA SE EXISTE CLASSE
            // DE EVENTOS DE FORMULÁRIO
            if( file_exists($filePath = "modules/$formFilename.php") && !empty($formFilename) ){
                require_once $filePath;
                $formEvents = new \FormEvents();
            }else{
                $formEvents = Array();
            }
            
            // TYPE OF FORM: INSERT OR UPDATE OR DUMMY
            switch (true){
                case is_null($updateId):
                    $formType = "insert"; 
                    break;
                
                case is_numeric($updateId):
                    $formType = "update"; 
                    break;
                
                case $updateId == "dummy":
                    $formType = "dummy"; 
                    break;
            }
            
            // MESCLA FORULÁRIOS SE NECESSÁRIO
            if( isset($formArray['forms'][ $formType ]['merge-form']) && is_array($formArray['forms'][ $formType ]['merge-form']) ){
                $formA = Array();
                $formB = Array();
                foreach($formArray['forms'][ $formType ]['merge-form'] as $formKey){
                    if( isset($formArray['forms'][ $formKey]) && is_array($formArray['forms'][ $formKey]) ){
                        $formA = $formArray['forms'][$formKey];
                    }
                    $formB = array_replace_recursive($formA, $formB);
                }
                $formArray['forms'][ $formType ] = $formB;
            }
            
            // VARIAVEIS QUE SERÃO ENVIADAS PARA A TELA
            $inputs  = Array();
            $headJS  = Array();
            $bodyJS  = Array();
            $headCSS = Array();

            // SETA ACTION CONFORME O TIPO DE FORMULÁRIO
            
            switch ($formType){
                case "insert":
                    $inputs[] = "<input name='_M_ACTION' type='hidden' value='insert'>";
                    break;
                
                case "dummy":
                    $inputs[] = "<input name='_M_ACTION' type='hidden' value='dummy-form'>";
                    break;
                
                case "update":
                    $inputs[] = "<input name='_M_ACTION' type='hidden' value='update:$updateId'>";

                    // busca colunas para a busca de dados
                    foreach ( $formArray['forms'][ $formType ]['input'] as $key => $val) {
                        $columns[] = $val['column'];

                        if( !isset($val['ignore-select']) || ($val['ignore-select']==false) ){
                            $selectColumns[] = $val['column'];
                        }
                    }

                    // executa FormEvents::beforeLoadData
                    if(method_exists($formEvents, "beforeLoadData")){
                        $formEvents->beforeLoadData(Array($formArray['header']['p-key']=>null), $formArray['header']);
                    }

                    // busca dados no banco
                    $db = new MySQL();
                    $loadedData = 
                        $db
                          ->setTable($formArray['header']['table'])
                          ->select(
                            $selectColumns,
                            "`{$formArray['header']['p-key']}` = '$updateId'"
                          )
                    ;

                    // executa FormEvents::afterLoadData
                    if(method_exists($formEvents, "afterLoadData")){
                        $formEvents->afterLoadData($loadedData[0], Array($formArray['header']['p-key']=>$updateId), $formArray['header']);
                    }
                    break;
            }

            // PERCORRE TODOS OS TYPES
            $counter = 0;
            foreach ( $formArray['forms'][ $formType ]['input'] as $key => $val) {

                // verifica se o arquivo de 
                // configuração do type existe
                if(file_exists($confTypePath = "types/{$val['type']}/config.yml")){
                    // carrega arquivo de configuração do type
                    $confTypeArray = Yaml::parse($confTypePath);

                    // caminho do type
                    $path = "types/{$val['type']}";
                    
                    // importa classe de type
                    if(file_exists($classPath = "$path/{$val['type']}.php")){
                        require_once $classPath;
                    }else{
                        return Array(
                            "error"     => true,
                            "errorCode" => "insertUpdate-loadType-3",
                            "message"   => "Arquivo de classe do tipo '{$val['type']}' não encontrado em '$classPath'."
                        );
                    }                    

                    // VARIAVEL QUE ENVIa VALORES PARA O LAYOUT
                    $toTypeLayout = Array();

                    // cria id unica para o type
                    $id = uniqid("{$val['column']}_");

                    // verifica parametros de type
                    $val['parameter'] = array_merge( 
                        ( isset($confTypeArray['default']['parameter']) && is_array($confTypeArray['default']['parameter']) ) ? $confTypeArray['default']['parameter'] : Array() ,
                        ( isset($val['parameter']) && is_array($val['parameter']) ) ? $val['parameter'] : Array()
                    );
                    
                    // executa beforeLoadDataToForm
                    $colname = ( is_array($colname = $val['column']) ) ? $colname[0] : $colname;
                    $typeObject = new $val['type']();
                    if( method_exists($typeObject, "beforeLoadDataToForm")){
                        $typeObject->beforeLoadDataToForm( $loadedData[0][$colname], $key, $loadedData[0], $val['parameter'], $toTypeLayout, Array("column" => $formArray['header']['p-key'], "value" => $updateId) );
                    }
                    
                    // variaveis que serão enviadas para 
                    // o layout do type carregado
                    $variables = Array(
                            "id"        => $id ,
                            "label"     => $val['label'] ,
                            "type"      => $val['type'] ,
                            "column"    => $val['column'] ,
                            "name"      => $val['column'] ,
                            "toLayout"  => $toTypeLayout,
                            "value"     => ( ($formType == "update")? $loadedData[0][ $val['column'] ] : "") ,
                            "parameter" => $val['parameter']
                        )
                    ;

                    // importa js do head
                    if(is_array($headJSPaths = $confTypeArray['interface']['javascript']['head'][ $formType ])){
                        foreach ($headJSPaths as $headJSPath) {
                            $headJS[] = "$path/$headJSPath";
                        }
                    }else{
                        $headJS[] = "$path/$headJSPaths";
                    }

                    // importa js do body
                    if(is_array($bodyJSPaths = $confTypeArray['interface']['javascript']['body'][ $formType ])){
                        foreach ($bodyJSPaths as $bodyJSPath) {
                            $bodyJS[] = (string)new Frontend("$path/$bodyJSPath", $variables);
                        }
                    }else{
                        $bodyJS[] = (string)new Frontend("$path/$bodyJSPaths", $variables);
                    }

                    // importa css
                    if(is_array($headCSSPaths = $confTypeArray['interface']['css'][ $formType ])){
                        foreach ($headCSSPaths as $headCSSPath) {
                            $headCSS[] = "$path/$headCSSPath";
                        }
                    }else{
                        $headCSS[] = "$path/$headCSSPaths";
                    }

                    // preloaded types
                    $preLoadedColumnsTypes['parameter'][ $val['label'] ]  = $val['parameter'];
                    $preLoadedColumnsTypes['columns'][ $val['label'] ]    = $val['column'];
                    $preLoadedColumnsTypes['type'][ $val['label'] ]       = $val['type'];
                    $preLoadedColumnsTypes['class-path'][ $val['label'] ] = $classPath;
                    $preLoadedColumnsTypes['classes'][ $val['label'] ]    = $typeObject;

                    // importa html de formulário
                    if( file_exists($htmlPath = "$path/". $confTypeArray['interface']['html'][ $formType ]) ){
                        $inputs[ $val['label'] ] = (string)new Frontend($htmlPath, $variables);
                    }else{
                        $inputs[ $val['label'] ] = "<div class='input-holder error {$val['type']}'>{$val['label']}: arquivo html '$htmlPath' não encontrado.</div>";
                    }

                }else{
                    // caso o arquivo de configuração do type não 
                    // exista, retorna um type com mensagem de erro
                    $inputs[ $val['label'] ] = "<div class='input-holder error {$val['type']}'>{$val['label']}: type '$confTypePath' não encontrado.</div>";
                }
            }

            // RETORNA DADOS PARA A INTERFACE GRÁFICA DO FORMULÁRIO
            $this->addToArrayLayout(
                    Array(
                        "main-title" => $formArray['header']['title'] ,
                        "title"      => $formArray['forms'][$formType]['title'] ,
                        "form"       => $formFilename ,
                        "inputs"     => implode("\r\n\r\n", $inputs) ,
                        "css"        => array_unique($headCSS) ,
                        "method"     => $formType ,
                        "javascript" => Array(
                            "head" => array_unique($headJS),
                            "body" => $bodyJS
                        )
                    )
                )
            ;
            
            //
            return $this;
        }



        /**
         *
         *  
         * @global array $_M_CONFIG
         * @global array $_M_THIS_CONFIG
         * @param type $formFilename
         * @param type $page
         * @param type $rowsPerPage
         * @param type $orderBy
         * @param type $condition
         * @return \backend\Frontend
         */
        public function viewList($formFilename, $page=null, $rowsPerPage=null, $orderBy=1, $condition=1){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;

            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "modules/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
            }

            // VERIFICA SE EXISTE CLASSE
            // DE EVENTOS DE FORMULÁRIO
            if( file_exists($filePath = "modules/$formFilename.php") && !empty($formFilename) ){
                require_once $filePath;
                $formEvents = new \FormEvents();
            }else{
                $formEvents = Array();
            }
            
            // VARIAVES COMUNS
            $table       = $formArray['header']['table'];
            $pk          = $formArray['header']['p-key'];
            $page        = (empty($page)       ) ? 1 : $page;
            $rowsPerPage = (empty($rowsPerPage)) ? $formArray['forms']['list']['rows-per-page'] : $rowsPerPage;
            $orderBy     = (empty($orderBy)    ) ? 1 : "$orderBy";
            $condition   = (empty($condition)  ) ? 1 : $condition;

            // VARIAVEIS QUE SERÃO ENVIADAS PARA A TELA
            $inputs  = Array();
            $headJS  = Array();
            $bodyJS  = Array();
            $headCSS = Array();
            
            // MESCLA FORULÁRIOS SE NECESSÁRIO
            if( isset($formArray['forms']['list']['merge-form']) && is_array($formArray['forms']['list']['merge-form']) ){
                $formA = Array();
                $formB = Array();
                foreach($formArray['forms']['list']['merge-form'] as $formKey){
                    if( isset($formArray['forms'][ $formKey]) && is_array($formArray['forms'][ $formKey]) ){
                        $formA = $formArray['forms'][ $formKey];
                    }
                    $formB = array_replace_recursive($formA, $formB);
                }
                $formArray['forms']['list'] = $formB;
            }
            
            // CARREGA TYPES COLUNAS A SEREM EXIBIDAS
            $defaultColumns = Array();
            $preLoadedColumnsTypes = Array();
            $columnNo = 1;
            foreach ( $formArray['forms']['list']['input'] as $key => $val) {
                if( !empty($val['column']) && !empty($val['type']) ){
                        // COLUNAS DO FORMULÁRIO PARA
                        // SEREM LISTADAS
                        $defaultColumns[ $val['label'] ] = $val['column'];
                                            
                        // caminho do type
                        $path = "types/{$val['type']}";

                        // cria id unica para o type
                        $id = uniqid("{$val['label']}_");

                        //
                        $pathType = "types/{$val['type']}";
                        // verifica se o arquivo de 
                        // configuração do type existe
                        if(file_exists($confTypePath = "$pathType/config.yml")){
                            // carrega arquivo de configuração do type
                            $loadedType = Yaml::parse($confTypePath);
                            $classPath = "$pathType/{$val['type']}.php";

                            // importa classe de type
                            if(file_exists($classPath)){
                                require_once $classPath;
                            }else{
                                return Array(
                                    "error"     => true,
                                    "errorCode" => "list-loadType-3",
                                    "message"   => "Arquivo de classe do tipo '{$val['type']}' não encontrado em '$classPath'."
                                );
                            }
                         
                            // verifica parametros de type
                            $val['parameter'] = array_merge( 
                                ( isset($loadedType['default']['parameter']) && is_array($loadedType['default']['parameter']) ) ? $loadedType['default']['parameter'] : Array() ,
                                ( isset($val['parameter']) && is_array($val['parameter']) ) ? $val['parameter'] : Array()
                            );
                            
                            // 
                            $variables = Array(
                                    "label"         => $val['label']     ,
                                    "type"          => $val['type']      ,
                                    "column"        => $val['column']    ,
                                    "column-number" => $columnNo++       ,
                                    "name"          => $val['column']    ,
                                    "value"         => ''                ,
                                    "parameter"     => $val['parameter']
                                )
                            ;


                            // importa js do head
                            if(is_array($headJSPaths = $loadedType['interface']['javascript']['head']['list'])){
                                foreach ($headJSPaths as $headJSPath) {
                                    $headJS[] = "$path/$headJSPath";
                                }
                            }else{
                                $headJS[] = "$path/$headJSPaths";
                            }

                            // importa js do body
                            if(is_array($bodyJSPaths = $loadedType['interface']['javascript']['body']['list'])){
                                foreach ($bodyJSPaths as $bodyJSPath) {
                                    $bodyJS[] = (string)new Frontend("$path/$bodyJSPath", $variables);;
                                }
                            }else{
                                $bodyJS[] = (string)new Frontend("$path/$bodyJSPaths", $variables);
                            }

                            // importa css
                            if(is_array($headCSSPaths = $loadedType['interface']['css']['list'])){
                                foreach ($headCSSPaths as $headCSSPath) {
                                    $headCSS[] = "$path/$headCSSPath";
                                }
                            }else{
                                $headCSS[] = "$path/$headCSSPaths";
                            }

                            // carrega propriedades do type e a sua classe
                            if( isset($loadedType['interface']['html']['list']) ){
                                if( file_exists($fileHtmlPath = "$pathType/{$loadedType['interface']['html']['list']}") ){
                                    $fileHtml = file_get_contents($fileHtmlPath);
                                }
                            }else{
                                $fileHtml = "";
                            }
                            
                            $preLoadedColumnsTypes[ $val['label'] ] = array_merge(
                                Array(
                                    "id"            => $id,
                                    "class-path"    => $classPath,
                                    "column-number" => $columnNo,
                                    "html"          => $fileHtml,
                                    "class"         => new $val['type']()
                                ),
                                $variables,
                                $val
                            );

                        }else{
                            // caso o arquivo de configuração do type não 
                            // exista, retorna um type com mensagem de erro
                            return Array(
                                "error"     => true,
                                "errorCode" => "list-loadType-2",
                                "message"   => "Formato '{$val['type']}' não encontrado em $confTypePath."
                            );
                        }
                }else{
                    return Array(
                        "error"     => true,
                        "errorCode" => "list-loadType-1",
                        "message"   => "Existe um campo do formulário de listagem ($formFilename.yml) que esta sem type e/ou column definidos."
                    );
                }
            }
            
            // MONTA TABLE SELECT
            $db = new MySQL();
            $selectTable = $db
              ->setTable($table)
              ->getSelectQuery(
                array_merge(
                    Array('_M_PRIMARY_KEY_VALUE_' => "$pk"),
                    $defaultColumns
                ), 
                1
              )
            ;

            // BUSCA DADOS NO BANCO DE DADOS
            $result = $db
              ->setTable("($selectTable)")
              ->setPage($page)
              ->setRowsPerPage($rowsPerPage)
              ->select(null, "$condition ORDER BY $orderBy")
            ;
            $resultReference = Array();
            $resultReference = $result;

            // BUSCA NUMÉRO DE PAGINAS
            $maxPages = $db->getLastPage($condition);
            
            //
            $TDs = "";
            $tbody = "";

            // EXECUTA FORMATAÇÃO DE TYPES
            array_walk(
                $result, 
                function(&$row, $rowKey) use($preLoadedColumnsTypes, $formArray, &$resultReference, &$tbody, &$TDs){
                    $TDs = "";
                    $pkValue = $row['_M_PRIMARY_KEY_VALUE_'];
                    unset($resultReference[ $rowKey ]['_M_PRIMARY_KEY_VALUE_']);
                    unset($resultReference[ $rowKey ]['_M_PRIMARY_KEY_VALUE_']);

                    // EXECUTA METODO DA CLASSE DO TYPE 
                    // E MONTA TDS PARA O BODY
                    array_walk(
                        $resultReference[ $rowKey ],
                        function(&$column, $columnKey) use($preLoadedColumnsTypes, &$resultReference, &$tbody, $rowKey, &$TDs){
                            // EXECUTA METODO DO TYPE
                            $typeObject = $preLoadedColumnsTypes[ $columnKey ]['class'];
                            if( method_exists($typeObject, "beforeList")){                            
                               $typeObject->beforeList($resultReference[ $rowKey ][ $columnKey ], $rowKey+1, $columnKey, $resultReference, $preLoadedColumnsTypes[ $columnKey ]['parameter']);
                            }

                            // CARREGA LAYOUT PARA 
                            // COLUNA SE FOR DEFINIDO
                            if( isset($preLoadedColumnsTypes[ $columnKey ]['html']) && !empty($preLoadedColumnsTypes[ $columnKey ]['html']) ){
                                $resultReference[ $rowKey ][ $columnKey ] = (string)new Frontend(
                                    $preLoadedColumnsTypes[ $columnKey ]['html'],
                                    array_merge(
                                        $preLoadedColumnsTypes[ $columnKey ],
                                        Array(
                                            "value" => $resultReference[ $rowKey ][ $columnKey ],
                                            "row"   => $rowKey+1
                                        )
                                    ),
                                    false
                                );
                            }

                            // CRIA TDs PARA TBODY
                            $rowOdd    = (($rowKey+1) % 2 == 0)? "even" : "odd";
                            $columnOdd = ($preLoadedColumnsTypes[$columnKey]['column-number'] % 2 == 0)? "even" : "odd ";

                            if( is_array($colname = $preLoadedColumnsTypes[$columnKey]['column']) ){
                                $colname = (isset($colname[0]))? $colname[0] : '';
                            }elseif( preg_match("/^[\d\W]/i", $colname) ){
                                 $colname = 'sql_injection';  
                            }
                            
                            $colname = urlencode($colname);

                            $TDs .= "  ";
                            $TDs .= "<td class='row_".($rowKey+1);
                            $TDs .= " column_{$preLoadedColumnsTypes[$columnKey]['column-number']}";
                            $TDs .= " row_$rowOdd";
                            $TDs .= " column_$columnOdd";
                            $TDs .= " column_". Util::slug($columnKey);
                            $TDs .= " {$preLoadedColumnsTypes[$columnKey]['type']}'";
                            $TDs .= " data-column='{$colname}'";
                            $TDs .= ">";
                            $TDs .=   "<a href='javascript:void(0)'>{$resultReference[$rowKey][$columnKey]}</a>";
                            $TDs .= "</td>\r\n";
                        }
                    );

                    // TBODY
                    $tbody .= "<tr rel='$pkValue' data-pk='{$formArray['header']['p-key']}' data-pk-value='$pkValue'>\r\n";
                    $tbody .= "  <td class='action'>\r\n";
                    $tbody .= "    <a href='$pkValue' class='select'></a>\r\n";
                    $tbody .= "    <a href='$pkValue' class='edit'></a>\r\n";
                    $tbody .= "    <a href='$pkValue' class='delete'></a>\r\n";
                    $tbody .= "  </td>\r\n";
                    $tbody .= $TDs;
                    $tbody .= "</tr>\r\n";
                }
            );

            // THs
            if(isset($resultReference[0])){
                $thead = "<tr>\r\n";
                $thead .= "  <th class='action'></th>\r\n";
                $columns = Array();
                foreach(array_keys($resultReference[0]) as $key => $val){
                    $columns[$key]['label'] = $val;
                    $columns[$key]['class'] = $val;
                    $columns[$key]['number'] = $key+1;
                    $thead .= "  <th rel='$val' class='". Util::slug($val) ." title_".($key+1)."'>$val</th>\r\n";
                }
                $thead .= "</tr>";
            }else{
                $columns[] = "<th>Nenhum registro encontrado</th>";
            }

            // MONTA RESULT
            $result = Array();
            $result['columns'] = $columns; 
            $result['data']    = $resultReference;
            $result['thead']   = (isset($thead) && !empty($thead)) ? $thead : $columns[0];
            $result['tbody']   = (isset($tbody) && !empty($tbody)) ? $tbody : "<td>...</td>";

            // PAGINATION
            $maxPageList = $_M_THIS_CONFIG['max-page-list'];
            $pageStart = $page - floor($maxPageList/2);
            $pageEnd = $page + ceil($maxPageList/2);

            // calcula primeira pagina
            if($pageStart <= 0){
                $pageStart = 1;
                $pageEnd = $pageStart + $maxPageList;
                $pageEnd = ($pageEnd > $maxPages) ? $maxPages : $pageEnd ; 
            }

            // calcula ultima pagina
            if($pageEnd > $maxPages){
                $pageEnd = $maxPages;
                $pageStart = $pageEnd - $maxPageList;
                $pageStart = ($pageStart < 0) ? 1 : $pageStart ;
            }

            // reticencias anterior
            if($pageStart > 1){
                $result['pages']['list'][] = Array( 
                    "number" => "...", 
                    "link"   => "page=" . ($page - floor($maxPageList/2)), 
                    "active" => "previus-five"
                );
            }

            // lista de paginas
            for($i=$pageStart; $i<=$pageEnd; $i++){
                $result['pages']['list'][] = Array( 
                    "number" => $i, 
                    "link"   => "page=" . $i, 
                    "active" => ($page == $i) ? "active" : "inactive"
                );
            }

            // reticencias proximos
            if($pageEnd !== $maxPages){
                $result['pages']['list'][] = Array( 
                    "number" => "...", 
                    "link"   => "page=" . ($page + $maxPageList + floor($maxPageList/2)), 
                    "active" => "previus-five"
                );
            }

            // ultima pagina
            $result['pages']['last'] = Array(
                "number" => $maxPages,
                "link"   => "page=$maxPages"
            );

            // FORMULÁRIO DE PESQUISA
            $searchForm = "";
            $first = true;
            $columnNo = 1;

            foreach ($preLoadedColumnsTypes as $key => $val) {

                if( is_array($colname = $val['column']) ){
                    $colname = (isset($colname[0]))? $colname[0] : '';
                }
                
                $searchForm .= "<div id='search_". Util::slug($val['label']) ."' class='input-holder search column_$columnNo'>\r\n";
                $searchForm .= "    <label>{$val['label']}</label>\r\n";
                $searchForm .= "    <input type='hidden' id='cond-column-$columnNo' name='cond[$columnNo][column]' value='". str_replace("'", "˙˙", $val['label']) ."'>\r\n";
                if(!$first){
                  $searchForm .= "    <select class='and-or' id='cond-and-or-$columnNo' name='cond[$columnNo][and-or]'>\r\n";
                  $searchForm .= "       <option value='-'>e (and)</option>\r\n";
                  $searchForm .= "       <option value='!'>ou (or)</option>\r\n";
                  $searchForm .= "    </select>\r\n";
                }
                $searchForm .= "    <select class='comparison' id='cond-comparison-$columnNo' name='cond[$columnNo][comparison]'>\r\n";
                $searchForm .= "       <option value=':'>igual (=)</option>\r\n";
                $searchForm .= "       <option value=';'>diferente (<>)</option>\r\n";
                $searchForm .= "       <option value='*'>parecido (like)</option>\r\n";
                $searchForm .= "       <option value='^'>expressão regular (rlike)</option>\r\n";
                $searchForm .= "       <option value='-'>entre (between), ex: 1,100</option>\r\n";
                $searchForm .= "    </select>\r\n";
                $searchForm .= "    <input class='value' type='text' id='cond-value-$columnNo' name='cond[$columnNo][value]'>\r\n";
                $searchForm .= "</div>\r\n";
                $first = false;
                $columnNo++;
            }

            // RETORNA VALORES PARA INTERFACE GRÁFICA
            $this->addToArrayLayout(
                    Array( 
                        "main-title"  => $formArray['header']['title']          ,
                        "title"       => $formArray['forms']['list']['title']   ,
                        "form"        => $formFilename                          ,
                        "pages"       => $result['pages']                       ,
                        "search-form" => $searchForm                            ,
                        "css"         => array_unique($headCSS)                 ,
                        "javascript" => array(
                            "head" => array_unique($headJS),
                            "body" => $bodyJS
                        ),
                        "table" => Array(
                            "columns" => $result['columns'],
                            "data"    => $result['data'],
                            "tbody"   => $result['tbody'],
                            "thead"   => $result['thead']
                        )
                    )
                )
            ;
            
            // 
            return $this;
        }

        
        /**
         * Salva ou atualiza dados no banco de dados
         *
         * @global array $_M_CONFIG
         * @global array $_M_THIS_CONFIG
         * @param type $formFilename
         * @param type $data
         * @return boolean
         */
        public function saveForm($formFilename, $data){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;

            // VERIFICA SE OS DADOS PRA 
            // ATUALIZAÇÃO ESTÃO CORRETOS
            if( !is_array($data) ){
                trigger_error("Dados informados para atualização não sao válidos", E_USER_ERROR);
                exit;
            }elseif( !isset($data['_M_ACTION']) ){
                trigger_error("_M_ACTION não foi definido", E_USER_ERROR);
                exit;
            }

            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "modules/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
            }
            
            // VERIFICA SE EXISTE CLASSE
            // DE EVENTOS DE FORMULÁRIO
            if( file_exists($filePath = "modules/$formFilename.php") && !empty($formFilename) ){
                require_once $filePath;
                $formEvents = new \FormEvents();
            }else{
                $formEvents = Array();
            }
            
            //
            $action = $data['_M_ACTION'];
            $table  = $formArray['header']['table'];
            $pk     = $formArray['header']['p-key'];
            unset($data['_M_ACTION']);

            // MESCLA FORULÁRIOS SE NECESSÁRIO
            $insertOrUpdate = preg_replace("/\:.*$/", "", $action);
            if( isset($formArray['forms'][ $insertOrUpdate ]['merge-form']) && is_array($formArray['forms'][ $insertOrUpdate ]['merge-form']) ){
                $formA = Array();
                $formB = Array();
                foreach($formArray['forms'][ $insertOrUpdate ]['merge-form'] as $formKey){
                    if( isset($formArray['forms'][ $formKey]) && is_array($formArray['forms'][ $formKey]) ){
                        $formA = $formArray['forms'][ $formKey];
                    }
                    $formB = array_replace_recursive($formA, $formB);
                }
                $formArray['forms'][ $insertOrUpdate ] = $formB;
            }
            
            // monta objeto do banco de dados
            $db = new MySQL();
            $db->setTable($table);

            // executa ações
            switch (true) {
  
                /*
                 * INSERIR
                 */
                case preg_match("/(insert)/i", $action, $matches):
                case preg_match("/(update)\:([0-9]+?)$/i", $action, $matches):
                    $action = $matches[1];
                    $id = ( isset($matches[2]) && !empty($matches[2]))? $matches[2] : $db->getNextId();
                    
                    // CARREGA ARRAY COM TYPES
                    $preLoadedColumnsTypes = Array();
                    foreach($formArray['forms'][$action]['input'] as $key=>$val){
                        $pathType = "types/{$val['type']}";
                        // verifica se o arquivo de 
                        // configuração do type existe
                        if(file_exists($confTypePath = "$pathType/config.yml")){
                            // carrega arquivo de configuração do type
                            $loadedType = Yaml::parse($confTypePath);
                            $classPath = "$pathType/{$val['type']}.php";

                            // importa classe de type
                            if(file_exists($classPath)){
                                require_once $classPath;
                            }else{
                                return Array(
                                    "error"     => true,
                                    "errorCode" => "$action-3",
                                    "message"   => "Arquivo de classe do tipo '{$val['type']}' não encontrado em '$classPath'."
                                );
                            }

                            // verifica parametros de type
                            $val['parameter'] = array_merge( 
                                ( isset($confTypeArray['default']['parameter']) && is_array($confTypeArray['default']['parameter']) ) ? $confTypeArray['default']['parameter'] : Array() ,
                                ( isset($val['parameter']) && is_array($val['parameter']) ) ? $val['parameter'] : Array()
                            );
                            
                            // carrega propriedades do type e a sua classe
                            $preLoadedColumnsTypes[ $val['column'] ] = array_merge(
                                Array(
                                    "type" => $val['type'],
                                    "label" => $val['label'],
                                    "class-path" => $classPath,
                                    "parameter" => $val['parameter'],
                                    "class" => new $val['type']()
                                )
                            );

                        }else{
                            // caso o arquivo de configuração do type não 
                            // exista, retorna um type com mensagem de erro
                            return Array(
                                "error"     => true,
                                "errorCode" => "$action-2",
                                "message"   => "Formato '{$val['type']}' não encontrado em $confTypePath."
                            );
                        }
                    }

                    // EXECUTA VALIDATE DE CADA TYPE 
                    // PARA CADA COLUNA DEFINIDA EM DATA
                    $errorArray = Array();
                    foreach($data as $key=>$val){
                        if(isset($preLoadedColumnsTypes[$key])){
                            if(method_exists($obj = $preLoadedColumnsTypes[$key]['class'], 'validate')){
                                $error = $obj->validate($data[$key], $key, $data, $preLoadedColumnsTypes[$key]['parameter'], $preLoadedColumnsTypes[$key]['label']);
                                if( isset($error['error']) && $error['error'] ){
                                    $errorArray[ $key ] = ( isset($error['message']) ) ? $error['message'] : "" ;
                                }
                            }
                        }
                    }

                    // CASE DE ALGUM CAMPO FOR INVALIDADO
                    if( !empty($errorArray) ){
                        return Array(
                                "error"     => true,
                                "errorCode" => "insert-notValid",
                                "message"   => $errorArray
                            )
                        ;
                    }

                    // ARMAZENA OS VALORES ORIGINAIS DE $data EM $origData
                    $origData = $data;

                    // EXECUTA BEFORE UPDATE OU INSERT 
                    // DE CADA TYPE PARA CADA COLUNA DEFINIDA EM DATA
                    foreach($data as $key=>$val){
                        if( isset($preLoadedColumnsTypes[$key]['class']) ){
                            if(is_string($val)){
                                $data[ $key ] = addslashes( $val );
                            }
                            
                            if( method_exists($obj = $preLoadedColumnsTypes[$key]['class'], $method = "before".ucwords($action)) ){
                                $obj->$method($data[$key], $key, $data, $preLoadedColumnsTypes[$key]['parameter'], Array( "column"=>$pk, "value"=>$id));
                            }
                        }
                    }

                    // VERIFICA SE É PRA INSERIR OU ATUALIZAR
                    // E EXECUTA EVENTO SE EXISTENTE
                    if($action == "update"){
                        $where = "`$pk` = '$id'";
                        // executa FormEvents::beforeUpdate
                        if(method_exists($formEvents, "beforeUpdate")){
                            $formEvents->beforeUpdate($data, Array($formArray['header']['p-key']=>$id), $formArray['header']);
                        }
                    }else{
                        $where = null;
                        // executa FormEvents::beforeInsert
                        if(method_exists($formEvents, "beforeInsert")){
                            $formEvents->beforeInsert($data, Array($formArray['header']['p-key']=>null), $formArray['header']);
                        }
                    }

                    // INSERE OU ATUALIZA DADOS NO BANCO DE DADOS
                    $db->save(
                        array_merge(
                            Array($pk => $id),
                            $data
                        ), 
                        $where
                      )
                    ;
                    
                    // EXECUTA EVENTO SE EXISTENTE
                    if($action == "update"){
                        // executa FormEvents::beforeUpdate
                        if(method_exists($formEvents, "afterUpdate")){
                            $formEvents->afterUpdate($data, Array($formArray['header']['p-key']=>$id), $formArray['header']);
                        }
                    }else{
                        // executa FormEvents::beforeInsert
                        if(method_exists($formEvents, "afterInsert")){
                            $formEvents->afterInsert($data, Array($formArray['header']['p-key']=>null), $formArray['header']);
                        }
                    }

                    // TRATA ERROS
                    if($db->getErrors()){
                        return Array(
                            "error"     => true,
                            "errorCode" => "insert-1",
                            "message"   => "Erro ao efetuar inserção/atualização no baco de dados. \r\n " . $db->getErrors()
                        );
                    }else{
                        $finalReturn = Array(
                            "error"     => false,
                            "message"   => "Dados inseridos/atualizados com sucesso.",
                            "result"    => array_merge(Array("_M_PRIMARY_KEY_VALUE_" => $id), $data)
                        );
                    }

                    // EXECUTA AFTER UPDATE OU INSERT 
                    // DE CADA TYPE PARA CADA COLUNA DEFINIDA EM DATA
                    $errorArray = Array();
                    $data = array_merge_recursive($origData, $data);
                    foreach($origData as $key=>$val){
                        if(method_exists($obj = $preLoadedColumnsTypes[$key]['class'], $method = "after".ucwords($action))){
                            $obj->$method($origData[$key], $key, $allData, $preLoadedColumnsTypes[$key]['parameter'], Array("column"=>$pk, "value"=>$id));
                        }
                    }

                    return $finalReturn;
                  break;
                  
                  case $action == "dummy-form":
                      $file = "modules/". dirname($formFilename). "/". (($formArray['forms']['dummy']['php'])?$formArray['forms']['dummy']['php']:"");
                      if(file_exists($file)){
                          return include $file;
                      }else{
                        return Array(
                            "error"     => true,
                            "errorCode" => "dummy-form-exec",
                            "message"   => "Arquivo '$file' de execução do formulário dummy não foi encontrado, verfique se foi setado um php para este formulário dummy."
                        );
                      }
                      break;

                default:
                    trigger_error("Ação '$action' desconhecida.", E_USER_ERROR);
                    exit;
                  break;
            }
        }

        
        /**
         * 
         * @global array $_M_CONFIG
         * @global array $_M_THIS_CONFIG
         * @global array $_M_MENU
         * @global array $_M_USER
         */
        public function deleteForm($formFilename, $deleteId){
            #global $_M_CONFIG;
            #global $_M_THIS_CONFIG;
            #global $_M_MENU;
            #global $_M_USER;
            
            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "modules/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
            }
            
            // VERIFICA SE EXISTE CLASSE
            // DE EVENTOS DE FORMULÁRIO
            if( file_exists($filePath = "modules/$formFilename.php") && !empty($formFilename) ){
                require_once $filePath;
                $formEvents = new \FormEvents();
            }else{
                $formEvents = Array();
            }
            
            // MESCLA FORULÁRIOS SE NECESSÁRIO
            if( isset($formArray['forms']['delete']['merge-form']) && is_array($formArray['forms']['delete']['merge-form']) ){
                $formA = Array();
                $formB = Array();
                foreach($formArray['forms']['delete']['merge-form'] as $formKey){
                    if( isset($formArray['forms'][ $formKey]) && is_array($formArray['forms'][ $formKey]) ){
                        $formA = $formArray['forms'][ $formKey];
                    }
                    $formB = array_replace_recursive($formA, $formB);
                }
                $formArray['forms']['delete'] = $formB;
            }
            
            // LISTA DE COLUNAS PARA O SELECT
            $selectColumns = Array();
            
            // PERCORRE TODOS OS TYPES
            if( isset($formArray['forms']['delete']['input']) ){
                foreach ( $formArray['forms']['delete']['input'] as $key => $val) {
                    // verifica se o arquivo de 
                    // configuração do type existe
                    if(file_exists($confTypePath = "types/{$val['type']}/config.yml")){
                        // carrega arquivo de configuração do type
                        $confTypeArray = Yaml::parse($confTypePath);

                        // caminho do type
                        $path = "types/{$val['type']}";

                        // importa classe de type
                        if(file_exists($classPath = "$path/{$val['type']}.php")){
                            require_once $classPath;
                        }else{
                            return Array(
                                "error"     => true,
                                "errorCode" => "insertUpdate-loadType-3",
                                "message"   => "Arquivo de classe do tipo '{$val['type']}' não encontrado em '$classPath'."
                            );
                        }                    

                        // verifica parametros de type
                        $val['parameter'] = array_merge( 
                            ( isset($confTypeArray['default']['parameter']) && is_array($confTypeArray['default']['parameter']) ) ? $confTypeArray['default']['parameter'] : Array() ,
                            ( isset($val['parameter']) && is_array($val['parameter']) ) ? $val['parameter'] : Array()
                        );

                        // preloaded types
                        $preLoadedColumnsTypes[ $val['label'] ]['parameter']  = $val['parameter'];
                        $preLoadedColumnsTypes[ $val['label'] ]['columns']    = $val['column'];
                        $preLoadedColumnsTypes[ $val['label'] ]['type']       = $val['type'];
                        $preLoadedColumnsTypes[ $val['label'] ]['class-path'] = $classPath;

                        // carrega objeto do type
                        if( file_exists($classPath) ){
                            require_once $classPath;
                            $preLoadedColumnsTypes[ $val['label'] ]['object'] = new $val['type'];
                        }else{
                            $preLoadedColumnsTypes[ $val['label'] ]['object'] = null;
                        }

                        // adiciona colua apara a lista de colunas do select
                        if( !isset($val['ignore-select']) || ($val['ignore-select']==false) ){
                            $selectColumns[ $val['label'] ] = $val['column'];
                        }
                    }
                }      
            }
            
            // BUSCA DADOS NO BANCO
            $db = new MySQL();
            $loadedData = 
                $db
                  ->setTable($formArray['header']['table'])
                  ->select(
                    array_merge(
                        Array('_M_PRIMARY_KEY_VALUE_' => $formArray['header']['p-key']),
                        $selectColumns
                    ),       
                    "`{$formArray['header']['p-key']}` = '$deleteId' LIMIT 1"
                  )
            ;
            
            // executa FormEvents::beforeDelete
            if(method_exists($formEvents, "beforeDelete")){
                $formEvents->beforeDelete($loadedData, Array($formArray['header']['p-key']=>$deleteId), $formArray['header'], $formArray['header']);
            }
                    
            // BEFORE DELETE
            if(isset($preLoadedColumnsTypes)){
                foreach($preLoadedColumnsTypes as $key => $type){
                    $typeObject = $type['object'];
                    if( method_exists($typeObject, "beforeDelete")){
                        $typeObject->beforeDelete($loadedData[$key], $key, $loadedData, $type['parameter'], Array("column"=>$formArray['header']['p-key'], "value"=>$deleteId) );
                    }
                }
            }
            
            // EXECUTA O DELETE
            $db->delete("`{$formArray['header']['p-key']}` = '$deleteId' LIMIT 1");
            
            if( $errors = $db->getErrors() ){
                return $errors;
            }
            
            // AFTER DELETE
            if(isset($preLoadedColumnsTypes)){
                foreach($preLoadedColumnsTypes as $key => $type){
                    $typeObject = $type['object'];
                    if( method_exists($typeObject, "afterDelete")){
                        $typeObject->afterDelete($loadedData[$key], $key, $loadedData, $type['parameter'], Array("column"=>$formArray['header']['p-key'], "value"=>$deleteId) );
                    }
                }
            }
            
            // executa FormEvents::afterDelete
            if(method_exists($formEvents, "afterDelete")){
                $formEvents->afterDelete($loadedData, Array($formArray['header']['p-key']=>$deleteId), $formArray['header']);
            }
            
            //
            return $loadedData;
        }
        
        /**
         * 
         * @param type $formLayout
         */
        public function setLayout($layout) {
            $this->htmlLayout = $layout;
            return $this;
        }

        /**
         * 
         * @global array $_M_THIS_CONFIG
         */
        public function writeHTML(){
            global $_M_THIS_CONFIG;
            echo new Frontend($_M_THIS_CONFIG['template'] ."/". $this->htmlLayout, $this->sendArrayToLayout);
        }
        
        /**
         * Adiciona informações a serem enviados para listagem e formulário
         * 
         * @global array $_M_THIS_CONFIG
         * @global array $_M_MENU
         * @global type $_M_MENU_PARTS
         * @global type $_M_MENU_MODULE
         * @global array $_M_USER
         * @param array $array
         */
        private function addToArrayLayout(array $array){
            global $_M_THIS_CONFIG;
            global $_M_MENU;
            global $_M_MENU_PARTS;
            global $_M_MENU_MODULE;
            global $_M_USER;
            
            // O minimo que o array deve ter
            $arrayBase = Array(  
                "user" => "" ,
                "main-menu" => "" ,
                "main-menu-parts" => "",
                "menu-modules" => "",
                "main-title" => "" ,
                "title" => "" ,
                "form" => "" ,
                "inputs" => "" ,
                "method" => "" ,
                "pages" => "" ,
                "search-form" => "" ,
                "css" => array() ,
                "javascript" => array(
                    "head" => array(),
                    "body" => array()
                ),
                "table" => Array(
                    "columns" => "" ,
                    "data" => "",
                    "tbody" => "" ,
                    "thead" => ""
                )
            );
            
            //
            $this->sendArrayToLayout = array_replace_recursive($arrayBase, $this->sendArrayToLayout);
            $array = array_replace($arrayBase, $array);
            
            // Monta o array
            $array = Array(  
                "user" => $_M_USER ,
                "main-menu" => $_M_MENU ,
                "main-menu-parts" => $_M_MENU_PARTS,
                "menu-modules" => $_M_MENU_MODULE,
                "main-title" => $array["main-title"] ,
                "title" => $array["title"] ,
                "form" => $array["form"] ,
                "inputs" => $this->sendArrayToLayout['inputs'] ."\r\n". $array['inputs'] ,
                "method" => (!empty($array['method'])) ? $array['method'] : "" ,
                "pages" => $array['pages'] ,
                "search-form" => $array['search-form'] ,
                "css" => array_unique( array_merge($array['css'], $this->sendArrayToLayout['css']) ) ,
                "javascript" => array(
                    "head" => array_unique( array_merge($array['javascript']['head'], $this->sendArrayToLayout['javascript']['head']) ),
                    "body" => array_unique( array_merge($array['javascript']['body'], $this->sendArrayToLayout['javascript']['body']) )
                ),
                "table" => Array(
                    "columns" => $array['table']['columns'] ,
                    "data" => $array['table']['data'] ,
                    "tbody" => $array['table']['tbody'] ,
                    "thead" => $array['table']['thead']
                )
            );
            
            // Retorna para a variavel que irá para a tela
            $this->sendArrayToLayout = array_merge($_M_THIS_CONFIG, $array);
        }
        
    }