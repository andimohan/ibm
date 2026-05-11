<?php	
include '../../_config.php'; 
include '../../_include-v2.php';

includeClass('ARPayment.class.php');
$arPayment = createObjAndAddToCol( new ARPayment()); 
$ar = createObjAndAddToCol( new AR()); 

$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency());

include '_global.php';  

$obj= $ar; 
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$_POST['selStatus[]'] = array(2,3);
//$_POST['selCurrency'] = CURRENCY['idr'];

$arrFilterInformation = array();
$detailCriteria = '';

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = true; // (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px" );
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");    
$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,"sortable" => false,'format'=>'number','calculateTotal' => true);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ARCardReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'dbfield' => 'code', 'width'=>'150px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'150px', 'format' => 'string' );
$arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");    
$arrDataDetailStructure['debit'] = array('title'=>ucwords($obj->lang['debit']),  'dbfield' => 'debit', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['credit'] = array('title'=>ucwords($obj->lang['credit']),  'dbfield' => 'credit', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),  'dbfield' => 'balance', 'width'=>"120px", 'format' => 'number');
$arrDataDetailStructure['paymentMethod'] = array('title'=>ucwords($obj->lang['paymentMethod']),  'dbfield' => 'paymentmethod', 'width'=>"200px");

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "680px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);   
}

$customerCriteria = '';

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	$cardCriteria = '';

	if(isset($_POST) && !empty($_POST['trStartDate'])){
		// utk histori kartu
		$cardCriteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
 
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
		$criteria .=  ' AND '.$obj->tableName.'.customerkey in('.$key.')';  
       	$customerCriteria .= ' AND '.$customer->tableName.'.pkey in('.$key.')';  

        $rsCriteria = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name'),
											   ' and '.$customer->tableName.'.pkey in ('.$key.')'
											  );
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = $class->oDbCon->paramString($_POST['selCurrency']);
        
		$cardCriteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  
       	$criteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
	}	
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $obj->tableCustomer.'.name'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	$order = 'order by '.$orderBy.' ' .$orderType; 
	
	// saldo awal
	$startDate = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
	
	$rsStartBalance = $obj->searchARCard($startDate,$criteria,$order);
	
	$startingBalance = array();
	foreach($rsStartBalance as $key=>$row){
		$customerkey = $row['customerkey'];
		if(!isset($startingBalance[$customerkey])) $startingBalance[$customerkey]['startingbalance'] = 0;
		$startingBalance[$customerkey]['startingbalance'] += $row['outstanding'];
	}
	
	// balance akhir, buat tau ad berapa byk list data, jadi harus ada
	//$rs = $obj->searchARCard($_POST['trEndDate'],$criteria,$order);
	
	$rs = $customer->searchDataRow(array($customer->tableName.'.pkey as customerkey',$customer->tableName.'.name as customername'),
										   ' and '.$customer->tableName.'.statuskey in (2) ' . $customerCriteria
										  ); 
		
	// biar kalo pas load pertama gk perlu cari byk pkey customer
	$arrCustomerKey = (isset($_POST['selCustomer'])) ? $_POST['selCustomer'] : array();
 	$rsHistory = $obj->getCustomerARCard($arrCustomerKey,$cardCriteria);
	
	$arrPaymentKey = array();
	foreach($rsHistory as $row) 
		if($row['tabletype'] == 2) array_push($arrPaymentKey, $row['pkey']); 
		
	
	$rsHistory = $obj->reindexDetailCollections($rsHistory,'customerkey');
	
	$rsPayment = $arPayment->getPaymentMethodDetail($arrPaymentKey);
	$rsPayment = $obj->reindexDetailCollections($rsPayment,'refkey');
	
    $tempreport = '';   
    if (empty($rs)) 
     	$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
	
    //$rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
    
    // currency master
    //$rsCurrency = $currency->searchData();
    //$rsCurrency = array_column($rsCurrency,null,'pkey');
     
		
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
		$customerkey = $rs[$i]['customerkey'];
		
       	if (!isset($rsHistory[$customerkey]))  continue;
		$rsDetail = $rsHistory[$customerkey];

		// buat header sama starting balance
		$currencyName = $rsDetail[0]['currencyname'];
			
		$balance = (isset($startingBalance[$customerkey])) ? $startingBalance[$customerkey]['startingbalance'] : 0; // nanti perlu lihat saldo awal
		
		//starting balance row
		$rsDetail = array_merge(
			array( 0 => array(
				'trdate' => str_replace('\'','',$obj->oDbCon->paramDate($startDate,' / ')),
				'code' => $obj->lang['startingBalance'],
				'refcode' => '', 
				'currencyname' => $currencyName, 
				'debit' => 0, 
				'credit' => 0, 
				'balance' => $balance,
				'paymentmethod' => ''
				)
			),
			$rsDetail
		);
		
		$totalDetail = count($rsDetail);
		
		// mulai dr index 1
		for ($j=1;$j<$totalDetail;$j++){    
			if($rsDetail[$j]['amount'] > 0 )
				$rsDetail[$j]['debit'] = $rsDetail[$j]['amount'];
			else
				$rsDetail[$j]['credit'] = $rsDetail[$j]['amount'];
				 
			$balance += $rsDetail[$j]['amount'];
			$rsDetail[$j]['balance'] = $balance;
			
			$paymentMethod = array();
			if($rsDetail[$j]['tabletype'] == 2 && isset( $rsPayment[$rsDetail[$j]['pkey']] )) { 
				
				foreach( $rsPayment[$rsDetail[$j]['pkey']] as $paymentrow)
					array_push($paymentMethod,$paymentrow['paymentmethodname']); 
			}
			
			$rsDetail[$j]['paymentmethod'] =implode('<br>',$paymentMethod);
		}

		$rs[$i]['currencyname'] = $currencyName;
		$rs[$i]['outstanding'] = $balance;
			
		// has detail
		$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
        
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
   	$_POST['trStartDate'] = date('01 / 01 / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code'); 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency', $arrCurrency); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportARCardCustomer.html', $arrTwigVar);   
?> 