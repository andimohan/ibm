<?php	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj = $itemDepotMovement;
$securityObject = 'reportStockCardDepot';
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    
  
// ===== FOR EXPORT SECTION
$dataToExport = array();


/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"120px" );
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"150px",'align' => 'center','format'=>'datetime');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px" );
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"100px" );
$arrDataStructure['doCode'] = array('title'=>ucwords($obj->lang['doCode']),'dbfield' => 'docode', 'width'=>"120px" );
$arrDataStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"150px" );
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"220px" );
$arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qtyinbaseunit', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
$arrDataStructure['totalWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'totalweight', 'width'=>"90px" ,'format'=>'decimal','calculateTotal' => true);
$arrDataStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
$arrDataStructure['totalVolume'] = array('title'=>ucwords($obj->lang['totalVolume']),'dbfield' => 'totalvolume', 'width'=>"100px" ,'format'=>'decimal','calculateTotal' => true);
$arrDataStructure['volumeUnit'] = array('title'=>'','dbfield' => 'volumeunit', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');   
  
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['depotItemMovementReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = ' and '.$obj->tableName.'.statuskey = 1 ';
    
	if(isset($_POST) && !empty($_POST['refCode'])) {
		$criteria .= ' AND refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref', 'filter' => $_POST['refCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $criteria .= ' AND itemname LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    if(isset($_POST) && !empty($_POST['customerName'])) { 
        $criteria .= ' AND customername LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $_POST['customerName']));
	}
	 
	if(isset($_POST) && !empty($_POST['selDepot'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   
        
       	$criteria .= ' AND depotkey in('.$key.')';  

        $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$depotName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Depot', 'filter' => $depotName ));
        
	}
	   
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
  
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order); 
     
    $tempreport = ''; 

    //$rsRefTableData = $obj->getRefTableData($obj,$rs,'',$criteria);
    
    for($i=0;$i<count($rs);$i++) { 
        $arrHeaderStyle = array();
        
      // Testing section
        /*$tabletypekey = $rs[$i]['reftabletype'];
        $refkey = $rs[$i]['refkey']; 
        $rs[$i]['refcode'] = $rsRefTableData[$tabletypekey][$refkey]['code'];
        $rs[$i]['policenumber'] = $rsRefTableData[$tabletypekey][$refkey]['policenumber'];
        $rs[$i]['docode'] = $rsRefTableData[$tabletypekey][$refkey]['docode'];  */
        // Testing section

        $rs[$i]['volumeunit'] = 'CM<sup>3</sup>';

        if ($rs[$i]['qtyinbaseunit'] < 0) {  
             foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                if (isset($el['dbfield']))
                    $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';      
        }
        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate);  

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
         
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);


}else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}

//$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrDepot = $class->convertForCombobox($depot->searchData($depot->tableName.'.statuskey',1,true,' and isprivate = 1','order by name asc'),'pkey','name');

$arrTwigVar['inputRefCode'] =  $class->inputText('refCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelDepot'] =  $class->inputSelect('selDepot[]', $arrDepot, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportItemMovementDepot.html', $arrTwigVar);      
?>