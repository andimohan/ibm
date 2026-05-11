<?php

require_once '../_config.php'; 
include_once '../_include-v2.php';
require_once '../assets/vendor/autoload.php';

if(isset($_FILES) && !empty($_FILES['fileToUpload'])){
	
	$tableName = 'diagnose';
	
	$class->oDbCon->startTrans();
	
	$pkey = 8000;
	
	$sql = 'delete from ' .$tableName;
	$class->oDbCon->execute($sql);
	
	$inputFileType = 'Xlsx';   
	$inputFileName = $_FILES['fileToUpload']['tmp_name']; 

	$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
	$reader->setReadDataOnly(true); 

	$spreadsheet = $reader->load($inputFileName); 
	
	$totalSheet = $spreadsheet->getSheetCount();
	for($i=0;$i<$totalSheet;$i++){
		$worksheet = $spreadsheet->getSheet($i);
		$highestRow = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
 
	    for ($row = 2; $row <= $highestRow; ++$row) { 
			$code = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()); 
			$subcode = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()); 
			$nameId = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()); 
			$nameEn = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue()); 
			
			$parentkey = 0;
			
			// gk boleh pake empty, karena bisa angka 0
			if($subcode <> ''){
				$sql = 'select pkey from ' .$tableName.' where code = \''.$code.'\' and parentkey = 0 order by pkey asc limit 1';
				$rs = $class->oDbCon->doQuery($sql);
				
				if(empty($rs)) {
					//echo $sql.'<br>';
					//die("error not found. " . $code);
					echo $code.'<br>';
				}
				
				$parentkey = $rs[0]['pkey'];
				
				$code .= '.'.$subcode;
			} 
			
			
			$sql = 'insert into '.$tableName.' (pkey,code,name,parentkey,statuskey)
					values (
							'.$class->oDbCon->paramString($pkey).',
							'.$class->oDbCon->paramString($code).',
							'.$class->oDbCon->paramString($nameId).',
							'.$class->oDbCon->paramString($parentkey).',
							1
							)';
			$class->oDbCon->execute($sql);
			
			// karena hanya 2 level, isleaf bisa ditembak
			$sql = 'update '.$tableName.' set isleaf = 1 where parentkey <> 0';
			$class->oDbCon->execute($sql);
			
			
			//echo $code.'<br>';
			
			$pkey++;
	    }
		
	}
		
	//$worksheet = $spreadsheet->getActiveSheet();
	// Get the highest row and column numbers referenced in the worksheet 
	//$filePath = DOC_ROOT.'tools/import/'.DOMAIN_NAME.'/'. basename($_SERVER['PHP_SELF']);  
 
	$class->oDbCon->endTrans();
	die ("done");
}
 
	

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<title>Upload ICD 10</title>  
</head> 
<body>    
	 <form action="importicd10.php" method="post" enctype="multipart/form-data" target="_blank" id="form-import"> 
        <div class="div-table"> 
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">File</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><input type="file" name="fileToUpload"></div>
            </div> 
            <div class="div-table-row">
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSubmit('btnSubmit','Import'); ?></div>
            </div>
        </div> 
    </form>
      
</body> 
</html> 
