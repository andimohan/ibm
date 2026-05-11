<?php 

function customGenerateQuickView($obj,$id){ 

	global $warehouse;
	global $itemMovement;
    global $itemUnit;
	    
	$rs = $obj->getDataRowById($id);   
	
	$detail = '';
	 
 	$rsItemImage = $obj->getItemImage($id);
	$itemDescription =  str_replace(chr(13),'<br>',$rs[0]['shortdescription']);		 
 	 
    $img = (!empty($rsItemImage)) ? '<div class="image" style="background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rsItemImage[0]['file'].'&w=200&h=160&far=C&hash='.getPHPThumbHash($rsItemImage[0]['file']).'\'); "></div>' : '';
	$itemImage  = ' <div class="data-card border-blue">
								<h1>'.ucwords($obj->lang['description']).'</h1> 
								<div class="content">
									<div class="div-table" style="float:left; width:100%">
										<div class="div-table-row">
											<div class="div-table-col" style=" padding:0.3em; ">'.$itemDescription.'</div> 
											<div class="div-table-col image-panel">'.$img.'</div>  
										</div>
									</div>  
								</div>	
					</div>';			
	
	  $rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'',' order by '.$warehouse->tableName.'.orderlist asc, name asc');
	  $rsItemUnit = $itemUnit->getDataRowById($rs[0]['baseunitkey']);

      $stockRow = '';
	  for($i=0;$i<count($rsWarehouse);$i++){
			 
            $qoh = 0;
            $qohInPcs = 0;
            $baseUnitName = '';

            $colorClass ="";
            $colorInPcsClass ="";
            
            $qoh = $obj->formatNumber($itemMovement->getItemQOH($id, $rsWarehouse[$i]['pkey']));
            $qohInPcs = $obj->formatNumber($itemMovement->getItemQOHInPcs($id, $rsWarehouse[$i]['pkey']),2);
            
            if ($rsWarehouse[$i]['isqohcount']) 
                $colorClass="text-green-avocado";
            
            if ($qoh == 0){
                $colorClass="text-red-cardinal";
            }

            if($qohInPcs == 0) {
                $colorInPcsClass = "text-red-cardinal";
            }
            
            if(!empty($rsItemUnit)) {
                $baseUnitName = $rsItemUnit[0]['name'];
            }
            
			 
			 $stockRow .= '
             <div class="div-table-row"> 
                 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;" >   <strong>'.$obj->lang['warehouse'].'</strong> </div> 
                 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" >  <strong>'.ucwords($obj->lang['qty']).'</strong>  </div> 
                 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:left;" >  <strong>'.ucwords($obj->lang['baseunit']).'</strong>  </div> 
                 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" >  <strong>'.ucwords($obj->lang['qty']).' (PCS)</strong>  </div> 
             </div>
			 <div class="div-table-row"> 
				 <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" > 
					'.$rsWarehouse[$i]['name'].'
				 </div> 
				 <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > 
					'.$qoh.'
				 </div> 
				 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:left;" > 
					'.$baseUnitName.'
				 </div> 
				 <div class="div-table-col-5 '.$colorInPcsClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > 
					'.$qohInPcs.'
				 </div> 
			 </div> 
			 ';
	}
		 
	$stockInformation = '<div  class="data-card border-green"><h1>'.ucwords($obj->lang['stockInformation']).'</h1>
								<div class="content">
										<div class="div-table" style="width:100%">
											'.$stockRow.'
										</div>
								</div>
						</div>';	
	
	$detail .= '<div class="div-table" style="width:100%; ">
						<div class="div-table-row">
							<div class="div-table-col-5"  style="width:67%;">
							'.$itemImage.' 
							</div>  
							<div class="div-table-col-5">
							 '.$stockInformation.'
							</div> 
						</div>
				</div>';
			  
	$detail .= '<div style="clear:both;"></div>';	
	 
  
	return $detail;  
}

?>