<?php	 
 
$obj= $apCommission;
$apCommissionPayment = $obj->getPaymentObj();
$securityObject = 'reportAPCommission'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(1,2);

$arrFilterInformation = array();    

$detailCriteria = '';
$objCriteria = '';
$joCriteria = '';
    
$arrDateType= array(
    '1' => $obj->lang['transactionDate'], 
    '2' => $obj->lang['etd']
);


// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;

// ====================== must be set before TWIG

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

/*
$arrAPType = array();
$arrAPType['1'] = 'Pembelian Barang';
$arrAPType['2'] = 'Outsource Jasa'; 
$arrAPType['3'] = 'Komisi Ritase'; 
$arrAPType['4'] = 'Komisi Penjualan'; 
$arrAPType['5'] = 'Biaya Maintenance (DN)'; 
$arrAPType['6'] = 'Biaya Lain (DN)';  
 
define('AP_IMPORT_TYPE',$arrAPType); */

$arrDataStructure = array();
$isGrouping = false;// (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$_POST['module'] = IMPORT_TEMPLATE['ap'];
 
$EMKLPoObj = $class->getTableKeyAndObj($emklPurchaseOrderExport->tableName);
$EMKLComObj = $class->getTableKeyAndObj($emklCommission->tableName);

$arrEMKLObj = array(); 
$arrEMKLObj[$EMKLPoObj['key']] = $EMKLPoObj['obj'];
$arrEMKLObj[$EMKLComObj['key']] = $EMKLComObj['obj']; 
$arrEMKLObjKey = array_keys($arrEMKLObj);

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);


if($isGrouping){
        $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
        $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"300px",'mergeExcelCell' => 12 );
        
        if(count($rsCurrency) == 1){
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);       
            $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'totaloutstanding', 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);       
        }else{ 
            foreach($rsCurrency as $currRow){
                $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'totalamount'.$currRow['pkey'],"sortable" => false, 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'totaloutstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);
            }
        }
    
        $arrDataStructure['dummy'] = array('title'=>'','dbfield' => '', 'width'=>"900px",'sortable' => false);       
}else{ 
     switch($EXPORT_TYPE){
        
        case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
            //$arrDataStructure['apType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'aptypename');
            $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"170px" );
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number');

            break;

        default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
            //$arrDataStructure['apType'] = array('title'=>ucwords($obj->lang['transactionType']),  'width'=>"150px", 'dbfield' => 'aptypename');
 
            $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']) ,  'width'=>"130px", 'dbfield' => 'refcode', );
           
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"60px", 'format' => 'number');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"130px" );
            $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"200px" );
            
            $arrDataStructure['emkljocode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'reftranscode', 'width'=>"120px");
            $arrDataStructure['emklbookingnumber'] = array('title'=>ucwords($obj->lang['bookingNumber']),'dbfield' => 'bookingnumber', 'width'=>"120px");
            $arrDataStructure['emklmbl'] = array('title'=>'MBL','dbfield' => 'mblnumber', 'width'=>"120px");
            $arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"100px",  'format'=>'date');
            $arrDataStructure['pod'] = array('title'=>ucwords($obj->lang['pod']),'dbfield' => 'podname', 'width'=>"100px");
            $arrDataStructure['shipper'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'customername', 'width'=>"150px");
            $arrDataStructure['containernumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"170px" , "sortable" => false);
      		$arrDataStructure['invoiceReference'] = array('title'=>ucwords($obj->lang['invoiceReference']),'dbfield' => 'refinvoicecode', 'width'=>"120px", "sortable" => false);
            //$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'width'=>"130px", 'dbfield' => 'amountidr','format'=>'number','calculateTotal' => true);
            
             if(count($rsCurrency) == 1){
                $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true); 
            }else{ 
                foreach($rsCurrency as $currRow){
                    $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                    $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                }
            } 
            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"250px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );

     }
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['apCommissionReport'];  
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
switch($EXPORT_TYPE){
    case 2 :  
        break;
        
    default :
        
        if($isGrouping){
             
            $arrDataDetailStructure = array();
            $arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
 
			$arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']). ' 1',  'width'=>"100px", 'dbfield' => 'refcode' );
			$arrDataDetailStructuretructure['refcode2'] = array('title'=>ucwords($obj->lang['refCode']). ' 2',  'width'=>"100px", 'dbfield' => 'refcode2');
           

            $arrDataDetailStructure['appaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
            $arrDataDetailStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"120px",'format'=>'date');
            $arrDataDetailStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"60px", 'format' => 'number');
            $arrDataDetailStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" ); 
                
            $arrDataDetailStructure['emkljocode'] = array('title'=>ucwords($obj->lang['soCode']),'dbfield' => 'reftranscode', 'width'=>"80px", "sortable" => false );
            $arrDataDetailStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"80px", "sortable" => false,'format'=>'date');
            $arrDataDetailStructure['containernumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"170px" , "sortable" => false);
      		$arrDataDetailStructure['invoiceReference'] = array('title'=>ucwords($obj->lang['invoiceReference']),'dbfield' => 'refinvoicecode', 'width'=>"120px", "sortable" => false);
   
            $arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align" =>"center");    
            $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"110px" ,'format'=>'number','calculateTotal' => true);
            $arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"110px" ,'format'=>'number','calculateTotal' => true);
            $arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"250px");
            $arrDataDetailStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px" );

            $arrDetailTemplate = array(); 
            $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
            $arrDetailTemplate['total'] = array();

            array_push($arrTemplate, $arrDetailTemplate); 

        }else{ 
            
            if($isShowDetail){
                $arrDataDetailStructure = array();
                $arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['paymentCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' );
                $arrDataDetailStructure['appaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
                $arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),  'dbfield' => 'currencyname', 'width'=>"60px",   'align'=>'center');
                $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"110px", 'format' => 'number' , 'calculateTotal' => true);

                $arrDetailTemplate = array(); 
                $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
                $arrDetailTemplate['total'] = array();

                array_push($arrTemplate, $arrDetailTemplate);
            }
        }
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrType = $class->convertForCombobox($obj->getAPType(),'pkey','name');


