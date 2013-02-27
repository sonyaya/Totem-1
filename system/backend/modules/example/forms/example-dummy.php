<?php

    $html = "<pre>" . print_r($_POST, true) . "</pre>";

    use vendor\mpdf\pdf;

    $mpdf = new PDF(); 
    $mpdf->WriteHTML($html);
    $pdfFile = $mpdf->Output('', 'S');
    
    die( '<object data="data:application/pdf;base64,'. base64_encode($pdfFile) .'" type="application/pdf" width="100%" height="100%"></object>' );
    
//    return Array(
//        "error"     => true,
//        "message"   => "Essa é uma mensagem de erro criada pelo programador do formulário dummy."
//    );