<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $itemDepotMovement;
$securityObject = 'reportStockCardDepot'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'name', 'width'=>"300px", 'mergeExcelCell' => 4); 
$arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"180px", 'dbfield' => 'categoryname', 'mergeExcelCell' => 4); 
$arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'qtyonhand', 'width'=>"70px",'format'=>'number', "sortable" => false, 'textColor' => '0093AF');  
$arrDataStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');  
$arrDataStructure['totalWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'totalweight', 'width'=>"90px",'format'=>'decimal', "sortable" => false, 'textColor' => '9A4EAE');  
$arrDataStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');  
$arrDataStructure['totalVolume'] = array('title'=>ucwords($obj->lang['totalVolume']),'dbfield' => 'totalvolume', 'width'=>"100px",'format'=>'decimal', "sortable" => false, 'textColor' => '568203');  
$arrDataStructure['volumeUnit'] = array('title'=>'','dbfield' => 'volumeunit', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');  
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"550px", 'mergeExcelCell' => 4);
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['stockCardDepotReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"130px",'format'=>'datetime'); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"90px"); 
$arrDataDetailStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"90px"); 
$arrDataDetailStructure['doCode'] = array('title'=>ucwords($obj->lang['doCode']),'dbfield' => 'docode', 'width'=>"130px"); 
$arrDataDetailStructure['policeNumber'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"80px"); 
$arrDataDetailStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px"); 
$arrDataDetailStructure['beginningQty'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startQty', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['movementQty'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'qtyinbaseunit', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['balanceQty'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'afterQty', 'width'=>"70px",'format'=>'number', 'textColor' => '0093AF');
$arrDataDetailStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px",'textColor' => '999999', 'style' => 'padding-left:0px;');     
$arrDataDetailStructure['beginningWeight'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startWeight', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['movementWeight'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'movementWeight', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['balanceWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'afterWeight', 'width'=>"70px",'format'=>'decimal', 'textColor' => '9A4EAE');  
$arrDataDetailStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px",'textColor' => '999999', 'style' => 'padding-left:0px;');    
$arrDataDetailStructure['beginningVolume'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startVolume', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['movementVolume'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'movementVolume', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['balanceVolume'] = array('title'=>ucwords($obj->lang['totalVolume']),'dbfield' => 'afterVolume', 'width'=>"90px",'format'=>'decimal', 'textColor' => '568203');  
$arrDataDetailStructure['volumeUnit'] = array('title'=>'','dbfield' => 'volumeunit', 'width'=>"40px",'textColor' => '999999', 'style' => 'padding-left:0px;'); 
$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'note');
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
	
if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	$detailCriteria = '';
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		//$detailCriteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableItem.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['itemName'])) {  
        $criteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    
    if(isset($_POST) && !empty($_POST['customerName'])) { 
        $detailCriteria .= ' AND customername LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $_POST['customerName']));
	}
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
         
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND categorykey in('.$key.')';  

        $rsCriteria = $itemCategory->searchData('','',true, ' and '.$itemCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
	} 
 
 	$depotCriteria = '';
	$depotkey = '';  

	if(isset($_POST) && !empty($_POST['selDepot'])) { 
        
        $depotkey = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   
        
       	$depotCriteria .= ' AND depotkey in('.$depotkey.')';  

        $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$depotkey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$depotName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Depot', 'filter' => $depotName ));
        
	}
	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
	$rsItem = $itemDepot->searchData('','',true,$criteria,$order); 
    
    $date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
    $depotkey =  (isset($_POST['selDepot'])) ? $_POST['selDepot']  : '';
	
	$tempreport = '';

    if (empty($rsItem))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
    for($j=0;$j<count($rsItem);$j++){  
		 
        $itemVolume = $rsItem[$j]['width'] * $rsItem[$j]['length'] * $rsItem[$j]['height'];
        $itemWeight = $rsItem[$j]['gramasi'] ;
         
        //$startCOGS = ($hasCOGSAccess) ? $obj->sumItemCOGSMovement($rsItem[$j]['pkey'], $depotkey ,$date) : 0;  
        $startQty = $obj->sumItemMovement($rsItem[$j]['pkey'],$depotkey,$date);  
           
        $dateMethod = $class->loadSetting('movementDateMethod');
        $datefield = ($dateMethod == 2) ? 'trdate' : 'createdon'; 
         
        $criteria = ' and '.$obj->tableName.'.statuskey = 1 and itemkey = ' .$class->oDbCon->paramString($rsItem[$j]['pkey']) . $depotCriteria . $detailCriteria . ' and '.$obj->tableName.'.'.$datefield.' between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ','Y-m-d 23:59:59');
        $rsDetail = $obj->searchData('','',true,$criteria ,'order by '.$obj->tableName.'.'.$datefield.' asc');

        //$rsRefTableData = $obj->getRefTableData($obj,$rsDetail,'',$criteria);
         
        $arrDetailStyle = array();
        for( $i=0;$i<count($rsDetail);$i++) {   
    
            // Testing section
            /*$tabletypekey = $rsDetail[$i]['reftabletype'];
            $refkey = $rsDetail[$i]['refkey']; 
            $rsDetail[$i]['refcode'] = $rsRefTableData[$tabletypekey][$refkey]['code'];
            $rsDetail[$i]['policenumber'] = $rsRefTableData[$tabletypekey][$refkey]['policenumber'];
            $rsDetail[$i]['docode'] = $rsRefTableData[$tabletypekey][$refkey]['docode']; */
            // Testing section
            
            $rsDetail[$i]['startQty'] = ($i==0) ? $startQty :  $rsDetail[$i-1]['afterQty'];  
            $rsDetail[$i]['startWeight'] =  $rsDetail[$i]['startQty'] * $itemWeight;
            $rsDetail[$i]['startVolume'] =  $rsDetail[$i]['startQty'] * $itemVolume;
            
            $rsDetail[$i]['afterQty'] = $rsDetail[$i]['startQty'] + $rsDetail[$i]['qtyinbaseunit'];  
            $rsDetail[$i]['afterWeight'] =  $rsDetail[$i]['afterQty'] * $itemWeight;
            $rsDetail[$i]['afterVolume'] =  $rsDetail[$i]['afterQty'] * $itemVolume;
 
            $rsDetail[$i]['movementWeight'] = $rsDetail[$i]['qtyinbaseunit'] * $itemWeight;
            $rsDetail[$i]['movementVolume'] = $rsDetail[$i]['qtyinbaseunit'] * $itemVolume;
            
            if ($rsDetail[$i]['qtyinbaseunit'] < 0) { 
                $redCardinal = 'C41E3A';
                $arrDetailStyle[$i]['qtyinbaseunit']['textColor'] = $redCardinal;   
                $arrDetailStyle[$i]['movementWeight']['textColor'] = $redCardinal;   
                $arrDetailStyle[$i]['movementVolume']['textColor'] = $redCardinal;    
            }
            
              $rsDetail[$i]['volumeunit'] = 'CM<sup>3</sup>';
                
        }
         
        
        $qoh = (isset($rsDetail[$i-1])) ?  $rsDetail[$i-1]['afterQty'] : $startQty; 
        
        $rsItem[$j]['qtyonhand'] = $qoh;  
        $rsItem[$j]['totalweight'] = $rsItem[$j]['qtyonhand'] * $itemWeight;
        $rsItem[$j]['totalvolume'] = $rsItem[$j]['qtyonhand'] * $itemVolume; 
        $rsItem[$j]['volumeunit'] = 'CM<sup>3</sup>';
        
        // has detail
        $rsItem[$j]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
                  
        $return = $obj->formatReportRows(array('data' => $rsItem[$j]),$arrTemplate); 
            
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


$arrDepot = $class->convertForCombobox($depot->searchData($depot->tableName.'.statuskey',1,true,' and isprivate = 1','order by name asc'),'pkey','name'); 
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   


$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');
$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName'); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelDepot'] =  $class->inputSelect('selDepot[]', $arrDepot, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportStockDepotCard.html', $arrTwigVar);   
?>

