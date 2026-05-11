<?php	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('AR.class.php','EMKLOrderInvoice.class.php','TruckingServiceOrderInvoice.class.php');
$ar = createObjAndAddToCol(new AR()); 
$arPayment = createObjAndAddToCol(new ARPayment()); 
$currency = createObjAndAddToCol(new Currency()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$customer = createObjAndAddToCol(new Customer()); 
$emklOrderInvoice = new EMKLOrderInvoice();
$truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();

include '_global.php';

$obj= $ar; 
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

// khusus trucking dan forwarding
$isTruckingPlanType = (  in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ) ? true : false; 
$splitReimbursement = 1; //(isset($_POST) && !empty($_POST['isSplitReimburse']) && $_POST['isSplitReimburse'] == 1) ? 1 : 0;

// kalo bkn truckign pasti gk ad reimbursement
$customerColumnMerge= 4;
$customerColumnWidth = 400;
if (!$isTruckingPlanType){
  $splitReimbursement = 0;   
  $customerColumnMerge = 4; // harusnya based on modul tanda terima invoice
  $customerColumnWidth = 300;
}

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

$arrFilterInformation = array();    
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),  'width'=>$customerColumnWidth."px", 'dbfield' => 'customername',"sortable" => false, 'mergeExcelCell' => $customerColumnMerge);

