<?php	 

$obj= $ar;
$arPayment = $obj->getPaymentObj();
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(1,2);
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;

$arrFilterInformation = array();   

$detailCriteria = '';


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
$arrARType = array();
$arrARType['1'] = 'Penjualan Barang';
$arrARType['2'] = 'Penjualan Jasa';
$arrARType['3'] = 'Nota Kredit';
define('AR_IMPORT_TYPE',$arrARType); 

$arrDataStructure = array();
$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$_POST['module'] = IMPORT_TEMPLATE['ar'];

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

if($isGrouping){
    $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
    $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px");
    //$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align" => "center");
    
    if(count($rsCurrency) == 1){
        $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"150px" ,'format'=>'number',"sortable" => false,'calculateTotal' => true);       
        $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'totaloutstanding', 'width'=>"150px" ,'format'=>'number',"sortable" => false,'calculateTotal' => true);       
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
            $arrDataStructure['arType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'artypename');
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"170px" );
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number');

            break;

        default :

            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
    		$arrDataStructure['arType'] = array('title'=>ucwords($obj->lang['transactionType']),  'width'=>"150px", 'dbfield' => 'artypename');
            $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"130px", 'dbfield' => 'refcode');
            $arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"130px", 'dbfield' => 'jocode');
            $arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
            $arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"100px",'format'=>'date');
            $arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"70px",'format'=>'number');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");

            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
            $arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'refcode2', 'width'=>"300px");
           
            if(count($rsCurrency) == 1){
                $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true); 
            }else{ 
                foreach($rsCurrency as $currRow){
                    $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                    $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                }
            }
       
            $arrDataStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'tax23outstanding','width'=>"80px",'format'=>'number','calculateTotal' => true);
            $arrDataStructure['paid'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'tax23balance','width'=>"100px",'format'=>'number','calculateTotal' => true );
            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px" );
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

    }
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ARReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
switch($EXPORT_TYPE){
    case 2 :
        '';
        break;
        
    default :
    
    if($isGrouping){
        $arrDataDetailStructure = array();
        $arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
        $arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"130px", 'dbfield' => 'refcode');
        $arrDataDetailStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"130px", 'dbfield' => 'jocode'); 
        $arrDataDetailStructure['arpaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'80px'); 
        $arrDataDetailStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"80px",'format'=>'date');
        $arrDataDetailStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"60px",'format'=>'number');
        $arrDataDetailStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");

        $arrDataDetailStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'refcode2', 'width'=>"150px");
       
        $arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"40px", "align" =>"center");    
        $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"80px" ,'format'=>'number');
        $arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"110px" ,'format'=>'number');
        $arrDataDetailStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'tax23outstanding','width'=>"80px",'format'=>'number','calculateTotal' => true);
        $arrDataDetailStructure['paid'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'tax23balance','width'=>"80px",'format'=>'number','calculateTotal' => true );
        $arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"200px" );
        $arrDataDetailStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

        $arrDetailTemplate = array(); 
        $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
        $arrDetailTemplate['total'] = array();

        array_push($arrTemplate, $arrDetailTemplate);
        
    }else{
         if($isShowDetail){
                $arrDataDetailStructure = array();
                $arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['paymentCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
                $arrDataDetailStructure['arpaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
                $arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),  'dbfield' => 'currencyname', 'width'=>"60px",   'align'=>'center');
                $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"130px", 'format' => 'number' ,  'calculateTotal' => true, 'align'=>'right');

                $arrDetailTemplate = array(); 
                $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
                $arrDetailTemplate['total'] = array();

                array_push($arrTemplate, $arrDetailTemplate);
            } 
       
    }
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
//$arrTemplateCustomer = $class->convertForCombobox($templateCustomer->searchData($templateCustomer->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name','- ' .$obj->lang['chooseTemplate'].' -');
$arrType = $class->convertForCombobox($obj->getARType(),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
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
$arrTwigVar['inputSelARType'] =  $class->inputSelect('selARType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
//$arrTwigVar['inputSelTemplateCustomer'] =  $class->inputSelect('selTemplateCustomer',$arrTemplateCustomer);	
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['inputSI'] = $class->inputText('si'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 

	/*if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Pelanggan', 'filter' =>  $_POST['customerName']));
	}*/
    
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
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
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	   
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selARType'])) { 
         
       	$criteria .= ' AND '.$obj->tableName.'.artype in('.$class->oDbCon->paramString($_POST['selARType'],',').')';  
        
        $rsCriteria = $obj->getARTypeName($_POST['selARType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['transactionType'], 'filter' => $statusName ));
        
	}
    

	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	} 
        
    if(isset($_POST) && !empty($_POST['si'])) { 
        $criteria .= ' AND '.$obj->tableName.'.refcode2  LIKE ('.$class->oDbCon->paramString('%'.$_POST['si'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'S/I', 'filter' => $_POST['si']));
	}

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by '.$orderBy.' ' .$orderType;  
 
    if($isGrouping){
        $rs = $obj->generateARReport($criteria,$order);
    }else{
        $rs = $obj->searchData('','',true,$criteria,$order);
        
         // cari detail pkey invoice utk konek ke JO
        $arrRefKey = array_column($rs,'refkey');
        $rsJO = $truckingServiceOrderInvoice->searchDataRow(
                                                            array($truckingServiceOrderInvoice->tableName.'.pkey',$truckingServiceOrderInvoice->tableName.'.salesordercodecache'), 
                                                            ' and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('.$class->oDbCon->paramString($arrRefKey,',').') '
                                                            );
        $rsJO = array_column($rsJO,null,'pkey'); 
    }
    
    
    if($isShowDetail)
           $rsPaymentDetail = $arPayment->getDetailPaymentCollections($rs, 'arkey');  
     
   
	$tempreport = '';  
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
        $arrHeaderStyle = array();
        if($isGrouping){ 

                $arPkey = explode (",",$rs[$i]['pkey']);
                $rsDetail = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey in ('.implode(',',$arPkey).')'.$detailCriteria);

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

                $arrRefKey = array_column($rsDetail,'refkey');
                $rsJO = $truckingServiceOrderInvoice->searchDataRow(
                                                        array($truckingServiceOrderInvoice->tableName.'.pkey',$truckingServiceOrderInvoice->tableName.'.salesordercodecache'), 
                                                        ' and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('.$class->oDbCon->paramString($arrRefKey,',').') '
                                                        );
                $rsJO = array_column($rsJO,null,'pkey');

                for($j=0;$j<count($rsDetail);$j++){
                    $rsDetail[$j]['jocode'] = $rsJO[$rsDetail[$j]['refkey']]['salesordercodecache']; 
                    $rsDetail[$j]['datediff'] = ($rsDetail[$j]['datediff'] > 0) ? $rsDetail[$j]['datediff'] : 0; 

                    if ( $rsDetail[$j]['datediff']  > 0 ){
                                  foreach($arrDataDetailStructure as $key=>$detailStructure)
                            $arrDetailStyle[$j][$detailStructure['dbfield']]['textColor'] = 'C41E3A';  
                    }else{
                        $arrDetailStyle[$j]['outstanding']['textColor'] = '0093AF';  
                    }
                }

                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail,'style'=>$arrDetailStyle);

        }else{

            $rs[$i]['artypename'] = AR_IMPORT_TYPE[$rs[$i]['artype']]; 
            $rs[$i]['jocode'] = $rsJO[$rs[$i]['refkey']]['salesordercodecache'];

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
                    break;

                default :

                    if($isShowDetail){ 
                        //$rsPayment = $arPayment->getDetailPaymentByARKey($rs[$i]['pkey']); 
                        $rsPayment = (isset($rsPaymentDetail[$rs[$i]['pkey']] )) ?  $rsPaymentDetail[$rs[$i]['pkey']] : array();
                        
                        $rsDetail = array();
                        for ($j=0;$j<count($rsPayment);$j++){ 

                            $rsARPayment= $arPayment->getDataRowById($rsPayment[$j]['refkey']);

                            $arrTemp = array();
                            $arrTemp['code'] = $rsARPayment[0]['code'];
                            $arrTemp['trdate'] = $rsARPayment[0]['trdate'];
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
       
echo $twig->render('reportAR.html', $arrTwigVar);   
?>
