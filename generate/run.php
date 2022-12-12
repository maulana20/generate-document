<?php

require 'vendor/autoload.php';

$docName = basename($argv[1], ".docx");
$final = $argv[2];

try {
    shell_exec("lowriter --headless --convert-to pdf /app/$docName.docx --outdir .");
} catch (Exception $e) {
    echo "failed doc2pdf : " . $e->getMessage();
    exit();
}

try {
    $im = new Imagick();
    $im->readimage('document.pdf');
    $pages = $im->getNumberImages();
    $im->clear();
    $im->destroy();
    for ($iteration = 0; $iteration < $pages; $iteration++) {
        $im = new Imagick();
        $im->setResolution(200, 200);
        $im->readimage("$docName.pdf[$iteration]");
        $im->setBackgroundColor('white');
        $im->setImageFormat('jpg');
        $im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
        $im->setImageCompression(imagick::COMPRESSION_JPEG);
        $im->setImageCompressionQuality(50);
        $im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        $im->writeImage("$docName-{$iteration}.jpg");
        $im->clear();
        $im->destroy();
    }
} catch (Exception $e) {
    echo "failed pdf2image : " . $e->getMessage();
    exit();
}

shell_exec("rm $docName.pdf $docName.docx");

try {
    $mpdf = new \Mpdf\Mpdf();
    $header = "<table width='100%' border='1'>
        <tr>
            <td width='40%' align='center'>Company</td>
            <td width='60%' align='center'>Document Title</td>
        </tr>
    </table>";
    $mpdf->defaultheaderline = 0;
    $mpdf->SetHeader($header);
    $template = "<body style='background-image: url(%s);
        background-position: top left;
        background-repeat: no-repeat;
        background-image-resize: 4;
        background-image-resolution: from-image;'>";
    for ($iteration = 0; $iteration < $pages; $iteration++) {
        $mpdf->addPage();
        $mpdf->WriteHTML(sprintf($template, "$docName-{$iteration}.jpg"));
        shell_exec("rm $docName-{$iteration}.jpg");
    }
    $mpdf->Output($final, 'F');
} catch (Exception $e) {
    echo "failed image2pdf : " . $e->getMessage();
    exit();
}
