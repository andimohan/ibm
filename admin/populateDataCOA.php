<?php 
 
function addDataListRow($rs,$arrColumn){
 	global $addDataFile; 
	global $obj;
	global $TAG_SHADOW;
	
	$datalistrow = '';
	
	for($i=0;$i<count($rs);$i++){    
        $shadowClass = (!empty($rs[$i]['tagkey'])) ? $obj->shadowClass[$rs[$i]['tagkey']] : '';  
        $inputStatusStyle = ($rs[$i]['statuskey'] == 2) ? 'text-silver' : ''; 
               
        $expandable = '';
        $style = '';
        if ($rs[$i]['isleaf'] == 0){ 
            $style .= 'font-weight:bold !important;'; 
            $expandable = 'expand-link clickable';
        }
            
        if ($rs[$i]['level'] <> 0)
            $style .= 'display:none';
        
        
        if (!empty($style))
            $style = 'style="'.$style.'"';
        
		$datalistrow .= '<li class="data-record '.$shadowClass.' '.$rs[$i]['rootpath'].'" relParentId="'.$rs[$i]['parentkey'].'" relId="'.$rs[$i]['pkey'].'" '.$style.'>';
        $datalistrow .= '<div class="table-data-record-header" >';   
	    $datalistrow .= ' <div class="div-table-row">';
					
         for($j=0;$j<count($arrColumn);$j++){ 
 
            $content = $rs[$i][$arrColumn[$j]['dbfield']];
            $format = (isset($arrColumn[$j]['format'])) ? strtolower($arrColumn[$j]['format']) : '';
            $width = (isset($arrColumn[$j]['width']) && !empty($arrColumn[$j]['width'])) ? 'width:'.$arrColumn[$j]['width'].'px' : '';
            $textAlign = (isset($arrColumn[$j]['align'])) ? 'text-align:'.$arrColumn[$j]['align'].';' : '';
             
            if ($rs[$i]['parentkey'] == 0)	
                    $content = strtoupper($content);

            if ($arrColumn[$j]['dbfield'] == 'coaname') 
              $content = '<span class="'.$expandable.'" style="margin-left:'.($rs[$i]['level']*2).'em">'.$content.'</span>';

            switch($format){
                case 'integer':  $content = $obj->formatNumber($content);
                                 break;
                case 'decimal':  $content = $obj->formatNumber($content,2);
                                 break;
                case 'number':  $content = $obj->formatNumber($content,-2);
                                 break;
                case 'accounting':  $content = ($content < 0 )  ? '('. $obj->formatNumber(abs($content),2) .')' : $obj->formatNumber($content,2);  
                                 break;
                case 'date':  $content = $obj->formatDbDate($content,'',array('returnOnEmpty' => true));
                                 break;
                case 'time':  $content = $obj->formatDbDate($content,'H:i');
                                 break;
                case 'datetime':  $content = $obj->formatDbDate($content,'d / m / Y H:i');
                                 break;
            }
  
             $datalistrow .= ' <div style="'.$textAlign.' '. $width.'"  class="div-table-col"><span class="unselectable '.$inputStatusStyle.'">'. $content .'</span></div> ';

         } 


        $rowIcon = (isset($rs[$i]['systemVariable']) && $rs[$i]['systemVariable'] == 1) ? '<i class="fas fa-lock"></i>' : ''; 
        $datalistrow .= '<div style="text-align:center; width: 30px;" class="div-table-col-5 tag">'.$rowIcon.'</div>'; 
        $datalistrow .= '</div>';
        $datalistrow .= '</div> ';   
        $datalistrow .= '<div class="table-data-record-detail'.$rs[$i]['pkey'].' table-data-record-detail" ></div>';  
        $datalistrow .=  '</li> '; 

	}
	
	
	 $datalistrow .= '
	  <script>
	  	$( document).ready(function() {    

        if(!isMobile()){
            $( "#" + selectedTab.newPanel[0].id + " .selectable").selectable({
				filter : "li",	
				cancel: ".unselectable, .data-card",
				 stop: function() {    
					var selectedPkey = Array();
					$( ".ui-selected", this ).each(function() { 
					     selectedPkey.push($(this).attr("relId"));
					});
					 
					tabParam[selectedTab.newPanel[0].id].selectedPkey = selectedPkey;  
					 
				  }
			 })   
                
        }
			
              $( "#" + selectedTab.newPanel[0].id + " .expand-link").on(\'click\', function() {
              
                 var liObj = $(this).closest(".data-record");
                 var id = liObj.attr("relId");  
                 
                 if (liObj.hasClass("expand")){
                    $( "#" + selectedTab.newPanel[0].id + " ." + id).removeClass("expand").hide();
                    liObj.removeClass("expand");
                 }else{
                    $( "#" + selectedTab.newPanel[0].id + " [relParentId="+id+"]").show();
                    liObj.addClass("expand");
                 }
                 
              });
			 
		});
        
        function expandAll(){
        
        }
	 
	 </script>
