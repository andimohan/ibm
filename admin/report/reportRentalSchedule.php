<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $salesOrderRental;
$securityObject = 'reportRentalTimesheet'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$tempreport='';
$arrHeaderTemplate['reportTitle'] = $class->lang['rentalSchedule'];
$criteria ='';
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y',strtotime("+1 week"));
} 
if (isset($_POST) && !empty($_POST['hidAction'])){
   
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$salesOrderRental->tableDeliveryDetail.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate']. ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selItem'])) { 
        
        $key = $class->oDbCon->paramString($_POST['selItem'],',');   
        
       	$criteria .= ' AND '.$item->tableName.'.pkey in ('.$key.')';  

        $rsCriteria = $item->searchData('','',true, ' and '.$item->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['item'], 'filter' => $statusName ));
        
	} 
    
    if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$salesOrderRental->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode SO', 'filter' => $_POST['salesCode']));
	}
    
    $startDate = $class->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d');
    $startDate = str_replace('\'','',$startDate);

    $endDate = $class->oDbCon->paramDate($_POST['trEndDate'],' / ','Y-m-d');
    $endDate = str_replace('\'','',$endDate);

    $currentDate = strtotime($startDate);
    $finalDate = strtotime($endDate);
    $finalMonth = date('m', $finalDate);
    $arrScheduleDate = array();
    $arrDate = array();
    $dayspan = 0;
    $colspan = 0;
    $dateWidth = 28;
    $totalDateWidth = 0;
    $colspanHeader = 8;
    $tableHeaderDays ='';
    $tableDays ='';
    $headerBackground = '#00BFFF';
    $fontHeaderBackground = '#FFFFFF';
     while ($currentDate <= strtotime($endDate)){ 
        $dmy = date('Y-m-d', $currentDate);
        $currentMonth = date('m', $currentDate);
        $currentYear = date('Y', $currentDate);
        $ldOfCurrentDate = strtotime(date('Y-m-t', $currentDate));
        $dayspan += 1; 
        if($currentDate==$ldOfCurrentDate || $currentDate==$finalDate){
            array_push($arrDate, array('month' => $obj->bulan($currentMonth), 'year' => $currentYear, 'dayspan' => $dayspan));
            $dayspan = 0;
        }
         
        $arrScheduleDate[$dmy] = array('label' => $currentDate); 
        $tableDays .= '<th style="text-align:center;width:'.$dateWidth.'px;">'.date('d', $currentDate).'</th>';
        $colspan +=1;
        
        $currentDate =  strtotime('+1 day', $currentDate);
    }
    $headerWitdh = 900;
    foreach($arrDate as $row){
       $tableHeaderDays .='<th style="text-align:center;" colspan="'.$row['dayspan'].'">'.$row['month'].' ' .$row['year'].'</th>';
       $totalDateWidth += $dateWidth*$row['dayspan'];
        
    } 
        
    $tempreport .= '<table class="timesheet-table" style="width:'.($headerWitdh+$totalDateWidth).'px;">';
    $tempreport .='<thead>';
    $tempreport .='<tr class="table-header">';
    $tempreport .='<th style="" colspan="9"></th>';
    
    $tempreport .=$tableHeaderDays;
    $tempreport .='</tr>';
    
    $tempreport .='<tr class="table-header">';
    $tempreport .= '<th style="text-align:right;width:40px">#</th>';
    $tempreport .= '<th style="width:90px;">'.ucwords($obj->lang['soCode']).'</th>';
    $tempreport .= '<th style="">'.ucwords($obj->lang['itemName']).'</th>';
    $tempreport .= '<th style="text-align:right;width:60px;">'.ucwords($obj->lang['qty']).'</th>';
    $tempreport .= '<th style="width:60px">'.ucwords($obj->lang['unit']).'</th>';
    $tempreport .= '<th style="text-align:right;width:60px;">'.ucwords($obj->lang['time']).'</th>';
    $tempreport .= '<th style="width:60px;">'.ucwords($obj->lang['unit']).'</th>';
    $tempreport .= '<th style="text-align:right;width:80px;">'.ucwords($obj->lang['price']).'</th>';
    $tempreport .= '<th style="text-align:right;width:100px;">'.ucwords($obj->lang['total']).'</th>';
    $tempreport .= $tableDays;
    
    $tempreport .= '</tr></thead>';
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'socode'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';	   
	$order = 'order by '.$orderBy.' ' .$orderType; 
    $rsDelivery = $obj->getDeliverySchedule('',$criteria,$order);
    $grandtotal = 0;
    
    for($i=0;$i<count($rsDelivery);$i++) {
        $tableDate ='';
        $qtyDays = 0;
        foreach($arrScheduleDate as $dateIndex => $dateRow){
            if($dateIndex>=$rsDelivery[$i]['trdate'] && $dateIndex<=$rsDelivery[$i]['enddate']){
                $tableDate .= '<td style="background-color:#FF0000;"></td>';
                $qtyDays +=1;
            }else
                $tableDate .= '<td></td>';
        }
        $total = $rsDelivery[$i]['qty']*$rsDelivery[$i]['priceinunit']*$qtyDays;
        $grandtotal += $total;
               
        $tempreport .= '<tr class="rewrite-row">';
        $tempreport .= '<td style="text-align:right;width:40px">'.($i+1).'.</td>';
        $tempreport .= '<td style="width:90px">'.$rsDelivery[$i]['socode'].'</td>';
        $tempreport .= '<td style="">'.$rsDelivery[$i]['itemname'].'</td>';
        $tempreport .= '<td style="text-align:right;width:60px">'.$obj->formatNumber($rsDelivery[$i]['qty']).'</td>';
        $tempreport .= '<td style="width:60px">'.$rsDelivery[$i]['unitname'].'</td>';
        $tempreport .= '<td style="text-align:right;width:60px">'.$obj->formatNumber($qtyDays).'</td>';
        $tempreport .= '<td style="width:60px">'.$rsDelivery[$i]['timename'].'</td>';
        $tempreport .= '<td style="text-align:right;width:80px">'.$obj->formatNumber($rsDelivery[$i]['priceinunit']).'</td>';
        $tempreport .= '<td style="text-align:right;width:100px">'.$obj->formatNumber($total).'</td>';
        $tempreport .= $tableDate; 
        $tempreport .= '</tr>';
        
    }
    $tempreport .= '<tr  class="subtotal">';
    $tempreport .= '<td colspan="'.$colspanHeader.'" style="text-align:right;">'.$obj->lang['total'].'</td>';
    $tempreport .= '<td style="text-align:right;width:100px">'.$obj->formatNumber($grandtotal).'</td>';
    $tempreport .= '<td colspan="'.($colspan).'" ></td>';
    $tempreport .= '</tr>';
    $tempreport .= '</table><br>';
    
	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;

    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array(); 
    }else{ 
        echo json_encode($reportResult);
        die;
    }
}else{
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrItem = $class->convertForCombobox($item->searchData($item->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelItem'] =  $class->inputSelect('selItem[]', $arrItem, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportRentalSchedule.html', $arrTwigVar);

?>
