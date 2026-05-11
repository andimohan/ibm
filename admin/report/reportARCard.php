<?php	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('AR.class.php');
$ar = createObjAndAddToCol(new AR());
$arPayment = createObjAndAddToCol(new ARPayment());
$currency = createObjAndAddToCol(new Currency());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $ar;
$apPayment = $obj->getPaymentObj();
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;
 

$arrFilterInformation = array();    

$detailCriteria = '';
    
// ====================== must be set before TWIG
if (!isset($_POST['trEndDate']) || empty($_POST['trEndDate'])){  
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
$isGrouping = false; // (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

if($isGrouping){
        $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
        $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px" );
        
        if(count($rsCurrency) == 1){
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);       
            $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'totaloutstanding', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);       
        }else{ 
            foreach($rsCurrency as $currRow){
                $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'totalamount'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'totaloutstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
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
            $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"140px", 'dbfield' => 'refcode');
             
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px" );
      		
            if(count($rsCurrency) == 1){
                $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true); 
            }else{ 
                foreach($rsCurrency as $currRow){
                    $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
                    $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
                }
            } 
            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px");
          
     }
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ARCardReport'];  
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
 
if(!$isGrouping && $isShowDetail){
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

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrType = $class->convertForCombobox($obj->getARType(),'pkey','name');
 
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
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
$arrTwigVar['order'] =  $orderCriteria; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	$detailCriteria = '';
	 
	if(isset($_POST) && !empty($_POST['trEndDate'])){
		//$criteria .= ' AND '.$ap->tableName.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
    	//$criteria .= ' AND '.$ap->tablePaymentHeader.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
     	if($isShowDetail)
            $detailCriteria .= ' AND '.$arPayment->tableName.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
        array_push($arrFilterInformation,array("label" => 'Periode', 'filter' => $_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Pelanggan', 'filter' => $statusName ));
        
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
    
    //$criteria .= ' AND '.$obj->tableName.'.statuskey <> 4';  

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $obj->tableName.'.pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	$order = 'order by '.$orderBy.' ' .$orderType;  
	
    $rs = (!$isGrouping) ? $obj->searchARCard($_POST['trEndDate'],$criteria,$order) :  $obj->generateARReport($criteria,$order);
	if($isShowDetail)
           $rsPaymentDetail = $arPayment->getDetailPaymentCollections($rs, 'arkey',$detailCriteria); 

    $tempreport = '';  
	 
    for( $i=0;$i<count($rs);$i++) {   
        $arrHeaderStyle = array();
 		 if($isGrouping){   
				 	 
                $arPkey = explode (",",$rs[$i]['pkey']);
                $rsDetail = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey in ('.implode(',',$arPkey).') '.$detailCriteria); 
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
                }

                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail,'style'=> $arrDetailStyle);

        }else{
            
            if(count($rsCurrency) >= 1){
                foreach($rsCurrency as $currRow){
                    $rs[$i]['amount'.$currRow['pkey']] = 0;
                    $rs[$i]['outstanding'.$currRow['pkey']] = 0;

                    $currencykey = $rs[$i]['currencykey'];
                    $rs[$i]['amount'.$currencykey] = $rs[$i]['amount'];
                    $rs[$i]['outstanding'.$currencykey] = $rs[$i]['outstanding'];
                }
            }
              
            $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
            if(count($rsCurrency) > 1){ 
                foreach($rsCurrency as $currRow) 
                     $arrHeaderStyle['outstanding'.$currRow['pkey']]['textColor'] = '0093AF';   
            }
 	
            if($isShowDetail){
                $rsPayment = (isset($rsPaymentDetail[$rs[$i]['pkey']])) ? $rsPaymentDetail[$rs[$i]['pkey']] : array();
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

       
echo $twig->render('reportARCard.html', $arrTwigVar);   
?>