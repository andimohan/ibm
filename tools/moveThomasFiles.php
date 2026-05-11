<?php
die("uncomment for execute");

require_once '../_config.php'; 
include_once '../_include-v2.php';

$yearBefore = 2024;

$class->oDbCon->startTrans();


$arrFile = array();
array_push($arrFile, array('fileFolder' => 'trucking-service-order-invoice/', 
                          'destination' => 'invoice/',
                          'fileNameField' => 'file'));
array_push($arrFile, array('fileFolder' => 'trucking-service-order-invoice-tax/',
                          'destination' => 'tax/',
                          'fileNameField' => 'filetax'));

//copyOldFiles($arrFile);
removeOldFolders($arrFile,$yearBefore);

function removeOldFolders($arrFile, $yearBefore, $simulation=true){ 
 global $class;
    

    $sql = 'select 
                  pkey,code,year(trdate) as year, month(trdate) as month,
                  file,filetax
            from 
               trucking_service_order_invoice_header
            where  
               year(trdate) < '.$yearBefore.' 
            order by pkey asc';


    $rs = $class->oDbCon->doQuery($sql);

    foreach($arrFile as $fileRow){

        $baseFolder = DEFAULT_DOC_UPLOAD_PATH.'backupthomas/'.$fileRow['destination'];
        if(!is_dir($baseFolder)) mkdir($baseFolder,0755,true);

        $fileFolder = $fileRow['fileFolder'];
        $fieldName = $fileRow['fileNameField'];

        foreach ($rs as $row){
            $pkey = $row['pkey'];
            $fileName =  $row[$fieldName];

            $folderPath=  DEFAULT_DOC_UPLOAD_PATH.$fileFolder.$pkey;
            deleteFolder($folderPath,$simulation);
            
        }

    }


    die;
}

function copyOldFiles($arrFile){
    global $class;
    
    // dibawah untuk pindahin file
    $sql = 'select 
                  pkey,code,year(trdate) as year, month(trdate) as month,
                  file,filetax
            from 
               trucking_service_order_invoice_header
            where  
                statuskey in (1,2,3)
            order by pkey asc';

    $rs = $class->oDbCon->doQuery($sql);

    // cek dulu file mana aj yg gk ada...

    foreach($arrFile as $fileRow){

        $baseFolder = DEFAULT_DOC_UPLOAD_PATH.'backupthomas/'.$fileRow['destination'];
        if(!is_dir($baseFolder)) mkdir($baseFolder,0755,true);

        $fileFolder = $fileRow['fileFolder'];
        $fieldName = $fileRow['fileNameField'];

        foreach ($rs as $row){
            $pkey = $row['pkey'];
            $year = $row['year'];
            $month = $row['month'];
            $code = str_replace('/','-',$row['code']); 
            $fileName =  $row[$fieldName];

            // kalo di database memang kosong
            if(empty($fileName)) continue;


            // buat folder per tahun / per bulan / kode job
            $folderPath = $baseFolder.$year.'/'.$month.'/'.$code;

            $sourceFile =  DEFAULT_DOC_UPLOAD_PATH.$fileFolder.$pkey.'/'.$fileName;
            $destinationFile =  $folderPath.'/'.$fileName;

             if (!file_exists($sourceFile)){  
                 echo 'File Not Exist : '.$row['code'].'<br>'; 
                 continue;
             }

            if(!is_dir($folderPath)) 
                mkdir($folderPath,0755,true);


            if (copy($sourceFile, $destinationFile)) {
    //            echo "File copied successfully.";
            } else {
                echo "Failed to copy file. " +$code+ "<br>";
            }


    //        echo $sourceFile.'<br>';
    //        echo $destinationFile.'<br>';
    //        die;
    }
 
}

die ('done');
}




function deleteFolder($folderPath,$simulation=true) {
    if (!is_dir($folderPath)) {
        return false;
    }

    // Scan all files and folders inside
    $items = scandir($folderPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;

        if (is_dir($itemPath)) {
            deleteFolder($itemPath); // Recursive call for subdirectory
        } else {
            echo 'file: '.$itemPath.'<br>';
            if($simulation){ 
                // echo 'file: '.$itemPath.'<br>';
            }else{
                
                unlink($itemPath); // Delete file
            }
        }
    }

    // Delete the main directory
         echo  'folder: '.$folderPath.'<br>';
    if($simulation){ 
 
    }else{ 
           return rmdir($folderPath);   
    }
}
?>