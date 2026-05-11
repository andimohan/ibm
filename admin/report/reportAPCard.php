<?php	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('AP.class.php');
$ap = createObjAndAddToCol(new AP());
$apPayment = createObjAndAddToCol(new APPayment());
$currency = createObjAndAddToCol(new Currency());
$supplier = createObjAndAddToCol(new Supplier());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $ap;
$apPayment = $obj->getPaymentObj();
$securityObject = 'reportAP'; // the value of security object is manually inserted to handle 
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
$arrAPType = array();
$arrAPType['1'] = 'Pembelian Barang';
$arrAPType['2'] = 'Outsource Jasa'; 
$arrAPType['3'] = 'Komisi Ritase'; 
$arrAPType['4'] = 'Komisi Penjualan'; 
$arrAPType['5'] = 'Biaya Maintenance (DN)'; 
$arrAPType['6'] = 'Biaya Lain (DN)';  
 
define('AP_IMPORT_TYPE',$arrAPType); 

$arrDataStructure = array();
// kalo grouping diaktifkan, harus ganti gk boleh pake generateAPReport, karena kena limit group_concat
$isGrouping = false; // (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

if($isGrouping){
        $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
        $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"300px" );
        
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
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"120px", 'dbfield' => 'code');
            $arrDataStructure['apType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'aptypename');
            $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"170px" );
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number');

            break;

        default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"120px", 'dbfield' => 'code');
            $arrDataStructure['apType'] = array('title'=>ucwords($obj->lang['transactionType']),  'width'=>"150px", 'dbfield' => 'aptypename');

            if(PLAN_TYPE['categorykey'] == 2){
                $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']). ' 1',  'width'=>"130px", 'dbfield' => 'refcode', );
                $arrDataStructure['refcode2'] = array('title'=>ucwords($obj->lang['refCode']). ' 2',  'width'=>"130px", 'dbfield' => 'refcode2');
                $arrDataStructure['refinvoicecode'] = array('title'=>ucwords($obj->lang['invoiceReference']) , 'width'=>"120px", 'dbfield' => 'refinvoicecode');
            }else{
                $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"100px", 'dbfield' => 'refcode');
            }

            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            //$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"120px",'format'=>'date');
            //$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"60px", 'format' => 'number');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
            $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"250px" );
      		
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
$arrHeaderTemplate['reportTitle'] = $obj->lang['APCardReport'];  
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if(!$isGrouping && $isShowDetail){
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

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrType = $class->convertForCombobox($obj->getAPType(),'pkey','name');
 
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
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
    	$detailCriteria = '';
	 
	if(isset($_POST) && !empty($_POST['trEndDate'])){
		//$criteria .= ' AND '.$ap->tableName.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
    	//$criteria .= ' AND '.$ap->tablePaymentHeader.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
	   if($isShowDetail) 
            $detailCriteria .= ' AND '.$apPayment->tableName.'.trdate <= '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');  
            
        array_push($arrFilterInformation,array("label" => 'Periode', 'filter' => $_POST['trEndDate'] ));
        
	}
    
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
    
    if(isset($_POST) && !empty($_POST['selAPType'])) {  
        
       	$criteria .= ' AND '.$obj->tableName.'.aptype in('.$class->oDbCon->paramString($_POST['selAPType'],',').')';  
        
        $rsCriteria = $obj->getAPTypeName($_POST['selAPType']);
	 
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
	
    $rs = (!$isGrouping) ? $obj->searchAPCard($_POST['trEndDate'],$criteria,$order) :  $obj->generateAPReport($criteria,$order);
	if($isShowDetail) $rsPaymentDetail = $apPayment->getDetailPaymentCollections($rs, 'apkey',$detailCriteria); 
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

                    $rsAPPayment= $apPayment->getDataRowById($rsPayment[$j]['refkey']);

                    $arrTemp = array();
                    $arrTemp['code'] = $rsAPPayment[0]['code'];
                    $arrTemp['trdate'] = $rsAPPayment[0]['trdate'];
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

       
echo $twig->render('reportAPCard.html', $arrTwigVar);   
?>
