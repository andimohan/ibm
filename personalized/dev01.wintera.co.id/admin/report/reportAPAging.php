<?php	 

$obj= $ap; 
$securityObject = 'reportAP'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);
    
$arrFilterInformation = array();    
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['supplierName'] = array('title'=>ucwords($obj->lang['supplier']),  'width'=>"420px", 'dbfield' => 'suppliername',"sortable" => false, 'mergeExcelCell' => 2);
if(count($rsCurrency) == 1){
    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
    $arrDataStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
    $arrDataStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);
}else{
    foreach($rsCurrency as $currRow) 
        $arrDataStructure['amount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['total']). ' ' .$currRow['name'],'dbfield' => 'totalamount'.$currRow['pkey'], 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
    
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
     

}
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['APAgingReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if ($isShowDetail){ 
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'apcode', 'width'=>'165px', 'format' => 'string' ); 
    $arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'170px', 'format' => 'string' ); 
    $arrDataDetailStructure['refInvoiceCode'] = array('title'=>ucwords($obj->lang['invoiceReference']),  'dbfield' => 'refinvoicecode', 'width'=>'120px', 'format' => 'string' ); 
    $arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'trdate', 'width'=>"80px",'format'=>'date');
    if(count($rsCurrency) == 1){
        $arrDataDetailStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
        $arrDataDetailStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
        $arrDataDetailStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
        $arrDataDetailStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
        $arrDataDetailStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);
    }else{
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
    
    array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' =>  date('d / m / Y') ));
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['supplier'], 'filter' => $statusName ));
        
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
 
	$order = 'order by suppliername asc'; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);

    $rAPBySupplier = array();
    
    // susun ulang list AP group by supplier
    foreach($rs as $row){
         $supplierkey = $row['supplierkey'];
         $datediff = $row['datediff']; 
         $outstanding = $row['outstanding'];

        if (!isset($rAPBySupplier[$supplierkey])) { 
                         
            $rAPBySupplier[$supplierkey] = array('suppliername' => $row['suppliername'], 'detail' => array());
            
            if(count($rsCurrency) == 1){
                $rAPBySupplier[$supplierkey] = array_merge($rAPBySupplier[$supplierkey], array( 
                                                  'notduedays' => 0,
                                                  '30days' => 0,
                                                  '60days' => 0,
                                                  '90days' => 0,
                                                  'moreThan90days' => 0,
                                                  'totalamount' => 0 
                                                ));
            }else{  
                 foreach($rsCurrency as $currRow){
                     $rAPBySupplier[$supplierkey] = array_merge($rAPBySupplier[$supplierkey], array( 
                                                  'notduedays'.$currRow['pkey'] => 0,
                                                  '30days'.$currRow['pkey'] => 0,
                                                  '60days'.$currRow['pkey'] => 0,
                                                  '90days'.$currRow['pkey'] => 0,
                                                  'moreThan90days'.$currRow['pkey'] => 0,
                                                  'totalamount'.$currRow['pkey'] => 0 
                                                ));
                 }
            }        }
 
        $arrAging = array();
         

         if(count($rsCurrency) == 1){
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
            
            $rAPBySupplier[$supplierkey]['totalamount'] += $outstanding;  
         }else{
             foreach($rsCurrency as $currRow){
                 $currencykey = $row['currencykey']; 
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
            
             $rAPBySupplier[$supplierkey]['totalamount'.$currencykey] += $outstanding;  
         }
         
        
       // if ($isShowDetail){
            $arrDetail = array(); 
            foreach($arrAging as $agingkey => $agingValue) { 
                $rAPBySupplier[$supplierkey][$agingkey] += $agingValue;
                $arrDetail[$agingkey] = $agingValue;
            }

            $arrDetail['apcode'] =  $row['code'];
            $arrDetail['refcode'] =  $row['refcode'];
            $arrDetail['refinvoicecode'] =  $row['refinvoicecode'];
            $arrDetail['trdate'] =  $row['trdate'];

            array_push($rAPBySupplier[$supplierkey]['detail'], $arrDetail);
       // }
         
    }

    $tempreport = '';  
    if (empty($rAPBySupplier)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    
    // disini generate report seperti biasa
    
    foreach($rAPBySupplier as $supplierkey => $row){
        
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
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
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

$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
       
echo $twig->render('reportAPAging.html', $arrTwigVar);   
?>