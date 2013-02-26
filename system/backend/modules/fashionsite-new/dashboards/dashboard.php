<?php

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
                        
            $ga["pais-estado-cidade"] = json_decode('{"kind":"analytics#gaData","id":"https://www.googleapis.com/analytics/v3/data/ga?ids=ga:34418456&dimensions=ga:country,ga:region,ga:city&metrics=ga:pageViews,ga:newVisits,ga:avgTimeOnSite&start-date=2013-01-16&end-date=2013-02-15","query":{"start-date":"2013-01-16","end-date":"2013-02-15","ids":"ga:34418456","dimensions":"ga:country,ga:region,ga:city","metrics":["ga:pageViews","ga:newVisits","ga:avgTimeOnSite"],"start-index":1,"max-results":1000},"itemsPerPage":1000,"totalResults":6,"selfLink":"https://www.googleapis.com/analytics/v3/data/ga?ids=ga:34418456&dimensions=ga:country,ga:region,ga:city&metrics=ga:pageViews,ga:newVisits,ga:avgTimeOnSite&start-date=2013-01-16&end-date=2013-02-15","profileInfo":{"profileId":"34418456","accountId":"17314531","webPropertyId":"UA-17314531-1","internalWebPropertyId":"35086254","profileName":"www.noblets.com.br","tableId":"ga:34418456"},"containsSampledData":false,"columnHeaders":[{"name":"ga:country","columnType":"DIMENSION","dataType":"STRING"},{"name":"ga:region","columnType":"DIMENSION","dataType":"STRING"},{"name":"ga:city","columnType":"DIMENSION","dataType":"STRING"},{"name":"ga:pageviews","columnType":"METRIC","dataType":"INTEGER"},{"name":"ga:newVisits","columnType":"METRIC","dataType":"INTEGER"},{"name":"ga:avgTimeOnSite","columnType":"METRIC","dataType":"TIME"}],"totalsForAllResults":{"ga:pageviews":"12","ga:newVisits":"8","ga:avgTimeOnSite":"32.2"},"rows":[["(not set)","(not set)","(not set)","1","1","0.0"],["Brazil","Minas Gerais","Para de Minas","1","1","0.0"],["Brazil","Parana","Curitiba","1","1","0.0"],["Brazil","Parana","Maringa","6","3","20.6"],["Latvia","(not set)","Riga","2","1","219.0"],["United States","California","Mountain View","1","1","0.0"]]}', true);
            
            // ACESSOS POR PAIS ESTADO CIDADE
            $paisEstadoCidade = Array();
            foreach ($ga["pais-estado-cidade"]['rows'] as $key=>$row){
                $nextRow = @$ga["pais-estado-cidade"]['rows'][$key+1];
                $prevRow = @$ga["pais-estado-cidade"]['rows'][$key-1];

                $pais   = $row[0];
                $estado = $row[1];
                $cidade = $row[2];
                
                $nextPais   = $nextRow[0];
                $nextEstado = $nextRow[1];
                
                $prevPais   = $prevRow[0];
                $prevEstado = $prevRow[1];
                
                $paisEstadoCidade['pais'][ $pais ]['pageViews'] = (isset(  $paisEstadoCidade['pais'][ $pais ]['pageViews'] ))?  $paisEstadoCidade['pais'][ $pais ]['pageViews'] : 0;
                $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews'] = (isset($paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews']))? $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews'] : 0;
                $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews'] = (isset($paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews']))? $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews'] : 0;
                
                // PAIS
                if( $pais == $nextPais || $pais == $prevPais ){
                    $paisEstadoCidade['pais'][ $pais ]['pageViews'] += $row[3]; 
                    
                    // ESTADO
                    if($estado == $nextEstado || $estado == $prevEstado){
                       $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews'] += $row[3];

                        // CIDADE
                        $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews'] = $row[3]; 
                        
                    }elseif($estado !== $prevEstado){
                        $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews'] = $row[3];
                        $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews'] = $row[3];
                    }

                }elseif($pais !== $prevPais){
                    $paisEstadoCidade['pais'][ $pais ]['pageViews'] = $row[3];
                    $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['pageViews'] = $row[3];
                    $paisEstadoCidade['pais'][ $pais ]['estado'][ $estado ]['cidade'][ $cidade ]['pageViews'] = $row[3];;
                }
            };
            
            return Array(
                "paisEstadoCidade" => $paisEstadoCidade
            );
        }
    }