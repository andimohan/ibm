<?php 

$pdf->setCustomSettings(
    array(
         'showPrintHeader' => false,
         'paperSetting' => 'A5,L',
         ) 
); 
   
$woContent =  function($dataset){

    $obj = new TruckingServiceWorkOrder();  
    $truckingServiceOrder = new TruckingServiceOrder();   
    $location = new Location();
    $service = new Service();
    $employee = new Employee();

    $rs = $dataset['rs']; 
	
	$qrResult = $obj->createQR($rs[0]['code'],2);
	
    $rsDetail = $truckingServiceOrder->getDetailByColumn('pkey',$rs[0]['refdetailkey']); 
    $rsService = $service->getDataRowById($rsDetail[0]['itemkey']);      

    $driverName = $rs[0]['drivername'];

    $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
    $locationname = (!empty($rsLocation)) ? $rsLocation[0]['name'] : '';

    $timeformat = ($obj->formatDBDate($rs[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';     

    $trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';     

    $container = $rs[0]['containernumber'];
    if (!empty($rs[0]['container2number'])) $container .= ', ' . $rs[0]['container2number']; 

    $seal = $rs[0]['sealnumber'];
    if (!empty($rs[0]['seal2number'])) $seal .= ', ' . $rs[0]['seal2number'];

    $depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
    $terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';

    $html = $obj->printSetting['defaultStyle'];
    $html .= ' 
    <table cellpadding="2" > 
    <tr><td><div class="title">SURAT JALAN / SURAT PENGANTAR KONTAINER</div></td></tr>
    <tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
    </table> 

    <div style="clear:both"></div>
    <table>
    <tr>
    <td>
    <table cellpadding="2"> 
    <tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
    <tr><td class="header-row-header">Tgl. Stuffing / Bongkar</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdatetime'],$timeformat).'</td></tr>  
    <tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['serviceordercode'] .'</td></tr> 
    <tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
    <tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>  
    <tr><td class="header-row-header">Jenis Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['jobtypename'] .'</td></tr>  
    <tr><td colspan="3"></td></tr> 
    </table>
    <table>
    <tr><td style="font-weight:bold">No. Mobil</td><td style="font-weight:bold">No. Chasis</td><td style="font-weight:bold">'.$obj->lang['driver'].'</td></tr>  
    <tr><td>'. $rs[0]['policenumber'] .'</td><td>'. $rs[0]['chassisnumber'] .'</td><td>'. $driverName.'</td></tr>  
    </table> 
    </td>
    <td>
    <table cellpadding="2" >
    <tr><td class="header-row-header" style="width:120px">Kepada Yth</td><td style="width:10px; text-align:center">:</td><td style="width:220px;">'.$rs[0]['consigneename'].'</td></tr> 
    <tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$locationname.'</td></tr> 
    <tr><td class="header-row-header">Pabrik / Gudang</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['warehouseconsigneename'].'</td></tr> 
    <tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$rs[0]['stuffingaddress']).'</td></tr>  
    <tr><td class="header-row-header">Depot / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / ' .$terminalname.'</td></tr>    
    </table>
    </td>
    </tr>
    </table>

    <div style="clear:both"></div> 

    <table cellpadding="4" class="table-transaction">
    <tr class="col-header"><td style="text-align:right;width:30px">No</td><td style="width:80px">Ukuran</td><td style="width:100px">No. Container</td><td style="width:100px">No. Segel</td><td style="width:160px">Rute</td><td style="width:200px">Jenis Barang</td></tr>  
    <tr><td style="text-align:right;">1.</td><td>'.$rsService[0]['name'].'</td><td>'.$container.'</td><td>'.$seal.'</td><td>'.$rs[0]['routefrom'].' - ' .$rs[0]['routeto'].'</td><td>'.$rs[0]['productdesc'].'</td></tr>   
    </table>  
    '.$trnotes.' 
    <div style="clear:both"></div>  
    ';

    $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));

    $arrSignLabel = array(); 
    array_push($arrSignLabel, array('Ops. Trucking /<br>Pengirim',$rsEmployee[0]['name']));
    array_push($arrSignLabel, array($obj->lang['driver'],$driverName) ); 
    array_push($arrSignLabel, array('Penerima / Gudang') ); 

     $html .=' 
            <table cellpadding="4" class="sign">
            <tr>'; 
            for ($i=0;$i<count($arrSignLabel);$i++){
                $html .='<td  class="sign-col" style="height:40px;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
                if ($i <> count($arrSignLabel) - 1)
                    $html .= '<td class="sign-col-space"></td>';
            }
	
            $html .= '<td rowspan="2" style="widtH: 70px"></td>';
            $html .= '<td rowspan="2" style="text-align:center" ><img src="'.$qrResult['url'].'" /></td>';
            $html .='</tr> 
            <tr>'; 
		
            for ($i=0;$i<count($arrSignLabel);$i++){
                $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
                $html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
                if ($i <> count($arrSignLabel) - 1)
                    $html .= '<td class="sign-col-space"></td>';
            }
                    
            $html .='</tr> 
            </table>' ;


     $html .=' 
    <div style="clear:both"></div> 
    <table cellpadding="2" style="width:300px">
    <tr><td style="width:120px">Jam Tiba Gudang</td><td>:</td><td style="width:100px"></td><td style="width:150px">Jam Keluar Gudang</td><td>:</td></tr>    
    </table>
    <div style="font-size:10px">
    * KOLOM CONTAINER DAN SEGEL DIISI OLEH MITRA DRIVER / OPS<br>
    ** SPK HARUS KEMBALI KE OPS BERSAMA DENGAN SURAT JALAN
    </div>
    ';

    return '<div style="font-size:10px">'. $html .'</div>';
};

