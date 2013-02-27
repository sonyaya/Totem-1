<?php

$html = '
<h1>mPDF</h1>
<h2>Basic HTML Example</h2
teste
';

    use vendor\mpdf\pdf;

    $mpdf = new PDF(); 
    $mpdf->WriteHTML($html);
    $mpdf->Output();

    exit;

    return Array(
        "error"     => true,
        "message"   => "Essa é uma mensagem de erro criada pelo programador do formulário dummy."
    );