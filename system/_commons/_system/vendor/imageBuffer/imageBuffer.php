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
    public function thumb($method, $w, $h){   
        // Monta o path da nova imagem
        $args = func_get_args();
        $newfolder = $this->buffer . "/" . $this->hash;
        $newfile   = implode($args, "_") . ".{$this->ext}";
        $newfile   = str_replace(array("%", "#"), array("P", "C"), $newfile);// tira os %
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
                case "stretch": $this->stretch($w, $h); break;
                case "fixed-w": $this->fixedW($w, $h) ; break;
                case "fixed-h": $this->fixedH($w, $h) ; break;
                case "inner"  : $this->inner($w, $h)  ; break;
                case "crop"   : $this->crop($w, $h)   ; break;
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
    
    
    /**
     * 
     * @param type $t
     * @param type $h
     * @return type
     */
    private function calculeTop($t, $h){
        if(!is_numeric($t)){
            switch ($t){
                case "top":
                    $t = 0;
                    break;

                case "center":
                    $t = ($h/2) - ($h/2);
                    break;

                case "bottom":
                    $t = $h - $h;
                    break;

                default:
                    $t = trim($t);
                    if( preg_match("/[0-9]*?\%/i", $t) ){
                        $t = (int)preg_replace("/\D/i", "", $t);
                        $t = ($h * $t) / 100;
                    }else{
                        $t = 0;
                    }
                    break;
            }
        }else{
            $t *= -1;
        }
        
        return $t;
    }
    
    /**
     * 
     * @param type $l
     * @param type $w
     * @return type
     */
    private function calculeLeft($l, $w){
        if(!is_numeric($l)){
            switch ($l){
                case "left":
                    $l = 0;
                    break;

                case "center":
                    $l = ($w/2) - ($w/2);
                    break;
                    break;

                case "right":
                    $l = $w - $w;
                    break;

                default:
                    $l = trim($l);
                    if( preg_match("/[0-9]*?\%/i", $l) ){
                        $l = (int)preg_replace("/\D/i", "", $l);
                        $l = ($w * $l) / 100;
                    }else{
                        $l = 0;
                    }
                    break;
            }
        }else{
            $l *= -1;
        }
        
        return $l;
    }
    
    /**
     * 
     * @param type $w
     * @param type $h
     */
    private function stretch($w, $h){
        $gdThumb = ImageCreateTrueColor( $w, $h );
        imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
        $this->gdImg = $gdThumb; 
    }
    
    /**
     * 
     * @param type $w
     * @param type $h
     */
    private function fixedW($w, $h){
        $h = ( int )(( $w/$this->size['w'] ) * $this->size['h'] );
        $gdThumb = ImageCreateTrueColor( $w, $h );
        imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
        $this->gdImg = $gdThumb;    
    }
    
    /**
     * 
     * @param type $w
     * @param type $h
     */
    private function fixedH($w, $h){
        $w = ( int )(( $h/$this->size['h'] ) * $this->size['w'] );
        $gdThumb = ImageCreateTrueColor( $w, $h );
        imagecopyresized($gdThumb, $this->gdImg, 0, 0, 0, 0, $w, $h, $this->size['w'], $this->size['h']);
        $this->gdImg = $gdThumb; 
    }
    
    /**
     * 
     * @param type $w
     * @param type $h
     */
    private function inner($w, $h){
        // Busca argumentos extras para o inner
        $args = func_get_args();
        $color = (isset($args[3]))? $args[3] : "#000000";

        // Cria a imagem 
        $gdThumb = ImageCreateTrueColor( $w, $h );

        // Converte cor RGB HTML para Decimal
        $r = hexdec( substr($color, -6, 2) );
        $g = hexdec( substr($color, -4, 2) );
        $b = hexdec( substr($color, -2, 2) );

        // Adiciona cor de fundo a imagem
        $color = imagecolorallocate($gdThumb, $r, $g, $b);
        imagefill($gdThumb, 0, 0, $color);

        // Calcula tamanho da imagem
        $crop_w = ( int )( ( $h/$this->size['h'] ) * $this->size['w'] );
        $crop_h = ( int )( ( $w/$this->size['w'] ) * $this->size['h'] );

        if($crop_w > $w ){
            $crop_w = $w;
            $crop_h = ( int )( ( $w/$this->size['w'] ) * $this->size['h'] );
        }else{
            $crop_w = ( int )( ( $h/$this->size['h'] ) * $this->size['w'] );
            $crop_h = $h;
        }

        // Calcula centro da imagem
        $crop_l = ($w/2) - ($crop_w/2);
        $crop_t = ($h/2) - ($crop_h/2);

        // Miniatura com o tamanho do crop
        imagecopyresized($gdThumb, $this->gdImg, $crop_l, $crop_t, 0, 0, $crop_w, $crop_h, $this->size['w'], $this->size['h']);

        //
        $this->gdImg = $gdThumb; 
    }
    
    private function crop($w, $h){
        // Busca argumentos extras para o crop
        $args = func_get_args();
        $crop_l = (isset($args[3]))? $args[3] : "center";
        $crop_t = (isset($args[4]))? $args[4] : "center";

        // Cria a imagem 
        $gdThumb = ImageCreateTrueColor( $w, $h );

        // Calcula tamanho da imagem
        $crop_w = ( int )( ( $h/$this->size['h'] ) * $this->size['w'] );
        $crop_h = ( int )( ( $w/$this->size['w'] ) * $this->size['h'] );

        if($crop_w < $w ){
            $crop_w = $w;
            $crop_h = ( int )( ( $w/$this->size['w'] ) * $this->size['h'] );
        }else{
            $crop_w = ( int )( ( $h/$this->size['h'] ) * $this->size['w'] );
            $crop_h = $h;
        }

        // Calcula left do crop
        $crop_l = $this->calculeLeft($crop_l, $crop_w);

        // Calcula top do crop
        $crop_t = $this->calculeLeft($crop_t, $crop_h);

        // Miniatura com o tamanho do crop
        imagecopyresized($gdThumb, $this->gdImg, $crop_l, $crop_t, 0, 0, $crop_w, $crop_h, $this->size['w'], $this->size['h']);

        //
        $this->gdImg = $gdThumb;
    }
}

// -----------------------------------------------------------------------------


//$image = new imageBuffer("http://blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg");
//$image->thumb("fixed-w", 100, 100)->show();
//$image->thumb("fixed-h", 100, 100)->show();


//$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
//$image->thumb("stretch", 100, 100)->show();

//$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
//$image->thumb("inner", 500, 500, "#330000")->show();

$image = new imageBuffer("http://blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg");
//$image->thumb("crop", 500, 500)->show();
$image->thumb("crop", 500, 500, "center", "top");

voce transformou o crop em um método agora
vc precisa fazer com que ele receba todos 
os parametros necessários

//$image->thumb("crop", 500, 500, "center", "center");
//$image->thumb("crop", 500, 500, "center", "bottom");

//$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
//$image->thumb("crop", 500, 500, "left", "center");
//$image->thumb("crop", 500, 500, "center", "center");
//$image->thumb("crop", 500, 500, "right", "center");

//$image->thumb("crop", 500, 500, 100, 10);

//$image->thumb("crop", 500, 100, "10%", "10%")->show();