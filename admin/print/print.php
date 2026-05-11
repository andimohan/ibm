<?php
$start_time = microtime(TRUE);
require_once '../../_config.php';
	
$temp = array('ap','ar','salesOrder','truckingServiceWorkOrder','emklJobHeader','emklFormJobHeader','cashBankRealization','carServiceMaintenance','cashAdvance','cashAdvanceRealization',
            'arEmployee','arapEmployeeNetting','truckingCostCashOut','cashOut','cashIn','cashBankTransfer','apEmployeePayment','arEmployeePayment','salesRentalQuotation',
            'emklJobOrderExport','emklJobOrderImport','emklJobOrderDomestic','warehouseTransfer','apCommission','apCommissionPayment','medicalRecord','salesOrderInvoiceReceipt','emklInvoiceReceipt','purchaseOrder',
			'purchaseRequest','termination','supplierDownpaymentSettlement','customerDownpaymentSettlement','arDiscountApproval','emklOrderInvoice','salesOrderProperty',
			'salesOrderPropertyComplete','arPayment','arapNetting','itemOut','itemIn','itemAdjustment','truckingServiceOrderInvoice','truckingServiceOrderComplete',
			'truckingServiceOrderCostCashOut','truckingServiceOrder','invoiceOrderSubscription','cashBankVoucher','apEmployeeCommissionPayment',
			'apPayment','apPaymentByCategory','installationWorkOrder','salesOrderSubscription','installationBAST','emklPurchaseOrderExport','emklPurchaseOrderImport','emklPurchaseOrderDomestic','ticketSupportWorkOrder',
			'generalJournal','customerDownpayment','supplierDownpayment','salesOrderDelivery','salesLabel','logisticSalesOrder','logisticSalesOrderCopy','emklHouseBL','logisticSalesOrderManifest','logisticShippingLabel',
			'creditNote', 'shippingInstruction','itemBarcode','apCustomerCommission','apCustomerCommissionPayment','costReconsile', 'medicalJobOrder', 
			'medicalJobOrderCompleteForm', 'medicalJobOrderAndQuotation', 'medicalSalesOrderQuotation', 'medicalRequestClaim', 'medicalRequestAndQuotation', 'medicalPurchaseOrder', 'letterOfGuarantee',
			'assetBarcode','disposalWorkOrder','disposalSalesInvoice','disposalContract','disposalPurchaseOrder','assetPurchase','emklOrderSheetImport','emklOrderSheetExport', 'debitNote',
            'truckingQuotation','disposalSalesInvoiceKop','disposalSalesWasteInvoice','arrivalNoticeExport','arrivalNoticeImport','arrivalNoticeDomestic','emklQuotationOrder','attachmentTruckingServiceOrderInvoice',
			'activityProgress','emklWorkOrderImport','truckingServiceOrderInvoiceExcel','debitNoteTigaRaksa','shippingInstructionHBL','preAlertNotice', 'cargoReleased',
            'performaShippingInstruction','overdueOutstandingLetter','employeeCommission','emklHouseBLWSI','truckingServiceOrderInvoiceMaersk','truckingServiceOrderInvoicePeriodeTCL',
            'packagingCode', 'emklCommission','emklOrderSheetWarehouse','emklOrderSheetTrucking','emklPurchaseOrderTrucking','emklPurchaseOrderWarehouse','emklJobOrderWarehouse','emklJobOrderTrucking','truckingServiceOrderInvoiceLSI',
            'truckingServiceOrderPointHistory','carSpareparts','truckingServiceOrderInvoiceSariRoti');

if(in_array($_GET['filename'],$temp))
    require_once '../../_include-v2.php';
else
    require_once '../../_include.php';

ob_start();

// gk bisa pake cached, karena ad kalanya user sekali print beberapa transaksi jadi 1 pdf
//$useCached = (isset($_GET) && !empty($_GET['cached'] == 1)) ? true : false;

// sementara utk envilog saja
//if (!in_array(DOMAIN_NAME, array('envilog.wintera.co.id'))) $useCached = false;


$OPT_FUNCTION = '';