';
	  
	  
	return $datalistrow;
}

function buildDataList($rs,$arrColumn){ 
	global $obj;
	 
	$datalist  = '<ol class="data-list-row selectable">';
	$datalist .= addDataListRow($rs,$arrColumn); 
	$datalist .= '</ol> '; 
	$datalist .= '<div class="load-more user-select-none">
						'.$obj->lang['nextPage'].'  '.$obj->loadingIcon.' </span> 
                  </div>';   
	return $datalist; 
} 


$quicksearchcriteria = '';
$quickSearchKey = '';  
$statusCriteria = '';
$tagCriteria = '';

if (isset($_POST) && !empty($_POST['quickSearchKey'])){
	$quickSearchKey = $_POST['quickSearchKey']; 
	for($i=0;$i<count($arrSearchColumn);$i++){
		$quicksearchcriteria .= $arrSearchColumn[$i][1] .' like ('.$obj->oDbCon->paramString( '%'.$quickSearchKey.'%' ).') ';	
		
		if($i<>count($arrSearchColumn) -1 )
			$quicksearchcriteria  .= ' or ';
			
	}
	$quicksearchcriteria = ' and (' .$quicksearchcriteria.')';
}

   
$selectedCriteriaStatusKey = array();
if(!empty($_POST['selectedCriteriaStatusKey']))
  $selectedCriteriaStatusKey = $_POST['selectedCriteriaStatusKey'];

$statusKeyCriteria = '';
if (!empty($selectedCriteriaStatusKey )){
	for ($i=0;$i<count($selectedCriteriaStatusKey); $i++){
		$statusKeyCriteria .= $obj->oDbCon->paramString($selectedCriteriaStatusKey[$i]);
		
		if ($i < count($selectedCriteriaStatusKey) -1 )
			$statusKeyCriteria .= ',';
	} 
	$statusCriteria = ' and ' . $obj->tableName .'.statuskey in ('.$statusKeyCriteria.')'; 
}  


   
$selectedCriteriaTagKey = array();
if(!empty($_POST['selectedCriteriaTagKey']))
  $selectedCriteriaTagKey = $_POST['selectedCriteriaTagKey'];

$tagKeyCriteria = '';
if (!empty($selectedCriteriaTagKey )){
	for ($i=0;$i<count($selectedCriteriaTagKey); $i++){
		$tagKeyCriteria .= $obj->oDbCon->paramString($selectedCriteriaTagKey[$i]);
		
		if ($i < count($selectedCriteriaTagKey) -1 )
			$tagKeyCriteria .= ',';
	} 
	$tagCriteria = ' and ' . $obj->tableName .'.tagkey in ('.$tagKeyCriteria.')'; 
}  


$orderby = 'orderlist';  
$ordertype = 'asc'; 
$adminTotalRowsPerPage = 999999;

$obj->setCriteria($quicksearchcriteria .$statusCriteria.$tagCriteria);  
$sortSql = ' order by '.  $orderby  .' '. $ordertype; 
$rs =  $obj->oDbCon->doQuery( $obj->getQuery() . $sortSql );  

$totalDataRows = count($rs);
$totalPages = ceil($totalDataRows/$adminTotalRowsPerPage);
$lastRowIndex = 0;
 
$arrReturn = array();
$arrReturn['dataList'] = buildDataList($rs,$arrColumn);   
$arrReturn['eof'] = true;   
$arrReturn['selectedPageIndex'] = 0;
$arrReturn['totalPages'] = 0; 
$arrReturn['lastRowIndex'] = $lastRowIndex + count($rs);

//status information
/*$arrStatusInformation = array();
$rsStatus = $obj->getAllStatus(); 

$changeStatusCallback = ''; 
$statusContextMenu = array();

for($i=0;$i<count($rsStatus);$i++){
	$statusCriteria = $quicksearchcriteria . ' and '.$obj->tableName.'.statuskey = ' .$obj->oDbCon->paramString($rsStatus[$i]['pkey']) ;	
		
	if(!empty($tagKeyCriteria))  
		$statusCriteria .= ' and '.$obj->tableName.'.tagkey in ('.$tagKeyCriteria.')';
 
	$arrStatusInformation[$i]['statusPkey'] = $rsStatus[$i]['pkey'];
	$arrStatusInformation[$i]['statusName'] = $rsStatus[$i]['status'];
	$arrStatusInformation[$i]['totalData'] = $obj->getTotalRows($statusCriteria);
	
	$changeStatusCallback  .= 'case "'.$rsStatus[$i]['status'].'":  
								 changeStatus("'.$rsStatus[$i]['pkey'].'",key);
								 break;'.chr(13);
								 
	$statusContextMenu[$rsStatus[$i]['status']]['name'] = $rsStatus[$i]['status'];
	
}

$arrReturn['statusInformation'] = $arrStatusInformation;*/

