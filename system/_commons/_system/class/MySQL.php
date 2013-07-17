<?php

    /**
     * Version: 0.6a
     *
     * Changes::
     *    0.7:
     *      - getSelectQuery  : Retorna query string do select
     *
     *    0.6a:
     *      - getTableInfo     : Retorna array com informações sobre a tabela
     *      - getNextId        : Retorna qual é o valor da próxima chave primária
     *
     *    0.5a:
     *      - getLastPage      : Retorna a quantidade de paginas existentes com base em setRowsPerPage()
     *      - rowsCount        : Equivalente ao Count do MySQL
     *
     *    0.4a:
     *      - affectedRows     : Retorna a quantidade de linhas afetadas na execução do ultimo SQL Query
     *
     *    0.3a:
     *      - executeLast      : Executa a ultima SQLs Query definida por setQuery()
     *
     *    0.2a:
     *      - cancelQuery      : Cancela uma query especifica do pool de queries
     *      - cancelLastQuery  : Cancela a ultima query do pool de queries
     *      - cancelAllQueries : Cancela todas as queries do pool de queries
     *      - getErrors        : Retorna um array com informações de todos os erros
     *      - clearErrors      : Limpa a lista de erros
     *      -
     *      -
     *
     *    0.1a:
     *      - execute        : Executa todas as SQLs Queries definidas por setQuery()
     *      - save           : Insere ou atualiza valores no banco de dados
     *      - select         : Permite efetuar busca de informação na base de dados de maneira simplificada, inluindo paginação e relacionamento entre tabelas
     *      - delete         : Exclui dados do banco de dados
     *      - setQuery       : Define uma query a ser executada pelo método execute()
     *      - setTable       : Selecionan uma tabela a ser utilizada pelo objeto, caso seja uma tabela inválida retorna mensagem de erro
     *      - setRowsPerPage : Define quantidade de linhas a serem exibidas por página
     *      - setPage        : Define a pagina a ser exibida
     *      - getQuery       : Retorna a ultima SQL Query definida não executada
     *      - getQueries     : Retorna um array com a lista de todas as SQL Query não executadas
     */

    namespace backend;

    use backend\Util;

    class MySQL{
        private $PDO = '';
        private $queries = array();
        private $errors = array();
        private $table = "";
        private $page = -1;
        private $rowsPerPage = 100;
        private $countAffectedRows = 0;

        /**
         * Conecta-se ao banco de dados e configura para que sejam retornados dados UTF8
         *
         * @global type $_M_CONFIG
         */
        public function __construct(){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;

            # VERIFICAR SE PDO MySQL ESTA ATIVO
            if( !extension_loaded('pdo_mysql') ){
                trigger_error("PDO MySQL não esta instalado neste servidor.", E_USER_ERROR);
                exit;
            }

            # CONECTA NO BANCO DE DADOS
            $connectionString = ''.
                'mysql:'
              . 'host='   . $_M_CONFIG->mysql['host']     . ';'
              . 'port='   . $_M_CONFIG->mysql['port']     . ';'
              . 'dbname=' . $_M_CONFIG->mysql['database']
            ;


            # CRIA OBJETO PDO
            $this->PDO = new \PDO($connectionString, $_M_CONFIG->mysql['username'], $_M_CONFIG->mysql['password']);

            # CONFIGURA O PDO
            $this->PDO->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            # DEFINE O CHARSET DO BANCO DE DADOS PARA UTF-8
            $this->PDO->exec("SET NAMES 'utf8'");
            $this->PDO->exec('SET character_set_connection=utf8');
            $this->PDO->exec('SET character_set_client=utf8');
            $this->PDO->exec('SET character_set_results=utf8');

            #
            $this->rowsPerPage = $_M_CONFIG->backend['rows-per-page'];
        }

        /**
         * Executa todas as SQL Queries definidas por setQuery()
         *
         */
        public function execute(){
            $this->PDO->beginTransaction();
            while ($query = array_shift($this->queries)) {
                $transaction = $this->PDO->prepare($query);
                $transaction->execute();
            }
            $this->countAffectedRows = $transaction->rowCount();
            $this->PDO->commit();
        }

        /**
         * Executa a ultima SQL Query definida por setQuery()
         *
         */
        public function executeLast(){
            $this->PDO->beginTransaction();
            $query = array_pop($this->queries);

            $transaction = $this->PDO->prepare($query);
            if( $transaction ){
                $transaction->execute();
                $this->countAffectedRows = $transaction->rowCount();
                $this->PDO->commit();
            }else{
                $this->errors[] = Array(
                    "query" => $query
                );
            }
        }

        /**
         * Insere ou atualiza valores no banco de dados
         *
         */
        public function save($data, $where=null){
            // VERIFICA TABLE
            if( !isset($this->table) ){
                trigger_error("Tabela não definida, utilize setTable antes de executar select()" , E_USER_ERROR);
                exit;
            }

            // VERIFICA SE É INSERÇÃO OU ATUALIZAÇÃO
            if(is_null($where)){
                // INSERE

                // MONTA VALORES vs COLUNAS
                $columns = Array();
                $values  = Array();

                foreach ($data as $key=>$val) {
                    $columns[] = "`$key`";
                    $values[]  = "'$val'";
                }

                $columns = implode( ", " , $columns );
                $values  = implode( ", " , $values  );

                // MONTA QUERY DE INSERÇÃO
                $this->setQuery("INSERT INTO {$this->table}($columns) VALUES($values)");

                // EXECUTA INSERÇÃO CASO NÃO OCORRA ERROS
                if($this->getErrors()){
                    trigger_error("Erro ao exeutar save - insert: \r\n" . print_r($this->getErrors(), true) , E_USER_ERROR);
                    exit;
                }else{
                    $this->executeLast();
                }

            }else{
                // ATUALIZA

                // MONTA VALORES vs COLUNAS
                $set = Array();

                foreach ($data as $key=>$val) {
                    $set[] = "`$key` = '$val'";
                }

                $set = implode( ", " , $set );

                // MONTA QUERY DE ATUALIZAÇÃO
                $this->setQuery("UPDATE {$this->table} SET $set WHERE $where");

                // EXECUTA ATUALIZAÇÃO CASO NÃO OCORRA ERROS
                if($this->getErrors()){
                    trigger_error("Erro ao exeutar save - update: \r\n" . print_r($this->getErrors(), true) , E_USER_ERROR);
                    exit;
                }else{
                    $this->executeLast();
                }
            }

            return $this;
        }

        /**
         * Permite efetuar busca de informação na base de dados de maneira simplificada, inluindo paginação e relacionamento entre tabelas
         *
         * @param Array   $columns   array contendo o nome das colunas a serem mostrada
         * @param String  $where     condição que será executado o select
         * @param Boolean $ifOneCuts caso seja retornado apenas uma linha desconsidera a chave de contagem
         * @param Boolean $paginate  se será considerado a paginação ou não
         * @return Array
         */
        public function select($columns, $where, $ifOneCuts=false, $paginate=true){
            $query = $this->getSelectQuery($columns, $where, $ifOneCuts, $paginate);

            // BUSCA ARRAY DE RESULTADO
            $this->setQuery($query);

            if( !$this->getErrors() ){
                if( $result = $this->PDO->query( $this->getQuery() ) ){
                    $return = $result->fetchAll(\PDO::FETCH_ASSOC);
                    array_pop($this->queries);

                    if(!is_null($columns)){

                        // BUSCA RELACIONAMENTOS
                        foreach ($columns as $key => $val) {
                            // SE A CHAVE NÃO FOR NUMERICA E O VALOR FOR
                            // UM ARRAY, SIGNIFICA QUE FOI DEFINIDO UM
                            // RELACIONAMENTO E QUE FOI DEFINIDO UM ALIAS
                            // PARA ESTE RELACIONAMENTO
                            if(!is_numeric($key) && is_array($val)){
                                $strColumns[] = "'$key'";
                                $relationships[$key] = $val;
                            }

                            // SE A CHAVE FOR NUMERICA E O VALOR FOR UM ARRAY
                            // SIGNIFICA QUE FOI DEFINIDO UM RELACIONAMENTO
                            // MAS QUE NÃO FOI DEFINIDO UM ALIAS PARA ESTE
                            // RELACIONAMENTO
                            else if(is_numeric($key) && is_array($val)){
                                $key = "column_without_alias_$key";
                                $strColumns[] = "'$key'";
                                $relationships[$key] = $val;
                            }
                        }

                        // EXECUTA RECURSIVO DE RELACIONAMENTO
                        if(!empty($relationships)){
                            // GUARDA A TABELA QUE ESTA SENDO UTILIZADA
                            // ANTES DE EXECUTAR O RELACIONAMENTO
                            $originalSelectTable = $this->table;

                            // PERORRE O ARRAY EXECUTANDO O RELACIONAMENTO
                            $that =& $this; // pog pra poder usar $this dentro do array_walk -- PHP 5.3
                            array_walk(
                                $return,
                                function(&$return, $key, $param) use($that){
                                    foreach ($param['relationships'] as $relation_key => $relation_val) {

                                        // SHORT CIRCUIT TO GET P-KEY
                                        ($pk = @$relation_val[0]) ||
                                        ($pk = @$relation_val['p-key']) ;

                                        // SHORT CIRCUIT TO GET F-KEY
                                        ($tableAndColumn = @$relation_val[1])     ||
                                        ($tableAndColumn = @$relation_val['f-key']) ;

                                        // SHORT CIRCUIT TO GET COLUMNS
                                        ($show_columns = @$relation_val[2]) ||
                                        ($show_columns = @$relation_val['columns']) ;

                                        // VERIFICA SE A TABLEANDCOLUMN ESTÃO CORRETOS
                                        if( substr_count($tableAndColumn, ".") !== 1 ){
                                            trigger_error(
                                                "O segundo parametro do relacionamento no método select() deve conter ".
                                                "tabelaRelaciona.colunaRelacionada, você informou {$tableAndColumn}.",
                                                E_USER_ERROR
                                            );
                                            exit;
                                        }

                                        // VARIAVEIS DE RELACIONAMENTO
                                        $tableAndColumn = explode(".", $tableAndColumn);
                                        $pk_value = @$return[$pk];
                                        $fk_table = $tableAndColumn[0];
                                        $fk       = $tableAndColumn[1];
                                        $where    = (
                                                        isset($relation_val['where'])  &&
                                                        !empty($relation_val['where'])
                                                    )
                                                        ? $relation_val['where']
                                                        : '1'
                                                    ;

                                        //
                                        if( isset($relation_val['concat']) && !empty($relation_val['concat'][0]) ){
                                            $return[$relation_key] =
                                                 Util::implode_recursive(
                                                    $that
                                                        ->setTable($fk_table)
                                                        ->select($show_columns, "`$fk` = '$pk_value' AND ($where)", $param['ifOneCuts'], true)
                                                    ,
                                                     $relation_val['concat'][0],
                                                 ( isset($relation_val['concat'][1]) ) ? $relation_val['concat'][1] : ""
                                                )
                                            ;
                                        }else{
                                            $return[$relation_key] =
                                                $that
                                                    ->setTable($fk_table)
                                                    ->select($show_columns, "`$fk` = '$pk_value' AND ($where)", $param['ifOneCuts'], false)
                                            ;
                                        }
                                    }
                                },
                                Array(
                                    'relationships' => $relationships,
                                    'ifOneCuts' => $ifOneCuts
                                )
                            );

                            // RETORNA PARA TABELA SETADA ANTES DE EXECUTAR O RELACIONAMENTO
                            $this->table = $originalSelectTable;
                        }
                    }

                    // SE O SELECT ENCONTRAR SOMENTE UMA LINHA DEVE SER
                    // RETORNADO UM ARRAY DE UMA ÚNICA DIMENSÃO
                    if( count($return)==1 && $ifOneCuts ){
                        $return = $return[0];
                    }

                    //
                    return $return;
                }else{
                    return null;
                }
            }else{
                trigger_error("Erro ao exeutar select: \r\n" . print_r($this->getErrors(), true) , E_USER_ERROR);
                exit;
            }
        }

        /**
         * Equivalente ao Count do MySQL
         *
         * @param String $where condição que será executado o select
         * @return Integer
         */
        public function rowsCount($where){
            // VERIFICA TABLE
            if( !isset($this->table) ){
                trigger_error("Tabela não definida, utilize setTable antes de executar select()" , E_USER_ERROR);
                exit;
            }

            // BUSCA ARRAY DE RESULTADO
            $query = "SELECT count(*) AS `counter` FROM {$this->table} WHERE {$where}";
            $this->setQuery($query);
            if( !$this->getErrors() ){
                if( $result = $this->PDO->query( $this->getQuery() ) ){
                    $return = $result->fetchAll(\PDO::FETCH_ASSOC);
                    return $return[0]['counter'];
                }else{
                    return null;
                }
            }else{
                trigger_error("Erro ao exeutar rowsCount: \r\n" . print_r($this->getErrors(), true) , E_USER_ERROR);
                exit;
            }
        }

        /**
         * Retorna a quantidade de paginas existentes com base em setRowsPerPage()
         *
         * @param String $where condição que será executado o select
         * @return Integer
         */
        public function getLastPage($where){
            $page = ceil( $this->rowsCount($where) / $this->rowsPerPage );
            return $page;
        }

        /**
         * Exclui dados do banco de dados
         *
         * @param String $where condição que será executado o delete
         */
        public function delete($where){
            // VERIFICA TABLE
            if( !isset($this->table) ){
                trigger_error("Tabela não definida, utilize setTable antes de executar select()" , E_USER_ERROR);
                exit;
            }

            // MONTA QUERY DE EXCLUSÃO
            $sql = "DELETE FROM {$this->table} WHERE $where";
            $this->setQuery($sql);

            // EXECUTA EXCLUSÃO
            $this->executeLast();

            return $this;
        }

        /**
         * Retorna a quantidade de linhas afetadas na execução do ultimo SQL Query
         *
         */
        public function affectedRows(){
            return $this->countAffectedRows;
        }

        /**
         * Define uma query a ser executada pelo método execute()
         *
         * @param String $query uma query sql a ser executada
         */
        public function setQuery($query){
            if (!$stmt = $this->PDO->prepare($query) ) {
                $this->errors[] = Array(
                    "query" => $query,
                    "info" => $this->PDO->errorInfo()
                );
                return false;
            }else{
                $this->queries[] = $query;
                return $this;
            }
        }

        /**
         * Selecionan uma tabela a ser utilizada pelo objeto, caso seja uma tabela inválida retorna mensagem de erro
         *
         * @param String $table nome da tabela
         */
        public function setTable($table, $alias="table"){
            global $_M_CONFIG;
            if( substr($table, 0, 1) !== "(" ){
                $query = "SELECT 1 FROM Information_schema.tables WHERE table_name = '$table' AND table_schema = '{$_M_CONFIG->mysql['database']}'";
                if( $result = $this->PDO->query( $query ) ){;
                    $resultArray = $result->fetchAll(\PDO::FETCH_ASSOC);
                    if( empty($resultArray) ){
                        trigger_error("A tabela $table não existe na base de dados {$_M_CONFIG->mysql['database']}." , E_USER_ERROR);
                        exit;
                    }else{
                        $this->table = "`$table`";
                    }
                }
            }else{
                $this->table = "$table as `$alias`";
            }
            return $this;
        }

        /**
         * Define quantidade de linhas a serem exibidas por página
         *
         * @param integer $rowsPerPage
         */
        public function setRowsPerPage($rowsPerPage){
            $this->rowsPerPage = $rowsPerPage;
            return $this;
        }

        /**
         * Define a pagina a ser exibida
         *
         * @param integer $page número da pagina a ser exibida
         */
        public function setPage($page){
            $this->page = $page;
            return $this;
        }

        /**
         * Retorna a ultima SQL Query definida
         *
         * @return String
         */
        public function getQuery(){
            if( !empty($this->queries) ){
                return $this->queries[ count($this->queries)-1 ];
            }else{
                trigger_error("Nenhuma query foi definida. (Execute setQuery() antes de executar getQuery()" , E_USER_ERROR);
                exit;
            }
        }

        /**
         * Retorna um array com a lista de todas as SQL Query executadas até o momento
         *
         * @return String
         */
        public function getQueries(){
            return $this->queries;
        }

        /**
         * Retorna query string do select
         *
         * @return string
         */
        public function getSelectQuery($columns, $where, $ifOneCuts=false, $paginate=true){
            // VERIFICA TABLE
            if( !isset($this->table) ){
                trigger_error("Tabela não definida, utilize setTable antes de executar select()" , E_USER_ERROR);
                exit;
            }

            // PAGINAÇÃO
            if($this->page>=1 && $paginate){
                $startRow = $this->rowsPerPage * ($this->page - 1);
                $limit = "LIMIT $startRow, {$this->rowsPerPage}";
            }else{
                $limit = "";
            }

            // MONTA LISTA DE COLUNAS E LISTA DE RELACIONAMENTOS
            if( !is_null($columns) ){
                $strColumns = Array();
                $relationships = Array();
                foreach ($columns as $key => $val) {
                    // SE A CHAVE FOR NUMERICA E O CAMPO NÃO FOR
                    // UM ARRAY SIGNIFICA QUE NÃO FOI DEFINIDO NENHUM
                    // ALIAS PARA A COLUNA
                    if(is_numeric($key) && !is_array($val)){
                        $strColumns[] = "`$val`";
                    }

                    // SE A CHAVE FOR NÃO FOR NUMERICA E O VALOR
                    // NÃO FOR UM ARRAY, SIGNIFICA QUE UM ALIAS
                    // FOI DEFINIDO PARA A COLUNA
                    else if(!is_numeric($key) && !is_array($val)){
                        if(preg_match("/^[\d\W]/i", $val) ){
                            $strColumns[] = "$val as '$key'";
                        }else{
                            $strColumns[] = "`$val` as '$key'";
                        }
                    }
                }
                $strColumns = implode(", ", $strColumns);
            }else{
                $strColumns = "*";
            }

            // BUSCA ARRAY DE RESULTADO
            return "SELECT $strColumns FROM {$this->table} WHERE {$where} $limit";
        }

        /**
         * Retorna um array com informações de todos os erros
         *
         * @return String
         */
        public function getErrors(){
            return $this->errors;
        }

        /**
         * Retorna array com informações sobre a tabela
         *
         * @return Array
         */
        private function getTableInfo(){
            // VERIFICA TABLE
            if( !isset($this->table) ){
                trigger_error("Tabela não definida, utilize setTable antes de executar select()" , E_USER_ERROR);
                exit;
            }

            //
            $table = Util::slug($this->table);
            $query = "SHOW TABLE STATUS LIKE '{$table}'";
            $this->setQuery($query);
            if( !$this->getErrors() ){
                if( $result = $this->PDO->query( $this->getQuery() ) ){
                    $return = $result->fetchAll(\PDO::FETCH_ASSOC);
                    array_pop($this->queries);
                }
            }else{
                trigger_error("Erro ao buscar informações da tabela '{$this->table}': \r\n" . print_r($this->getErrors(), true) , E_USER_ERROR);
                exit;
            }

            //
            return $return[0];
        }

        /**
         * Retorna qual é o valor da próxima chave primária
         *
         * @return integer
         */
        public function getNextId(){
            $ret = $this->getTableInfo();
            return $ret['Auto_increment'];
        }

        /**
         * Cancela uma query especifica do pool de queries
         *
         * @param integer $number número da query a ser cancelada no pool de queries
         */
        public function cancelQuery($number){
            if( isset($this->queries[$number]) ){
                unset($this->queries[$number]);
            }
        }

        /**
         * Cancela a ultima query do pool de queries
         *
         */
        public function cancelLastQuery(){
            array_pop($this->queries);
        }

        /**
         * Cancela todas as queries do pool de queries
         *
         */
        public function cancelAllQueries(){
            $this->queries = "";
        }

        /**
         * Limpa a lista de erros
         *
         */
        public function clearErrors(){
            $this->errors = "";
        }

    }