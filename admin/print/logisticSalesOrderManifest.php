<?php
	
$PRINT_SETTINGS=array(
    'showPrintHeader' => false,
 //   'showPrintFooter' => false,
//    'paperSetting' => 'A5,L', 

);

includeClass(array('LogisticSalesOrderManifest.class.php','Customer.class.php','TermOfPayment.class.php'));
$logisticSalesOrderManifest = createObjAndAddToCol( new LogisticSalesOrderManifest()); 
$logisticSalesOrder = createObjAndAddToCol( new LogisticSalesOrder()); 
$customer= createObjAndAddToCol( new Customer()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 

$obj = $logisticSalesOrderManifest;

$content = function ($dataset,$param){

$obj = new LogisticSalesOrderManifest(); 
$logisticSalesOrder = new LogisticSalesOrder(); 
$customer = new Customer(); 
$setting = new Setting();
$city = new City();
$termOfPayment = new TermOfPayment();

$rs = $dataset['rs'];
    
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
//	
//$obj->setLog(count($rsDetail),true);
//	
//if (empty($rsDetail)){
//	$obj->setLog("test",true);
//	return '';	
//} 
	
$hasTOPKey = (isset($param['topkey'])) ? true : false;
	
$trDate = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name'),' and '.$customer->tableName.'.statuskey = 2');
$rsCustomer = array_column($rsCustomer,null,'pkey');
    
$rsCity = $city->searchDataRow(array($city->tableName.'.pkey',$city->tableName.'.name'),' and '.$city->tableName.'.statuskey = 1');
$rsCity = array_column($rsCity,null,'pkey');

$criteriaTOP = (isset($param) && !empty($param['topkey'])) ? ' and '.$termOfPayment->tableName.'.pkey = '. $obj->oDbCon->paramString($param['topkey']) : '';
	
$rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey',$termOfPayment->tableName.'.name'),' and '.$termOfPayment->tableName.'.statuskey = 1 ' .$criteriaTOP);
$topNameFilter= (isset($param) && !empty($param['topkey'])) ? $rsTOP[0]['name'] : 'Semua';
	
$rsTOP = array_column($rsTOP,null,'pkey');    
	
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyName = strtoupper($setting->loadSetting('companyName'));
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg;
	
$html = $obj->printSetting['defaultStyle'];

$title = (isset($param['title']) && !empty($param['title'])) ? $param['title'] : 'Cargo Manifest';
	
$html .= ' 
<style> 
    .colon { width: 20px;   }
    .width-left { width: 100px; } 
</style>

<table cellpadding="2">
    <tr>
        <td style="width:100px;vertical-align:middle;"><img src="'.$img.'" alt="Logo"></td>
        <td style="width:250px;"><b style="font-size:16px;">'.$companyName.'</b><br>'.str_replace(chr(13),'<br>',$companyAddress).'</td>
        <td style="width:410px;text-align:right;"></td>
    </tr>
</table>
<div style="clear:both"></div>
<table cellpadding="2">
    <tr align="center" >
        <td style="height: 1px;" class="title"> <b>'.$title.'</b> </td> 
    </tr>
    <tr align="center">
        <td style="height: 1px;" class="subtitle">'.$rs[0]['code'].'</td>
    </tr>
</table>
<div style="clear:both"></div>

<table cellpadding="2"> 
<tr>
	<td>
		<table cellpadding="2"> 
			<tr><td class="header-row-header" style="width: 100px;">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width: 100px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
			<tr><td class="header-row-header" style="width: 100px;">Tujuan</td><td style="width:10px; text-align:center">:</td><td style="width: 100px;">'.$rs[0]['recipientcityname'].', '.$rs[0]['transportationname'].'</td></tr>  
		</table> 
	</td>
	<td> 
		<table cellpadding="2"> 
			<tr><td class="header-row-header" style="width: 100px;">Pembayaran</td><td style="width:10px; text-align:center">:</td><td style="width: 100px;">'.$topNameFilter.'</td></tr>  
		</table> 
	</td>
</tr>
</table>

<div style="clear:both"></div>
';
    

$cellArray = array();
array_push($cellArray, array('label' => 'No', 'width' => '30', 'align' => 'right'));
array_push($cellArray, array('label' => 'No STT', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['bale'], 'width' => '50','align' => 'center'));
array_push($cellArray, array('label' => 'KG', 'width' => '50','align' => 'center'));
array_push($cellArray, array('label' => $obj->lang['recipient'] ));
//array_push($cellArray, array('label' =>  $obj->lang['destination'] , 'width' => '80'));
if($hasTOPKey)
	array_push($cellArray, array('label' =>  $obj->lang['price'] , 'width' => '80', 'align' => 'right'));

if(!$hasTOPKey)
	array_push($cellArray, array('label' => 'Keterangan ', 'width' => '150'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));
    
$totalKgCash = 0;    
$totalCollieCash = 0;

$arrLogisticCol = $logisticSalesOrder->searchData('','',true,' and '. $logisticSalesOrder->tableName.'.pkey in ('.$obj->oDbCon->paramString( array_column($rsDetail,'sokey') ,',').')' );
$arrLogisticCol = array_column($arrLogisticCol,null,'pkey');

$grandTotal = 0;
for ($i=0;$i<count($rsDetail);$i++){  

  $rsLogistic = $arrLogisticCol[ $rsDetail[$i]['sokey'] ]; // $logisticSalesOrder->getDataRowById($rsDetail[$i]['sokey']);
	
  if( isset($param['topkey']) && $rsLogistic['termofpaymentkey'] <> $param['topkey'] ) continue;
    
  $totalKgCash += $rsLogistic['totalweight'];
  $totalCollieCash += $rsLogistic['totalqty'];
    
  $topname = (!$hasTOPKey) ? '<td>'.$rsTOP[$rsLogistic['termofpaymentkey']]['name'] .'</td>' : '';
  $totalAmount = ($hasTOPKey) ? '<td style=" text-align:right;">'. $obj->formatNumber($rsLogistic['grandtotal']) .'</td>': '';
	
  $html .= '<tr><td style="text-align:right">'.($i+1).'</td><td>'.$rsDetail[$i]['socode'].'</td><td style="text-align:center">'.$obj->formatNumber($rsLogistic['totalqty']).'</td><td  style="text-align:center">'.$obj->formatNumber($rsLogistic['totalweight']).'</td><td>'. $rsCustomer[$rsLogistic['recipientkey']]['name'] .'</td>'.$totalAmount.$topname.'</tr>' ; 
	
  $grandTotal += $rsLogistic['grandtotal'];
}

$html .= '</table>' ;

$totalLabel = ($hasTOPKey) ? '<td style=" text-align:right;">'. $obj->formatNumber($grandTotal) .'</td>': '';

$html .= '<table cellpadding="4">';  
$html .= '<tr><td style="width:130px;"></td><td style="text-align:center; width: 50px">'.$obj->formatNumber($totalCollieCash).'</td><td style="text-align:center; width: 50px">'.$obj->formatNumber($totalKgCash).'</td><td style="width:366px"></td><td style="width:80px">'.$totalLabel.'</td></tr>' ; 
$html .= '</table>';    

$html .= '
<div style="clear:both"></div>
';

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

//$html .=$obj->generateSignLabel($rs);
    
return $html;
};

$generateReportContent = array();
array_push($generateReportContent , $content);

$rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey',$termOfPayment->tableName.'.name'),' and '.$termOfPayment->tableName.'.statuskey = 1 and duedays > 0' );

//$rsTotalDetail = $obj->countTotalRowsByTOP($_GET['id']);
//$obj->setLog(get_defined_vars(), true);

foreach($rsTOP as $key=>$row)  {
	// kalo gk ad transaksi, gk perlu add
	array_push($generateReportContent , array('content' => $content, 'newGroup' => true, 'param' => array('title' => 'Account Manifest', 'topkey' => $row['pkey'])));
}

?>