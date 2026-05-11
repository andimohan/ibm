<?php   

include_once '../../_config.php';  
include_once '../../_include-v2.php';
 
$arrTable = array();
array_push($arrTable, array('tableName' => 'trucking_service_order_header', 'tableFile' => 'trucking_service_order_file', 'uploadFolder' => 'trucking-service-order/', 'cancelStatus' => 7, 'storage' => 'S3'));
array_push($arrTable, array('tableName' => 'trucking_cost_cash_out_header', 'tableFile' => 'trucking_cost_cash_out_file', 'uploadFolder' => 'trucking-cost-cash-out/', 'cancelStatus' => 5 ));
array_push($arrTable, array('tableName' => 'cash_out_header', 'uploadFolder' => 'cash-out/' )); // sementara baru 1 field file

$totalSize = 0;

$s3Client = $class->initS3Client();

foreach($arrTable as $tableRow){
    
    $tableName = $tableRow['tableName'];
    $tableFile = (isset($tableRow['tableFile'])) ? $tableRow['tableFile'] : '';
    $uploadFolder = $tableRow['uploadFolder'];
    $cancelStatus = (isset($tableRow['cancelStatus'])) ? $tableRow['cancelStatus'] : 4;
    $fieldName = (isset($tableRow['fieldName'])) ? $tableRow['fieldName'] : 'file';
    $storage =  (isset($tableRow['storage'])) ? $tableRow['storage'] : '';
    
    if(!empty($tableFile)){
        $sql = 'select '.$tableName.'.pkey ,  '.$tableFile.'.file 
            from  '.$tableFile.', '.$tableName.'
            where '.$tableFile.'.refkey = '.$tableName.'.pkey
            and '.$tableName.'.statuskey = '. $cancelStatus;  
    }else{
        $sql = 'select '.$tableName.'.pkey ,  '.$tableName.'.'.$fieldName.'  
            from  '.$tableName.' 
            where '.$tableName.'.'.$fieldName.' <> \'\' and not '.$tableName.'.'.$fieldName.' is null 
                    and '.$tableName.'.statuskey = '. $cancelStatus;
    }
         
    
    $rs = $class->oDbCon->doQuery($sql);

//    echo count($rs).'<br>';

    $arrFile = array();
    foreach($rs as $row){
       $filePath = $uploadFolder.$row['pkey'].'/'.$row['file'];
       array_push($arrFile,$filePath); 
       //echo $filePath.'<br>' ;
        
        
        if($storage == 'S3'){
            
            try { 
                            $result = $s3Client->headObject([
                                'Bucket' => STORAGE['bucket'],
                                'Key'    => DOMAIN_NAME.'/'.$filePath,
                            ]);
                        
                            $fileSize = $result['ContentLength'];
                            $totalSize += $fileSize;
            } catch (\Aws\Exception\AwsException $e) {
                echo "AWS SDK Error: " . $e->getAwsErrorMessage().'<br>';
            } catch (\Exception $e) {
                echo "General Error: " . $e->getMessage().'<br>';
            }
                       
        }else{
                $file = DEFAULT_DOC_UPLOAD_PATH.$filePath;
            
                if (file_exists($file)) { 
                    $totalSize += filesize($file);
                } else {
                    echo "$file - File not found<br>";
                }
        }
    }

}


    
    echo $totalSize.'<br>';
    echo $class->convertSize($totalSize,'mb').'<br>';

 
?>