manyToMany = Object();

manyToMany.add = function($this){
    // BUSCA DOM
    $holder = $this.closest('div.input-holder');
    $leftList  = $holder.find("div.manyToMany.left div.selections ul");
    $rightList = $holder.find("div.manyToMany.right div.selections ul");
    $leftList.find(".active").each(function(){
        $this = $(this);
        $this
            .removeClass("over")
            .removeClass("active")
            .clone()
            .append( "<input type='hidden' name='"+$this.attr('data-name')+"' value='"+$this.attr('data-value')+"'>" )
            .appendTo( $rightList )
        ;
    });   
}

manyToMany.remove = function($this){
    // BUSCA DOM
    $holder = $this.closest('div.input-holder');
    $rightList = $holder.find("div.manyToMany.right div.selections ul");
    $rightList.find("li.active").remove();
}

$(function(){
    $('form')

        // AO PRECIONAR UMA TECLA NO finder
        .on('keyup', 
            'input.manyToMany-finder', 
            function(e){
                // BUSCA DOM
                $this = $(this);
                $list = $this.parent().find("div.selections ul");

                switch( e.which ){

                    // TECLA ENTER
                    case 13:                   
                        break;
                    
                    // TECLA ESC
                    case 27:
                        break;
                        
                    // TECLAS PRA ESQUERDA
                    case 39:
                        $over = $list.find(".over").click();
                        manyToMany.add($(this));  
                        $over.addClass("over");
                        break;

                    // TECLA PRA CIMA
                    case 38:
                        $over = $list.find('.over');
                        $over.removeClass('over');
                        $prev = $over.prev()
                        if( typeof($prev[0]) !== "undefined" ){
                            $prev.addClass('over');
                        }else{
                            $list.find('li:last').addClass('over');
                        }
                        break;

                    // TECLA PRA BAIXO
                    case 40:
                        $over = $list.find('.over');
                        $over.removeClass('over');
                        $next = $over.next()
                        if( typeof($next[0]) !== "undefined" ){
                            $next.addClass('over');
                        }else{
                            $list.find('li:first').addClass('over');
                        }
                        break;

                    // QUALQUER TECLA
                    default:
                        // ENVIAR POR POST
                        postData =  
                            "value="         + $this.val()                     +
                            "&middle-table=" + $this.attr('data-middle-table') +
                            "&middle-fk="    + $this.attr('data-middle-fk')    +
                            "&middle-pk="    + $this.attr('data-middle-pk')    +
                            "&right-table="  + $this.attr('data-right-table')  + 
                            "&right-fk="     + $this.attr('data-right-fk')     +
                            "&right-label="  + $this.attr('data-right-label') 
                        ;

                        // VERIFICA SE O AJAX ANTERIOR AINDA E
                        // STA RODANDO E CANCELA SUA EXECUÇÃO
                        if( typeof(xhlr_manyToMany) !== 'undefined' ){
                            xhlr_manyToMany.abort();
                        }

                        // EXECUTA O TYPE-AJAX
                        xhlr_manyToMany = $.post(
                            "?action=type-ajax&type=manyToMany",
                            postData,
                            function(data){
                                html = '';
                                $.each(data, function(key, val){
                                    html += "<li class='li_"+val.value+"' data-value='"+val.value+"' data-name='"+$this.attr('data-name')+"[]'>";
                                    html +=     val.label;
                                    html += "</li>";
                                });
                                $list.html( html );
                            }, 
                            "json"
                        );
                    break;
                }

                // PREVINE A EXECUÇÃO DE MAIS DE UM KEYUP
                return false;
            }
        )

        // AO CLICAR NO BOTÃO ADD
        .on('click',
            '.add',
            function(){
                manyToMany.add($(this), "active");
            }
        )

        // AO CLICAR NO BOTÃO REMOVE
        .on('click',
            '.remove',
            function(){
                manyToMany.remove($(this));
            }
        )
            
        // AO CLICAR NO BOTÃO ADD
        .on('click',
            '.new',
            function(){
                var w = 640;
                newWindow = window.open(
                    "?action=view-insert-window-form&form=" + $(this).attr('rel'), 
                    'Insert', 
                    'toolbar=no,'
                    +'scrollbars=yes,'
                    +'location=no,'
                    +'resizable=yes,'
                    +'width='+w+','
                    +'height=0'
                );
                    
                newWindow.onload = function(){
                    $popBody = newWindow.$('body');
                    var h = $popBody.height() + 50;
                    newWindow.resizeTo( w, h );
                }
                
                return false;
            }
        )

        // AO finder RECEBER FOCO
        .on('focusin', 
            'input.manyToMany-finder', 
            function(){
                $(this).keyup();
            }
        )

        // AO CLICAR EM UM DOS ITENS DA LISTAGEM DA ESQUERDA
        .on('click',
            'div.manyToMany.left div.selections ul li',
            function(){
                // BUSCA DOM
                $this = $(this);
                $holder = $this.closest('div.input-holder');
                $leftList  = $holder.find("div.manyToMany.left div.selections ul");
                $rightList = $holder.find("div.manyToMany.right div.selections ul");
                
                // SÓ DEIXA SELECIONAR SE AINDA 
                // NÃO ESTIVER NA LISTA DA DIREITA
                value = $this.attr("data-value");
                $sameInRightList = $rightList.find(".li_" + value);
                
                if( typeof($sameInRightList[0]) == 'undefined' ){
                    $this.toggleClass("active");                    
                }
            }
        )

        // AO CLICAR EM UM DOS ITENS DA LISTAGEM DA DIREITA
        .on('click',
            'div.manyToMany.right div.selections ul li',
            function(){
                // BUSCA DOM
                $this = $(this);
                $this.toggleClass("active");
            }
        )

        // AO COLOCAR O MOUSE SOBRE UM DOS ITENS DA LISTAGEM
        .on('mouseover',
            '.selections ul li',
            function(){
                // BUSCA OS ELEMENTOS A SEREM UTILIZADOS
                $this   = $(this);
                $ul = $this.closest('ul');

                // SELECIONA O CAMPO CLICADO
                $ul.find('.over').removeClass('over');
                $this.addClass('over');
            }
        )
    ;
});