if(count($rsCurrency) == 1){
    $arrDataStructure['unconfirminvoice'] = array('title'=> ucwords($obj->lang['unconfirm']),  'width'=>"110px", 'dbfield' => 'unconfirminvoice',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
}else{
    
   foreach($rsCurrency as $currRow) 
		$arrDataStructure['unconfirminvoice'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['unconfirm']). ' ' .$currRow['name'],  'width'=>"150px", 'dbfield' => 'unconfirminvoice'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
   
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['notduedays'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['notDue']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => 'notduedays'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
     
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['30days'.$currRow['pkey']] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => '30days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
    
    foreach($rsCurrency as $currRow) 
         $arrDataStructure['60days'.$currRow['pkey']] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px",  'dbfield' => '60days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
    
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['90days'.$currRow['pkey']] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => '90days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
    
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['moreThan90days'.$currRow['pkey']] = array('title'=>'> 90 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px",  'dbfield' => 'moreThan90days'.$currRow['pkey'], "sortable" => false, "format" => 'number','calculateTotal' => true);
     
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['amount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['total']). ' ' .$currRow['name'],'dbfield' => 'totalamount'.$currRow['pkey'], 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
}

$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),  'dbfield' => 'salesman', 'width'=>'120px', 'format' => 'string',"sortable" => false ); 

//if($splitReimbursement == 1){    
//    //$arrDataStructure['separator'] = array();   
//    
//    if(count($rsCurrency) == 1){
//        $arrDataStructure['totalselling'] = array('title'=> ucwords($obj->lang['selling']),  'width'=>"110px", 'dbfield' => 'totalselling',  "sortable" => false, "format" => 'number','calculateTotal' => true, 'textColor' => '568203');   
//        $arrDataStructure['totalreimbursement'] = array('title'=> ucwords($obj->lang['reimburse']),  'width'=>"110px", 'dbfield' => 'totalreimbursement',  "sortable" => false, "format" => 'number','calculateTotal' => true, 'textColor' => '0093AF');    
//    } else{
//       foreach($rsCurrency as $currRow) {
//        $arrDataStructure['totalselling'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['selling']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => 'totalselling'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true, 'textColor' => '568203');   
//        $arrDataStructure['totalreimbursement'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['reimburse']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => 'totalreimbursement'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true, 'textColor' => '0093AF');   
//       }
//        
//  } 
//}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['statementOfAccountReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'arcode', 'width'=>'85px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'120px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'trdate', 'width'=>"80px",'format'=>'date');

if($isTruckingPlanType)
    $arrDataDetailStructure['invoiceReceiptDate'] = array('title'=>ucwords($obj->lang['receiptDate']),'dbfield' => 'receiptdt', 'width'=>"100px",'format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
    
 
if(count($rsCurrency) == 1){
    $arrDataDetailStructure['unconfirminvoice'] = array('title'=> ucwords($obj->lang['unconfirm']),  'width'=>"110px", 'dbfield' => 'unconfirminvoice',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataDetailStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataDetailStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataDetailStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataDetailStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataDetailStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);
}else{
	
	  foreach($rsCurrency as $currRow)  
		  $arrDataDetailStructure['unconfirminvoice'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['unconfirm']). ' ' .$currRow['name'],  'width'=>"150px", 'dbfield' => 'unconfirminvoice'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
      
    foreach($rsCurrency as $currRow) 
        $arrDataDetailStructure['notduedays'.$currRow['pkey']] = array('title'=> ucwords($obj->lang['notDue']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => 'notduedays'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
    
    foreach($rsCurrency as $currRow) 
        $arrDataDetailStructure['30days'.$currRow['pkey']] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => '30days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
     
    foreach($rsCurrency as $currRow) 
        $arrDataDetailStructure['60days'.$currRow['pkey']] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px",  'dbfield' => '60days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
     
    foreach($rsCurrency as $currRow) 
        $arrDataDetailStructure['90days'.$currRow['pkey']] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px", 'dbfield' => '90days'.$currRow['pkey'],  "sortable" => false, "format" => 'number','calculateTotal' => true);
    
    foreach($rsCurrency as $currRow) 
        $arrDataDetailStructure['moreThan90days'.$currRow['pkey']] = array('title'=>'> 90 '.ucwords($obj->lang['days']). ' ' .$currRow['name'],  'width'=>"110px",  'dbfield' => 'moreThan90days'.$currRow['pkey'], "sortable" => false, "format" => 'number','calculateTotal' => true);

}

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "680px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);  
}

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = ' and '.$obj->tableName.'.statuskey in (1,2)'; 
	$emklInvoiceCriteria = ' and '.$emklOrderInvoice->tableName.'.statuskey in (1)'; 
	
    array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' =>  date('d / m / Y') )); 
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  
		$emklInvoiceCriteria .= ' AND '.$emklOrderInvoice->tableName.'.customerkey in('.$key.')';  
			
        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['customer'], 'filter' => $statusName ));
        
	}
     
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  
		$emklInvoiceCriteria .= ' AND '.$emklOrderInvoice->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
 
	$order = 'order by customername asc'; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
    

		 
    // ambil informasi invoice, dipisah saja dr query AR agar gk berat dan lebih stabil utk AR nya
    // perlu pisahin jg khsusu yg reftabletype di AR nya adalah dr EMKL invoice / trucking
    // artinyq refheadernya pasti 0 dan reftabletype pasti 0 jg kalo bkn dr invoice, jd harusnya gk masalah
    
    $rsInvoice = array();
    

    if($isTruckingPlanType){
        // get keys
        $forwardingTableKey = $obj->getTableKeyAndObj($emklOrderInvoice->tableName,array('key'))['key']; 
        $truckingTableKey = $obj->getTableKeyAndObj($truckingServiceOrderInvoice->tableName,array('key'))['key'];  
 
  
		// sementara hanya dari EMKL 
		$rsUnconfirmedInvoice = $emklOrderInvoice->searchData('','',true,$emklInvoiceCriteria); 

		// manipulasi array AR
		foreach($rsUnconfirmedInvoice as $invoiceRow){ 
				array_push($rs, array('reftabletype' => $forwardingTableKey,
									  'refheaderkey' => $invoiceRow['pkey'],
									  'currencykey' =>  $invoiceRow['currencykey'], 
									  'customerkey' =>  $invoiceRow['customerkey'], 
									  'customername' =>   $invoiceRow['customername'], 
									  'datediff' =>  -1,  
									  'unconfirmed' => true,  
									  'refcode' => $invoiceRow['code'], 
									  'trdate' => $invoiceRow['trdate'], 
									  'outstanding' =>   round(($invoiceRow['grandtotal'] - $invoiceRow['totaldownpayment']) , 2)
									 ));
		}



        // harus pisahin antara trucking dan forwarding
        $arTrucking = array();
        $arForwarding = array();

        foreach($rs as $row){
            switch ($row['reftabletype']){
                case $forwardingTableKey :  array_push($arForwarding, $row['refheaderkey']);
											break;
                case $truckingTableKey : array_push($arTrucking, $row['refheaderkey']);
                                        break;
            } 
        }
		
			
                
    }
    
    //if($splitReimbursement == 1){
    // perlu jg utk ambil tgl tanda terima
    // invloce key ada kemungkinan sama pkeynya / kode nya, jd harus ditambah index lain
        // status invoice pasti gk mungkin batal

        if(!empty($arForwarding)){ 
            $arrTempInvoice = $emklOrderInvoice->searchData('','',true, ' and '.$emklOrderInvoice->tableName.'.pkey in ('.$class->oDbCon->paramString($arForwarding,',').')');
            $rsInvoice[$forwardingTableKey] = array_column($arrTempInvoice,null,'pkey'); 
       
        }
        
        if(!empty($arTrucking)){
            $arrTempInvoice = $truckingServiceOrderInvoice->searchData('','',true, ' and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('.$class->oDbCon->paramString($arTrucking,',').')');
            $rsInvoice[$truckingTableKey] = array_column($arrTempInvoice,null,'pkey'); 
       }  
   // }

    
    
    $rARByCustomer = array();
    
    // susun ulang list AR group by customer
    foreach($rs as $row){
         $customerkey = $row['customerkey'];
         $datediff = $row['datediff']; 
         $outstanding = $row['outstanding'];
         $unconfirmed = (isset($row['unconfirmed']) && $row['unconfirmed'] ) ? true :false;

        if (!isset($rARByCustomer[$customerkey])) { 
            
            $rARByCustomer[$customerkey] = array('customername' => $row['customername'], 'detail' => array());
            
            if(count($rsCurrency) == 1){
                $rARByCustomer[$customerkey] = array_merge($rARByCustomer[$customerkey], array( 
                                                  'unconfirminvoice' => 0,
                                                  'notduedays' => 0,
                                                  '30days' => 0,
                                                  '60days' => 0,
                                                  '90days' => 0,
                                                  'moreThan90days' => 0,
                                                  'totalamount' => 0 ,
                                                  'totalselling' => 0 ,
                                                  'totalreimbursement' => 0 
                                                ));
            }else{  
                 foreach($rsCurrency as $currRow){
                     $rARByCustomer[$customerkey] = array_merge($rARByCustomer[$customerkey], array( 
                                                  'unconfirminvoice'.$currRow['pkey'] => 0,
                                                  'notduedays'.$currRow['pkey'] => 0,
                                                  '30days'.$currRow['pkey'] => 0,
                                                  '60days'.$currRow['pkey'] => 0,
                                                  '90days'.$currRow['pkey'] => 0,
                                                  'moreThan90days'.$currRow['pkey'] => 0,
                                                  'totalamount'.$currRow['pkey'] => 0,
                                                  'totalselling'.$currRow['pkey'] => 0 ,
                                                  'totalreimbursement'.$currRow['pkey'] => 0 
                                                ));
                 }
            }
        }
 
        $arrAging = array();
     
        
         if($splitReimbursement == 1){    
             
             // kalo gk ad refheaderkey, anggap aj selling dulu 
             if(!isset($rsInvoice[$row['reftabletype']][$row['refheaderkey']])){
                 $arrIndex = 'totalselling' ;
             }else{ 
                 // pisah per selling/ invoice
                 $arrInv = $rsInvoice[$row['reftabletype']][$row['refheaderkey']];
                 $arrIndex = ($arrInv['isreimburse'] == 0) ? 'totalselling' : 'totalreimbursement' ;
             }
             
          
         }
            
  
         if(count($rsCurrency) == 1){
			 
			 if($unconfirmed ){ 
					$arrAging['unconfirminvoice'] = $outstanding;
			 }else{
				 if ($datediff <= 0) 
					$arrAging['notduedays'] = $outstanding;
				else if ($datediff <= 30) 
					$arrAging['30days'] = $outstanding;
				else if ($datediff <= 60) 
					$arrAging['60days'] = $outstanding;
				else if ($datediff <= 90) 
					$arrAging['90days'] = $outstanding;
				else  
					$arrAging['moreThan90days'] = $outstanding;   

			 }
             
 
            $rARByCustomer[$customerkey]['totalamount'] += $outstanding;   
            if($splitReimbursement == 1) $rARByCustomer[$customerkey][$arrIndex] += $outstanding;  
			 
         }else{

			 if($unconfirmed ){ 
				  foreach($rsCurrency as $currRow){
					$currencykey = $row['currencykey'];
					$arrAging['unconfirminvoice'.$currencykey] = $outstanding; 
				  }
			 }else{
		   
				   foreach($rsCurrency as $currRow){
						 $currencykey = $row['currencykey'];
						 //$class->setLog($outstanding,true);
						 if ($datediff <= 0) 
							$arrAging['notduedays'.$currencykey] = $outstanding;
						else if ($datediff <= 30) 
							$arrAging['30days'.$currencykey] = $outstanding;
						else if ($datediff <= 60) 
							$arrAging['60days'.$currencykey] = $outstanding;
						else if ($datediff <= 90) 
							$arrAging['90days'.$currencykey] = $outstanding;
						else  
							$arrAging['moreThan90days'.$currencykey] = $outstanding;   
					 }
			 }
             
             
             $rARByCustomer[$customerkey]['totalamount'.$currencykey] += $outstanding;   
             if($splitReimbursement == 1) $rARByCustomer[$customerkey][$arrIndex.$currencykey] += $outstanding;  
         }
         
        
        //if ($isShowDetail){
            $arrDetail = array(); 
            foreach($arrAging as $agingkey => $agingValue) { 
                $rARByCustomer[$customerkey][$agingkey] += $agingValue;
                $arrDetail[$agingkey] = $agingValue;
            }

            $arrDetail['arcode'] =  $row['code'];
            $arrDetail['refcode'] =  $row['refcode'];
            $arrDetail['trdate'] =  $row['trdate'];
            $arrDetail['receiptdt'] = $rsInvoice[$row['reftabletype']][$row['refheaderkey']]['receiptdt'];

            array_push($rARByCustomer[$customerkey]['detail'], $arrDetail);
      //  }
            
        
    }

 
		
    
    $tempreport = '';  
    if (empty($rARByCustomer)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    
    // disini generate report seperti biasa
 //   $arrInvoice = array();


//    foreach($rsInvoiceUnconfirmed as $currencyInvoiceKey => $rowData){
//       
//        
//        
//         foreach($rowData as $invoiceData){
//                $invoiceCustomerKey = $invoiceData['customerkey'];
//                $invoiceOutstanding = $invoiceData['outstanding'];
//               if (!isset($arrInvoice[$invoiceCustomerKey])) { 
//                
//                    if(count($rsCurrency) == 1 && count($currencyInvoiceKey) == 1){
//                        $arrInvoice[$invoiceCustomerKey] = array_merge($arrInvoice[$invoiceCustomerKey], array( 
//                                                          'totalunconfirminvoice' => 0,
//
//                                                        ));
//                    }else{  
//                       
//                        
//                          $arrInvoice[$invoiceCustomerKey] = array_merge($arrInvoice[$invoiceCustomerKey], array( 
//                                                          'totalunconfirminvoice'.$currencyInvoiceKey => 0,
//
//                                                        ));
//                                             
//                         
//                    }
//                }
//             
//            
//                  if(count($rsCurrency) == 1 && count($currencyInvoiceKey) == 1){
//                    $arrInvoice[$invoiceCustomerKey]['totalunconfirminvoice'] += $invoiceOutstanding;  
//
//
//
//                 }else{
//
//                     $arrInvoice[$invoiceCustomerKey]['totalunconfirminvoice'.$currencyInvoiceKey] += $invoiceOutstanding;   
//                 }
//             
//         }
//                
//
//
//        
//    }
	
	
	$rsSales = $customer->getSalesman(array_keys($rARByCustomer));
	$rsSales = array_column($rsSales,null,'customerkey');

    foreach($rARByCustomer as $customerkey => $row){

        $row['salesman'] = $rsSales[$customerkey]['name']; 
		
        if ($isShowDetail){
            // has detail 
            $row['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $row['detail']);
        }
        
        $return = $obj->formatReportRows(array('data' => $row),$arrTemplate); 
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
        
    }
    
    unset($row);
 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
 
}
 
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name'); 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));       
$arrTwigVar['inputTemplateCustomer'] = $class->inputAutoComplete(array(   
                                                                        'element' => array('value' => 'selTemplateCustomer',
                                                                                           'key' => 'hidTemplateCustomerKey'),
                                                                        'source' => array(
                                                                                            'url' => '../ajax-template-customer.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        ), 
                                                                        'placeholder' => $obj->lang['searchTemplate'].'...',
                                                                        'callbackFunction' => 'updateCustomer(this)' 
                                                                      )); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSplitReimburse'] =  $class->inputCheckBox('isSplitReimburse');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportStatementOfAccount.html', $arrTwigVar);   
?>
