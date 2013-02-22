$(function(){
    // SELECT and UNSELECT
    $('div.imagesUpload div.inner-holder ul').on("click", "li", function(){
        $(this).toggleClass("selected");
    });

    // IMAGE EDIT DATA
    $('div.imagesUpload div.inner-holder ul').on("dblclick taphold", "li", function(){
        $this = $(this);
        $inputHolder = $this.closest(".input-holder");
        $windowFiles = $this.closest(".window");
        $imagesDataWindow = $windowFiles.find('.edit-image-window')
        $imagesData = $windowFiles.find(".imagesUpload_images-data");

        // Adiciona classe edit para a imagem clicada
        $this.siblings().removeClass("edit");
        $this.addClass("edit");

        // carrega json de data image anterior
        oldJson = $imagesData.val();
        if( oldJson == "[]" || oldJson == "null" ){
          ret = {};
        }else{
          ret = JSON.parse( oldJson );
        }

        // Mostra a tela de edição
        $windowFiles.find("ul").css("width" , "50%");
        $windowFiles.find(".edit-image-window").show();

        // Busca o nome do arquivo
        fileName = $this.find("input").val().replace(/.*\//i , "");

        // Monta formulário
        inputList = JSON.parse( $inputHolder.attr("data-inputs") );
        html  = '';
        html += '<div class="imageDataForm">';
        html +=   '<a class="bt bt_imageDataForm_close">close</a>';
        html +=   '<a class="bt bt_imageDataForm_open">open</a>';
        html +=   '<input type="hidden" name="_m_filename" class="_m_filename" value="'+ fileName +'">';
        $.each(inputList, function(key, val){
          html +=   '<div class="input-holder">';
          html +=     '<label>'+ key +'</label>';
          html +=     val;
          html +=   '</div>';
        });
        html += '</div>';

        // Insere formulário na tela
        $imagesDataWindow.html( html );

        // popula formulário de edição de dados da imagem
        if(typeof ret[ fileName ] !== 'undefined'){
            $.each(ret[ fileName ], function(key, val){
                $imagesDataWindow.find("[name="+key+"]").val( val );
            });
        }
    });

    // SAVE EDIT IMAGE DATA
    $('div.imagesUpload div.inner-holder').on("input paste", "textarea,input", function(){ 
        // doms
        $this = $(this);
        $windowFiles = $this.closest(".window");
        $editImageWindow = $windowFiles.find(".edit-image-window");
        $imagesData = $windowFiles.find(".imagesUpload_images-data");

        // carrega json de data image anterior
        oldJson = $imagesData.val();
        if( oldJson == "[]" || oldJson == "null" ){
            ret = {};
        }else{
            ret = JSON.parse( oldJson );
        }

        // monta array dos dados da imagem atual
        thisImage = {};
        $editImageWindow.find("input,textarea,select").each(function(){
            $this = $(this);
            name = $this.attr("name");
            value = $this.val();
            thisImage[name] = value;
        });

        // adiciona imagem ao array
        imageFileName = thisImage._m_filename;
        delete( thisImage._m_filename );
        ret[ imageFileName ] = thisImage;

        // manda json para o input
        $imagesData.val( JSON.stringify(ret) );

        // prevent default
        return false;
    });


    // CLOSE EDIT IMAGE DATA
    $('div.imagesUpload div.inner-holder').on("click", ".bt_imageDataForm_close", function(){
        $windowFiles = $(this).closest(".window");
        $windowFiles.find("ul").css("width" , "auto").find(".edit").removeClass("edit");
        $windowFiles.find(".edit-image-window").hide();
        $windowFiles.find(".edit-image-window").empty();
        return false;
    });

    // OPEN RAW IMAGE
    $('div.imagesUpload div.inner-holder').on("click", ".bt_imageDataForm_open", function(){
         $this = $(this);
         $holder = $this.closest(".input-holder");
         uploadFolder = $holder.attr("data-upload-folder");
         folder = $holder.attr("data-folder");
         fileName = $this.siblings("._m_filename").val();
         window.open(uploadFolder+"/"+folder+"/"+fileName, fileName);
         return false;
    });

    // TABS
    $('div.imagesUpload nav.small-tabs ul').on("click", ".bt-tab", function(){
        $this = $(this);
        $innerHolder = $this.closest(".input-holder").find(".inner-holder");
        $files = $innerHolder.find("ul");
        $window = $innerHolder.find(".window");
        $actualWindow = $innerHolder.find(".window."+$this.attr('rel'));

        $this.parent().find(".bt-tab").removeClass("active");
        $this.addClass("active");

        $window.hide();
        $actualWindow.show();
        
        return false;
    });

    // TAB UPLOAD
    $('div.imagesUpload nav.small-tabs ul').on("click", ".bt-tab.send", function(){
        $this = $(this);
        $inputHolder = $this.closest(".input-holder");
        $innerHolder = $inputHolder.find(".inner-holder");
        $uploadOfImages = $innerHolder.find(".uploaderOfImages");
        
        // verifica se existe algo na lista de upload ainda não enviado
        // caso axista, impede de que o pupload seja recriado
        recreate = true;
        if( typeof $(".uploaderOfImages").pluploadQueue() == "object" ){
            total = $(".uploaderOfImages").pluploadQueue().total;
            if( total.size !== total.loaded ){
                recreate = false;
            }else{
                recreate = true;
            }
            return false;
        }
        
        // recria o pupload caso não exista nada 
        // na fila de upload ainda não enviado
        if( recreate ){
            $uploadOfImages.pluploadQueue({
                runtimes : 'flash,silverlight,browserplus,html5',
                url : '?action=type-ajax&ajax=upload&type=imagesUpload&folder=' + $inputHolder.attr("data-folder"),
                max_file_size : '1024mb',
                chunk_size : '1mb',
                unique_names : true,
                filters : [ {title : "Image files" , extensions : "jpg,gif,png"} ],
                flash_swf_url : 'types/imagesUpload/plupload/js/plupload.flash.swf',
                silverlight_xap_url : 'types/imagesUpload/plupload/js/plupload.silverlight.xap',
                init : {
                    UploadProgress: function(up, file) {
                        if(up.total.percent == 100){
                            $inputHolder.find(".bt-tab.list").click();
                            $inputHolder.find(".bt-reload").click();
                        }
                    }
                }
            });
        }
    });

    // DELETE bt-del-file
    $('div.imagesUpload').on("click", ".bt-del-file", function(){
        $this = $(this);
        $inputHolder = $this.closest(".input-holder");
        $inputHolder.find(".window.files").find(".selected").remove();
        return false;
    });

    // RELOAD bt-reload
    $('div.imagesUpload').on("click", ".bt-reload", function(){
        $this = $(this);
        $inputHolder = $this.closest(".input-holder");
        $innerHolder = $inputHolder.find(".inner-holder");
        $windowListUl = $innerHolder.find(".window.files ul");
        $.post(
            '?action=type-ajax&ajax=list&type=imagesUpload&folder=' + $inputHolder.attr("data-folder"),
            function(data){
                html = '';
                $.each(data.files.order, function(key, val){
                    html += '<li data-file="'+ data.folder +"/"+ val +'"><input value="'+ data.folder +"/"+ val +'" name="'+ $inputHolder.attr("data-name") +'[]" type="hidden"><img src="image-buffer/130x95/'+ data.folder +"/"+ val +'"></li>';
                });
                $windowListUl.html( html ).sortable();
            }, "json"
        );
        return false;
    });

    // SELECT ALL bt-select-all
    $('div.imagesUpload').on("click", ".bt-select-all", function(){
        $(this).closest(".input-holder").find(".inner-holder ul li").addClass("selected");
        return false;
    });

    // SELECT NONE bt-select-none
    $('div.imagesUpload').on("click", ".bt-select-none", function(){
        $(this).closest(".input-holder").find(".inner-holder ul li").removeClass("selected");
        return false;
    });

    // SELECT INVERSE bt-select-inverse
    $('div.imagesUpload').on("click", ".bt-select-inverse", function(){
        $(this).closest(".input-holder").find(".inner-holder ul li").toggleClass("selected");
        return false;
    });

    // REMOVER INPUTS DE TODOS OS PL-UPLOADER PRA E FECHAR A JANELA DE EDIÇÃO 
    // DE DADOS DA IMAGEM NÃO DAR PROBLEMAS AO INSERIR NO BANCO DE DADOS
    $("form").submit(function(){
        $(".uploaderOfImages").find("input").remove();
        $('div.imagesUpload div.inner-holder').find(".bt_imageDataForm_close").click();
    });

});