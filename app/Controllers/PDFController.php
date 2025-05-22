<?php

use Slim\Psr7\Response;
use \TCPDF as TCPDF;

class PDFController
{
    public function GetPDF($request, $response, $args)
    {
        $rutaImagen = 'Utilidades/logo.jpg';

        $pdf = new TCPDF();
        $pdf->AddPage();

        $pdf->Image($rutaImagen, ($pdf->GetPageWidth() - 100) / 2, 10, 100, 0, 'JPEG');

        $pdfContent = $pdf->Output('', 'S');

        $response = new Response();
        $response = $response->withHeader('Content-Type', 'application/pdf');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="logo.pdf"');
        $response->getBody()->write($pdfContent);

        return $response;
    }
}