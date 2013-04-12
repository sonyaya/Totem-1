<?php
    class imagesUpload{

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            //
            $fromFolder = "{$parameters['folder']}/temp/{$_SESSION['user']['login']}";
            $toFolder   = "{$parameters['folder']}/{$pKey['value']}";
            $ret = Array();

            // salvar informações das imagens
            $ret['data'] = json_decode( $thisData["images-data"], true );
            unset( $thisData["images-data"] );
            
            // linka arquivos para a pasta onde serão salvos os arquivos
            foreach($thisData as $key=>$val){
                $ret['order'][] = str_replace($fromFolder, $toFolder, $val);
            }
            
            //
            $thisData = json_encode($ret);
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function beforeUpdate(&$thisData, $thisColumn, &$allData, $parameters,  $pKey){
            $ret['data'] = json_decode( $thisData["images-data"], true );
            unset( $thisData["images-data"] );
            $ret['order'] = $thisData;
            $thisData = json_encode($ret);
        }

        /**
         * 
         * @global array $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $toTypeLayout
         * @param type $pKey
         */
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){
            global $_M_CONFIG;
            
            if( !isset($pKey['value']) || empty($pKey['value']) ){
                $folder = "{$parameters['folder']}/temp/{$_SESSION['user']['login']}";
            }else{
                $folder = "{$parameters['folder']}/{$pKey['value']}";
            }
            $toTypeLayout = Array(
                "folder" => $folder,
                "files" => json_decode($thisData, true),
                "upload-folder" => preg_replace("/\.\.\//", "", $_M_CONFIG->system['upload-path'], 1) 
            );
        }

        /**
         * 
         * @global type $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function afterInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            global $_M_CONFIG;
            $fromFolder = "{$_M_CONFIG->system['upload-path']}/{$parameters['folder']}/temp/{$_SESSION['user']['login']}";
            $toFolder   = "{$_M_CONFIG->system['upload-path']}/{$parameters['folder']}/{$pKey['value']}";
            mkdir($toFolder, 0777, true);           
            
            // SERIA MELHOR ASSIM, MAS POR ALGUM MOTIVO
            // TENEBROSO ISSO NÃO FUNCIONA NO WINDOWS
            // QUEM SABE UM DIA...
            #if(file_exists($fromFolder)){
            #    rename($fromFolder, $toFolder);
            #}

            $dir = scandir($fromFolder);
            $fileList = Array();
            foreach ($dir as $key => $val) {
                if( (int)strpos($val, ".") > 0 ){
                    rename("$fromFolder/$val", "$toFolder/$val");
                }
            }
        }

        /**
         * 
         * @global type $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         * @param type $key
         */
        public function afterUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            global $_M_CONFIG;
            $folder = "{$parameters['folder']}/{$pKey['value']}";

            // DELETA ARQUIVOS REMOVIDOS DA LISTA

            // Busca arquivos na pesta
            if( !file_exists($destinyFolder = "{$_M_CONFIG->system['upload-path']}/$folder") ){
                mkdir($destinyFolder);
            }
            
            $dir = scandir( $destinyFolder );
            $fileList = Array();
            foreach ($dir as $key => $val) {
                if( (int)strpos($val, ".") > 0 ){
                    $fileList[] = "$folder/$val";
                }
            }

            // Cria lista de arquivos a serem removidos
            $fileListDiff = array_diff($fileList, $thisData);

            // Percorre lista de arquivos a 
            // serem removidos, deletando um por um
            array_walk(
                $fileListDiff, 
                function($item, $key) use($_M_CONFIG){
                    unlink("{$_M_CONFIG->system['upload-path']}/$item");
                }
            );
        }
        
        /**
         * 
         * @global type $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function afterDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            global $_M_CONFIG;
            $dir = "{$_M_CONFIG->system['upload-path']}/{$parameters['folder']}/{$pKey['value']}";
            if(is_dir($dir) ){
                foreach (scandir($dir) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    unlink($dir.DIRECTORY_SEPARATOR.$item);
                }
                rmdir($dir);
            }
        }
        
        /**
         * 
         * @global type $_M_CONFIG
         */
        public function ajax(){
            global $_M_CONFIG;
            $folder = $_M_CONFIG->system['upload-path'] . "/" . $_GET['folder'];

            switch ( @$_GET['ajax'] ) {
                // 
                // UPLOAD
                // 
                case 'upload':{
                    /**
                     * upload.php
                     *
                     * Copyright 2009, Moxiecode Systems AB
                     * Released under GPL License.
                     *
                     * License: http://www.plupload.com/license
                     * Contributing: http://www.plupload.com/contributing
                     */

                    // HTTP headers for no cache etc
                    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                    header("Cache-Control: no-store, no-cache, must-revalidate");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");

                    // Settings
                    //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
                    $targetDir = $folder;

                    $cleanupTargetDir = true; // Remove old files
                    $maxFileAge = 5 * 3600; // Temp file age in seconds

                    // 5 minutes execution time
                    @set_time_limit(5 * 60);

                    // Get parameters
                    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
                    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
                    $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

                    // Clean the fileName for security reasons
                    $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

                    // Make sure the fileName is unique but only if chunking is disabled
                    if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
                        $ext = strrpos($fileName, '.');
                        $fileName_a = substr($fileName, 0, $ext);
                        $fileName_b = substr($fileName, $ext);

                        $count = 1;
                        while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                            $count++;

                        $fileName = $fileName_a . '_' . $count . $fileName_b;
                    }

                    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

                    // Create target dir
                    if (!file_exists($targetDir))
                        @mkdir($targetDir, 0777, true);

                    // Remove old temp files    
                    if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
                        while (($file = readdir($dir)) !== false) {
                            $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                            // Remove temp file if it is older than the max age and is not the current file
                            if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                                @unlink($tmpfilePath);
                            }
                        }

                        closedir($dir);
                    } else {
                        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                    }
                        

                    // Look for the content type header
                    if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
                        $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

                    if (isset($_SERVER["CONTENT_TYPE"]))
                        $contentType = $_SERVER["CONTENT_TYPE"];

                    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
                    if (strpos($contentType, "multipart") !== false) {
                        if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                            // Open temp file
                            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                            if ($out) {
                                // Read binary input stream and append it to temp file
                                $in = fopen($_FILES['file']['tmp_name'], "rb");

                                if ($in) {
                                    while ($buff = fread($in, 4096))
                                        fwrite($out, $buff);
                                } else
                                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                                fclose($in);
                                fclose($out);
                                @unlink($_FILES['file']['tmp_name']);
                            } else
                                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                        } else
                            die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                    } else {
                        // Open temp file
                        $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                        if ($out) {
                            // Read binary input stream and append it to temp file
                            $in = fopen("php://input", "rb");

                            if ($in) {
                                while ($buff = fread($in, 4096))
                                    fwrite($out, $buff);
                            } else
                                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                            fclose($in);
                            fclose($out);
                        } else
                            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                    }

                    // Check if file has been uploaded
                    if (!$chunks || $chunk == $chunks - 1) {
                        // Strip the temp .part suffix off 
                        rename("{$filePath}.part", $filePath);
                    }

                    // Return JSON-RPC response
                    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
                }#case

                // 
                // LISTA ARQUIVOS NO INSERT
                // 
                case 'list':{
                    $fileList = Array();
                    
                    if(file_exists($folder) ){
                        $dir = scandir($folder);
                        foreach ($dir as $key => $val) {
                            if( (int)strpos($val, ".") > 0 ){
                                $fileList[] = $val;
                            }
                        }
                    }else{
                        $fileList = Array();
                    }

                    echo json_encode(Array(
                        "folder" => $_GET['folder'],
                        "files"  => Array(
                            "order" => $fileList
                        )
                    ));
                }#case
            }
        }
    }