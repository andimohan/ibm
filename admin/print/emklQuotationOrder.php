<?php

$PRINT_SETTINGS =  array(   
    'footer' => '<table style="text-align:right"><tr><td style="width:680px">'.$class->lang['page'].' {{ GROUP_PAGE_NO }}</td></tr></table>'
);


includeClass(array('EMKLQuotationOrder.class.php'));
$emklQuotationOrder = createObjAndAddToCol(new EMKLQuotationOrder());

$obj = $emklQuotationOrder;
$content = function ($dataset) {
global $pdf;    
    
    
$generateHeaderTable = function ($dataset, $param){
	global $pdf;    

    $obj = new EMKLQuotationOrder();
    $supplier = new Supplier();
    $city = new City();
    $customer = new Customer();
	$employee = new Employee();

    $termOfPayment = new TermOfPayment();
    $setting = new Setting();

    
    $rs = $dataset['rs'];
             
	$rsEmployee = $employee->getDataRowById($rs[0]['saleskey']);
    //header

    $rsPICCol = $customer->getContactPerson($rs[0]['customerkey']); 
    $rsPICCol = array_column($rsPICCol,null,'pkey'); 
    $revision = ($rs[0]['revision'] == 0) ? '' : '-'.$rs[0]['revision'];
      
     
    $jobType = $obj->getJobType($rs[0]['jobtypekey']); 

    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
 
  
//    $companyName = strtoupper($setting->loadSetting('companyName'));
//    $companyAddress = $setting->loadSetting('companyAddress');

//    $profileImg = $obj->loadSetting('companyLogo'); 
//    $logo = (isset($_GET['logo']) && $_GET['logo'] == 0) ? '' : '<img src="'.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'" style="height:150px">';
 
    $html = $obj->printSetting['defaultStyle']; 
	$html .= '<style>
					.header-row-header {font-weight:bold; width: 100px;}
					.terms ol, .terms ul {padding:0; margin:0 ;}	 
			 </style>';
	
  	$currRow = '';
	
    if($rs[0]['isshowcurrency'] == 1){
                $currRow = '<tr>
							<td class="header-row-header">Rate</td>
							<td style="width:10px; text-align:center">:</td>
							<td >'.$rs[0]['currencyname'].' '.$obj->formatNumber($rs[0]['rate'],-2).'</td>
						</tr> 
						';
    }
    
	$picName = (!empty($rs[0]['pickey'])) ? $rsPICCol[$rs[0]['pickey']]['name'].'<br>' : '';
	
	$html .= '
		<h1 style="text-align:center">Negotiated Rate Arrangement (NRA)</h1>
		<table>
			<tr>
				<td style="width: 415px">
					<table cellpadding="2" style="width:100%">
						<tr>
							<td class="header-row-header">'.$obj->lang['quoteTo'].'</td>
							<td style="width:10px; text-align:center"></td>
							<td ></td>
						</tr><tr> 
							<td colspan="3">'.$picName.'<b>'.$rs[0]['customername'].'</b><br>'.nl2br($rsCustomer[0]['address']).'</td>
						</tr>
					</table> 
				</td>
				<td style="width: 255px">
						<table cellpadding="2" style="width:100%">
						<tr>
							<td class="header-row-header">'.$obj->lang['quoteNo'].'</td>
							<td style="width:10px; text-align:center">:</td>
							<td style="width: 120px">'.$rs[0]['code'].'</td>
						</tr>
						<tr>
							<td class="header-row-header">'.$obj->lang['quoteDate'].'</td>
							<td style=" text-align:center">:</td>
							<td >'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
						</tr>
						<tr>
							<td class="header-row-header">'.$obj->lang['expDate'].'</td>
							<td style="text-align:center">:</td>
							<td >'.$obj->formatDBDate($rs[0]['expdate'],'d / m / Y').'</td>
						</tr>
						 '.$currRow.' 
						 
						<tr>
							<td class="header-row-header">'.$obj->lang['preparedBy'].'</td>
							<td style=" text-align:center">:</td>
							<td >'.$rsEmployee[0]['name'].'</td>
						</tr>
					</table>	
				</td>
			</tr>
		</table>';
    
    $html .= '</table>
            </td>
        </tr>
		</table>';

	$html .= '<div style="clear:both;"></div>';
	if(!empty($rs[0]['headertext'])){ 
		$html .= '<div style="clear:both;"></div>';
		$html .= html_entity_decode($rs[0]['headertext']); 
		$html .= '<div style="clear:both"></div>'; 
	}
    
    return $html;
    
};
    
$generateBodyTable = function ($dataset, $param){
    
    $obj = new EMKLQuotationOrder();

    $container = new Container();
    $customer = new Customer();
    $service = new Service(SERVICE);
    $locType = $param['locTypeKey'];
    
        
    $rs = $dataset['rs'];     
    

    
    switch($locType){
        case LOC_TYPE['origin']:
            
            $labelHeader = 'Origin Charges';
            $rsDetail = $obj->getDetailOriginInformation($rs[0]['pkey']);  
            break;
            
        case LOC_TYPE['freight']:
            
            $labelHeader = 'Freight';
            $rsDetail = $obj->getDetailFreight($rs[0]['pkey']); 
            break;
            
        case LOC_TYPE['destination']:
            
            $labelHeader = 'Destination Charges';
            $rsDetail = $obj->getDetailDestinationInformation($rs[0]['pkey']); 
            break;
    }
	
    $arrServiceKey = array();
    $arrDetailKey = array_column($rsDetail,'pkey');
	$arrServiceFreightKey = array_column($rsDetail,'servicekey');
	array_push($arrServiceKey,$arrServiceFreightKey);

	$rsDetail = $obj->reindexDetailCollections($rsDetail,'polpodkey'); 
	
	
    $arrContainerType = $obj->getContainerPrice($arrDetailKey,$locType);


    $rsService = $service->searchDataRow(array($service->tableName.'.pkey',$service->tableName.'.name',$service->tableName.'.aliasname'), 
                                               ' and '.$service->tableName.'.pkey in ('.$obj->oDbCon->paramString(array_unique($arrServiceKey[0]),',').')
                                                 and '.$service->tableName.'.statuskey = 1'
                                        
                                        );  
    $rsService = array_column($rsService,null,'pkey');
    
    $rsAliasItemCust = $customer->getItemAliasDetail($rs[0]['customerkey']);
    $rsAliasItemCust = array_column($rsAliasItemCust,null,'itemkey');
    
    $arrContainerKey = array_column($arrContainerType,'containerkey');
    $arrContainerKey = array_unique($arrContainerKey);
 
    
    $arrContainers = $container->searchDataRow(array($container->tableName.'.pkey',$container->tableName.'.name'), 
                                               ' and '.$container->tableName.'.statuskey = 1 
                                                and '.$container->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrContainerKey,',').')');
    $totalContainer = count($arrContainers);
    
    
    $arrContainerCol = array_column($arrContainers,null,'pkey');

    $containerCol = '';
    $containerColFreight = '';
    
    $arrTemp = array();

	if(!empty($rsDetail)){
		$arrCarrierData = array();
		$arrTempPOLPOD = array(); 
		$arrContKey = array();

	 foreach ($rsDetail as $polpodKey => $dataDetail){  
 
		 $arrPort = array();
		 if(!empty($dataDetail[0]['polname'])) array_push($arrPort,$dataDetail[0]['polname']);
		 if(!empty($dataDetail[0]['podname'])) array_push($arrPort,$dataDetail[0]['podname']);
		 
		 $arrPort = array_unique($arrPort);
		 $destinationPort = implode(' to ',$arrPort);
 
			if(!isset($arrTemp[$polpodKey])){ 
				$arrTempPOLPOD[$polpodKey]['polpodname'] = $destinationPort;

				$arrTemp[$polpodKey] = array();
				$arrTemp[$polpodKey]['polpodname'] = $destinationPort; 
				$arrTemp[$polpodKey]['price'] = 0; 
			}


			$totalDestinationPort = 0;
			for($i=0;$i<count($dataDetail);$i++){

				$itemname = (!empty($rsAliasItemCust[$dataDetail[$i]['servicekey']]['alias'])) ? $rsAliasItemCust[$dataDetail[$i]['servicekey']]['alias'] : $rsService[$dataDetail[$i]['servicekey']]['name'];
				$servicename = (!empty($dataDetail[$i]['alias'])) ?  $dataDetail[$i]['alias'] : $itemname;
				$carrierName = (!empty($dataDetail[$i]['carriername'])) ? $dataDetail[$i]['carriername'] : '';

				$arrGrouping = array();

				if(!empty($carrierName) && $locType == LOC_TYPE['freight'])
					array_push($arrGrouping,'-'.$carrierName);

				if(!empty($dataDetail[$i]['isperreciept']) && in_array($locType,NO_FREIGHT_TYPE))
					array_push($arrGrouping,'-'.$dataDetail[$i]['isperreciept']);

				$groupingIndex = '';
				$groupingIndex .= implode('',$arrGrouping);

				$indexKey = strtolower($servicename).$groupingIndex;

				  if(!isset($arrTemp[$polpodKey][$indexKey])){

						$arrTemp[$polpodKey][$indexKey] = array();
						$arrTemp[$polpodKey][$indexKey]['servicename'] = $servicename;
               
						if(!empty($dataDetail[$i]['carriername']) && !empty($dataDetail[$i]['carrierkey'])){
							$arrTemp[$polpodKey][$indexKey]['carriername'] = $dataDetail[$i]['carriername'];
							$arrTemp[$polpodKey][$indexKey]['carrierkey'] = $dataDetail[$i]['carrierkey'];
						}
					  
					  	$unitName = (!empty($dataDetail[$i]['unitname'])) ? '/ '.$dataDetail[$i]['unitname'] : '';

						$arrTemp[$polpodKey][$indexKey]['currencyname'] = $dataDetail[$i]['currencyname'];
						$arrTemp[$polpodKey][$indexKey]['unitname'] = $unitName;
						$arrTemp[$polpodKey][$indexKey]['remarks'] = $dataDetail[$i]['remarks'];
						$arrTemp[$polpodKey][$indexKey]['isperreciept'] = $dataDetail[$i]['isperreciept'];
						$arrTemp[$polpodKey][$indexKey]['price'] = 0;

					}

					   $arrCost = array();


						foreach(RATE_TYPE as $rateType => $key){


							switch($key ){
								case RATE_TYPE['rate']:
									$fieldRate = 'price';
									break;
								case RATE_TYPE['minimum']:
									$fieldRate = 'minimumprice';
									break;
								case RATE_TYPE['normal']:
									$fieldRate = 'normalprice';
									break;
							}



							  if($dataDetail[$i][$fieldRate] > 0 ) {

								// add tipe rate ke container key

								if (!in_array($rateType, $arrContKey))
									array_push($arrContKey,$rateType);

								$arrCost[$rateType]['price'] = $dataDetail[$i][$fieldRate];
								$sellingRatePrice = ($arrCost[$rateType]['price'] == 0) ? 0 : $arrCost[$rateType]['price'];
								$arrTemp[$polpodKey][$indexKey][$rateType]['price'] += $sellingRatePrice;
								$arrTemp[$polpodKey][$indexKey]['price'] += $sellingRatePrice;
								$totalDestinationPort += $sellingRatePrice;


							}   

						}




					if(!empty($arrContainerKey)){


						for($k=0;$k<count($arrContainerKey);$k++){

								$contKey = $arrContainerKey[$k]; 
								$rsContainer = $obj->getContainerPrice($dataDetail[$i]['pkey'],$locType,$contKey);
							
								for($ctr=0;$ctr<count($rsContainer);$ctr++){

									if (!in_array($rsContainer[$ctr]['containerkey'], $arrContKey))
									array_push($arrContKey,$rsContainer[$ctr]['containerkey']);

									$arrCost[$rsContainer[$ctr]['containerkey']]['price'] = $rsContainer[$ctr]['price'];
								}


								$sellingPrice = ($arrCost[$contKey]['price'] == 0) ? 0 : $arrCost[$contKey]['price'];               
								$arrTemp[$polpodKey][$indexKey][$contKey]['price'] += $sellingPrice;
								$arrTemp[$polpodKey][$indexKey]['price'] += $sellingPrice;
 
								$totalDestinationPort += $sellingPrice; 
						}

					}


			}

			$arrTemp[$polpodKey]['price'] = $totalDestinationPort; 


			}


			$count = count($arrContKey) + 4; 
			$totalPrice = 0;


			$arrTempData = array();
			foreach($arrTemp as $temp => $value){ 

				$arrTempData[$temp] = array();

				foreach($value as $key  => $val){ 

					$carierKey = (!empty($val['carriername'])) ? $val['carriername'] : '';
					if(!isset($arrTempData[$temp][$carierKey])){
						$arrTempData[$temp][$carierKey] = array();

					}

					array_push($arrTempData[$temp][$carierKey],$val);
				}

			}

		//origin destination
		foreach($arrTemp as $temp =>$value ){


				if(!empty($arrTemp[$temp]['polpodname']) && $arrTemp[$temp]['price'] > 0 ){ 
					  $containerCol .='<tr><td></td></tr>';
					  $containerCol .='<tr><td colspan ="'.$count.'" style="font-weight:bold;">'.$arrTemp[$temp]['polpodname'].'</td></tr>';
				}
 
					foreach($value as $val){ 
						
						if(is_array($val)){
							if($val['isperreciept'] == 1 || $val['price'] > 0){
								$totalPrice += $val['price'];

								$containerCol .='
									<tr>
										<td>'.$val['servicename'].'</td>
										<td style="text-align: center;">'.$val['currencyname'].'</td>';


								   foreach($arrContKey as $row){

										if ($val['isperreciept'] == 1) {
											$price = 'as per receipt';
										} else { 
											$price = ($val[$row]['price'] <= 0) ? '' : $obj->formatNumber($val[$row]['price']);
										}

										$containerCol .='<td style="text-align: right;">'.$price.'</td>';

								   }


									$containerCol  .= '<td style="">'.$val['unitname'].'</td>
													   <td style="">'.$val['remarks'].'</td>
									</tr>
							   ';
							}
						}
						
					}   

			}

		//freight only
		//di freight bisa grouping berdasarkan carrier
		foreach($arrTempData as $temp => $value){


				if(!empty($arrTempPOLPOD[$temp]['polpodname'])){ 

					  	$containerColFreight .='<tr><td colspan ="'.$count.'"> </td></tr>'; 
						$containerColFreight .='<tr> <td colspan ="'.$count.'" style="font-weight:bold;">'.$arrTempPOLPOD[$temp]['polpodname'].'</td></tr>'; 
				}


				foreach($value as $carrier => $val){

					if(!empty($carrier)){
						$containerColFreight .='
						<tr>
							<td colspan ="'.$count.'" style="font-weight:bold; font-style:italic;">'.$carrier.'</td>
						</tr>';
					}


					foreach($val as $v){
                 
						if(is_array($v)){
							if($v['isperreciept'] == 1 || $v['price'] > 0){
									$totalPrice += $v['price'];

									$containerColFreight .='
										<tr>
											<td style="width:150px;">'.$v['servicename'].'</td>
											<td style="width:30px;">'.$v['currencyname'].'</td>';

									if(!empty($arrContKey)){

									   foreach($arrContKey as $row){


											if ($v['isperreciept'] == 1) {
												$price = 'as per receipt';
											} else { 
												$price = ($v[$row]['price'] <= 0) ? '' : $obj->formatNumber($v[$row]['price']);
											}


										   $containerColFreight .='<td style="text-align:right;width:90px;">'.$price.'</td>';

									   }
									}

										$containerColFreight  .= '<td style="">'.$v['unitname'].'</td>
																	<td style="">'.$v['remarks'].'</td>
																</tr>
														   ';

							}
						}



				}
					

			}


		}

			$containerCol = ($locType == LOC_TYPE['freight']) ? $containerColFreight : $containerCol;
			//ini kalau misalkan total price nya 0
			if($totalPrice > 0){

			   $html .='
				<table cellpadding="2">';
				$cellArray = array ();
				array_push($cellArray, array('label' => strtoupper($labelHeader), 'width' => '150'));
				array_push($cellArray, array('label' => 'Curr', 'align' => 'center', 'width' => '30'));

				if(!empty($arrContKey)){
					foreach($arrContKey as $row){

						$containerName = '';

						foreach(RATE_TYPE as $rateType => $typeKey){ 
							if($row == $rateType) 
								 $containerName .= ucwords($rateType);  
						}
						
						if($row == $arrContainerCol[$row]['pkey'])
						  $containerName .= $arrContainerCol[$row]['name'];

						array_push($cellArray, array('label' => $containerName, 'align' => 'right', 'width' => '90'));

					}
				}

				array_push($cellArray, array('label' => 'Unit', 'width' => '80'));
				array_push($cellArray, array('label' => 'Remarks'));
				$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  


				$html .= $containerCol;

			   $html .= '</table>';
			}

			$html .= '<br><br><br>';
			return $html;

		}


};
    
$generateFooterTable = function ($dataset, $param){
    
    $obj = new EMKLQuotationOrder();
    $rs = $dataset['rs'];     
 
	$html .='<div class="terms" >';
	$html .= '<div><b>'.$obj->lang['termsAndConditions'].'</b></div>';
	$html .= html_entity_decode($rs[0]['termsandconditions']);
	$html .'</div>';
	
//	$rsTermsAndCondition = $obj->getDetailTermAndCondition($rs[0]['pkey']);
    
      
//	$html .= '	
//		<table style="width: 670px;">
//
//			<tr><td colspan="2" style="font-weight:bold;"></td></tr>';
//            
//            for($i=0;$i<count($rsTermsAndCondition);$i++){ 
//                $html .='<tr><td style="width:25px;text-align:right;">'.($i+1).'.</td><td style="width: 645px">'.$rsTermsAndCondition[$i]['name'].'</td></tr>';
//
//            }
//            
//       $html .='</table>';
  
    return $html;
 

};

    $obj = new EMKLQuotationOrder();
    
    $html = $generateHeaderTable($dataset,array('locTypeKey' => ''));
    $html .= $generateBodyTable($dataset,array('locTypeKey' => LOC_TYPE['freight']));
    $html .= $generateBodyTable($dataset,array('locTypeKey' => LOC_TYPE['origin']));
    $html .= $generateBodyTable($dataset,array('locTypeKey' => LOC_TYPE['destination']));
    $html .= $generateFooterTable($dataset,array('locTypeKey' => ''));
	return  $html;

};

$generateReportContent = array();
array_push($generateReportContent , array('content' => $content));

?>