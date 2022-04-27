<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Mpdf\Mpdf;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;


class HomeController extends Controller
{
    public function index () {
        try {
            $content = view('supervisor.templates.ftel')->render();

            $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array('0','0','0','0'));
            $html2pdf->setDefaultFont('arial');
            $html2pdf->AddFont('dejavusans');
            $html2pdf->writeHTML($content);
            $html2pdf->output('example01.pdf');
        } catch (Html2PdfException $e) {
            $html2pdf->clean();

            $formatter = new ExceptionFormatter($e);
            echo $formatter->getHtmlMessage();
        }
    }
}
