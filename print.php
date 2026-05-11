<?php  


require_once '_config.php'; 


$temp = array('simulator');


if(in_array($_GET['filename'],$temp))
    require_once '_include-v2.php';
else
    require_once '_include.php';

ob_start();

$OPT_FUNCTION = '';

// ====================== SET DEFAULT FILE  
$fileName = $_GET['filename']; 
if(empty($fileName)) die;  
$ext = pathinfo($fileName, PATHINFO_EXTENSION);
$fileName = (empty($ext)) ? $fileName .'.php' : $fileName; 
require_once DOC_ROOT.'print/'.$fileName;   
// ====================== END OF SET DEFAULT FILE  


require_once DOC_ROOT.'print/_global.php';

if (!isset($arrID))
$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();
$title = array(); 
foreach($arrID as $id){ 
      
    $rsData = $obj->searchData($obj->tableName.'.pkey',$id); 
    
    $class->validateAllowedStatus($rsData);
    
    $dataset = array();
    $dataset['rs'] = $rsData;
       
    $pdf->dataset = $dataset;
    
    // reset custom settings
    $pdf->customSettings = (isset($PRINT_SETTINGS))  ? $PRINT_SETTINGS : null;
    
    
    $opt = (isset($OPT_FUNCTION) && !empty($OPT_FUNCTION)) ? $OPT_FUNCTION($dataset) : ''; 
    addNewPDFPage($pdf,$obj, $generateReportContent, $opt); 

    $obj->afterPrintTransaction($rsData);
    array_push($title,$dataset['rs'][0]['code'] ); 
}

$title = implode(', ', $title);

$pdf->SetTitle($title);  
$pdf->Output( substr($title,0,$class->printSetting['fileNameLength']) .'.pdf', 'I');

?>