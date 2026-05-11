<?php

includeClass(array('APEmployeeCommission.class.php','EmployeeCommission.class.php'));

$employeeCommission = createObjAndAddToCol(new EmployeeCommission());
$apEmployeeCommission = createObjAndAddToCol(new APEmployeeCommission());
$obj = $employeeCommission;
        
$arrID = array();
if (isset( $_GET['id']) && !empty( $_GET['id'])){ 
    $arrID = explode(',',$_GET['id']); 
}else if (isset( $_GET['apkey']) && !empty( $_GET['apkey'])){ 
    
    $employeeCommissionTableKey = $obj->getTableKeyAndObj($employeeCommission->tableName, array('key'))['key']; 
    
    $rsTemp = $apEmployeeCommission->searchDataRow(array($apEmployeeCommission->tableName.'.refkey'),
                                                    ' and '.$apEmployeeCommission->tableName.'.reftabletype = '.$obj->oDbCon->paramString($employeeCommissionTableKey).'
                                                      and '.$apEmployeeCommission->tableName.'.pkey = '.$obj->oDbCon->paramString($_GET['apkey'])) ;
    array_push($arrID,$rsTemp[0]['refkey']); 
    
}
        

$generateReportContent = function ($dataset) {

$obj = new EmployeeCommission(); 

$rs = $dataset['rs']; 

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['salesCommission'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
';  
$html .= '<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['period']).'</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['perioddate'],'F Y').'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['employee']).'</td><td style="text-align:center">:</td><td>'. $rs[0]['employeename'] .'</td></tr>    
</table> 
 
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction" >';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['jobOrder'], 'width' => '120'));
array_push($cellArray, array('label' => $obj->lang['date'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['customer']  ));
array_push($cellArray, array('label' => $obj->lang['lastPayment'], 'align' => 'center', 'width' => '130'  ));
array_push($cellArray, array('label' => $obj->lang['profit'], 'align' => 'right', 'width' => '90'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '680', 'cell' =>  $cellArray));   
 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],'',' order by jodate asc, jocode asc'); 

$arrJOKey = array_column($rsDetail, 'jokey');

$rsData = $obj->getLastPaymentDate($arrJOKey);

    
$total = 0;
for ($i=0;$i<count($rsDetail);$i++){ 

  $jokey = $rsDetail[$i]['jokey'];
  $transactionDate = $rsData[$jokey];

    
  $total += $rsDetail[$i]['profit'];
  $html .= '<tr><td>'.$rsDetail[$i]['jocode'].'</td><td style="text-align:center">'.$obj->formatDBDate($rsDetail[$i]['jodate']).'</td><td>'.$rsDetail[$i]['customername'].'</td><td style="text-align:center">' . $obj->formatDBDate($transactionDate['arpaymentdate']) . '</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['profit']).'</td></tr>' ; 
}
$html .= '</table>' ;
    
$balance = $total-$rs[0]['targetprofit'];
if($balance > 0)  $color = 'color:#568203;' ;
else if ($balance < 0)    $color = 'color:#C41E3A;' ;
else $color = '';  
    
$html .= '<table cellpadding="4" style="width:680; font-weight:bold;">';
$html .= '<tr><td style="text-align:right;width: 590px;">TOTAL PROFIT</td><td style="text-align:right; width: 90px;">'.$obj->formatNumber($total).'</td></tr>';
$html .= '<tr><td style="text-align:right;">TARGET PROFIT</td><td style="text-align:right">'.$obj->formatNumber($rs[0]['targetprofit']).'</td></tr>';
$html .= '<tr><td style="text-align:right;">DASAR PERHITUNGAN INSENTIF</td><td style="text-align:right; '.$color.'">'.$obj->formatNumber($total-$rs[0]['targetprofit']).'</td></tr>';
$html .= '<tr><td style="text-align:right;">INSENTIF '.$obj->formatNumber($rs[0]['commissionpercentage'],2).' %</td><td style="text-align:right">'.$obj->formatNumber($rs[0]['totalcommission']).'</td></tr>';
$html .= '</table>' ; 


return $html;
}

?>