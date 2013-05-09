<?php
/**
 * Description of imageBuffer
 *
 * @author danielvarela
 */
class imageBuffer {
    
    private $mime   = "";
    private $size   = "";
    private $hash   = "";
    private $gdImg  = "";
    private $strImg = "";
    private $buffer = "buffer/";
    private $redirect = "";
    private $thumbFile = "";
    
    public function __construct($filename) {
        // Carrega a imagem como string
        $this->strImg = file_get_contents($filename);
        
        // Cria md5 da imagem
        $this->hash = md5( $this->strImg );
        
        //
        $this->ext = basename($filename);
        preg_match("/\.(.*?)$/i", $this->ext, $this->ext);
        $this->ext = $this->ext[1];

        // Busca mime type do arquivo
        $finfo = new finfo(FILEINFO_MIME);
        $this->mime = preg_replace("/;.*?$/i", "", $finfo->buffer($this->strImg));
        
        //
        return $this;
    }
    
    /**
     * 
     * @param type $method
     */
    public function thumb($method, $w, $h, $cropTop="center", $cropLeft="center"){   
        // Monta o path da nova imagem
        $newfolder = $this->buffer . "/" . $this->hash;
        $newfile = "{$method}__{$w}x{$h}.{$this->ext}";
        $this->redirect = "$newfolder/$newfile";
        
        // Verifica se existe
        // redireciona para a imagem já criada anteriormente
        // caso a thumb ainda não exista cria a imagem baseada na imagem original
        if( !file_exists( $this->redirect ) ){
            // Cria a pasta para salvar as imagens
            if(!file_exists($newfolder))
                mkdir($newfolder, 0777);
            
            // Carrega a imagem como gd
            $this->gdImg  = imagecreatefromstring($this->strImg);

            // Busca tamanho
            list(
              $this->size['w'], 
              $this->size['h']
            ) = getimagesizefromstring($this->strImg);
            
            // Redimenciona a imagem
            switch( strtolower($method) ){
                case "stretch":
                    $gdThumb = ImageCreateTrueColor( $w, $h );
                    imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
                    $this->gdImg = $gdThumb;  
                    break;

                case "fixed-w":
                    $h = ( int )(( $w/$this->size['w'] ) * $this->size['h'] );
                    $gdThumb = ImageCreateTrueColor( $w, $h );
                    imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
                    $this->gdImg = $gdThumb;                    
                    break;

                case "fixed-h":
                    $w = ( int )(( $h/$this->size['h'] ) * $this->size['w'] );
                    $gdThumb = ImageCreateTrueColor( $w, $h );
                    imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
                    $this->gdImg = $gdThumb;                    
                    break;
                
                case "crop":

                    break;
            }
            
            // Salva a imagem
            switch ($this->mime){
                case "image/png"  : 
                    imagepng( $this->gdImg, $this->redirect ); 
                    break;
                
                case "image/gif"  : 
                    imagegif( $this->gdImg, $this->redirect ); 
                    break;
                
                case "image/jpeg" : 
                case "image/pjpeg": 
                    imagejpeg( $this->gdImg, $this->redirect ); 
                    break;
            }
        }        
        
        //
        return $this;
    }
    
    /**
     * 
     */
    public function show(){
        header("Location: {$this->redirect}");
    }
}

// -----------------------------------------------------------------------------

$image = new imageBuffer("http://blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg");
$image
  ->thumb("crop", 500, 250)
  //->show()
;