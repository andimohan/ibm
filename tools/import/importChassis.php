<?php

require_once '../../_config.php'; 
require_once '../../_include.php';  
require_once '../../assets/vendor/autoload.php';  
require_once 'function.php';   

$arrTable = array( 
            'chassis', 
            'chassis_category', 
);  
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<title>Upload - Chassis</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating Chassis...</h1>
        <ul class="progress-list"> 
            <?php 
                $obj = new Chassis(); 
                $objCategory = new ChassisCategory();
            
                if (isset($_POST) && !empty($_POST['chkReset'])) resetTable($obj,$arrTable);
                    
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    
                    $category = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
                    // cek kategori sudah ada atau blm ...  
                    $rsCategory = $objCategory->searchData($objCategory->tableName.'.name', $category);
                    if (empty($rsCategory)){
                        $benchmark = array('field' => 'name' , 'value' => $category);  
                        $arrParam = array(); 
                        $arrParam['selStatus'] = 1; 
                        $arrParam['code'] = 'xxxx';
                        $arrParam['name'] = $category; 
                        $result = addData($objCategory,$benchmark, $arrParam);
                        $categorykey = $result[0]['pkey'];
                    }else{
                        $categorykey = $rsCategory[0]['pkey']; 
                    }        

                    
                    $kir = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); 
                    $kirDate = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); 
                    
                    if (!empty($kirDate)){ 
                        $kirDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($kirDate);
                        $kirDate = $kirDate->format('d / m / Y'); 
                    }else{
                        $kirDate = '00 / 00 / 0000';
                    }
                    
                    $sumbu = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $color = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $description = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    
                    
                    if (!empty($code)){
                        $benchmark = array('field' => 'code' , 'value' => $code);
                        $overwriteCode = true;
                    }else{
                        $benchmark = array('field' => 'chassisnumber' , 'value' => $name);
                        $overwriteCode = false;
                    }
                    
                      
                    $arrParam = array(); 
                    $arrParam['selStatus'] = 1;
                    $arrParam['overwriteCode'] = $overwriteCode;
                    $arrParam['code'] = $code;
                    $arrParam['chassisNumber'] = $name;
                    $arrParam['kir'] = $kir;
                    $arrParam['kirExpiryDate'] = $kirDate;
                    $arrParam['sumbu'] = $sumbu; 
                    $arrParam['hidCategoryKey'] = $categorykey;
                    $arrParam['color'] = $color;
                    $arrParam['trDesc'] = $description;    

                    addData($obj,$benchmark, $arrParam); 
                     
                }
                        
 
                echo '<li class="text-blue-munsell">Inserting data to <strong>'.$arrTable[0].'</strong>. done.</li>';  
  
            ?>
        </ul>
    </div>
</div>     
    
</body> 
</html> 
