<?php

$emklPurchaseOrderExport = createObjAndAddToCol( new EMKLPurchaseOrder()); 
$emklCommission = createObjAndAddToCol( new EMKLCommission()); 
$emklJobOrder = createObjAndAddToCol( new EMKLJobOrder()); 
$emklJobOrderHeader = createObjAndAddToCol( new EMKLJobOrderHeader()); 
    
$obj= $apPayment;
$ap = $obj->getAPObj();
$securityObject = 'reportAPPayment'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$_POST['selStatus[]'] = array(2,3);
$joCriteria = '';

$arrFilterInformation = array();    
$detailCriteria = '';
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
 
$EMKLPoObj = $class->getTableKeyAndObj($emklPurchaseOrderExport->tableName);
$EMKLComObj = $class->getTableKeyAndObj($emklCommission->tableName);

$arrEMKLObj = array(); 
$arrEMKLObj[$EMKLPoObj['key']] = $EMKLPoObj['obj'];
$arrEMKLObj[$EMKLComObj['key']] = $EMKLComObj['obj']; 
$arrEMKLObjKey = array_keys($arrEMKLObj);


$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"170px" , 'mergeExcelCell' => 2);
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"130px" ); 
//$arrDataStructure['shipper'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'shippername', 'width'=>"150px");
$arrDataStructure['rate'] = array('title'=>ucwords($obj->lang['rate']),'dbfield' => 'rate', 'width'=>"130px" ,'format'=>'number');
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");
$arrDataStructure['paidtotal'] = array('title'=>ucwords($obj->lang['payingOffAmount']),'dbfield' => 'totalpaid', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totaldiscount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'totaldiscount', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataStructure['downpayment'] = array('title'=>ucwords($obj->lang['downpayment']),'dbfield' => 'totaldownpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'totalcost', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['payableTax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'payabletax23', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['paymentAmount']),'dbfield' => 'totalpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trnotes','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['accountsPayablePaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 

$arrDataDetailStructure = array();
$arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'apcode', 'width'=>'100px', 'format' => 'string' );
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'apdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'dbfield' => 'jocode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refcode2'] = array('title'=>ucwords($obj->lang['invoiceReference']),  'dbfield' => 'refcode2', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"100px", "sortable" => false,'format'=>'date');
//$arrDataDetailStructure['shipper'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'shippername', 'width'=>"150px");
$arrDataDetailStructure['containernumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"170px" , "sortable" => false);
$arrDataDetailStructure['rate'] = array('title'=>ucwords($obj->lang['rate']),  'dbfield' => 'rate', 'width'=>"120px", 'format' => 'number');
$arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");    
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amountap', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),  'dbfield' => 'outstanding', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'discount', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['payment'] = array('title'=>ucwords($obj->lang['payingSettlement']),  'dbfield' => 'paymentamount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),  'dbfield' => 'taxamount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "900px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);
}


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
	
	/*if(isset($_POST) && !empty($_POST['supplierName'])) {
		$criteria .= ' AND '.$obj->tableSupplier.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Pemasok', 'filter' =>  $_POST['supplierName']));
	}*/
    
	
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Pemasok', 'filter' => $statusName ));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
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
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
	$arrCurrencyCalculate = array_unique(array_column($rs,'currencykey'));
	$arrUnsetTotal = array('paidtotal','totaldiscount','downpayment','cost','payableTax23','totalPayment');
	if(count($arrCurrencyCalculate)>1){
		foreach($arrUnsetTotal as $row)
		unset($arrTemplate[0]['dataStructure'][$row]['calculateTotal']);
	} 
    $tempreport = '';  

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
	 
    // currency master
    $rsCurrency = $currency->searchData();
    $rsCurrency = array_column($rsCurrency,null,'pkey');
     
    // ambil semua detail AP dulu
    $arrDetailAPKey = array(); 
    foreach($rsDetailCol as $detailColRow) 
        $arrDetailAPKey = array_merge($arrDetailAPKey, array_column($detailColRow,'apkey')); 
      
    $arrDetailAPKey = array_values(array_unique($arrDetailAPKey));
        
    $rsAPCol = $ap->searchDataRow( array($ap->tableName.'.pkey', 
                                      $ap->tableName.'.code', 
                                      $ap->tableName.'.refcode', 
                                      $ap->tableName.'.trdate', 
                                      $ap->tableName.'.amount',  
                                      $ap->tableName.'.rate',  
                                      $ap->tableName.'.reftabletype',  
                                      $ap->tableName.'.refheaderkey',  
                                      $ap->tableName.'.currencykey'), 
                                   ' and ' . $ap->tableName.'.pkey in ('.$class->oDbCon->paramString($arrDetailAPKey,',').')' 
                              ); 
    $rsAPCol = array_column($rsAPCol,null,'pkey'); 
    
    // semua AP key per komisi / per PO
    // semua AP PO <-- ini nanti kebagi lg
    $rsAPByTypeCol = array(); 
    foreach($rsAPCol as $pkey=>$apRow){
         $tabletpye = $apRow['reftabletype'];
         if(!isset($rsAPByTypeCol[$tabletpye])) $rsAPByTypeCol[$tabletpye] = array();
        
         array_push($rsAPByTypeCol[$tabletpye], $apRow['refheaderkey']);
    }
    
    // search data berdasarkaan jenis komisi / PO
    $refObjCol = array();
    $arrRefJOKey = array();
    $arrRefJOHeaderKey = array();
    foreach($arrEMKLObj as $reftabletype=>$emklObj){ 
        // kalo gk ad AP dr tipe table tertentu
        if (!isset($rsAPByTypeCol[$reftabletype])) continue;
        
        $refObj = $arrEMKLObj[$reftabletype];  
        
        $arrColumn = array($refObj->tableName.'.pkey', $refObj->tableName.'.refkey');
        if($reftabletype == $EMKLPoObj['key']) { 
            array_push($arrColumn, $refObj->tableName.'.refinvoicecode');
            array_push($arrColumn, $refObj->tableName.'.refjoheaderkey');
        }
        
        $refObjCol[$reftabletype] = $refObj->searchDataRow( $arrColumn,
                                                        ' and '.$refObj->tableName.'.pkey in ('.$class->oDbCon->paramString($rsAPByTypeCol[$reftabletype],',').')'
                                                      );  
        
        $arrRefJOKey = array_merge($arrRefJOKey, array_column($refObjCol[$reftabletype],'refkey'));
        $arrRefJOHeaderKey = array_merge($arrRefJOHeaderKey, array_column($refObjCol[$reftabletype],'refjoheaderkey'));
        
        $refObjCol[$reftabletype] = array_column($refObjCol[$reftabletype],null,'pkey');
        
    }
    
    $arrRefJOKey = array_unique($arrRefJOKey);    
    $arrRefJOHeaderKey = array_unique($arrRefJOHeaderKey);
    
    // ambil semua JO & Header yang berhubungan dengan Komisi / PO diatas
    $rsJOCol =  $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey', $emklJobOrder->tableName.'.code', $emklJobOrder->tableName.'.etdpol', $emklJobOrder->tableName.'.containernumber'),
                                              ' and '.$emklJobOrder->tableName.'.pkey in ('.$class->oDbCon->paramString($arrRefJOKey,',').')'
                                            );
    $rsJOCol = array_column($rsJOCol,null,'pkey');
    
     
    $rsJOHeaderCol =  $emklJobOrderHeader->searchDataRow(array($emklJobOrderHeader->tableName.'.pkey', $emklJobOrderHeader->tableName.'.code', $emklJobOrderHeader->tableName.'.etdpol', $emklJobOrderHeader->tableName.'.containernumber'),
                                              ' and '.$emklJobOrderHeader->tableName.'.pkey in ('.$class->oDbCon->paramString($arrRefJOHeaderKey,',').')'
                                            );
    $rsJOHeaderCol = array_column($rsJOHeaderCol,null,'pkey');
 
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {    

        $pkey = $rs[$i]['pkey'];
        
        if($isShowDetail){ 
            if (!isset($rsDetailCol[$pkey]))  continue;
                $rsDetail = $rsDetailCol[$pkey]; 
        
        //$paymmentamount= 0;
        for ($j=0;$j<count($rsDetail);$j++){    
            
            $rsAP = $rsAPCol[$rsDetail[$j]['apkey']];

             if(in_array($rsAP['reftabletype'],$arrEMKLObjKey)){

                   $reftabletype = $rsAP['reftabletype'];
                   $refheaderkey = $rsAP['refheaderkey'];

                   //$refObj = $arrEMKLObj[$reftabletype];

                   // ambil data dari PO / Refund utk dapetin refkey agar bisa link ke JO
                   //$rsObj = $refObj->getDataRowById($refheaderkey);  
                   $rsObj = $refObjCol[$reftabletype][$refheaderkey]; 
                 
                   // ini bisa dr header skrg
                   //$rsJO = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsObj['refkey'],true,$joCriteria);
                   
                   // kalo kosong. coba ambil dr header JO
                   $rsJO  = (isset($rsObj['refkey']) && !empty($rsObj['refkey'])) ? $rsJOCol[$rsObj['refkey']] : $rsJOHeaderCol[$rsObj['refjoheaderkey']];
                        
                   $rsDetail[$j]['jocode'] = $rsJO['code'];
                   $rsDetail[$j]['etdpol'] = $rsJO['etdpol'];
                   $rsDetail[$j]['containernumber'] = $rsJO['containernumber'];
                   $rsDetail[$j]['refcode2'] = (isset($rsObj['refinvoicecode'])) ? $rsObj['refinvoicecode'] : '';  

               }


            //$total = $rsDetail[$j]['amount'] - $rsDetail[$j]['taxamount'];
            $rsDetail[$j]['apcode'] =  $rsAP['code'];
            $rsDetail[$j]['refcode'] =  $rsAP['refcode'];
            //$rsDetail[$j]['refcode2'] =  $rsAP['refcode2'];
            $rsDetail[$j]['apdate'] =  $rsAP['trdate'];
            $rsDetail[$j]['amountap'] =  $rsAP['amount'];
            $rsDetail[$j]['rate'] =  $rsAP['rate'];   
            $rsDetail[$j]['currencyname'] =  $rsCurrency[$rsAP['currencykey']]['name']; 
            $rsDetail[$j]['paymentamount'] =  $rsDetail[$j]['amount'] - $rsDetail[$j]['taxamount']; 
            //$amountpayment+=$total;
        }

        //$rs[$i]['paymentamount'] = $amountpayment ;
        
        // has detail
        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
            
        }
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

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
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputHidSupplierKey'] = $class->inputHidden('hidSupplierKey');
//$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));    
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportAPPayment.html', $arrTwigVar);   
?>
