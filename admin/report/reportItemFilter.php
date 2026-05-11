<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $item;
$securityObject = 'reportItemFilter'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    
$arrItemFilter= $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true,'order by name asc');
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['hidItemKey'])) {
		$criteria .= ' AND '.$obj->tableName.'.pkey = '.$class->oDbCon->paramString($_POST['hidItemKey']); 
		$rsItem = $item->getDataRowById($_POST['hidItemKey']); 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $rsItem[0]['name']));
	}
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType;

	$rs = $item->searchData('','',true,$criteria,$order);
	
		$temp = 1;
		$tempreport = '';
		
		if (empty($rs)){
			$tempreport .= '<tr class="report-row rewrite-row"><td ></td></tr>';	
		}
		for( $i=0;$i<count($rs);$i++) {   
		
			$itemFilterDetail = $itemFilter->getDetailByColumn('itemkey',$rs[$i]['pkey']);
			$temptablerow = ''; 
	
			$temptablerow  .= '<tr class="rewrite-row"> ';
			$temptablerow  .= '<td style="text-align:right;">'.$temp.'.</td>';  
			$temptablerow  .= '<td>'.$rs[$i]['code'].'</td>'; 
			$temptablerow  .= '<td>'. $rs[$i]['name'].'</td>';
			
			for($j=0;$j<count($arrItemFilter);$j++){
				$checked = '';
				for($k=0;$k<count($itemFilterDetail);$k++){
					if($itemFilterDetail[$k]['refkey'] == $arrItemFilter[$j]['pkey'])
						$checked = '<div title="'.$arrItemFilter[$j]['name'].'">V</div>';
				}
				$temptablerow  .= '<td style="text-align:center;">'.$checked .'</td>';
			}
			
			
			$temptablerow  .= '</tr>';
			$temptablerow  .= '<tr class="detail-row rewrite-row">';
			$temptablerow  .= '<td>';
			$temptablerow  .= '';
			$temptablerow  .= '</td>';
			$temptablerow  .= '</tr>';
			
			
			$tempreport .= $temptablerow; 
			  
			$temp++; 
		}
		
		$tempreport .= '<tr class="subtotal rewrite-row"> ';  
		$tempreport .= '<td ></td>';   
			  
		
		$tempreport .= '</tr> '; 
	
	$reportResult = array(); 
	 
	$reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;  
 	echo json_encode($reportResult);
	die;
}  

$arrTwigVar['inputItemCode'] =  $class->input('text','itemCode');  
$arrTwigVar['inputHidItemKey'] = $class->input('hidden','hidItemKey');
$arrTwigVar['inputItemName'] =  $class->input('text','itemName');
$arrTwigVar['rsItemFilter'] =  $arrItemFilter;
      
echo $twig->render('reportItemFilter.html', $arrTwigVar);  
 
?>

