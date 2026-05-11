<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
require_once '_report-config.php';  
  
$obj = $itemDepotMovement;
 
$arrFilterInformation = array();    
  
// ===== FOR EXPORT SECTION
$dataToExport = array(); 

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"120px");
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"150px",'align' => 'center','format'=>'datetime'); 
$arrDataStructure['doCode'] = array('title'=>ucwords($obj->lang['doCode']),'dbfield' => 'docode', 'width'=>"120px" );
$arrDataStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"100px" );
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"280px" );
$arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qtyinbaseunit', 'width'=>"80px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
$arrDataStructure['totalWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'totalweight', 'width'=>"120px" ,'format'=>'decimal','calculateTotal' => true);
$arrDataStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
//$arrDataStructure['totalVolume'] = array('title'=>ucwords($obj->lang['totalVolume']),'dbfield' => 'totalvolume', 'width'=>"100px" ,'format'=>'decimal','calculateTotal' => true);
//$arrDataStructure['volumeUnit'] = array('title'=>'','dbfield' => 'volumeunit', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');   
	  
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['dailyReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

 
// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['file'] = array('title'=>ucwords($obj->lang['file']),'dbfield' => 'filelist', 'width'=>"1000px");  

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();
 
array_push($arrTemplate, $arrDetailTemplate); 


if (!isset($_POST['hidAction']) && empty($_POST['hidAction'])){
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y'); 
}

$criteria = ' and '.$obj->tableName.'.statuskey = 1 and customerkey = ' . $class->oDbCon->paramString(USERKEY);

if(isset($_POST) && !empty($_POST['refCode'])) {
    $criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
    array_push($arrFilterInformation,array("label" => 'Kode Ref', 'filter' => $_POST['refCode']));
}

if(isset($_POST) && !empty($_POST['trStartDate'])){
    $criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
    array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}

if(isset($_POST) && !empty($_POST['selDepot'])) { 

    $key = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   

    $criteria .= ' AND depotkey in('.$key.')';  

    $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$key.')');

    $arrTempStatus = array();
    for ($k=0;$k<count($rsCriteria);$k++)
        array_push($arrTempStatus,$rsCriteria[$k]['name']);

    $depotName = implode(", ",$arrTempStatus); 
    array_push($arrFilterInformation,array("label" => $obj->lang['depot'], 'filter' => $depotName ));

    $_POST['selDepot[]'] = $_POST['selDepot'];
}


if(isset($_POST) && !empty($_POST['itemName'])) { 
    $criteria .= ' AND itemname LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
    array_push($arrFilterInformation,array("label" => $obj->lang['itemName'], 'filter' => $_POST['itemName']));
}
  
$orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?$obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate';  
$orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 

$order = 'order by '.$orderBy.' ' .$orderType;
 
$rs = $obj->searchData('','',true,$criteria,$order); 

$tempreport = '';  
for($i=0;$i<count($rs);$i++) { 
    $arrHeaderStyle = array();
 
    $rs[$i]['volumeunit'] = 'CM<sup>3</sup>';

    if ($rs[$i]['qtyinbaseunit'] < 0) {  
         foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
            if (isset($el['dbfield']))
                $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';      
    }
    
    
    // cek jenis nya item in, out atau transfer
    $arrFileList = array();
    $rsKey = $obj->getTableNameAndObjById($rs[$i]['reftabletype']); 
    $movementObj = $rsKey['obj'];
    
    
    $rsDetail = array();
    $rsLink = array();
    foreach($movementObj->fileType as $key=>$row){
        $rsItemFile = $movementObj->getItemFile($rs[$i]['refkey'],$key);
        foreach($rsItemFile as $file){
            $filePath = $row['uploadFileFolder'].$rs[$i]['refkey'].'/'.$file['file']; 
            if (!is_file($obj->defaultDocUploadPath.$filePath)) continue;
            $filelink = '<div class="tag"><a href="/download.php?filename='.$filePath.'" target="_blank">'.$file['file'].'</a></div>';
            array_push($rsLink, $filelink);
        }
    }
      
    array_push($rsDetail, array('filelist' => implode('',$rsLink)) ); 
    
    // has detail
    $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);


    $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate);  

    // ===== FOR EXPORT SECTION 
    array_push($dataToExport, $return['data']);  
    // ===== END FOR EXPORT SECTION

    $tempreport .= $return['html'];  

    $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

}

// EXPORT TO EXCEL
if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){   
    $excel = new Excel();

    $arrTemplate[0]['dataToExport'] = $dataToExport;
    $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
    $excel->exportToSave($arrTemplate,null,array('name' => $rsCustomer[0]['name'] ));       
    die;
}


$arrHeaderTemplate = $arrTemplate[0];     

$reportContent = $tempreport;
if (empty($reportContent)) 
    $reportContent = '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

// total rows
$reportContent .= $obj->formatReportFooterRows($arrHeaderTemplate['total'],$arrHeaderTemplate['dataStructure']); 
$arrDepot = $class->convertForCombobox($depot->searchData($depot->tableName.'.statuskey',1,true,' and isprivate = 1','order by name asc'),'pkey','name');
   
//$arrTwigVar['inputRefCode'] = $class->inputText('refCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelDepot'] = $class->inputSelect('selDepot[]', $arrDepot, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputItemName'] = $class->inputText('itemName'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
  
$arrTwigVar['arrFilterInformation'] = $arrFilterInformation;  
$arrTwigVar['reportContent'] = $reportContent;

$arrTwigVar['hidOrderBy'] = $class->inputHidden('hidOrderBy');
$arrTwigVar['hidOrderType'] = $class->inputHidden('hidOrderType'); 
$arrTwigVar['order'] = array("orderBy" => $orderBy, "orderType" => (isset($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1);
 

echo $twig->render('report-item-movement-depot.html', $arrTwigVar);

?>