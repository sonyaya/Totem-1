$(function(){
    $("body")
        
        // Adicionar nova TAG
        .on(
            "keyup", 
            "div.jsonArray input.add",
            function(e){
                $this = $(this);
                switch( e.which ){

                    // TECLA ENTER
                    case 13:  
                        val = $this.val();
                        $json = $this.siblings("input.json");
                        
                        // carrega json de data image anterior
                        oldJson = $json.val();
                        if( oldJson == "null" || oldJson == "" ){
                          ret = [];
                        }else{
                          ret = JSON.parse( oldJson );
                        }
                        
                        // Adiciona no ul e no json
                        if( $.inArray(val, ret) < 0 ){
                            ret.push( val );
                            $json.val( JSON.stringify(ret) );
                            $this.siblings("ul").append( "<li rel='"+ val +"'>" + val + " <a href='#'>[x]</a></li>" );
                            $this.val("");
                        }
                        break;
                    
                    // TECLA ESC
                    case 27:
                        $this.val("");
                        break;
                }
            }
        )
            
        //
        .on(
            "click",
            "div.jsonArray ul li a",
            function(){
                $this = $(this);
                $li = $this.parent();
                $json = $this.closest(".input-holder").find("input.json");
                    
                // carrega json de data image anterior
                oldJson = $json.val();
                if( oldJson == "null" || oldJson == "" ){
                  ret = [];
                }else{
                  ret = JSON.parse( oldJson );
                }                
                
                // remove do array
                rmPos = $.inArray($li.attr('rel'), ret);
                ret.splice(rmPos, 1);              
                $json.val( JSON.stringify(ret) );
                
                // remove o li
                $li.remove();
                return false;
            }
        )
    ;
});
