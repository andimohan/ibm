<?php

function customGenerateQuickView($obj,$id){ 
  
    $supplier = new Supplier();
	
    $rs = $obj->searchData($obj->tableName.'.pkey', $id);
   
    $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    
    $generalInformation = '<div class="data-card border-orange">
                                <h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
                                <div class="content">
                                    <div class="div-table  general-information-table">  
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['code']).'</div>
                                            <div class="div-table-col">'.$rs[0]['code'].'</div> 
                                        </div>  
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['jobOrderDate']).'</div>
                                            <div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
                                        </div>    
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                            <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                                        </div>  
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.strtoupper($obj->lang['si']).'</div>
                                            <div class="div-table-col">'.$rs[0]['donumber'].'</div> 
                                        </div>   
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['customer']).'</div>
                                            <div class="div-table-col">'.$rs[0]['customername'].'</div> 
                                        </div>
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['consignee']).'</div>
                                            <div class="div-table-col">'.$rs[0]['consigneename'].'</div> 
                                        </div>
                                        <div class="div-table-row">
                                            <div class="div-table-col">'.ucwords($obj->lang['supplier']).'</div>
                                            <div class="div-table-col">'.$rsSupplier[0]['name'].'</div> 
                                        </div>
                                    </div>
                                </div>
                          </div>
                      ';
       
    $rsCarDetail = $obj->getCarDetail($rs[0]['pkey']);
    $carInformation = '<div class="data-card border-green">
						<h1>'.ucwords($obj->lang['car']).'</h1> 
						<div class="content">
						 <div class="div-table quick-view-table">
                                <div class="div-table-row">  
                                    <div class="div-table-col detail-col-header" style="text-align:right; width: 30px">'.ucwords($obj->lang['qty']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:left;">'.ucwords($obj->lang['service']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:left; width: 80px">'.ucwords($obj->lang['carRegistrationNumber']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:left; width: 150px">'.ucwords($obj->lang['containerNumber']). '</div> 
                                    <div class="div-table-col detail-col-header" style="text-align:left; width: 100px">'.ucwords($obj->lang['sealNumber']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['price']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['tax']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['total']).'</div>
                                </div>';

    for($i=0;$i<count($rsCarDetail);$i++){ 
                    $carInformation .= '
                        <div class="div-table-row">
                            <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCarDetail[$i]['qty']).'</div>
                            <div class="div-table-col">'.$rsCarDetail[$i]['itemname'].'</div>
                            <div class="div-table-col">'.$rsCarDetail[$i]['carregistrationnumber'].'</div> 
                            <div class="div-table-col">'.$rsCarDetail[$i]['container'].'</div> 
                            <div class="div-table-col">'.$rsCarDetail[$i]['seal'].'</div> 
                            <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCarDetail[$i]['price']).'</div> 
                            <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCarDetail[$i]['taxvalue']).'</div> 
                            <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCarDetail[$i]['total']).'</div> 
                        </div>   
                    ';
     }
 
    $carInformation .= '</div></div></div>'; 

    
    $rsCost = $obj->getCostDetail($id); 
    $costInformation = '<div class="data-card border-blue"  style="margin-top:1em">
						<h1>'.ucwords($obj->lang['cost']).'</h1> 
						<div class="content"> 
                             <div class="div-table  quick-view-table">
                             <div class="div-table-row">  
                                <div class="div-table-col detail-col-header" style="text-align:right; width: 30px">'.ucwords($obj->lang['qty']).'</div>
                                <div class="div-table-col detail-col-header" style="text-align:left; ">'.ucwords($obj->lang['costName']).'</div>
                                <div class="div-table-col detail-col-header" style="text-align:left; width: 130px">'.ucwords($obj->lang['employee']).'</div>
                                <div class="div-table-col detail-col-header" style="text-align:left; width: 130px">'.ucwords($obj->lang['supplier']).'</div> 
                                <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['cost']).'</div>
                                <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['tax']).'</div>
                                <div class="div-table-col detail-col-header" style="text-align:right; width: 80px">'.ucwords($obj->lang['total']).'</div>
                            </div>';
 
    for($i=0;$i<count($rsCost);$i++){

        $requestAmount = false;
        $cost = (!empty($rsCost)) ? $rsCost[$i]['amount'] : 0;

        if ($cost == 0){
          if(empty($rsCost[$i]['requestamount']))   continue;  

          $requestAmount = true;    
          $cost = $rsCost[$i]['requestamount'];
        } 

        $suppliername = (isset($rsCost[$i]['suppliername'])) ? $rsCost[$i]['suppliername'] : '';
        $employeename = (isset($rsCost[$i]['employeename'])) ?$rsCost[$i]['employeename'] : ''; 
        $asterix = ($requestAmount) ? '<span class="asterix">*</span>' : '';  
        $costInformation .= '
            <div class="div-table-row">
                <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCost[$i]['qty']).'</div>
                <div class="div-table-col">'.$rsCost[$i]['name'].$asterix.'</div>
                <div class="div-table-col">'.$employeename.'</div> 
                <div class="div-table-col">'.$suppliername.'</div> 
                <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($cost).'</div> 
                <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCost[$i]['taxvalue']).'</div> 
                <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsCost[$i]['total']).'</div> 
            </div>   
        ';
    }
    
    $costInformation .= '</div></div></div>';
 
    
    $detail = '<div class="div-table" style="width:100%; ">
                    <div class="div-table-row">
                        <div class="div-table-col-5" style="width:25%;">
                        '.$generalInformation.'
                        </div> 
                        <div class="div-table-col-5">
                            '.$carInformation.' 
                            '.$costInformation.'
                        </div>
                    </div>
				</div>';
				  
    $detail .= '<div style="clear:both;"></div>';	
    

	return $detail;  
}
 
 
?>