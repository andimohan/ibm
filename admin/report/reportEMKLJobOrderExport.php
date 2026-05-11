<?php
	 
include '../../_config.php'; 
include '../../_include-v2.php';


includeClass('EMKLJobOrder.class.php','CreditNote.clas.php');
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$container = createObjAndAddToCol(new Container());
$currency = createObjAndAddToCol(new Currency());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$creditNote = createObjAndAddToCol(new CreditNote());
include '_global.php';

$obj = $emklJobOrderExport;
$securityObject = 'reportSalesOrderExportFF'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3); 

if(!isset($_POST['selDateType']) || empty($_POST['selDateType']))
    $_POST['selDateType'] = 2;

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
//$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
$arrDataStructure['containertype'] = array('title'=>ucwords($obj->lang['type']),'dbfield' => 'containertype', 'width'=>"60px" );
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'customername', 'width'=>"250px");
$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");
$arrDataStructure['pol'] = array('title'=>'POL','dbfield' => 'polname', 'width'=>"100px" );
$arrDataStructure['pod'] = array('title'=>'POD','dbfield' => 'podname', 'width'=>"100px");
$arrDataStructure['20'] = array('title'=>'20\'','dbfield' => 'volume20', 'align'=>'right', 'width'=>"60px", 'format'=>'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['40'] = array('title'=>'40\'','dbfield' => 'volume40', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['45'] = array('title'=>'45\'','dbfield' => 'volume45', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['cbm'] = array('title'=>'CBM','dbfield' => 'volume', 'width'=>"100px", 'align' =>'right', 'format' => 'decimal','calculateTotal' => true );
$arrDataStructure['carrier'] = array('title'=>ucwords($obj->lang['carrier']),'dbfield' => 'carriername', 'width'=>"150px" );
$arrDataStructure['mbl'] = array('title'=>ucwords($obj->lang['mbl']),'dbfield' => 'mblnumber', 'width'=>"150px" );
$arrDataStructure['vessel'] = array('title'=>ucwords($obj->lang['vessel']),'dbfield' => 'vesselname', 'width'=>"150px" );
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"90px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"90px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['typeOfJob'] = array('title'=>ucwords($obj->lang['typeOfJob']),'dbfield' => 'jobtypeunion', 'width'=>"200px" );

$arrDataStructure['containerNumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"250px" );
$arrDataStructure['referencePO'] = array('title'=>ucwords($obj->lang['poReference']),'dbfield' => 'ponumber', 'width'=>"150px" );
$arrDataStructure['bookingNumber'] = array('title'=>ucwords($obj->lang['bookingNumber']),'dbfield' => 'bookingnumber', 'width'=>"150px" );

//$arrDataStructure['pod'] = array('title'=>ucwords($obj->lang['pod']),'dbfield' => 'podname', 'width'=>"150px" );

$arrDataStructure['totalSelling'] = array('title'=>ucwords($obj->lang['totalSales']),'dbfield' => 'totalselling','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalInvoiced'] = array('title'=>ucwords($obj->lang['invoiced']),'dbfield' => 'totalinvoiced','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalBeforeTaxInvoiced'] = array('title'=>ucwords($obj->lang['beforeTax']),'dbfield' => 'totalbeforetaxinvoiced','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalBuying'] = array('title'=>ucwords($obj->lang['totalBuying']),'dbfield' => 'totalbuying','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalCommission'] = array('title'=>ucwords($obj->lang['purchaseRefund']),'dbfield' => 'totalcommission','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalCreditNote'] = array('title'=>ucwords($obj->lang['creditNote']),'dbfield' => 'totalcreditnote','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalDebitNote'] = array('title'=>ucwords($obj->lang['debitNote']),'dbfield' => 'totaldebitnote','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['grossProfit'] = array('title'=>ucwords($obj->lang['grossProfit']),'dbfield' => 'grossprofit','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['grossProfitPercentage'] = array('title'=>'GP %','dbfield' => 'grossprofitpercentage','align'=>'right', 'width'=>"80px",'format'=>'decimal', "sortable" => false); 
$arrDataStructure['totaltax23value'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'totaltax23value','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['invoicedCode'] = array('title'=>ucwords($obj->lang['invoiceCode']),'dbfield' => 'invoicecode', 'width'=>"150px"); 
$arrDataStructure['arStatus'] = array('title'=>ucwords($obj->lang['ar']),'dbfield' => 'arstatusname', 'width'=>"100px"); 

$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['created'] = array('title' => ucwords($obj->lang['createdBy']), 'dbfield' => 'createdbyname', 'width' => "200px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderExportReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    if ($isShowDetail){ 
        // detail ...
        $arrDataDetailStructure = array(); 
        $arrDataDetailStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'dbfield' => 'code', 'width'=>'130px' );
        $arrDataDetailStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),  'dbfield' => 'customername', 'width'=>'200px' );
        $arrDataDetailStructure['hbl'] = array('title'=>ucwords($obj->lang['hbl']),  'dbfield' => 'hbl', 'width'=>'150px' );
        //$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"80px",'format'=>'number');
        $arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'description', 'width'=>"300px", 'mergeExcelCell' => 3);

        $arrDetailTemplate = array();
        $arrDetailTemplate['reportWidth'] = "750px";
        $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
        $arrDetailTemplate['total'] = array();

        array_push($arrTemplate, $arrDetailTemplate); 
    }

    if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
      switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableName.'.etapod'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
        
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
	
	if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['salesman'], 'filter' => $statusName ));
        
	}

      
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['customer'], 'filter' => $customerName ));
        
	}