$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputRefCode2'] =  $class->inputText('refCode2');
$arrTwigVar['inputContainerNumber'] =  $class->inputText('containerNumber');
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
//$arrTwigVar['inputHidSupplierKey'] = $class->inputHidden('hidSupplierKey');
//$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType ); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputTemplateSupplier'] = $class->inputAutoComplete(array(   
                                                                        'element' => array('value' => 'selTemplateSupplier',
                                                                                           'key' => 'hidTemplateSupplierKey'),
                                                                        'source' => array(
                                                                                            'url' => '../ajax-template-supplier.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        ), 
                                                                        'placeholder' => $obj->lang['searchTemplate'].'...',
                                                                        'callbackFunction' => 'updateSupplier(this)' 
                                                                      ));  
$arrTwigVar['inputSelAPType'] =  $class->inputSelect('selAPType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
$arrTwigVar['order'] =  $orderCriteria; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
    if(isset($_POST) && !empty($_POST['refCode2'])) {
		$criteria .= ' AND '.$obj->tableJobOrder.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode2'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode JO', 'filter' => $_POST['refCode2']));
	} 
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        $dateCriteria = ' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');
        $label = '';
        switch($_POST['selDateType']){
            case '1' :  $criteria .= ' and  '.$obj->tableName.'.trdate'.$dateCriteria;  
                        $label = $obj->lang['transactionDate'];
                        break;
            case '2' :  $criteria .= ' and  '.$obj->tableJobOrder.'.etdpol'.$dateCriteria;  
                        $label = $obj->lang['etd'];
                        break;   
        }
	    
        array_push($arrFilterInformation,array("label" => $label, 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    
     if(isset($_POST) && !empty($_POST['contianerNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode2 LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode2'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode JO', 'filter' => $_POST['refCode2']));
	}

	/*if(isset($_POST) && !empty($_POST['supplierName'])) {
		$criteria .= ' AND '.$obj->tableSupplier.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Pemasok', 'filter' =>  $_POST['supplierName']));
	}*/
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Pemasok', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
	}	
    
	if(isset($_POST) && !empty($_POST['chkDueDate'])){  
        
        if($isGrouping){ 
			$detailCriteria .= ' and DATEDIFF(NOW(),duedate) > 0';
			$criteria .= ' and DATEDIFF(NOW(),duedate) > 0';
        }else{  
            $criteria .= ' having datediff > 0';
		}
        
        array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
    }
    
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selAPType'])) {  
        
       	$criteria .= ' AND '.$obj->tableName.'.aptype in('.$class->oDbCon->paramString($_POST['selAPType'],',').')';  
        
        $rsCriteria = $obj->getAPTypeName($_POST['selAPType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['transactionType'], 'filter' => $statusName ));
        
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

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	$order = 'order by '.$orderBy.' ' .$orderType;  
	
    $rs = (!$isGrouping) ? $obj->searchData('','',true,$criteria,$order) :  $obj->generateAPCommissionReport($criteria,$order);
	if($isShowDetail && !$isGrouping)
           $rsPaymentDetail = $apCommissionPayment->getDetailPaymentCollections($rs, 'apkey'); 
            
    $tempreport = '';   
    
    for( $i=0;$i<count($rs);$i++) {   
        $arrHeaderStyle = array();
 		 if($isGrouping){   
				 	 
				 	$apPkey = explode (",",$rs[$i]['pkey']);
                    $rsDetail = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey in ('.implode(',',$apPkey).') '.$detailCriteria); 
                    $arrDetailStyle = array();
             
                    if(count($rsCurrency) >= 1){
                        foreach($rsCurrency as $currRow){
                            $rs[$i]['totalamount'.$currRow['pkey']] = 0;
                            $rs[$i]['totaloutstanding'.$currRow['pkey']] = 0;

                            $currencykey = $rs[$i]['currencykey'];
                            $rs[$i]['totalamount'.$currencykey] = $rs[$i]['totalamount'];
                            $rs[$i]['totaloutstanding'.$currencykey] = $rs[$i]['totaloutstanding'];
                        }
                    }
                 
                    for($j=0;$j<count($rsDetail);$j++){  
                        $rsDetail[$j]['datediff'] = ($rsDetail[$j]['datediff'] > 0) ? $rsDetail[$j]['datediff'] : 0;
                        
                        if ($rsDetail[$j]['datediff']  > 0 ){ 
                            foreach($arrDataDetailStructure as $key=>$detailStructure)
                                $arrDetailStyle[$j][$detailStructure['dbfield']]['textColor'] = 'C41E3A';   
                        }else{
                            $arrDetailStyle[$j]['outstanding']['textColor'] = '0093AF'; 
                        }
                        
                       if(in_array($rsDetail[$j]['reftabletype'],$arrEMKLObjKey)){

                           $reftabletype = $rsDetail[$j]['reftabletype'];
                           $refheaderkey = $rsDetail[$j]['refheaderkey'];

                           $refObj = $arrEMKLObj[$reftabletype];

                           // ambil data dari PO / Refund utk dapetin refkey agar bisa link ke JO
                           /*$rsObj = $refObj->getDataRowById($refheaderkey);  
                           $rsJO = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsObj[0]['refkey'],true,$joCriteria);*/

                           //$rsDetail[$j]['emkljocode'] = $rsJO[0]['code'];
                           //$rsDetail[$j]['etdpol'] = $rsJO[0]['etdpol'];
                           //$rsDetail[$j]['containernumber'] = $rsJO[0]['containernumber'];
                           //$rsDetail[$j]['refinvoicecode'] = $rsObj[0]['refinvoicecode']; 

                       }

					}
                 
                    $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail,'style'=> $arrDetailStyle);

        }else{
                //$rs[$i]['aptypename'] = AP_IMPORT_TYPE[$rs[$i]['aptype']];
        
               // kalo jenis AP berasal dr EMKL
               // sementara asumsi, PO dan Refund refkey nya berasal semua dr JO
               //$obj->setLog( $rs[$i],true);
               if(in_array($rs[$i]['reftabletype'],$arrEMKLObjKey)){
                 
                   $reftabletype = $rs[$i]['reftabletype'];
                   $refheaderkey = $rs[$i]['refheaderkey'];
                    
                   $refObj = $arrEMKLObj[$reftabletype];
                   
                   // ambil data dari PO / Refund utk dapetin refkey agar bisa link ke JO
                   /*$rsObj = $refObj->getDataRowById($refheaderkey);  
                   $rsJO = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsObj[0]['refkey'],true,$joCriteria);*/
                   
                   //$rs[$i]['emkljocode'] = $rsJO[0]['code'];
                   //$rs[$i]['etdpol'] = $rsJO[0]['etdpol'];
                   //$rs[$i]['containernumber'] = $rsJO[0]['containernumber'];
                   //$rs[$i]['refinvoicecode'] = $rsObj[0]['refinvoicecode']; 
                
               }
             
                if(count($rsCurrency) >= 1){
                    foreach($rsCurrency as $currRow){
                        $rs[$i]['amount'.$currRow['pkey']] = 0;
                        $rs[$i]['outstanding'.$currRow['pkey']] = 0;
                        
                        $currencykey = $rs[$i]['currencykey'];
                        $rs[$i]['amount'.$currencykey] = $rs[$i]['amount'];
                        $rs[$i]['outstanding'.$currencykey] = $rs[$i]['outstanding'];
                    }
                }

            $rs[$i]['datediff'] = ($rs[$i]['datediff'] > 0) ? $rs[$i]['datediff'] : 0; 

            if ($rs[$i]['datediff']  > 0 ){
                foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                    if (isset($el['dbfield']))
                        $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
            }else{
                $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
                  if(count($rsCurrency) > 1){ 
                        foreach($rsCurrency as $currRow) 
                             $arrHeaderStyle['outstanding'.$currRow['pkey']]['textColor'] = '0093AF';   
                    }
            }

            switch($EXPORT_TYPE){
                    case 2 :
                        '';
                        break;

                    default : 
                        if($isShowDetail){
                           $rsPayment = (isset($rsPaymentDetail[$rs[$i]['pkey']])) ? $rsPaymentDetail[$rs[$i]['pkey']] : array();
                           $rsDetail = array();
                            for ($j=0;$j<count($rsPayment);$j++){ 

                                $rsApPayment= $apCommissionPayment->getDataRowById($rsPayment[$j]['refkey']);

                                $arrTemp = array();
                                $arrTemp['code'] = $rsApPayment[0]['code'];
                                $arrTemp['trdate'] = $rsApPayment[0]['trdate'];
                                $arrTemp['currencyname'] = $rsPayment[$j]['currencyname'];
                                $arrTemp['amount'] = $rsPayment[$j]['amount']; 

                                array_push($rsDetail, $arrTemp);
                            }
                            // has detail
                            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                        }
          }
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

      }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
 
}

       
echo $twig->render('@custom/reportAPCommission.html', $arrTwigVar);   
?>
