<?php

ini_set('max_execution_time', '30000'); //300 seconds = 5 minutes i

require_once '../../_config.php';
require_once '../../_include-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Service.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ChartOfAccount.class.php'; 


$OBJ = new Service(SERVICE);
$chartOfAccount = new ChartOfAccount(); 

$MODULE_NAME = 'Services';

$inputFileType = 'Xlsx';
$inputFileName = 'coamapping.xlsx';
$ext           = strtolower(pathinfo('coamapping.xlsx', PATHINFO_EXTENSION));


if ($ext <> 'xlsx')
   die($class->errorMsg[602]);

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$reader->setReadDataOnly(true);

$spreadsheet = $reader->load($inputFileName);

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$worksheet   = $spreadsheet->getActiveSheet();

$worksheet = $spreadsheet->getActiveSheet();
// Get the highest row and column numbers referenced in the worksheet
$highestRow         = $worksheet->getHighestRow(); // e.g. 10
$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5


function resetTable($obj, $arrTable)
{

   $obj->oDbCon->startTrans();

   for ($i = 0; $i < count($arrTable); $i++) {

      //cek ad nilai default gk
      $sql      = 'select * from ' . $arrTable[$i] . ' limit 1';
      $rs       = $obj->oDbCon->doQuery($sql);
      $criteria = '';
      if (isset($rs[0]['systemVariable']))
         $criteria = ' and systemVariable <> 1';

      echo '<li class="text-red-cardinal">Deleting <strong>' . $arrTable[$i] . '</strong>.</li>';
      $sql = 'delete from ' . $arrTable[$i] . ' where 1=1 ' . $criteria;
      $obj->oDbCon->execute($sql);

      echo '<li class="text-green-avocado">Updating key for <strong>' . $arrTable[$i] . '</strong>.</li>';
      $sql = 'update _nextkey set nextkey = 1 where table_name = \'' . $arrTable[$i] . '\'';
      $obj->oDbCon->execute($sql);

      echo '<li class="text-green-avocado">Updating code for <strong>' . $arrTable[$i] . '</strong>.</li>';

      // ambil kode terakhir
      $sql = 'SHOW COLUMNS FROM `' . $arrTable[$i] . '` LIKE \'code\'';
      $rs  = $obj->oDbCon->doQuery($sql);
      if (!empty($rs)) {
         $sql    = 'select code,digit from _code where code = \'' . $arrTable[$i] . '\'';
         $rs     = $obj->oDbCon->doQuery($sql);
         $length = $rs[0]['digit'];

         $sql = 'select code from ' . $arrTable[$i] . ' order by pkey desc limit 1';
         $rs  = $obj->oDbCon->doQuery($sql);
         if (empty($rs)) {
            $codeCtr = 1;
         } else {
            $codeCtr = (int) substr($rs[0]['code'], $length * -1);
            $codeCtr++;
         }

         $sql = 'update _user_code, _code set _user_code.counter = ' . $codeCtr . ' where _user_code.codekey = _code.pkey and _code.code = \'' . $arrTable[$i] . '\'';
         $obj->oDbCon->execute($sql);
      }


   }

   $obj->oDbCon->endTrans();
}

function validateSecurity($obj, $moduleName, $spreadsheet)
{

   $security = new Security();

   if (!$security->isAdminLogin($obj->securityObject, 11, true))
      ; // kalo gk boleh add, kick !

   $keywords = $spreadsheet->getProperties()->getKeywords();



   if (strtolower(IMPORT_TEMPLATE[$moduleName]) <> strtolower($keywords))
      die('<div style="padding:1em" class="text-red-cardinal">' . $obj->errorMsg[602] . '</div>');


   if (isset($_POST) && !empty($_POST['chkReset'])) {
      if (!$security->isAdminLogin($obj->securityObject, 12, false))
         die('<div style="padding:1em"  class="text-red-cardinal">' . $obj->errorMsg[251] . '</div>');
   }


}


