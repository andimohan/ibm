<?php
ini_set('max_execution_time', 30000000);
ini_set('memory_limit', '2024M');

require_once '../_config.php';
include_once '../_include-v2.php';
require_once '../assets/vendor/autoload.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/SalesOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/BuildingUnit.class.php';

$OBJ = new SalesOrder();
$customer = new Customer();
$buildingUnit = new BuildingUnit();


if (isset($_FILES) && !empty($_FILES['fileToUpload'])) {

   $inputFileType = 'Xlsx';
   $inputFileName = $_FILES['fileToUpload']['tmp_name'];

   $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
   $reader->setReadDataOnly(true);

   $spreadsheet = $reader->load($inputFileName);
   $totalSheet = $spreadsheet->getSheetCount();

   $warehousekey = 1;
   $termOfPaymentKey = 8001;
   $itemKey = 8001;
   $itemUnitKey = 8000;
   $qty = 1;

   $customersWithMissingPayments = [];
   for ($i = 0; $i < $totalSheet; $i++) {

      $worksheet = $spreadsheet->getSheet($i);
      $highestRow = $worksheet->getHighestRow(); // e.g. 10
      $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
      $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

      $headersDate = [];
      for ($col = 4; $col <= $highestColumnIndex; ++$col) {
         $trdate = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
         $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
         $trdate = $trdate->getTimestamp();
         if (empty($trdate) || $trdate == "")
            continue;
         //$trdate = date("d / m / Y", $trdate);
         $trdate = $trdate;
         $headersDate[] = $trdate;
      }

      for ($row = 2; $row <= $highestRow; ++$row) {
         $uniqueCode = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
         $owner = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
         $contribution = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
      
         // $january = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
         // $february = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());
         // $march = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue());
         // $april = trim($worksheet->getCellByColumnAndRow(7, $row)->getValue());
         // $may = trim($worksheet->getCellByColumnAndRow(8, $row)->getValue());
         // $june = trim($worksheet->getCellByColumnAndRow(9, $row)->getValue());
         // $july = trim($worksheet->getCellByColumnAndRow(10, $row)->getValue());
         // $august = trim($worksheet->getCellByColumnAndRow(11, $row)->getValue());
         // $september = trim($worksheet->getCellByColumnAndRow(12, $row)->getValue());
         // $october = trim($worksheet->getCellByColumnAndRow(13, $row)->getValue());
         // $november = trim($worksheet->getCellByColumnAndRow(14, $row)->getValue());
         // $december = trim($worksheet->getCellByColumnAndRow(15, $row)->getValue());

         $missingPayments = false;
         $monthlyPayments = [];
		   
		  
         for ($col = 4; $col <= $highestColumnIndex; ++$col) {
			 
   			$arrParam = array();
			 
			 
            $payment = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
			$payment = intval($payment);
			 

			$trdate = $headersDate[$col - 4]; 

			$rsBuildingUnit = $buildingUnit->searchDataRow(
			 array(
				$buildingUnit->tableName . '.pkey',
				$buildingUnit->tableName . '.code',
				$buildingUnit->tableName . '.block',
				$buildingUnit->tableName . '.unit',
			 ), ' and ' . $buildingUnit->tableName . '.code = (' . $buildingUnit->oDbCon->paramString($uniqueCode) . ') ');

			if (empty($buildingUnit)) continue;

			$rsCustomer = $customer->searchDataRow(array(
			   $customer->tableName.'.pkey',
			   $customer->tableName.'.code',
			   $customer->tableName.'.name',
			   $customer->tableName.'.refbuildingunitkey'
			), ' and ' . $customer->tableName.'.refbuildingunitkey = ('. $customer->oDbCon->paramString($rsBuildingUnit[0]['pkey']) .') ');


			 if(empty($rsCustomer)) continue;


			$arrParam['hidDetailKey'] = array();
			$arrParam['hidVoucherKey'] = '';
			$arrParam['hidVoucherType'] = '';
			$arrParam['finalDiscount'] = 0;
			$arrParam['selFinalDiscountType'] =0; 
				 
			$arrParam['hidItemKey'] = array();
			$arrParam['qty'] = array();
			$arrParam['selUnit'] = array();
			$arrParam['priceInUnit'] = array();
			$arrParam['detailSubTotal'] = array();
			$arrParam['trDetailDesc'] = array();

			$arrParam['code'] = 'xxxxx';
			$arrParam['trDate'] = date("d / m / Y 00:00:00", $trdate);
			$arrParam['selWarehouseKey'] = $warehousekey;
			$arrParam['hidCustomerKey'] = $rsCustomer[0]['pkey'];

			 // kalo sudah ad pembayaran (bisa karena history atau kesepakatan harga)
			 // pake data pembayaran
			
			if(!empty($payment))
				$unitPrice = $payment;
			else 
				$unitPrice = $contribution;
			 
			$detailSubtotal  = $qty * $unitPrice;
//			$detailSubtotal = $OBJ->formatNumber($detailSubTotal);

			array_push($arrParam['hidDetailKey'], 0);
			array_push($arrParam['hidItemKey'], $itemKey);
			array_push($arrParam['qty'], $qty);
			array_push($arrParam['selUnit'], $itemUnitKey);
			array_push($arrParam['priceInUnit'], $OBJ->formatNumber($unitPrice));
			array_push($arrParam['detailSubTotal'], $detailSubTotal);
			$detailDesc = 'Periode ' . date("M Y", $trdate);
			array_push($arrParam['trDetailDesc'], $detailDesc);

			$arrParam['selStatus'] = 1;
			$arrParam['subtotal'] = $detailSubTotal;
			$arrParam['total'] = $detailSubTotal;
			$arrParam['selTermOfPaymentKey'] = $termOfPaymentKey;
			
			// harus diset jg kalo 0, kalo gk semua masuknya 0
			$arrParam['overwriteGL'] = (!empty($payment)) ? 1 : 0 ;  
			
			$arrayToJs = $OBJ->addData($arrParam);
		

			if (!$arrayToJs[0]['valid']) { 
			   echo $OBJ->errorMsg[201] . ' ' . $arrayToJs[0]['message'].'<br>';
			}else{ 
				$newPkey = $arrayToJs[0]['data']['pkey']; 
				$result = $OBJ->changeStatus($newPkey,2);   
			}
			
            
         }


      }

   }


	echo 'done';
   //$OBJ->setLog($customersWithMissingPayments, true);
}


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<title>Import Sales Order (South Thames)</title>  
</head> 
<body>    
	 <form action="importInvoiceST.php" method="post" enctype="multipart/form-data" target="_blank" id="form-import"> 
        <div class="div-table"> 
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">File</div>
                <br>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><input type="file" name="fileToUpload"></div>
            </div> 
            <div class="div-table-row">
            <div class="div-table-col-5"></div>
            <div class="div-table-col-5"></div>
            <br>
            <div class="div-table-col-5"><?php echo $class->inputSubmit('btnSubmit', 'Import'); ?></div>
         </div>
      </div>
   </form>

</body>

</html>