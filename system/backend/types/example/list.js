$(function(){
    $("body").on("keypress", "table td.example a", function(){
        $this = $(this);
        $td   = $this.parent('td');

        // VERIFICA SE JÁ NÃO ESTA MODIFICANDO
        if( typeof($this.find('input')[0]) == 'undefined' ){
            // TEXT e HTML ORIGINAL
            oldHtml = $td.html();
            oldText = $.trim($td.text());

            // MOSTRA INPUT
            $this.html("<input class='azimute' value='"+oldText+"'>");

            // BUSCA O DOM DO INPUT E DEFINE ELE COM O FOCO
            $input = $this.find('input');
            $input.focus();

            // CANCELA CASO O INPUT PERCA O FOCO
            $input.focusout(function(){
                $td.html( oldHtml );
            })

            // GRAVA SOMENTE ESTE CAMPO CASO SEJA PRECIONADO ENTER
            // CASO SEJA PRECIONADO TAB OU ESC IGNORA A ALTERAÇÃO
            $input.keypress(function(event){
                pkValue = $td.closest('tr')[0].dataset.pkValue;
                column  = $td[0].dataset.column;
                value   = $input.val();
                switch(event.keyCode){
                    case 13:
                        $.post(
                            "?action=save-form&path=" + layout.uri("path"),                 
                            "_M_ACTION=update:"+pkValue+"&"+column+"="+value,
                            function(data){
                                // coloca o resultado retornado na coluna
                                $this.html( data.result[column] );

                                // mensagem de erro
                                if( data.error ){
                                    alert(data.message);
                                }
                            },
                            "json"
                        );
                        break;
                    case 9:
                    case 27:
                        $td.html( oldHtml );
                        break
                }//switch

            });//input keypress

        }//if

    });//body .example click

});//ready