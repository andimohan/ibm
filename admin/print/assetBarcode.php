<?php 

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'footer' => '',
		 'marginFooter' => 6
);

includeClass('Asset.class.php');
 
$asset = createObjAndAddToCol( new Asset());  
$obj = $asset;

$generateReportContent = function ($dataset){   
global $pdf;
	
$rs = $dataset['rs']; 

// define barcode style
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);    

$pdf->write1DBarcode($rs[0]['code'], 'C128', '', '', '', 18, 0.4, $style, 'N');

return $html;
}
?>