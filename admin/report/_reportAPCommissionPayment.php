<?php	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $apCommissionPayment;
$ap = $obj->getAPObj();
$securityObject = 'reportAPCommissionPayment'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();    


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"170px" , 'mergeExcelCell' => 2);  
$arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['paymentAmount']),'dbfield' => 'totalpayment', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trnotes','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesCommissionPaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();
$arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'apcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amountap', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),  'dbfield' => 'outstanding', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['payment'] = array('title'=>ucwords($obj->lang['payment']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true); 

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "900px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
	
	if(isset($_POST) && !empty($_POST['salesName'])) {
		$criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Sales', 'filter' =>  $_POST['salesName']));
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

    for( $i=0;$i<count($rs);$i++) {   
            $rsDetail = $obj->getDetailById($rs[$i]['pkey']); 
        if (empty($rsDetail))
            continue;

        for ($j=0;$j<count($rsDetail);$j++){   
            $rsAP = $ap->getDataRowById($rsDetail[$j]['apkey']);
            $rsDetail[$j]['apcode'] =  $rsAP[0]['code'];
            $rsDetail[$j]['refcode'] =  $rsAP[0]['refcode']; 
            $rsDetail[$j]['refdate'] =  $rsAP[0]['trdate'];
            $rsDetail[$j]['amountap'] =  $rsAP[0]['amount']; 
        }

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
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputHidSalesKey'] = $class->inputHidden('hidSalesKey');
$arrTwigVar['inputSalesName'] =  $class->inputText('salesName');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate');
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate');  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    
       
echo $twig->render('reportAPCommissionPayment.html', $arrTwigVar);   
?>

