<?php

require_once '../../_config.php'; 
require_once '_include.php';
require_once 'function-v2.php';
 

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';

$employee = new Employee();
$warehouse = new Warehouse();

$obj = new Employee();  
validateSecurity($obj, 'employee', $spreadsheet);

$arrTable = array( 
            'employee', 
            'employee_category',  
);

function addData($obj,$benchmark, $arrParam,$criteria = ''){
    
    $obj->oDbCon->startTrans(); 

    $arrParam['createdBy'] = 1;  
    $arrParam['_isImport_'] = true;

    // cek kalo blm ad data, add
    
    // akan masalah utk subkategori yg punya nama sama
    $rs = $obj->searchData($obj->tableName.'.'.$benchmark['field'], $benchmark['value'],true,$criteria);
    
    if (empty($rs)){
         $result = $obj->addData($arrParam);   
    }else{
         $arrParam['hidId'] = $rs[0]['pkey'];
         $arrParam['hidModifiedOn'] = $rs[0]['modifiedon'];
         $arrParam['modifiedBy'] = 1;
        
         // kalo kode kosong, pake kode lama
        if(empty($arrParam['code']))
        $arrParam['code'] =  $rs[0]['code'] ;
        
         $result = $obj->editData($arrParam);  
         //$result[0]['pkey'] = $rs[0]['pkey']; 
         
    }

    if (!$result[0]['valid']) { 
        $obj->oDbCon->rollback();
        echo '<li class="text-red-cardinal"><strong>'.$benchmark['value'].'</strong>, '.$result[0]['message'].'</li>';  
    }else{ 
        $obj->oDbCon->endTrans();
       // echo '<li class="text-black-jet"><strong>'.$benchmark['value'].'</strong>, '.$result[0]['message'].'</li>';  
    }
 
    return (isset($result[0]['data'])) ? $result[0]['data'] : array();
 
} 
function updateCity($location){ 

    $objCity = new City(); 
    $objCityCategory = new CityCategory();

    // cari default city
    $rsCity = $objCity->searchData('','',true,'',' order by pkey asc limit 1'); 
    $citykey = $rsCity[0]['pkey'];

     if (empty($location)) 
         return $citykey;
         
    
    $arrLocation = explode(',',$location);   

    if(!empty(trim($arrLocation[1]))){
        $cityCategoryName = trim($arrLocation[1]);

        // city category                                                 
        $rsCityCategory = $objCityCategory->searchData($objCityCategory->tableName.'.name', $cityCategoryName);
        if (empty($rsCityCategory)){
            $benchmark =  array('field' => 'name' , 'value' => $cityCategoryName);
            $arrParam = array(); 
            $arrParam['selStatus'] = 1; 
            $arrParam['code'] = 'xxxx';
            $arrParam['name'] = $cityCategoryName; 
            $result = addData($objCityCategory,$benchmark, $arrParam); 
             
            $citycategorykey = $result['pkey'];
        }else{
            $citycategorykey = $rsCityCategory[0]['pkey'];
        }

    }else{
         // cari default kategori
         $rsCityCategory = $objCityCategory->searchData('','',true,'',' order by pkey asc limit 1'); 
         $citycategorykey = $rsCityCategory[0]['pkey'];
    }


    $cityName = trim($arrLocation[0]);

    // city        
    $rsCity = $objCity->searchData($objCity->tableName.'.name', $cityName); 
    if (empty($rsCity)){
        $benchmark =  array('field' => 'name' , 'value' => $cityName);
        $arrParam = array(); 
        $arrParam['selStatus'] = 1; 
        $arrParam['code'] = 'xxxx';
        $arrParam['cityname'] = $cityName; 
        $arrParam['hidCategoryKey'] = $citycategorykey; 
        $result = addData($objCity,$benchmark, $arrParam);
        $citykey = $result['pkey'];
    } else {
        $citykey = $rsCity[0]['pkey']; 
    }

    return $citykey;
 
}
 
?> 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<title>Upload - Employee</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating Employee...</h1>
        <ul class="progress-list"> 
            <?php 
                $obj = new Employee(); 
            
                if (isset($_POST) && !empty($_POST['chkReset'])) resetTable($obj,$arrTable);
                $rsWarehouse = $warehouse->searchData();
                $rsWarehouse = array_column($rsWarehouse,'pkey','name');
            
                  for ($row = 2; $row <= $highestRow; ++$row) {
                        $obj = new Employee(); 
                        $objCategory = new EmployeeCategory();
                        
                        $code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $warehouseName = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
                        $category = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $isdriver = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $isdriver = (strtolower($isdriver) == 'ya' || $isdriver == 1) ? 1 : 0;
                        $issales = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $issales = (strtolower($issales) == 'ya'|| $issales == 1) ? 1 : 0;
                        $pob = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                        $dob = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); 
                        $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob);
                        $dob = $dob->format('d / m / Y');  
                      
                        $livingaddress = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                        $address = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                        $city = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                        $mobile = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                        $drivinglicense = $worksheet->getCellByColumnAndRow(13, $row)->getValue(); 
                        $drivinglicenseexpdate = $worksheet->getCellByColumnAndRow(14, $row)->getValue(); 
                        $drivinglicenseexpdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($drivinglicenseexpdate);
                        $drivinglicenseexpdate = $drivinglicenseexpdate->format('d / m / Y');  
                      
                        $idnumber = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                        $emergencyname = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                        $emergencyphone = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                       
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
                       
                        // sementara di
                        $pob = 0; //updateCity($pob); 
                        $citykey = 0; //updateCity($city); 

                    
                        if (!empty($code)){
                            $benchmark = array('field' => 'code' , 'value' => $code);
                            $overwriteCode = true;
                        }else{
                            $benchmark = array('field' => 'name' , 'value' => $name);
                            $overwriteCode = false;
                        }
                    
                        $arrParam = array(); 
                        $arrParam['selStatus'] = 2; 
                        $arrParam['overwriteCode'] = $overwriteCode;
                        $arrParam['code'] = (!isset($code)) ? '' : $code; // menghindari null
                        $arrParam['selCategory'] = $categorykey;
                        $arrParam['selWarehouse'] = $rsWarehouse[$warehouseName];
                        $arrParam['memberName'] = $name;
                        $arrParam['livingAddress1'] = $livingaddress;
                        $arrParam['memberAddress1'] = $address;
                        $arrParam['hidCityKey'] = $citykey;
                        $arrParam['memberZipCode'] = '';
                        $arrParam['memberPhone'] = $mobile;
                        $arrParam['memberPhone'] = '';
                        $arrParam['memberMobile'] = $mobile;
                        $arrParam['memberEmail'] = '';
                        $arrParam['chkIsDriver'] = $isdriver;
                        $arrParam['chkIsSales'] = $issales;
                        $arrParam['hidPlaceOfBirthKey'] = $pob;
                        $arrParam['dateOfBirth'] = $dob;
                        $arrParam['drivingLicense'] = $drivinglicense;
                        $arrParam['drivingLicenseExpDate'] = $drivinglicenseexpdate;
                        $arrParam['emergencyName'] = $emergencyname;
                        $arrParam['emergencyPhone'] = $emergencyphone; 
                        $arrParam['IDNumber'] = $idnumber; 
                      
                        //echo $name;
                        //echo $address; 
                        addData($obj,$benchmark, $arrParam);
                    
                }
                echo '<li class="text-blue-munsell">Inserting data to <strong>'.$arrTable[0].'</strong>. done.</li>';  
  
            ?>
        </ul>
    </div>
</div>     
    
</body> 
</html> 
