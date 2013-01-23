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
        private $formLayout = "form.html";
        
        /**
         *
         * @var type 
         */
        private $listLayout = "list.html";

        /**
         * Gera HTML do formulário
         * 
         * @global type $_M_THIS_CONFIG
         * @global type $_M_MENU
         * @global type $_M_USER
         * @param type $formFilename
         * @param type $updateId
         * @return \backend\Frontend
         */
        public function viewForm($formFilename, $updateId=null){
            global $_M_THIS_CONFIG;
            global $_M_MENU;
            global $_M_USER;

            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "forms/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
            }

            // INSERT OR UPDATE
            $insertOrUpdate = is_null($updateId) ? "insert" : "update";
            
            // MESCLA FORULÁRIOS SE NECESSÁRIO
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
            
            // VARIAVEIS QUE SERÃO ENVIADAS PARA A TELA
            $inputs  = Array();
            $headJS  = Array();
            $bodyJS  = Array();
            $headCSS = Array();

            // BUSCA DADOS NO BANCO DE DADOS 
            // CASO SEJA INFORMADO $id
            if(is_null($updateId)){
                // define input de ação para inserção
                $inputs[] = "<input name='_M_ACTION' type='hidden' value='insert'>";
            }else{
                // define input de ação para atualização
                $inputs[] = "<input name='_M_ACTION' type='hidden' value='update:$updateId'>";

                // busca colunas para a busca de dados
                foreach ( $formArray['forms'][ $insertOrUpdate ]['input'] as $key => $val) {
                    $columns[] = $val['column'];

                    if( !isset($val['ignore-select']) || ($val['ignore-select']==false) ){
                        $selectColumns[] = $val['column'];
                    }
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
                
            }

            // PERCORRE TODOS OS TYPES
            foreach ( $formArray['forms'][ $insertOrUpdate ]['input'] as $key => $val) {

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
                            "value"     => ( is_null($updateId)? "" : $loadedData[0][ $val['column'] ] ) ,
                            "parameter" => $val['parameter']
                        )
                    ;

                    // importa js do head
                    if(is_array($headJSPaths = $confTypeArray['interface']['javascript']['head'][ $insertOrUpdate ])){
                        foreach ($headJSPaths as $headJSPath) {
                            $headJS[] = "$path/$headJSPath";
                        }
                    }else{
                        $headJS[] = "$path/$headJSPaths";
                    }

                    // importa js do body
                    if(is_array($bodyJSPaths = $confTypeArray['interface']['javascript']['body'][ $insertOrUpdate ])){
                        foreach ($bodyJSPaths as $bodyJSPath) {
                            $bodyJS[] = (string)new Frontend("$path/$bodyJSPath", $variables);;
                        }
                    }else{
                        $bodyJS[] = (string)new Frontend("$path/$bodyJSPaths", $variables);
                    }

                    // importa css
                    if(is_array($headCSSPaths = $confTypeArray['interface']['css'][ $insertOrUpdate ])){
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
                    if( file_exists($htmlPath = "$path/". $confTypeArray['interface']['html'][ $insertOrUpdate ]) ){
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

            // RETORNA A INTERFACE GRÁFICA DO FORMULÁRIO
            return 
                new Frontend(
                    $_M_THIS_CONFIG['template'] ."/". $this->formLayout,
                    array_merge(
                        $_M_THIS_CONFIG,
                        Array(  
                            "user"       => $_M_USER ,
                            "main-menu"  => $_M_MENU ,
                            "main-title" => $formArray['header']['title'] ,
                            "title"      => $formArray['forms'][$insertOrUpdate]['title'] ,
                            "form"       => $formFilename ,
                            "inputs"     => implode("\r\n\r\n", $inputs) ,
                            "css"        => array_unique($headCSS) ,
                            "method"     => $insertOrUpdate ,
                            "javascript" => Array(
                                "head" => array_unique($headJS),
                                "body" => $bodyJS
                            )
                        )
                    )
                )
            ;
        }



        /**
         *
         *  
         * @global type $_M_CONFIG
         * @global \backend\type $_M_THIS_CONFIG
         * @global \backend\type $_M_MENU
         * @global \backend\type $_M_USER
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
            global $_M_MENU;
            global $_M_USER;

            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "forms/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
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
                                $fileHtmlPath = "$pathType/{$loadedType['interface']['html']['list']}";
                            }else{
                                $fileHtmlPath = "$pathType/";
                            }
                            
                            $preLoadedColumnsTypes[ $val['label'] ] = array_merge(
                                Array(
                                    "id"            => $id,
                                    "class-path"    => $classPath,
                                    "column-number" => $columnNo,
                                    "html"          => ( file_exists($fileHtmlPath) ) ? file_get_contents($fileHtmlPath) : "",
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
            
            

            // BUSCA DADOS NO BANCO DE DADOS
            $db = new MySQL();
            $result = $db
              ->setTable($table)
              ->setPage($page)
              ->setRowsPerPage($rowsPerPage)
              ->select(
                array_merge(
                    Array('_M_PRIMARY_KEY_VALUE_' => "$pk"),
                    $defaultColumns
                ),
                $condition . " ORDER BY " . $orderBy
              )
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

                $searchForm .= "<div id='search_{$val['label']}' class='input-holder {$val['type']} search column_$columnNo column_{$val['label']}'>\r\n";
                $searchForm .= "    <label>{$val['label']}</label>\r\n";
                if(!$first){
                  $searchForm .= "    <select class='and-or' name='search_andOr-{$colname}'>\r\n";
                  $searchForm .= "       <option value='-'>e (and)</option>\r\n";
                  $searchForm .= "       <option value='!'>ou (or)</option>\r\n";
                  $searchForm .= "    </select>\r\n";
                }
                $searchForm .= "    <select class='comparison' name='search_comparison-{$colname}'>\r\n";
                $searchForm .= "       <option value=':'>igual (=)</option>\r\n";
                $searchForm .= "       <option value=';'>diferente (<>)</option>\r\n";
                $searchForm .= "       <option value='*'>parecido (like)</option>\r\n";
                $searchForm .= "       <option value='^'>expressão regular (rlike)</option>\r\n";
                $searchForm .= "       <option value='-'>entre (between), ex: 1,100</option>\r\n";
                $searchForm .= "    </select>\r\n";
                $searchForm .= "    <input class='value' type='text' name='search_value-{$colname}'>\r\n";
                $searchForm .= "</div>\r\n";
                $first = false;
                $columnNo++;
            }

            // RETORNA INTERFACE GRÁFICA
            return 
                new Frontend(
                    $_M_THIS_CONFIG['template'] ."/". $this->listLayout,
                    array_merge(
                        $_M_THIS_CONFIG,
                        Array( 
                            "user"        => $_M_USER ,
                            "main-menu"   => $_M_MENU ,
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
                )
            ;
        }

        /**
         * Salva ou atualiza dados no banco de dados
         *
         * @global \backend\type $_M_CONFIG
         * @global \backend\type $_M_THIS_CONFIG
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
            if( file_exists($filePath = "forms/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
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
                            if( method_exists($obj = $preLoadedColumnsTypes[$key]['class'], $method = "before".ucwords($action)) ){
                                $obj->$method($data[$key], $key, $data, $preLoadedColumnsTypes[$key]['parameter'], Array( "column"=>$pk, "value"=>$id ));
                            }
                        }
                    }

                    // VERIFICA SE É PRA INSERIR OU ATUALIZAR
                    if($action == "update"){
                        $where = "`$pk` = '$id'";
                    }else{
                        $where = null;
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

                default:
                    trigger_error("Ação '$action' desconhecida.", E_USER_ERROR);
                    exit;
                  break;
            }
        }

        
        /**
         * 
         * @global \backend\type $_M_CONFIG
         * @global \backend\type $_M_THIS_CONFIG
         * @global \backend\type $_M_MENU
         * @global \backend\type $_M_USER
         */
        public function deleteForm($formFilename, $deleteId){
            #global $_M_CONFIG;
            #global $_M_THIS_CONFIG;
            #global $_M_MENU;
            #global $_M_USER;
            
            // VERIFICA SE O ARQUIVO 
            // DE FORMULÁRIO EXISTE
            if( file_exists($filePath = "forms/$formFilename.yml") && !empty($formFilename) ){
                // caso exista, carrega o formulário
                $formArray = Yaml::parse(file_get_contents($filePath));
            }else{
                // caso não exista, mostra mensagem de erro
                trigger_error("Erro ao carregar formulário: $filePath", E_USER_ERROR);
                exit;
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
            
            // BEFORE DELETE
            foreach($preLoadedColumnsTypes as $key => $type){
                $typeObject = $type['object'];
                if( method_exists($typeObject, "beforeDelete")){
                    $typeObject->beforeDelete($loadedData[$key], $key, $loadedData, $type['parameter'], Array("column"=>$formArray['header']['p-key'], "value"=>$deleteId) );
                }
            }
            
            // EXECUTA O DELETE
            $db->delete("`{$formArray['header']['p-key']}` = '$deleteId' LIMIT 1");
            
            if( $errors = $db->getErrors() ){
                return $errors;
            }
            
            // AFTER DELETE
            foreach($preLoadedColumnsTypes as $key => $type){
                $typeObject = $type['object'];
                if( method_exists($typeObject, "afterDelete")){
                    $typeObject->afterDelete($loadedData[$key], $key, $loadedData, $type['parameter'], Array("column"=>$formArray['header']['p-key'], "value"=>$deleteId) );
                }
            }
            
            //
            return $loadedData;
        }
        
        /**
         * 
         * @param type $formLayout
         */
        public function setFormLayout($formLayout) {
            $this->formLayout = $formLayout;
        }

        /**
         * 
         * @param type $listLayout
         */
        public function setListLayout($listLayout) {
            $this->listLayout = $listLayout;
        }

    }