//    if(isset($_POST) && !empty($_POST['customerName'])) { 
//        $criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
//	    array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' => $_POST['customerName']));
//	}	
        
    if(isset($_POST) && !empty($_POST['bookingNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.bookingnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['bookingNumber'].'%').')';
	 	array_push($arrFilterInformation,array("Booking Number" => 'DO Pelanggan', 'filter' =>  $_POST['bookingNumber']));
	} 
	if(isset($_POST) && !empty($_POST['mblNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.mblnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['mblNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'MBL', 'filter' =>  $_POST['mblNumber']));
	} 
    	       
if (isset($_POST) && !empty($_POST['selCreated'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCreated']));

        $criteria .= ' AND ' . $obj->tableName . '.createdby in(' . $key . ')';

        $rsCriteria = $employee->searchData('', '', true, ' and ' . $employee->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $createdName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['created'], 'filter' => $createdName));

    }  
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
		 
/*    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$detailCriteria .= ' AND '.$obj->tableNameDetail.'.currencykey in ('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');;
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$currencyName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['currency'], 'filter' => $currencyName ));
        
	} */

    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer'];  
       	$criteria .= ' AND '.$obj->tableName.'.containertypekey in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['containerType'], 'filter' => $statusName ));
        
	} 

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
      
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
        
    //$totalCurr = count(array_unique(array_column($rs,'currencykey'))); 
    //$calculateTotal = ($totalCurr <= 1) ? true : false;
        
    $arrKeys = array_column($rs,'pkey');
        
    $tempreport = '';

    $rsDetailCol = ($isShowDetail || !empty($detailCriteria)) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
    
    // count container for each row
    $rsContainerQty = $obj->getDetailVolume($arrKeys);
    
    $arrContainerCol = array();
    $totalContainerRows = count($rsContainerQty); 
    for( $i=0;$i<$totalContainerRows;$i++) {  
        $sokey = $rsContainerQty[$i]['refkey'];
        $vol = $rsContainerQty[$i]['volume'];
        $qty = $rsContainerQty[$i]['qty'];
        if(!isset($arrContainerCol[$sokey])) $arrContainerCol[$sokey] = array();
        
        $arrContainerCol[$sokey][strval(intval($vol))] += $qty; 
    }
    
    // utk LCL
    $containerLCLKey = array_unique(array_column($rs,'itemkey'));
    $rsContainerCol = $container->searchDataRow(array($container->tableName.'.pkey', $container->tableName.'.volume'),
                                             ' and '.$container->tableName.'.pkey in ('.$class->oDbCon->paramString($containerLCLKey,',').')'
                                            );
    $rsContainerCol = array_column($rsContainerCol,null,'pkey');
    
    // total invvoiced
    $rsInvoiceInformation = $obj->getAmountInvoiced($arrKeys);
    $rsInvoiceInformation = $obj->reindexDetailCollections($rsInvoiceInformation,'refsalesorderheaderkey');    
    
	// total CN
	//$rsCNCol = $creditNote->getCreditNoteByEMKLJO( array_column($rs,'pkey'), ' and '.$creditNote->tableName.'.statuskey in (2,3)' );
    //$rsCNCol = $obj->reindexDetailCollections($rsCNCol,'jokey');   
		
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
        $sokey = $rs[$i]['pkey'];
        
        $arrHeaderStyle = array();
           
        $rs[$i]['code'] = '<a href="/admin/print/emklJobOrderExport/'.$rs[$i]['pkey'].'" target="_blank">'.$rs[$i]['code'].'</a>';
        $rs[$i]['totalinvoiced'] = 0;
        $rs[$i]['totalbeforetaxinvoiced'] = 0;
        $rs[$i]['totaltax23value'] = 0;
        $arrStatus = array();  
        $arrInvoiceCode = array();
        
        $hasOutstanding = false;
			
        if(isset($rsInvoiceInformation[$sokey])){
            foreach($rsInvoiceInformation[$sokey] as $invoiceRow){ 
                $rs[$i]['totalinvoiced'] += $invoiceRow['amount']; 
                $rs[$i]['totaltax23value'] += $invoiceRow['tax23value'];
                array_push($arrInvoiceCode, $invoiceRow['code']); 
                array_push($arrStatus, $invoiceRow['arstatusname']);
                
                if(in_array($invoiceRow['arstatuskey'],array(1,2)))
                    $hasOutstanding = true; 
                
                if ($invoiceRow['ispriceincludetax']==1){
                    $beforeTax =  ($invoiceRow['taxpercentage']/(100 + $invoiceRow['taxpercentage'])) * $invoiceRow['amount']; 
                    $rs[$i]['totalbeforetaxinvoiced'] += ($invoiceRow['amount']-$beforeTax); 
                }else{
                    $rs[$i]['totalbeforetaxinvoiced'] += $invoiceRow['amount']; 
                }  
            }
   
        }
        
        $arrHeaderStyle['arstatusname']['textColor'] = ($hasOutstanding) ? 'C41E3A' : '568203';
        
        if ($rs[$i]['totalselling'] == 0 || $rs[$i]['totalinvoiced'] < $rs[$i]['totalselling']){ 
            $arrHeaderStyle['totalinvoiced']['textColor'] = 'FFFFFF';
            $arrHeaderStyle['totalinvoiced']['backgroundColor'] = 'C41E3A';
        }
        
        $rs[$i]['invoicecode'] = implode('<br>',$arrInvoiceCode);
        $rs[$i]['arstatusname'] = implode('<br>',$arrStatus);
         
        $arrContainerQty = $rsContainerQty[$rs[$i]['pkey']];
        //$obj->setLog($arrContainerQty,true);
        
        $totalBuying = $rs[$i]['totalcommission'] + $rs[$i]['totalbuying'] - $rs[$i]['totalcreditnote'] + $rs[$i]['totaldebitnote'];
        $rs[$i]['grossprofitpercentage'] = ($totalBuying == 0 ) ? 100 : ($rs[$i]['grossprofit'] / $totalBuying) * 100; // kalo gk ad modal sama sekali berarti profit 100%
        
        // kalo lcl dan bukan master
        if( in_array( $rs[$i]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc'])) ){ 
            if($rs[$i]['ismaster']){
                $volLCL = strval(intval($rsContainerCol[$rs[$i]['itemkey']]['volume']));
                $rs[$i]['volume'.$volLCL] = 1;     
            }else{ 
                $rs[$i]['volume20'] =  0;
                $rs[$i]['volume40'] =  0;
                $rs[$i]['volume45'] =  0;
            }
        }else{ 
            $rs[$i]['volume20'] =  $arrContainerCol[$sokey]['20'];
            $rs[$i]['volume40'] =  $arrContainerCol[$sokey]['40'];
            $rs[$i]['volume45'] =  $arrContainerCol[$sokey]['45'];
        }
         
        $arrHeaderStyle['grossprofit']['textColor'] = ($rs[$i]['grossprofit'] < 0) ? 'C41E3A' :  '568203';
        
        // has detail
        // sementara, nanti kal osudah pake shipperkey, gk perlu lg, krena gk ad pencarian didetail lg
        if($isShowDetail || !empty($detailCriteria)){  
            $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
            
            if(!empty($detailCriteria) && empty($rsDetail))  continue;
              
            if($isShowDetail) 
                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
        }
          
        //$arrTemplate[0]['dataStructure']['total']['calculateTotal'] = $calculateTotal;
        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }
    
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
    
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');   
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and '.$employee->tableName.'.issales = 1'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCreated = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');

      
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputBookingNumber'] =  $class->inputText('bookingNumber');
$arrTwigVar['inputMblNumber'] =  $class->inputText('mblNumber');
$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelCreated'] = $class->inputSelect('selCreated[]', $arrCreated, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportEMKLJobOrderExport.html', $arrTwigVar);  
 
?>