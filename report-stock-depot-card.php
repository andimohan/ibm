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
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'name', 'width'=>"300px", 'mergeExcelCell' => 4); 
$arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"180px", 'dbfield' => 'categoryname', 'mergeExcelCell' => 4); 
$arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'qtyonhand', 'width'=>"70px",'format'=>'number', 'textColor' => '0093AF');  
$arrDataStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');  
$arrDataStructure['totalWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'totalweight', 'width'=>"90px",'format'=>'decimal', 'textColor' => '9A4EAE');  
$arrDataStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');   
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"150px", 'mergeExcelCell' => 4);
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['stockCardDepotReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"130px",'format'=>'datetime'); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"90px"); 
$arrDataDetailStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"90px"); 
$arrDataDetailStructure['doCode'] = array('title'=>ucwords($obj->lang['doCode']),'dbfield' => 'docode', 'width'=>"130px"); 
$arrDataDetailStructure['policeNumber'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"80px");  
$arrDataDetailStructure['beginningQty'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startQty', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['movementQty'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'qtyinbaseunit', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['balanceQty'] = array('title'=>ucwords($obj->lang['balanceQty']),'dbfield' => 'afterQty', 'width'=>"70px",'format'=>'number', 'textColor' => '0093AF');
$arrDataDetailStructure['unit'] = array('title'=>'','dbfield' => 'baseunitname', 'width'=>"40px",'textColor' => '999999', 'style' => 'padding-left:0px;');     
$arrDataDetailStructure['beginningWeight'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startWeight', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['movementWeight'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'movementWeight', 'width'=>"70px",'format'=>'decimal'); 
$arrDataDetailStructure['balanceWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),'dbfield' => 'afterWeight', 'width'=>"100px",'format'=>'decimal', 'textColor' => '9A4EAE');  
$arrDataDetailStructure['weightUnit'] = array('title'=>'','dbfield' => 'weightunitname', 'width'=>"40px",'textColor' => '999999', 'style' => 'padding-left:0px;');    
//$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'note');
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();
 
array_push($arrTemplate, $arrDetailTemplate); 

if (!isset($_POST['hidAction']) && empty($_POST['hidAction'])){
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y'); 
}

$criteria = ' and ' . $item->tableName.'.statuskey = 1';
$detailCriteria = '';
 
if(isset($_POST) && !empty($_POST['trStartDate'])){
   // $criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
    array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}


$depotCriteria = '';
$depotkey = '';  

if(isset($_POST) && !empty($_POST['selDepot'])) { 

    $depotkey = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   

    $depotCriteria .= ' AND depotkey in('.$depotkey.')';  

    $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$depotkey.')');

    $arrTempStatus = array();
    for ($k=0;$k<count($rsCriteria);$k++)
        array_push($arrTempStatus,$rsCriteria[$k]['name']);

    $depotName = implode(", ",$arrTempStatus); 
    array_push($arrFilterInformation,array("label" => 'Depot', 'filter' => $depotName ));

}

if(isset($_POST) && !empty($_POST['itemName'])) { 
    $criteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
    array_push($arrFilterInformation,array("label" => $obj->lang['itemName'], 'filter' => $_POST['itemName']));
}

$orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'code';  
$orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 


//tambahin criteria item yg hanya pernah terjadi transaksi dengan cust
$customerItem = $item->getCustomerItem(USERKEY);
if(empty($customerItem))  $customerItem = '0';
    
$criteria .= ' and itemkey in ('.$obj->oDbCon->paramString($customerItem,',').') ' ;
      
$order = 'order by '.$orderBy.' ' .$orderType;  	 
 
$rsItem = $itemDepot->searchData('','',true,$criteria,$order); 

$date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
$depotkey =  (isset($_POST['selDepot'])) ? $_POST['selDepot']  : '';

$tempreport = ''; 
   
for($j=0;$j<count($rsItem);$j++){  
		 
        $itemVolume = $rsItem[$j]['width'] * $rsItem[$j]['length'] * $rsItem[$j]['height'];
        $itemWeight = $rsItem[$j]['gramasi'] ;
         
        //$startCOGS = ($hasCOGSAccess) ? $obj->sumItemCOGSMovement($rsItem[$j]['pkey'], $depotkey ,$date) : 0;  
        $startQty = $obj->sumItemMovement($rsItem[$j]['pkey'],$depotkey,$date);  
           
        $dateMethod = $class->loadSetting('movementDateMethod');
        $datefield = ($dateMethod == 2) ? 'trdate' : 'createdon'; 
         
        $criteria = ' and '.$obj->tableName.'.statuskey = 1 and customerkey = ' . $obj->oDbCon->paramString(USERKEY) .' and itemkey = ' .$class->oDbCon->paramString($rsItem[$j]['pkey']) . $depotCriteria . $detailCriteria . ' and '.$obj->tableName.'.'.$datefield.' between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ','Y-m-d 23:59:59');
        $rsDetail = $obj->searchData('','',true,$criteria ,'order by '.$obj->tableName.'.'.$datefield.' asc');
          
        $arrDetailStyle = array();
        for( $i=0;$i<count($rsDetail);$i++) {   
             
            $rsDetail[$i]['startQty'] = ($i==0) ? $startQty :  $rsDetail[$i-1]['afterQty'];  
            $rsDetail[$i]['startWeight'] =  $rsDetail[$i]['startQty'] * $itemWeight;
            $rsDetail[$i]['startVolume'] =  $rsDetail[$i]['startQty'] * $itemVolume;
            
            $rsDetail[$i]['afterQty'] = $rsDetail[$i]['startQty'] + $rsDetail[$i]['qtyinbaseunit'];  
            $rsDetail[$i]['afterWeight'] =  $rsDetail[$i]['afterQty'] * $itemWeight;
            $rsDetail[$i]['afterVolume'] =  $rsDetail[$i]['afterQty'] * $itemVolume;
 
            $rsDetail[$i]['movementWeight'] = $rsDetail[$i]['qtyinbaseunit'] * $itemWeight;
            $rsDetail[$i]['movementVolume'] = $rsDetail[$i]['qtyinbaseunit'] * $itemVolume;
            
            if ($rsDetail[$i]['qtyinbaseunit'] < 0) { 
                $redCardinal = 'C41E3A';
                $arrDetailStyle[$i]['qtyinbaseunit']['textColor'] = $redCardinal;   
                $arrDetailStyle[$i]['movementWeight']['textColor'] = $redCardinal;   
                $arrDetailStyle[$i]['movementVolume']['textColor'] = $redCardinal;    
            }
            
              $rsDetail[$i]['volumeunit'] = 'CM<sup>3</sup>';
                
        }
         
        
        $qoh = (isset($rsDetail[$i-1])) ?  $rsDetail[$i-1]['afterQty'] : $startQty; 
        
        $rsItem[$j]['qtyonhand'] = $qoh;  
        $rsItem[$j]['totalweight'] = $rsItem[$j]['qtyonhand'] * $itemWeight;
        $rsItem[$j]['totalvolume'] = $rsItem[$j]['qtyonhand'] * $itemVolume; 
        $rsItem[$j]['volumeunit'] = 'CM<sup>3</sup>';
        
        // has detail
        $rsItem[$j]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
                  
        $return = $obj->formatReportRows(array('data' => $rsItem[$j]),$arrTemplate); 
            
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
 
$arrTwigVar['inputRefCode'] = $class->inputText('refCode'); 
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
 

echo $twig->render('report-stock-depot-card.html', $arrTwigVar);

?>