// ====================== SET DEFAULT FILE  
$fileName = $_GET['filename']; 
if(empty($fileName)) die;  
$ext = pathinfo($fileName, PATHINFO_EXTENSION);
$fileName = (empty($ext)) ? $fileName .'.php' : $fileName; 
require_once DOC_ROOT.'admin/print/'.$fileName;   
// ====================== END OF SET DEFAULT FILE  

$securityObject  = $obj->securityObject; 

// dari API
/*if(isset($postVars) && isset($fileContent)){ 
    $userkey = (isset($postVars['userkey']) && !empty($postVars['userkey']) ) ? $postVars['userkey'] : 0; 
    $security->APIGatePass($userkey, $fileContent,$securityObject, 10,true, $postVars['auth']);  
}*/

require_once DOC_ROOT.'admin/print/_global.php';
 

if (!isset($arrID))
$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();

// test log
//if (count($arrID) > 5){
//	$obj->setLog(date('d/m/Y H:i').' >> ' . count($arrID) . ' >> ' . $_GET['filename'],true);
//}

$title = array(); 
$rsDataCol = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey  in (' . $obj->oDbCon->paramString($arrID ,',').')'); 
$rsDataCol = array_column($rsDataCol,null,'pkey');
		
$totalPages = count($arrID);

$mergeAttachment = (isset($_GET['attachment']) && $_GET['attachment'] == 1) ? true : false;
$mergeToPrint = array();

for($i=0;$i<$totalPages;$i++){ 
	
	$id = $arrID[$i];
	
    //$rsData = $obj->searchData($obj->tableName.'.pkey',$id); 
	$rsData = array($rsDataCol[$id]); // agar tetep ad index [0] 
    
    $obj->validateAllowedStatus($rsData);
    
    $dataset = array();
    $dataset['rs'] = $rsData;
	
    $pdf->dataset = $dataset;
    
    // reset custom settings
    $pdf->customSettings = (isset($PRINT_SETTINGS))  ? $PRINT_SETTINGS : null;
	
	// print footer taro disini saja, kalo di function bawaan TCPDF akan ad selisih 1 page
	if(!isset($pdf->customSettings['footer'])){ 
		
		 $employeeLogName = array();

		 $rsEmployee = $employee->getDataRowById($rsData[0]['createdby']);
		 array_push($employeeLogName, 'Buat: ' .$rsEmployee[0]['name']); 

		 if (!empty($rsData[0]['confirmedby'])){ 
			$rsEmployee = $employee->getDataRowById($rsData[0]['confirmedby']);
			array_push($employeeLogName, 'Konfirmasi: ' .$rsEmployee[0]['name']);
		 } 

		 $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
		 array_push($employeeLogName, 'Cetak: ' .$rsEmployee[0]['name']);
 
		 $footerHTML = '<table style="width:'.$pdf->contentWidth.'px">
		 					 <tr><td style="width:70%">'.implode('. ' ,$employeeLogName).'.</td><td style="width:30%; text-align:right">Status: '.ucwords($rsData[0]['statusname']).'</td></tr>
							 <tr><td>'.$class->lang['page'].' {{ GROUP_PAGE_NO }}</td><td style="text-align:rright"> '.date('d / m / Y H:i').'</td></tr>
						</table>';
		
    	 $pdf->customSettings['footer'] = $footerHTML;
		
	}
	
    $opt = (isset($OPT_FUNCTION) && !empty($OPT_FUNCTION)) ? $OPT_FUNCTION($dataset) : ''; 
    $returnInfo = addNewPDFPage($pdf,$obj, $generateReportContent, $opt); 
    
    $obj->afterPrintTransaction($rsData);
    array_push($title,$dataset['rs'][0]['code'] );
    
    if($mergeAttachment){  
        array_push($mergeToPrint, array(
                                        'headerPageIndex' => $returnInfo['pageNo'],
                                        'totalHeaderPages' => $returnInfo['totalPages'],
                                        'attachment' =>  $obj->getAttachmentToPrint($id)   
                                        )
                    );
         
    }
    
}
    
$title = implode(', ', $title);

$pdf->SetTitle($title);

