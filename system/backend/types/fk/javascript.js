$(function(){
    $('form')

        // AO CLICAR NO form input.fk-finder
        .on('click', 
            '.fk input.fk-finder', 
            function(){
                $(this).keyup();
            }
        )        

        // AO PRECIONAR UMA TECLA NO form input.fk-finder
        .on('keyup', 
            '.fk input.fk-finder', 
            function(e){
                // BUSCA DOM
                $this   = $(this);
                $holder = $this.closest('.inner-holder');
                $list   = $holder.find('.fk-list');
                $finder = $holder.find('.fk-finder');
                $value  = $holder.find('.fk-value');

                switch( e.which ){

                    // TECLA ENTER OU ESC
                    case 13:
                    case 27:
                        $actual = $list.find(".active");
                        $finder.val( $actual.attr("data-label") );
                        $value.val( $actual.attr("data-value") );
                        break;

                    // TECLA PRA CIMA
                    case 38:
                        $actual = $list.find(".active");
                        $prev = $actual.prev();
                        $list.show();

                        if( typeof($prev[0]) !== 'undefined' ){
                          $actual.removeClass("active");
                          $prev.addClass("active");
                        }
                        
                        if( $.trim($list.text()) == "" ){
                            $this.keyup();
                        }

                        break;

                    // TECLA PRA BAIXO
                    case 40:
                        $actual = $list.find(".active");
                        $next = $actual.next();
                        $list.show();
                        
                        if( typeof($next[0]) !== 'undefined' ){
                          $actual.removeClass("active");
                          $next.addClass("active");
                        }

                        if( $.trim($list.text()) == "" ){
                            $this.keyup();
                        }

                        break;

                    // QUALQUER TECLA
                    default:
                        // VARIAVIS UTEIS
                        column = $holder.attr("data-column");
                        table  = $holder.attr("data-table");
                        label  = $holder.attr("data-label");

                        // ENVIAR POR POST
                        postData =  
                            "value="   + $this.val() +
                            "&label="  + label + 
                            "&column=" + column + 
                            "&table="  + table 
                        ;

                        // VERIFICA SE O AJAX ANTERIOR AINDA E
                        // STA RODANDO E CANCELA SUA EXECUÇÃO
                        if( typeof(xhlr_fk) !== 'undefined' ){
                            xhlr_fk.abort();
                        }

                        // EXECUTA O TYPE-AJAX
                        xhlr_fk = $.post(
                            "?action=type-ajax&type=fk",
                            postData,
                            function(data){
                                html = '';
                                $.each(data, function(key, val){
                                    html += "<li data-value='"+val.value+"' data-label='"+val.label+"'>"+val.label+"</li>";
                                });

                                $list
                                    .html( html )
                                    .stop( true, true )
                                    .fadeIn()
                                ;

                                $list.find("li:first").addClass("active");
                            }, 
                            "json"
                        );
                        break;
                }

                // PREVINE A EXECUÇÃO DE MAIS DE UM KEYUP
                return false;
            }
        )

        // AO form input.fk-finder PERDER O FOCO
        .on('focusout', 
            '.fk input.fk-finder', 
            function(){
                $this   = $(this);
                $holder = $this.closest('.inner-holder');
                $list   = $holder.find('.fk-list');
                $actual = $list.find(".active");

                $list.stop(true, true).fadeOut();
                $finder.val( $actual.attr("data-label") );
                $value.val( $actual.attr("data-value") );
            }
        )

        // AO form input.fk-finder RECEBER O FOCO
        .on('focusin', 
            '.fk input.fk-finder', 
            function(){
                $this   = $(this);
                $holder = $this.closest('.inner-holder');
                $list   = $holder.find('.fk-list');
                $holder.keyup();
            }
        )

        // AO CLICAR EM UM DOS ITENS DA LISTAGEM
        .on('click',
            '.fk ul.fk-list li',
            function(){
                // BUSCA OS ELEMENTOS A SEREM UTILIZADOS
                $this   = $(this);
                $holder = $this.closest('.inner-holder');
                $list   = $holder.find('.fk-list');
                $finder = $holder.find('.fk-finder');
                $value  = $holder.find('.fk-value');
                $actual = $list.find(".over");

                // COLOCA VALORES NOS CAMPOS
                $finder.val( $actual.attr("data-label") );
                $value.val( $actual.attr("data-value") );
            }
        )


        // AO COLOCAR O MOUSE SOBRE UM DOS ITENS DA LISTAGEM
        .on('mouseover',
            '.fk ul.fk-list li',
            function(){
                // BUSCA OS ELEMENTOS A SEREM UTILIZADOS
                $this   = $(this);
                $holder = $this.closest('.inner-holder');
                $list   = $holder.find('.fk-list');

                // SELECIONA O CAMPO CLICADO
                $list.find('.over').removeClass('over');
                $this.addClass('over');
            }
        )
           
        // AO CLICAR NO BOTÃO NEW
        .on('click',
            '.fk .combo .new',
            function(){
                if( typeof layout == 'object' ){
                    if( typeof layout.popup == 'function' ){
                        layout.popup("?action=view-insert-window-form&form=" + $(this).attr('rel'), "Inserindo");
                    }else{
                         alert("O layout que você esta utilizando não possui o método popup. \r\nTenha como exemplo o arquivo do template default 'script/default.js'.");
                    }
                }else{
                    alert("O layout que você esta utilizando não possui um objeto javascript chamado layout. \r\nTenha como exemplo o arquivo do template default 'script/default.js'.");
                }
                return false;
            }
        )
    ;
});