function convertValue($value, $structureRow)
{
   // test kalo hasil convert gk ketemu, misal nama gudnag ke kode gudang, 
   // tetep balikin nilai awal.
   // MUNGKIN nanti bisa jd otomatis user bisa masukin nama gudang atau kode gudang (tp gk tau di field lain bisa gk)

   global $class;
   $value = trim($value);

   $fieldName = $structureRow['field'];

   // convert 
   if (isset($structureRow['convert']) && !empty($structureRow['convert'])) {


      $convert = $structureRow['convert'];
      $obj     = $convert['obj'];

      $fromColumn = (isset($convert['columnfrom']) && !empty($convert['columnfrom'])) ? $convert['columnfrom'] : 'name';
      $fromColumn = $obj->tableName . '.' . $fromColumn;

      $toColumn = (isset($convert['columnto']) && !empty($convert['columnto'])) ? $convert['columnto'] : 'code';

      $rs = $obj->searchData($fromColumn, $value);

      $value = (!empty($rs)) ? $rs[0][$toColumn] : $value;

   }

   return $value;
}

function removeUnusedParameter($arrData)
{

   foreach ($arrData as $key => $row) {
      foreach ($row as $subkey => $rowField) {
         // biar OBJ ny gk kebawa, OBJ yg ad di dalam replace
         if (isset($row[$subkey]['data']['replace']))
            unset($arrData[$key][$subkey]['data']['replace']);
      }
   }

   return $arrData;

}

function importData($DATA_STRUCTURE, $importSet)
{

   global $class;


   $arrData = array();

   if ($importSet['datatype'] == 'excel') {
      $startRow  = 2;
      $endRow    = $importSet['highestRow'] + 1;
      $worksheet = $importSet['worksheet'];
   } else {
      $startRow = 0;
      $endRow   = count($importSet['dataset']);
      $dataset  = $importSet['dataset'];
   }

   for ($row = $startRow; $row < $endRow; ++$row) {
      $arrTemp = array();
      $isEmpty = true;

      foreach ($DATA_STRUCTURE as $colIndex => $dataStructureRow) {

         if (!isset($dataStructureRow['field']) || empty($dataStructureRow['field']))
            continue;

         $fieldName = $dataStructureRow['field'];

         if ($importSet['datatype'] == 'excel') {

            $tempValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();

            if (isset($dataStructureRow['format'])) {
               switch ($dataStructureRow['format']) {
                  case 'date':
                     $tempValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tempValue);
                     $tempValue = $tempValue->getTimestamp();
               }
            }


            $value = convertValue($tempValue, $dataStructureRow);
         } else {

            // kalo jenis nya detail  
            if (isset($dataStructureRow['detail']) && !empty($dataStructureRow['detail'])) {
               $value = importData($dataStructureRow['detail'], array('datatype' => 'datastructure', 'dataset' => $dataset[$row][$fieldName]));

            } else {
               $tempValue = (isset($dataset[$row][$fieldName])) ? $dataset[$row][$fieldName] : '';
               $value     = convertValue($tempValue, $dataStructureRow);
            }
         }
          
//                       $class->setLog($dataStructureRow['replace']['field'],true);

         // kalo ad ref, utk v3
         if (isset($dataStructureRow['replace'])) {

            $newObj = $dataStructureRow['replace']['obj'];

            // sementara selalu nyari dr 'name' dulu
            $replaceField = (isset($dataStructureRow['replace']['field'])) ? $dataStructureRow['replace']['field'] : 'name';
             

            $rsReplace    = $newObj->searchDataRow(
               array($newObj->tableName . '.code'),
               'and ' . $newObj->tableName . '.' . $replaceField . '  = ' . $newObj->oDbCon->paramString($value)
            );

             
//            	$newObj->setLog( 'and '.$newObj->tableName.'.'.$replaceField.'  = '. $newObj->oDbCon->paramString($value),true);
//				$newObj->setLog($rsReplace,true);
            $value = $rsReplace[0]['code'];
         }


         if (!empty($value))
            $isEmpty = false;

         // agar &  tetep ke save, gk bisa pake urlencode, spasi jd error
         // kalo perlu lebih dr satu, pake array
         //$arrTemp[$fieldName]['value']  =  (!is_array($value)) ? str_replace('&','+',$value) : $value; 
         //$arrTemp[$fieldName]['value']  =  (!is_array($value)) ? html_entity_decode($value) : $value; 

         $arrTemp[$fieldName]['value'] = $value;
         $arrTemp[$fieldName]['data']  = $dataStructureRow;

         if (isset($arrTemp[$fieldName]['data']['convert']))
            $arrTemp[$fieldName]['data']['convert'] = '';

      }


      // cek jika semua field kosong, lewatin...
      // kemungkin juga dikosongin karena gk mau diupdate.
      // tp bagaimana jadiny kalo utk delete isi field ? tetep harus di post harusnya...
      //if($isEmpty) continue; 

      /*       $arrTemp['createdby']['value'] = $_SESSION[$class->loginAdminSession]['id'];
             $arrTemp['createdby']['data'] = array();
      */

      //$arrTemp['_userkey']['value'] = base64_decode($_SESSION[$class->loginAdminSession]['id']);
      //$arrTemp['_userkey']['data'] = array();

      array_push($arrData, $arrTemp);
   }


   return $arrData;
}