if(!$mergeAttachment){
     
    $outputType = 'I'; 
    $returnAPI = false;
    
    
    // utk akses dari API dan sementara utk domain wintera / pstn dulu utk testing
    // utk kirim email attachment
    if (isset($_GET['token']) && !empty($_GET['token']) && in_array(DOMAIN_NAME,array('pstn.program-stok.com','wintera.co.id'))){
        $outputType = 'S'; 
        $returnAPI = true;
    }
    
    $pdfFileName = substr($title,0,$obj->printSetting['fileNameLength']).'.pdf';
    $pdfResult = $pdf->Output($pdfFileName,$outputType);    
    
    if ($returnAPI){
        while (ob_get_level()) { ob_end_clean(); }
        header_remove();
        
        // Send correct headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$pdfFileName.'"');
        header('Content-Length: ' . strlen($pdfResult));
        
        // Echo the PDF content
        echo $pdfResult;
        die;
    }
        
}else{
 
    if(!is_dir($obj->uploadTempDoc)) mkdir ($obj->uploadTempDoc,  0755, true);
    
    $filePath = $obj->uploadTempDoc.time().rand().'.pdf'; 
 
    $pdf->Output($filePath, 'F');
    
    require_once($DOC_ROOT.'assets/PDFMerger.php');
    $pdfMerger = new PDFMerger; 
    
    foreach($mergeToPrint as $printRow){ // akalin, selipin attachment di setiap akhir file pemiliknya
        
        $pageToAdd = $printRow['headerPageIndex'];
        if($printRow['totalHeaderPages'] > 1)
            $pageToAdd .= '-'. ( $pageToAdd + $printRow['totalHeaderPages'] - 1);
            
        $pdfMerger->addPDF($filePath,$pageToAdd);  // add file invoice
    
        $ctr = 0;
        
        //pishin dulu image dan pdf biar lebih efisien
        $arrExt = array();
        foreach($printRow['attachment'] as $file){ 
            $parsedUrl = parse_url($file, PHP_URL_PATH); // buat hilangin query dulu (kalo ad dari S3)
            $path_parts  = pathinfo($parsedUrl); 
            $ext = strtolower($path_parts['extension']);
            if ($ext == 'jpeg') $ext = 'jpg';
            
            if (!isset($arrExt[$ext])) $arrExt[$ext] = array(); 
            array_push($arrExt[$ext], $file);               
        }
           
        $pxConvertion = 0.264583333;
        
        foreach($arrExt as $ext=>$arrFiles){
            if ($ext == 'pdf') { 
                  foreach($arrFiles as $file) 
                      $pdfMerger->addPDF($file);    
            }else{
                
                $imgPDF = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);  
                $imgPDF->setPrintHeader(false);
                
                $pos = array('x' => 15,'y' => 10);
                $docSize = array('w' => 180,'h' => 260); // sementara A4 dulu, dn dipotong bleed / padding
                
                $imgPDF->AddPage();
                
                foreach($arrFiles as $file) { 
                    
                    // sementara pake furl open 
                    $imgSize = getimagesize($file);  
                    $imgSize = $obj->resizeRatio($imgSize[0] * $pxConvertion,$imgSize[1] * $pxConvertion,array('type' => 1, 'size' => $docSize) ); 

                    // width pake docsize, biar gampang mau rata tengah
                    $imgPDF->Image($file, $pos['x'], $pos['y'], $docSize['w'], $imgSize['h'], $ext, '', '', true, 150, '', false, false, 1, 'C', false, false); 
                    $pos['y'] += $imgSize['h'] +2;
                  
                }

                $attachFile = str_replace('.pdf','',$filePath);
                $attachFile .= '_'.$ctr++.'.pdf';  
                $imgPDF->Output($attachFile, 'F');

                $pdfMerger->addPDF($attachFile);    
            }
        } 

    }
     
    $pdfMerger->merge('browser',$title);
     
}

// $perfomanceLog = getPerformanceLog($start_time);
// if( $perfomanceLog['memoryPeak']> 10000)
//     $obj->setLog($fileName.chr(13).$perfomanceLog['msg'].chr(13).$perfomanceLog['memoryPeak'],true,'../'.DOMAIN_NAME.'-print-performance.txt');
?>
