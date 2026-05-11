<?php 
$notification = array();
$hasDiskUsageAccess = $security->isAdminLogin('diskUsage',10);

$warehouse = new Warehouse();
$isActiveModule = $class->isActiveModule(array('car'));
	
// license...
$rs = $security->checkLicense();
$showWarning = false;

if ($rs['duedate'] < 0){  
    $temp = array();
    $temp['css'] =  'bg-pastel-red text-red-cardinal';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
    $showWarning = true;
    
}else if ($rs['duedate'] <= 14){
    $temp = array();
    $temp['css'] =  'bg-pastel-orange text-ginger';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
    $showWarning = true;
    
} else{ 
    $temp = array();
    $temp['css'] =  'bg-pastel-green text-green-avocado';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
}
      
// USER
$maxuser = PLAN_TYPE['maxuser']; 
$maxuser =  ($maxuser < 0) ? '&infin;' :  $class->formatNumber($maxuser);
$rsEmployee = $employee->searchData();
$maxuser = $obj->formatNumber(count($rsEmployee)) .' / '.$maxuser;


// WAREHOUSE
$maxwarehouse = PLAN_TYPE['maxwarehouse'];
$maxwarehouse =  ($maxwarehouse < 0) ? '&infin;' :  $class->formatNumber($maxwarehouse);
$rsWarehouse = $warehouse->searchData();
$maxwarehouse = $obj->formatNumber(count($rsWarehouse)) .' / '.$maxwarehouse;

// VEHICLE
if($isActiveModule['car']){
includeClass('Car.class.php');
$car = new Car();
$maxvehicle = PLAN_TYPE['maxvehicle'];
$maxvehicle =  ($maxvehicle < 0) ? '&infin;' :  $class->formatNumber($maxvehicle);
$rsCar = $car->searchData();
$maxvehicle = $obj->formatNumber(count($rsCar)) .' / '.$maxvehicle;  
}

// PRODUCTS
$productmax = PLAN_TYPE['maxproduct'];
$productmax =  ($productmax < 0) ? '&infin;' :  $class->formatNumber($productmax);
 
// PURCHASE
$maxpurchaseorder = PLAN_TYPE['maxpurchaseorder'];
$maxpurchaseorder =  ($maxpurchaseorder < 0) ? '&infin;' :  $class->formatNumber($maxpurchaseorder);
 
// SALES
$maxsalesorder = PLAN_TYPE['maxsalesorder'];
$maxsalesorder =  ($maxsalesorder < 0) ? '&infin;' :  $class->formatNumber($maxsalesorder);
  
 
// DISK USAGE
$maxdiskusage = PLAN_TYPE['maxdiskusage'];
$unit = 'GB'; 
$maxdiskusage =  ($maxdiskusage < 0) ? '&infin;' :  $class->formatNumber($security->convertSize($maxdiskusage,$unit,'MB')) . ' '.$unit;
 

$webFrontEnd = (PLAN_TYPE['usefrontend']) ? 'Ya' : 'Tidak';


$details = array();
array_push($details, array('title' => $class->lang['user'], 'value' => $maxuser ));
array_push($details, array('title' => $class->lang['item'], 'value' => $productmax, 'category' => array(1)));
array_push($details, array('title' => $class->lang['warehouse'], 'value' => $maxwarehouse)); 

if($isActiveModule['car'])
	array_push($details, array('title' => $class->lang['vehicle'], 'value' => $maxvehicle, 'category' => array(2)));

array_push($details, array('title' => $class->lang['imagesPerItem'], 'value' => $class->formatNumber(PLAN_TYPE['maxproductimage'],0), 'category' => array(1)));
array_push($details, array('title' => $class->lang['imageSize'], 'value' => $class->formatNumber(PLAN_TYPE['maximagesize'],0). ' MB' , 'category' => array(1))) ;
array_push($details, array('title' => $class->lang['filesPerItem'], 'value' => $class->formatNumber(PLAN_TYPE['maxproductfile'],0), 'category' => array(1)));
array_push($details, array('title' => $class->lang['maxSizeUploadPerFile'], 'value' => $class->formatNumber(PLAN_TYPE['maxfilesize'],0). ' MB'));
 

$planDetail = '<div class="div-table">';

foreach($details as $row){ 
    if (isset($row['category']) && !in_array(PLAN_TYPE['categorykey'] ,$row['category']) ) continue; 
    $planDetail .= '<div class="div-table-row">
                        <div class="div-table-col">
                            <div class="row-header">'.$row['title'].'</div>
                            <div>'.$row['value'].'</div>
                        </div> 
                    </div>';
}
 
$planDetail .= '<div class="div-table-row">
                        <div class="div-table-col">
                            <div class="row-header">'.$class->lang['fileDiskUsage'].'</div>
                            <div><span class="file-disk-usage">'. $class->formatNumber($class->convertSize(PLAN_TYPE['useddiskusage'],'GB','MB'),2) .' GB / '.$maxdiskusage.'</div>';

if ($hasDiskUsageAccess) $planDetail .= '<div  style="margin-top:0.5em"><a href="javascript: openTab(\''.$class->lang['diskUsage'].'\',\'diskUsage\');">'.$class->lang['manage'].' '.$class->lang['diskUsage'].'</a></div>';

$planDetail .= '</div>  
                    </div>';
 

$planDetail .= '</div>';
 
/*
    <div class="div-table-row">
        <div class="div-table-col row-header">Jenis Plan</div>
        <div class="div-table-col">'.PLAN_TYPE['name'].'</div>
    </div>
        <div class="div-table-row">
        <div class="div-table-col row-header">Maks. Transaksi Pembelian</div>
        <div class="div-table-col">'.$maxpurchaseorder.' / bln</div>
    </div>
    <div class="div-table-row">
        <div class="div-table-col row-header">Maks. Transaksi Penjualan</div>
        <div class="div-table-col">'.$maxsalesorder.' / bln</div>
    </div>
    */

$temp = array();
$temp['css'] =  'bg-light-gray text-black-jet';
$temp['message'] = $planDetail; 

array_push($notification,$temp);

$returnNotification = '';

if (!empty($notification)){
    $returnNotification = '<ul class="system-notification-list">';

    for($i=0;$i<count($notification);$i++){
        $returnNotification .= '<li class="'.$notification[$i]['css'].'">'.$notification[$i]['message'].'</li>'; 
    }
        
    $returnNotification .= '</ul>';
    
    if ($showWarning)    
      $jqueryScript .= '$(".notification-item").addClass("warning");';
        //$jqueryScript .= '$(".notification-icon .warning-icon").show();';
}

echo $returnNotification;
?>