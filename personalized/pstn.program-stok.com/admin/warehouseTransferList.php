<?php

function customGenerateQuickView($obj,$id){ 
  
	$rsDetail = $obj->getDetailWithRelatedInformation($id); 
	 
	$detail = '';
	
	$detailInformation  = ' <div class="data-card no-border">
					<h1>Detail Transfer</h1> 
					<div class="content">
					<div class="div-table  quick-view-table">
						  <div class="div-table-row"> 
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div> 
								<div class="div-table-col detail-col-header" style="width:50px;">Unit</div>
								<div class="div-table-col detail-col-header"  style="width:300px;">Item</div>
								<div class="div-table-col detail-col-header"></div>  
							</div>';
	
	for ($i=0;$i<count($rsDetail);$i++){ 
	
		$detailInformation  .= '
			<div class="div-table-row"> 
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div> 
				<div class="div-table-col">'.$rsDetail[$i]['unitname'].'</div> 
				<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
				<div class="div-table-col"></div>  
			</div>
		';
	}
							
	$detailInformation  .= ' </div>
					</div>
				</div>  
	'; 	
		

	$detail .= $detailInformation;
			  
	$detail .= '<div style="clear:both;"></div>';							
							
	return $detail;  
}
 
 
?>