<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';
require_once '../../assets/vendor/autoload.php';  
    
$rsItem = $item->searchData();

$inputFileName = 'kd_image.xlsx';

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx'); 
$reader->setReadDataOnly(true); 
 
$spreadsheet = $reader->load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
 
$worksheet = $spreadsheet->getActiveSheet();
// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

$arrExcel = array();
for($i=2;$i<=$highestRow;$i++){
    $sku = $worksheet->getCellByColumnAndRow(1,$i)->getValue();
    $url = $worksheet->getCellByColumnAndRow(2,$i)->getValue();
    $arrExcel[$sku]  = $url;
}

foreach($rsItem as $itemRow){
 
    try{

            if(!$class->oDbCon->startTrans(true))
                throw new Exception($class->errorMsg[100]);

            $sql = 'delete from item_image where refkey = ' . $class->oDbCon->paramString($itemRow['pkey']);
            $class->oDbCon->execute($sql);

            if(!empty($arrExcel[$itemRow['code']])){  
                $arrImageLink = explode(',',$arrExcel[$itemRow['code']]);
                
                foreach($arrImageLink as $url){
                    $url = trim($url);
                    $fileName = basename($url);
                    
                    // grab image
                    $destPath = $class->defaultDocUploadPath.'item/'.$itemRow['pkey'].'/';
                    if (!is_dir($destPath))  mkdir($destPath, 0755, true);
                    
                    $class->grabImage($url,$destPath.$fileName);
                    
                    $sql = 'insert into item_image (refkey,file) values ('.$class->oDbCon->paramString($itemRow['pkey']).','.$class->oDbCon->paramString($fileName).') ';
                    $class->oDbCon->execute($sql);
                }
            }
            
            $class->oDbCon->endTrans(); 

    }catch(Exception $e){
        echo $e->getMessage();
        $class->oDbCon->rollback(); 
    }	


}


echo 'done';
die; 
?>