// $templateFile             = basename($_SERVER['PHP_SELF']); //str_replace('.php','',basename($_SERVER['PHP_SELF']));
// $personalizedTemplateFile = PERSONALIZED_DOC_PATH . 'tools/import/' . $templateFile;

if (is_file($personalizedTemplateFile)) {

//   require_once($personalizedTemplateFile);
   die;
}

/* 'icaRevenueAccount' => 4 , 'icaCostAccount' => 5 , */
define('COST_TYPE', array( 'revenueAccount' => 1, 'prepaidCost' => 2, 'costAccount' => 3)); // , 'cipRevenueAccount' => 6, 'cipCostAccount' => 7));
define('JOB_TYPE', array( 'import' => 1, 'export' => 2, 'domestic' => 3)); // , 'trucking' => 4 ));
define('CATEGORY_KEY', array( 'FreighFCL' => 1, 'FreightLCL' => 5, 'FreightCustomFCL' => 6,'FreightCustomLCL' => 7, 'customFCL' => 8, 'customLCL' => 9, 'trucking' => 3, 'warehouse' => 10 , 'others' => 4 ));

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'service_key', 'convert' => array('obj' => new Service(SERVICE),  'columnto'=> 'pkey' ))); //master_service
array_push($DATA_STRUCTURE, array('field' => 'job_category_key')); //Business Unit
array_push($DATA_STRUCTURE, array('field' => 'type_key')); //jenis coa
array_push($DATA_STRUCTURE, array('field' => 'coa_key', 'convert' => array('obj' => new ChartOfAccount(),'columnfrom' =>'code',  'columnto'=> 'pkey' ))); //coa
array_push($DATA_STRUCTURE, array('field' => 'eximkey')); //job type => export/import/domestic


   $arrDataCoa = array();

   for ($row = 4; $row <= $highestRow; ++$row){
      
   //key => JobType - BusinessUnit - CostType
      $arrTempRow = array();
      $colIndex = 2;  
	  
	  // LOOP PER JENIS JOB
	  foreach(JOB_TYPE as $jobTypeRow){  
		  foreach(CATEGORY_KEY as $categoryRow){  
			  foreach(COST_TYPE as $costTypeRow){ 
				  array_push($arrTempRow ,
							array(
								'indexkey' => $jobTypeRow . '-' . $categoryRow . '-' . $costTypeRow,
								'service_key' => strtolower(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue())),
								'job_category_key' => $categoryRow,
								'type_key' => $costTypeRow,         
								'coa_key' => strtolower(trim($worksheet->getCellByColumnAndRow($colIndex++, $row)->getValue())),
								'eximkey' => $jobTypeRow
							 ));
                  
			  }
		  }
	  }
   array_push($arrDataCoa, $arrTempRow);
}


function explodeCoa($coa)
{
   $explode = explode(' - ', $coa);
   return $explode[0];

}

$arrCoa = array();
foreach ($arrDataCoa as $coa) {
   foreach ($coa as $data) {

      $arrCoa[] = $data;
   }
}

$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrCoa )); 

$arrServiceKey = array();
$coaKey = array();

