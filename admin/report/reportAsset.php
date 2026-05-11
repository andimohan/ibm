<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Asset.class.php');
$asset = createObjAndAddToCol( new Asset());
$assetDepreciation = createObjAndAddToCol( new AssetDepreciation());
$assetCategory = createObjAndAddToCol( new AssetCategory());
$warehouse = createObjAndAddToCol( new Warehouse());
    
include '_global.php';

$obj= $asset;
$securityObject = 'reportAsset'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$_POST['selStatus[]'] = array(1);

$arrFilterInformation = array();    

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
$_POST['module'] = IMPORT_TEMPLATE['asset'];

$arrDataStructure = array();


 switch($EXPORT_TYPE){

        case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code'); 
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'width'=>"200px", 'dbfield' => 'name');   
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"150px");
            //$arrDataStructure['usefullife'] = array('title'=>ucwords($obj->lang['usefulLife']),'dbfield' => 'aging', 'width'=>"120px", 'format' => 'number', 'text-align' =>'center');
            $arrDataStructure['acquisitiondate'] = array('title'=>ucwords($obj->lang['acquisitionDate']),'dbfield' => 'acquisitiondate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['acquisitionvalue'] = array('title'=>ucwords($obj->lang['acquisitionValue']),'dbfield' => 'acquisitionvalue', 'width'=>"120px", 'format' => 'number');
            $arrDataStructure['accDepreciation'] = array('title'=>ucwords($obj->lang['accDepreciation']),'dbfield' => 'accdepreciation', 'width'=>"120px", 'format' => 'number');
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
            break;

        default : 
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code' , 'width'=>"100px");
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'width'=>"200px", 'dbfield' => 'name');
            $arrDataStructure['acquisitiondate'] = array('title'=>ucwords($obj->lang['acquisitionDate']),'dbfield' => 'acquisitiondate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"150px");
            $arrDataStructure['type'] = array('title'=>ucwords($obj->lang['type']),'dbfield' => 'typename', 'width'=>"150px");
            $arrDataStructure['usefullife'] = array('title'=>ucwords($obj->lang['usefulLife']. ' ('.$obj->lang['year'].')'),'dbfield' => 'aging', 'width'=>"140px", 'format' => 'number', 'text-align' =>'center');
            $arrDataStructure['acquisitionvalue'] = array('title'=>ucwords($obj->lang['acquisitionValue']),'dbfield' => 'acquisitionvalue', 'width'=>"120px", 'format' => 'number','calculateTotal' => true);
            $arrDataStructure['accDepreciation'] = array('title'=>ucwords($obj->lang['accDepreciation']),'dbfield' => 'accdepreciation', 'width'=>"120px", 'format' => 'number','calculateTotal' => true);
            $arrDataStructure['bookvalue'] = array('title'=>ucwords($obj->lang['bookValue']),'dbfield' => 'bookvalue', 'width'=>"120px", 'format' => 'number','calculateTotal' => true);
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

    }


$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['assetReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

 if($isShowDetail && $EXPORT_TYPE != 2){
  	$arrDataDetailStructure = array();
  	$arrDataDetailStructure['depreciationcode'] = array('title'=>ucwords($obj->lang['transactionCode']),  'dbfield' => 'code', 'width'=>'150px', 'format' => 'string' ); 
  	$arrDataDetailStructure['depreciationdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
  	$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"130px", 'format' => 'decimal' ,  'calculateTotal' => true, 'align'=>'right');

  	$arrDetailTemplate = array(); 
  	$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
  	$arrDetailTemplate['total'] = array();

  	array_push($arrTemplate, $arrDetailTemplate);
}
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	$criteriaArr = array();
	
    // untuk pencarian berdasarkan kode
	array_push($criteriaArr, array('postVariable' => 'assetCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	

    // untuk pencarian berdasarkan nama
	array_push($criteriaArr, array('postVariable' => 'assetName', 
								   'fieldName' => $obj->tableName.'.name', 
								   'label' => $obj->lang['name']));


	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));

	array_push($criteriaArr, array('postVariable' => 'selCategory', 
								   'fieldName' => $obj->tableName.'.categorykey', 
								   'label' => $obj->lang['category'], 
								   'useArrayKey' => array('obj' => $assetCategory) ));
	
	//gabisa menggunakan model baru karena get tipe nya manual
  	if(isset($_POST) && !empty($_POST['selType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selType']));   
         
	  	$criteria .= ' AND '.$obj->tableAssetCategory.'.typekey in('.$key.')';  

        $rsCriteria =  $obj->getAssetType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$typeName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($class->lang['category']), 'filter' => $typeName));
        
	}  

	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));


	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);


	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
    // select data
    $rs = $obj->searchData('','',true,$criteria,$order);
	
    $rsDepreciationDetail = ($isShowDetail) ? $assetDepreciation->getDetailDepreciationCollections($rs, 'assetkey') : array();  
	
    $tempreport = ''; 

    for( $i=0;$i<count($rs);$i++) {     
		
        $rs[$i]['accdepreciation'] = ($rs[$i]['acquisitionvalue'] -  $rs[$i]['bookvalue']);
            
        switch($EXPORT_TYPE){
            case 2 : 
                break;

            default :
                if($isShowDetail){ 
                    $rsDepreciation = (isset($rsDepreciationDetail[$rs[$i]['pkey']])) ? $rsDepreciationDetail[$rs[$i]['pkey']] : array();
                    $rsDetail = array();
                    for ($j=0;$j<count($rsDepreciation);$j++){ 

                        $rsAssetDepreciation = $assetDepreciation->getDataRowById($rsDepreciation[$j]['refkey']);

                        $arrTemp = array();
                        $arrTemp['code'] = $rsAssetDepreciation[0]['code'];
                        $arrTemp['trdate'] = $rsAssetDepreciation[0]['trdate'];
                        $arrTemp['amount'] = $rsDepreciation[$j]['value']; 

                        array_push($rsDetail, $arrTemp);
                    }
                                   
                    // has detail
                    $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                } 
        }

		
		
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION
        $tempreport .= $return['html'];  
		
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }	  
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrType = $class->convertForCombobox($obj->getAssetType(),'pkey','name');    
$arrCategory = $class->convertForCombobox($assetCategory->searchData($assetCategory->tableName.'.statuskey',1,true, ' order by name asc'),'pkey','name');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputAssetCode'] =  $class->inputText('assetCode');  
$arrTwigVar['inputAssetName'] =  $class->inputText('assetName');   
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelType'] =  $class->inputSelect('selType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

echo $twig->render('reportAsset.html', $arrTwigVar);  
    
?>