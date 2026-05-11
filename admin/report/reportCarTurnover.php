<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('CarTurnover.class.php','Car.class.php','Item.class.php'));

include '_global.php';

$car = new Car();
$carTurnover = new CarTurnover();
$warehouse = new Warehouse();
$carCategory = new CarCategory();
$item = new Item();

$obj= $carTurnover;

$securityObject = 'reportCarTurnOver'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array();  
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');  
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"150px", 'dbfield' => 'policenumber'); 
$arrDataStructure['carCategory'] = array('title'=>ucwords($obj->lang['carCategory']),  'width'=>"150px", 'dbfield' => 'categoryname'); 
/*$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");*/
$arrDataStructure['totalRevenue'] = array('title'=>ucwords($obj->lang['revenue']), 'dbfield' => 'totalRevenue', 'width'=>"110px" ,'format'=>'number', "sortable" => false, 'textColor' => '568203', 'calculateTotal' => true);
$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['cost']), 'dbfield' => 'totalCost', 'width'=>"110px" ,'format'=>'number', "sortable" => false,  'textColor' => 'C41E3A', 'calculateTotal' => true);
$arrDataStructure['totalBalance'] = array('title'=>ucwords($obj->lang['balance']), 'dbfield' => 'totalBalance', 'width'=>"110px" ,'format'=>'number', "sortable" => false , 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carTurnoverReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');  
$arrDataDetailStructure['jodate'] = array('title'=>ucwords($obj->lang['jobOrderDate']),'dbfield' => 'jodate', 'width'=>"90px",'format'=>'date');  
$arrDataDetailStructure['revenue'] = array('title'=>ucwords($obj->lang['revenue']),'dbfield' => 'revenueamount', 'width'=>"90px",'format'=>'number', 'textColor' => '568203'); 
$arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'costamount', 'width'=>"90px",'format'=>'number', 'textColor' => 'C41E3A'); 
$arrDataDetailStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balanceamount', 'width'=>"90px",'format'=>'number', 'textColor' => '568203');
$arrDataDetailStructure['reference1'] = array('title'=>ucwords($obj->lang['reference']),'dbfield' => 'refcode', 'width'=>"120px" ); 
$arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"250px" ); 

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
	
$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['jobOrderDate'], 
);

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria =' and '. $obj->tableName.'.statuskey = 1';
	$carCriteria = ''; 
	$orderByDetail = $obj->tableName.'.trdate';
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$carCriteria .= ' AND '.$car->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
	
//	if(isset($_POST) && !empty($_POST['trStartDate'])){
//		//$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
//		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
//	} 
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
      switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate'; 
					   $orderByDetail = $obj->tableName.'.trdate';
			  		   break;
            case '2' : $fieldName = $obj->tableName.'.jodate';  
						$orderByDetail = $obj->tableName.'.jodate';
			  			break;
            default : $fieldName = $obj->tableName.'.trdate';  
					   $orderByDetail = $obj->tableName.'.trdate';
			  			break; 
        }
        
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
  
   
    if(isset($_POST) && !empty($_POST['selCarCategory'])) { 

        $key = implode(",", $class->oDbCon->paramString($_POST['selCarCategory']));   

        $carCriteria .= ' AND '.$car->tableName.'.categorykey in ('.$key.')';  
        $rsCriteria = $carCategory->searchData('','',true, ' and '.$carCategory->tableName.'.pkey in ('.$key.')');
        $arrTempNumber = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempNumber,$rsCriteria[$k]['name']);

        $category = implode(", ",$arrTempNumber); 
        array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $category));
	} 
    
    
   
    if(isset($_POST) && !empty($_POST['selPoliceNumber'])) { 

        $key = implode(",", $class->oDbCon->paramString($_POST['selPoliceNumber']));   

        $carCriteria .= ' AND '.$car->tableName.'.pkey in('.$key.')';  
        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
        $arrTempNumber = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempNumber,$rsCriteria[$k]['policenumber']);

        $policeNumber = implode(", ",$arrTempNumber); 
        array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' => $policeNumber));
	} 
    
		 
	$orderBy = 'policenumber'; 
	$orderType = 'asc'; 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rsCar = $car->searchData($car->tableName.'.statuskey','1',true,$carCriteria,$order);
	$arrCarKeys = array_column($rsCar,'pkey');
	
    $rsDetailCol = $obj->searchData('','',true,$criteria . ' and carkey in (' .$class->oDbCon->paramString($arrCarKeys,',').') order by '.$orderByDetail.'  asc');
	$arrDetailCol =$obj->reindexDetailCollections($rsDetailCol,'carkey');    
		
	$tempreport = '';

    if (empty($rsCar))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
    for($j=0;$j<count($rsCar);$j++){ 
        $arrHeaderStyle = array();
		   
        $totalRevenue = 0;
        $totalCost = 0;

		$rsDetail = $arrDetailCol[ $rsCar[$j]['pkey'] ] ?? [];
//		$obj->setLog( $rsCar[$j]['pkey'],true);
//		$obj->setLog( $rsDetail,true);
//		die;
			
        $arrDetailStyle = array();
        for( $i=0;$i<count($rsDetail);$i++) {
            
            $amount = $rsDetail[$i]['amount'];
            
            if($rsDetail[$i]['amount']>0){
                $rsDetail[$i]['revenueamount'] = $amount;
                $totalRevenue = $totalRevenue + $amount; 
            }
            if($rsDetail[$i]['amount']<0){
                $rsDetail[$i]['costamount'] = abs($amount);
                $totalCost = $totalCost + $amount; 
            }   
            
            $rsDetail[$i]['balanceamount'] = (isset($rsDetail[$i-1]['balanceamount'])) ? $rsDetail[$i-1]['balanceamount'] + $amount : $amount;   
            
            if ($rsDetail[$i]['balanceamount'] < 0)  
                $arrDetailStyle[$i]['balanceamount']['textColor'] = 'C41E3A'; 
        }
        
        $rsCar[$j]['totalRevenue'] = $totalRevenue; 
        $rsCar[$j]['totalCost'] = $totalCost; 
        $rsCar[$j]['totalBalance'] = (isset($rsDetail[$i-1]['balanceamount'])) ? $rsDetail[$i-1]['balanceamount'] : 0; 
               
        // has detail
        $rsCar[$j]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
                  
        
         $color = '333333';  
        if ($rsCar[$j]['totalBalance'] < 0)  
           $color = 'C41E3A';
        else if ($rsCar[$j]['totalBalance'] > 0)  
           $color = '568203';  
            
        $arrHeaderStyle['totalBalance']['textColor'] = $color;
        
        
        $return = $obj->formatReportRows(array('data' => $rsCar[$j], 'style' => $arrHeaderStyle),$arrTemplate); 
            
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

 
$arrPoliceNumber = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true, ' and '.$car->tableName.'.statuskey = 1', ' order by policenumber asc'),'pkey','policenumber'); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCarCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputPoliceNumber'] =  $class->inputSelect('selPoliceNumber[]', $arrPoliceNumber, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCarCategory'] =  $class->inputSelect('selCarCategory[]', $arrCarCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportCarTurnover.html', $arrTwigVar);   
?>