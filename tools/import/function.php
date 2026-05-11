<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  

<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '2024M');

if (!isset($_FILES) || empty($_FILES['fileToUpload']['tmp_name']))
   die ('<div class="text-red-cardinal" style="padding:1em">Missing File !</div>');
 
 
if (!IS_DEVELOPMENT) { 
    // cek token
    if (!isset($_POST) || empty($_POST['token']))
        die ('<div class="text-red-cardinal" style="padding:1em">Missing Token !</div>');

    //validasi token 
    require_once '../../assets/vendor/autoload.php';     
    $g = new \Google\Authenticator\GoogleAuthenticator();

    $userkey = base64_decode($_SESSION[$class->loginAdminSession]['id']); 
    $rsLogin = $employee->getDataRowById($userkey);
    if (!$g->checkCode($rsLogin[0]['secretAuth'], $_POST['token']))  
        die ('<div class="text-red-cardinal" style="padding:1em">Invalid Token !</div>'); 

}

$inputFileType = 'Xlsx';   
$inputFileName = $_FILES['fileToUpload']['tmp_name']; 
 
$ext = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));

if ($ext <> 'xlsx') die($class->errorMsg[602]);

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
$reader->setReadDataOnly(true); 
 
$spreadsheet = $reader->load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();

// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
 
$filePath = DOC_ROOT.'tools/import/'.DOMAIN_NAME.'/'. basename($_SERVER['PHP_SELF']);  

if (is_file($filePath)){ 
    include $filePath; 
    die;
}


function resetTable($obj,$arrTable){
    
    $obj->oDbCon->startTrans();
    
    for ($i=0;$i<count($arrTable);$i++){
        
        //cek ad nilai default gk
        $sql = 'select * from '.$arrTable[$i].' limit 1';
        $rs =  $obj->oDbCon->doQuery($sql);  
        $criteria = ''; 
        if (isset($rs[0]['systemVariable']))
            $criteria = ' and systemVariable <> 1';
              
        echo '<li class="text-red-cardinal">Deleting <strong>'.$arrTable[$i].'</strong>.</li>'; 
        $sql = 'delete from ' . $arrTable[$i] .' where 1=1 ' . $criteria;
        $obj->oDbCon->execute($sql);     

        echo '<li class="text-green-avocado">Updating key for <strong>'.$arrTable[$i].'</strong>.</li>'; 
        $sql = 'update _nextkey set nextkey = 1 where table_name = \''.$arrTable[$i].'\'';
        $obj->oDbCon->execute($sql);    

        echo '<li class="text-green-avocado">Updating code for <strong>'.$arrTable[$i].'</strong>.</li>'; 
        
        // ambil kode terakhir
        $sql = 'SHOW COLUMNS FROM `'.$arrTable[$i].'` LIKE \'code\'';
        $rs =  $obj->oDbCon->doQuery($sql);     
        if(!empty($rs)){
            $sql = 'select code,digit from _code where code = \''.$arrTable[$i].'\'';
            $rs =  $obj->oDbCon->doQuery($sql);     
            $length = $rs[0]['digit'];

            $sql = 'select code from '.$arrTable[$i].' order by pkey desc limit 1';
            $rs =  $obj->oDbCon->doQuery($sql);  
            if (empty($rs)){
                $codeCtr = 1;
            }else{
                 $codeCtr = (int) substr($rs[0]['code'], $length * -1 ); 
                 $codeCtr++;
            } 

            $sql = 'update _user_code, _code set _user_code.counter = '.$codeCtr.' where _user_code.codekey = _code.pkey and _code.code = \''.$arrTable[$i].'\'';
            $obj->oDbCon->execute($sql);     
        }
        
        
    }

    $obj->oDbCon->endTrans();
} 
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
function isExistCategoryPath($categoryList, $objCategory){  
  
  $parentKey = 0;
  foreach( $categoryList as $categoryName ){
    $categoryName = trim($categoryName);
    
    $rsCategory = $objCategory->searchData('', '', true, ' AND  '.$objCategory->tableName.'.name = '.$objCategory->oDbCon->paramString($categoryName).' AND '.$objCategory->tableName.'.parentkey = '.$objCategory->oDbCon->paramString($parentKey));
    
    if( empty($rsCategory) ) 
      return 0; 
    else 
      $parentKey = $rsCategory[0]['pkey']; 
    
  }
  
  return $parentKey;
} 
function validateSecurity($obj,$moduleName,$spreadsheet){ 
    $security = new Security();
    
    if(!$security->isAdminLogin($obj->securityObject,11,true)); // kalo gk boleh add, kick !

    $keywords = $spreadsheet->getProperties()->getKeywords();
    if (strtolower(IMPORT_TEMPLATE[$moduleName]) <> strtolower($keywords))
        die('<div style="padding:1em" class="text-red-cardinal">'.$obj->errorMsg[602].'</div>');   


    if (isset($_POST) && !empty($_POST['chkReset'])){ 
        if(!$security->isAdminLogin($obj->securityObject,12,false)) 
         die('<div style="padding:1em"  class="text-red-cardinal">'.$obj->errorMsg[251].'</div>');    
    }


}
?>