foreach($arrData as $data)
{
   array_push($arrServiceKey, $data['service_key']['value']);
   array_push($coaKey, $data['coa_key']['value']);
}

//$class->setLog(array_unique($arrServiceKey),true);

$rsService = $OBJ->searchDataRow(
   array($OBJ->tableName . '.pkey', 'lower(' . $OBJ->tableName . '.code) as code'),
   ' and ' . $OBJ->tableName . '.statuskey = 1 and ' . $OBJ->tableName . '.pkey in (' . $OBJ->oDbCon->paramString(array_unique($arrServiceKey), ',') . ')'
);

$rsChartOfAccount = $chartOfAccount->searchDataRow(
   array($chartOfAccount->tableName . '.pkey', 'lower('.$chartOfAccount->tableName . '.name) as name', 'lower(' . $chartOfAccount->tableName . '.code) as code'),
   ' and ' . $chartOfAccount->tableName . '.pkey in (' . $OBJ->oDbCon->paramString(array_unique($coaKey), ',') . ')'
);

function errorMessage($params)
{
	if (empty($params)) return '';
	
   $error = array_unique($params);
   $error = implode(' <br> ', $error);

   return $error;
}

try{  
   if (!$OBJ->oDbCon->startTrans())
   throw new Exception($OBJ->errorMsg[100]);

	// jgn reset semua, reset per item sja agar bis masukin excel partial
//   if(!empty($arrData))
//   {  
//      $sql = 'delete from item_coa_link where 1=1';
//      $OBJ->oDbCon->execute($sql);
//   }

   $serviceError = array();
   $coaError = array();
	
   $arrServiceKey = array_column($rsService, 'pkey');
   $arrCOAKey = array_column($rsChartOfAccount, 'pkey');
	   
   foreach($arrData as $key =>  $data){
       
	  // cek jika service dn coa terdaftar
      if (in_array($data['service_key']['value'], $arrServiceKey) && in_array($data['coa_key']['value'], $arrCOAKey)) {

		 // hapus dulu item_coa_link yg berhubungan dengan item ini, 
		 $sql = 'delete from item_coa_link
		 		  where 
				  		refkey = ' . $OBJ->oDbCon->paramString($data['service_key']['value']) .' and 
				  		typekey = ' . $OBJ->oDbCon->paramString($data['type_key']['value']) .' and 
				  		categorykey = ' . $OBJ->oDbCon->paramString($data['job_category_key']['value']) .' and 
				  		eximkey = ' . $OBJ->oDbCon->paramString($data['eximkey']['value']);
		  echo $sql.'<br>';
		 $OBJ->oDbCon->execute($sql);
		  
         $sql = 'insert into item_coa_link (refkey,typekey,categorykey,coakey,eximkey) values (' . $OBJ->oDbCon->paramString($data['service_key']['value']) . ', ' . $OBJ->oDbCon->paramString($data['type_key']['value']) . ', ' . $OBJ->oDbCon->paramString($data['job_category_key']['value']) . ', ' . $OBJ->oDbCon->paramString($data['coa_key']['value']) . ', ' . $OBJ->oDbCon->paramString($data['eximkey']['value']) . ')';
         echo $sql.'<br>';
		  $OBJ->oDbCon->execute($sql);

      } else{
		 if(!in_array($data['service_key']['value'], $arrServiceKey))
		  {       
			 $serviceError[] = '<span style="color: red"><b>'. ucwords($data['service_key']['value']) .'.</b>'. ' ' . $OBJ->errorMsg['service'][3] .'</span>';
		  }

		  if(!in_array($data['coa_key']['value'], $arrCOAKey) && !empty($data['coa_key']['value']))
		  {
			 $coaError[] = '<span style="color: red"><b>'. ucwords($data['coa_key']['value']) .'.</b>'. ' ' . $OBJ->errorMsg['chartOfAccount'][1] .'</span>';
		  }
	  }

  

   }

   echo 'Error Services :<br> ';
   echo errorMessage($serviceError);
   echo '<br>';
   echo 'Error COA :<br> ';
   echo errorMessage($coaError);

	echo '<br>done';
   $OBJ->oDbCon->endTrans();
} catch (Exception $e) {

}

?>