$costListContent = function ($dataset){
	global $pdf;

    $obj = new TruckingServiceWorkOrder();  
//	$service = new Service();
    $location = new Location();
    $employee = new Employee();
	
    $rs = $dataset['rs'];  
	
	$rsLocation = $location->getDataRowById($rs[0]['locationkey']);
	$locationname = (!empty( $rsLocation[0]['name'])) ? $rsLocation[0]['name'] : '';

	$timeformat = ($obj->formatDBDate($rs[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';      

	$depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
	$terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';

    $arrHTML = array();
	
	$html = $obj->printSetting['defaultStyle'];
	$html .= ' 
	<table cellpadding="2" > 
	<tr><td><div class="title">TANDA TERIMA PENGELUARAN UANG</div></td></tr>
	<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
	</table> 

	<div style="clear:both"></div>
	<table>
	<tr>
	<td style="width:300px;" >
	<table cellpadding="2"> 
	<tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
	<tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['serviceordercode'] .'</td></tr> 
	<tr><td class="header-row-header">No. SPK</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['code'] .'</td></tr> 
	<tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
	<tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>   
	<tr><td class="header-row-header">Jenis Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['containername'].', '. $rs[0]['jobtypename'] .'</td></tr>    
	</table> 
	</td>
	<td style="width:370px;">
	<table cellpadding="2" >
	<tr><td class="header-row-header" style="width:120px">Shipper</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$rs[0]['consigneename'].'</td></tr> 
	<tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$locationname.'</td></tr> 
	<tr><td class="header-row-header">Pabrik / Gudang</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['warehouseconsigneename'].'</td></tr> 
	<tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$rs[0]['stuffingaddress']).'</td></tr> 
	<tr><td class="header-row-header"></td></tr> 
	<tr><td class="header-row-header">Tgl. Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdatetime'],$timeformat).'</td></tr>  
	<tr><td class="header-row-header">Depo / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / '.$terminalname.'</td></tr>   
	</table>
	</td>
	</tr>
	</table>
	<div style="clear:both"></div>  
	
	<table cellpadding="2">';
	
	

	$cellArray = array ();
//	array_push($cellArray, array('label' => $obj->lang['number'], 'align' => 'right','width' => '30')); 
	array_push($cellArray, array('label' => $obj->lang['description']));
	array_push($cellArray, array('label' => $obj->lang['recipient'], 'width' => '200'));
	array_push($cellArray, array('label' => $obj->lang['amount'], 'align' => 'right', 'width' => '100'));
	$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  


	$rsTruckingCost = $obj->getCostDetail($rs[0]['pkey'],'',' and '. $obj->tableCost .'.refcashoutkey = 0 and '. $obj->tableCost .'.realizationkey = 0 and '. $obj->tableCost .'.supplierkey = 0');
	$rowNumber = 1;
	$total = 0;
	foreach($rsTruckingCost as $row){
		$html .= '<tr> 
					<td>'.$row['name'].'</td>
					<td>'.$row['employeename'].'</td>
					<td style="text-align:right">'.$obj->formatNumber($row['requestamount']).'</td>
				</tr>';
		
		$total +=$row['requestamount'];

	}
	$html .= '<tr> 
				<td style="border-top:1px solid #333"></td>
				<td style="border-top:1px solid #333"></td>
				<td style="border-top:1px solid #333; font-weight:bold; text-align:right">'.$obj->formatNumber($total).'</td>
			</tr>';
	$html .= '</table>';

	
	$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));

	$arrSignLabel = array(); 
	array_push($arrSignLabel, array('Admin',$rsEmployee[0]['name']) ); 
	array_push($arrSignLabel, array('Kasir'));
	array_push($arrSignLabel, array('Ops. Trucking') ); 
	array_push($arrSignLabel, array($obj->lang['driver'],$driverName) ); 

	 $html .=' 
			<table cellpadding="4" class="sign">
			<tr>'; 
			for ($i=0;$i<count($arrSignLabel);$i++){
				$html .='<td  class="sign-col" style="height:40px;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
				if ($i <> count($arrSignLabel) - 1)
					$html .= '<td class="sign-col-space"></td>';
			}
			$html .='</tr> 
			<tr>'; 
			for ($i=0;$i<count($arrSignLabel);$i++){
				$arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
				$html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
				if ($i <> count($arrSignLabel) - 1)
					$html .= '<td class="sign-col-space"></td>';
			}
			$html .='</tr> 
			</table>' ;

	$html = '<div style="font-size:10px">'. $html .'</div>'; 

	array_push($arrHTML,$html);
	return $arrHTML;
};

$generateReportContent = array();
array_push($generateReportContent , $woContent);
array_push($generateReportContent , array('content' => $costListContent, 'newGroup' => true));

?>
