<?php	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';
$obj= $apPayableTax23Payment;
$ap = $obj->getAPObj();
$securityObject = 'reportARPrepaidTax23'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$_POST['selStatus[]'] = array(2,3);

$arrDateType= array(
    '1' => $obj->lang['transactionDate'], 
    '2' => $obj->lang['taxPeriod'],
);


$arrFilterInformation = array();    
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['typePPH'] = array('title'=>'Jenis PPH yang dipotong',  'width'=>"150px", 'dbfield' => 'typepph', 'align' => 'center', "sortable" => false);
$arrDataStructure['paymentMethod'] = array('title'=> 'Cara pembayaran',  'width'=>"120px", 'dbfield' => 'paymentmethod','align' => 'center', "sortable" => false);
$arrDataStructure['paymentCode'] = array('title'=> 'Nomor bukti potong / pungut',  'width'=>"160px", 'dbfield' => 'refcode',"sortable" => false);
$arrDataStructure['incomeType'] = array('title'=> 'Jenis penghasilan',  'width'=>"100px", 'dbfield' => 'incometype','align' => 'center', "sortable" => false);
$arrDataStructure['amount'] = array('title'=> 'Objek pemotongan / pemungutan',  'width'=>"200px", 'dbfield' => 'amount','align'=>'right','format'=>'integer',"sortable" => false);
$arrDataStructure['tax'] = array('title'=> 'PPh yang dipotong / dipungut',  'width'=>"200px", 'dbfield' => 'grandtotal', 'align' => 'right','format'=>'integer',  "sortable" => false);
$arrDataStructure['trdate'] = array('title' => 'Tanggal bukti potong / pungut',  'width'=>"200px", 'dbfield' => 'trdate', 'format' => 'date', 'align' => 'center', "sortable" => false);
$arrDataStructure['taxIdentificationNumber'] = array('title'=> 'NPWP pemotong / pemungut',  'width'=>"200px", 'dbfield'=>'suppliertaxid', "sortable" => false);
$arrDataStructure['namePPH'] = array('title'=> 'Nama pemotong / pemungut',  'width'=>"200px", 'dbfield' => 'suppliername',"sortable" => false);
$arrDataStructure['address'] = array('title'=> 'Alamat pemotong / pemungut',  'width'=>"200px", 'dbfield' => 'customeraddress',"sortable" => false);
$arrDataStructure['mapcode'] = array('title' => 'Kode MAP / iuran pembayaran',  'width'=>"200px", 'dbfield' => 'mapcode',"sortable" => false);
$arrDataStructure['ntpnCode'] = array('title'=> 'NTPN',  'width'=>"160px", 'dbfield' => 'ntpn',"sortable" => false);
$arrDataStructure['totalPayment'] = array('title'=>'Jumlah pembayaran','dbfield' => 'paymentamount', 'width'=>"150px" , "sortable" => false);
$arrDataStructure['paymentDate'] = array('title'=>'Tanggal setor','dbfield' => 'paymentdate', 'width'=>"120px", "sortable" => false);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['payableTax23PaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
    

    if(isset($_POST) && !empty($_POST['selDateType'])){
        
        if($_POST['selDateType'] == 1){
            $criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
            array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
        }else if($_POST['selDateType'] == 2){
            $startPeriod = date('01 / m / Y',strtotime($_POST['startTaxPeriod']));  
            $endPeriod = date('01 / m / Y',strtotime($_POST['endTaxPeriod']));  
            $criteria .= ' and '.$obj->tableName.'.taxperiod between '.$class->oDbCon->paramDate($startPeriod,' / ').' AND '.$class->oDbCon->paramDate( $endPeriod,' / '); 
            array_push($arrFilterInformation,array("label" => 'Masa Pajak', 'filter' => $_POST['startTaxPeriod'] . ' - ' .$_POST['endTaxPeriod'] ));
        }
        
    }
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pemasok', 'filter' => $statusName ));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= 'AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
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
 
    $tempreport = '';  

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

	 
    for( $i=0;$i<count($rs);$i++) {   
            $rs[$i]['typepph'] = '23';
            $rs[$i]['paymentmethod'] = '2';
            $rs[$i]['incometype'] = '6'; 

            $percentage = (!empty($rs[$i]['suppliertaxid'])) ? 0.02 : 0.04;
            $rs[$i]['amount'] = $rs[$i]['grandtotal'] / $percentage; 
            
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
    
	$_POST['startTaxPeriod'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['endTaxPeriod'] = date('F Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');


$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartTaxPeriod'] = $class->inputMonth('startTaxPeriod', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndTaxPeriod'] = $class->inputMonth('endTaxPeriod', array('etc' => 'style="text-align:center"'));   

$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));    
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    
echo $twig->render('reportAPPayableTax23PaymentTemplate.html', $arrTwigVar);   
?>
