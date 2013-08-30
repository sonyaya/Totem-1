<?php
    error_reporting(0);

    use vendor\mpdf\pdf;

    $html = "<h1>Example</h1><pre>" . print_r($_POST, true) . "</pre>";
    


//    // RETORNO EM HTML
//    return Array(
//        "title" => "Retorno em HTML",
//        "mime" => "text/html",
//        "file" => base64_encode($html)
//    );
    
    // RETORNO EM PDF
    $mpdf = new PDF(); 
    $mpdf->WriteHTML($html);
    $pdfFile = $mpdf->Output('', 'S');
    return Array(
        "title" => "Retorno em PDF",
        "mime" => "application/pdf",
        "file" =>  base64_encode($pdfFile)
    );
    
//    // CASO DE ERRO
//    return Array(
//        "error"     => true,
//        "message"   => "Essa é uma mensagem de erro criada pelo programador do formulário dummy."
//    );