//tag information
/*$arrTagInformation = array();
$rsTag = $obj->getAllTag();

$tagCallback = ''; 
$tagContextMenu = array();

$tagCallback  .= 'case "ClearTag":  
						 changeTag("0",key);
						 break;'.chr(13);

$tagContextMenu['ClearTag']['name'] = $obj->lang['clearTag'];
	  
	
for($i=0;$i<count($rsTag);$i++){
	$tagCriteria = $quicksearchcriteria . ' and '.$obj->tableName.'.tagkey = ' .$obj->oDbCon->paramString($rsTag[$i]['pkey']) ;	
	
	if(!empty($statusKeyCriteria))  
		$tagCriteria .= ' and '.$obj->tableName.'.statuskey in ('.$statusKeyCriteria.')';
 
	$arrTagInformation[$i]['tagPkey'] = $rsTag[$i]['pkey'];
	$arrTagInformation[$i]['tagName'] = $rsTag[$i]['tagname'];
	$arrTagInformation[$i]['hexColor'] = $rsTag[$i]['hexcolor']; 
	$arrTagInformation[$i]['totalData'] = $obj->getTotalRows($tagCriteria);
	
	
	$tagCallback  .= 'case "'.$rsTag[$i]['tagname'].'":  
								 changeTag("'.$rsTag[$i]['tagkey'].'",key);
								 break;'.chr(13);
								 
	$tagContextMenu[$rsTag[$i]['tagname']]['name'] = $rsTag[$i]['tagname'];
	
}

$arrReturn['tagInformation'] = $arrTagInformation; */


   
$contextMenu = array();
$contextMenu["selectAll"] = array("name"=>$obj->lang['selectAll'], "icon"=>"selectall");
$contextMenu["deselectAll"] = array("name"=>$obj->lang['deselectAll'], "icon"=>"deselectall");
$contextMenu["separator1"] = "-----";
$contextMenu["showDetail"] = array("name" => $obj->lang['showDetail'], "icon"=>"showdetail");
$contextMenu["hideDetail"] = array("name" => $obj->lang['hideDetail'], "icon"=>"hidedetail");
$contextMenu["edit"] = array("name" => $obj->lang['viewOrEdit'], "icon" =>"edit");
$contextMenu["delete"] = array("name" =>  $obj->lang['delete'], "icon" =>"delete");
//$contextMenu["changeStatus"] =  array("name" => $obj->lang['changeStatus'], "icon" =>"changestatus","items" => $statusContextMenu);
//$contextMenu["changeTag"] = array("name" => $obj->lang['tag'],"icon" =>"tag", "items" => $tagContextMenu);  


$callbackFunction = '';
if (isset($overwriteContextMenu)){	 
	foreach ($overwriteContextMenu as $key => $value) {   
		$contextMenu[$key] = $overwriteContextMenu[$key];  
		if (!empty($contextMenu[$key]['callbackFunction']))  
		  $callbackFunction  .=  $contextMenu[$key]['callbackFunction']; 
	} 
} 

$arrReturn['contextMenu'] = array();
foreach ($contextMenu as $key => $value) {   
	if (!empty($value)) 
	  $arrReturn['contextMenu'][$key]=$contextMenu[$key];  
} 

//'.$changeStatusCallback .' 
// '.$tagCallback .' 
$arrReturn['contextMenuCallback'] = '  
									  switch(key) {
											case "selectAll":    
												selectAllRows(); 
												break;
											case "deselectAll": 
												deselectAllRows();
												break;
											case "showDetail": 
												toggleAllSelectedDataDetail(2);
												deselectAllRows(); 
												break;
											case "hideDetail": 
												toggleAllSelectedDataDetail(1);
												deselectAllRows(); 
												break;
											case "edit": 
												openTabForEdit(); 
												break;
											case "delete":  
												 deleteData();
												 break;    
											'.$callbackFunction.'
											default: 
												break;
										} '; 
 

echo json_encode($arrReturn);
die;

?>