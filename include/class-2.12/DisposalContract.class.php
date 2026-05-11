<?php
class DisposalContract extends BaseClass
{
    // CustomerTypeCategory
    function __construct()
    {

        parent::__construct();

        $this->tableName = 'disposal_contract';
        $this->tableStatus = 'transaction_status';
        $this->tableCustomer = 'customer';
        $this->tableService = 'item';
        $this->tableCity = 'city';
        $this->tableCityCategory = 'city_category';
        $this->tableEmployee = 'employee';
        $this->tableItem = 'item';
        $this->tableItemUnit = 'item_unit';
        $this->tableWasteCategory = 'waste_category';
        $this->tableWaste = 'waste';
        $this->tableDetailAssetGroup = 'disposal_contract_asset_group_detail';
        $this->tableDetailWaste = 'disposal_contract_waste_detail';
        $this->tableDetailItem = 'disposal_contract_item_detail';
        $this->tableAssetGroup = 'asset_group';
        $this->isTransaction = true;
        $this->tableFile = 'disposal_contract_file'; 
	    $this->uploadFileFolder = 'disposal-contract-file/';
        $this->newLoad = true;
        $this->overwriteContractSecurityObject = 'overwriteContract';
        $this->securityObject = 'DisposalContract';
 
        $this->useStorage = $this->useStorage('S3');	
        
        $this->arrItemDetail = array();
        $this->arrItemDetail['pkey'] = array('hidItemDetailKey');
        $this->arrItemDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemDetail['itemkey'] = array('hidItemKey');
        $this->arrItemDetail['qty'] = array('qty', 'number'); 
        $this->arrItemDetail['unitkey'] = array('selUnit');
       
        $this->arrAssetGroup = array();
        $this->arrAssetGroup['pkey'] = array('hidAssetGroupDetailKey');
        $this->arrAssetGroup['refkey'] = array('pkey', 'ref');
        $this->arrAssetGroup['assetgroupkey'] = array('hidAssetGroupKey', array('mandatory' => true));
        $this->arrAssetGroup['qty'] = array('qtyAsset', 'number');
    
        $this->arrWaste = array();
        $this->arrWaste['pkey'] = array('hidWasteDetailKey');
        $this->arrWaste['refkey'] = array('pkey', 'ref');
        $this->arrWaste['wastekey'] = array('hidWasteKey', array('mandatory' => true));
        $this->arrWaste['maxweight'] = array('maxWeight', 'number');
        $this->arrWaste['minweight'] = array('minWeight', 'number');
        $this->arrWaste['weightprice'] = array('weightPrice', 'number');

        $arrDetails = array();   

        array_push($arrDetails, array('dataset' => $this->arrAssetGroup, 'tableName' => $this->tableDetailAssetGroup));
        array_push($arrDetails, array('dataset' => $this->arrItemDetail, 'tableName' => $this->tableDetailItem));
        array_push($arrDetails, array('dataset' => $this->arrWaste, 'tableName' => $this->tableDetailWaste));
        
          if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }else{ 
              
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                      'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                      'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));   
        }

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
//        $this->arrData['name'] = array('name');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['startingdate'] = array('startingDate', 'date');
        $this->arrData['validdate'] = array('validDate', 'date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['servicekey'] = array('hidServiceKey');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['areakey'] = array('hidAreaKey');
        $this->arrData['sellingprice'] = array('sellingPrice', 'number');
        $this->arrData['maximumweight'] = array('maximumWeight', 'number');
        $this->arrData['extraprice'] = array('exceedWeightPriceArea', 'number');
        $this->arrData['exceedprice'] = array('exceedSellingPriceArea', 'number');
        $this->arrData['qtyservice'] = array('qtyService', 'number');
        $this->arrData['duration'] = array('duration', 'number');
        $this->arrData['qtyjo'] = array('qtyJO', 'number');
        $this->arrData['contractduration'] = array('contractDuration', 'number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['wastecategorykey'] = array('hidWasteCategoryKey');
        $this->arrData['servicefacilities'] = array('serviceFacilities');
        $this->arrData['pic'] = array('pic');
        $this->arrData['jobposition'] = array('jobPosition');
        $this->arrData['correspondentname'] = array('correspondentName');
        $this->arrData['correspondentjobposition'] = array('correspondentJobPosition');
        $this->arrData['correspondentaddress'] = array('correspondentAddress');
        $this->arrData['correspondentphone'] = array('correspondentPhone');
        $this->arrData['correspondentemail'] = array('correspondentEmail');
        $this->arrData['servicedetailwastekey'] = array('hidServiceDetailWasteKey');

  
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'categoryname', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'service', 'title' => 'service', 'dbfield' => 'servicename', 'default' => true, 'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duration', 'title' => 'duration', 'dbfield' => 'contractduration', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'selingPrice', 'title' => 'sellingPrice', 'dbfield' => 'sellingprice', 'default' => true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        //array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Customer', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('service', $this->tableService . '.name'));
        array_push($this->arrSearchColumn, array('kategori', $this->tableWasteCategory . '.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));

        $this->printMenu = array();
         array_push($this->printMenu, array('code' => 'printDisposalWorkOrder', 'name' => $this->lang['print'],  'icon' => 'print', 'url' => 'print/disposalContract'));  
        $this->overwriteConfig();

 
        $this->includeClassDependencies(array(
            'Customer.class.php',
            'DisposalJobOrder.class.php',
            'Employee.class.php',
            'City.class.php',
            'Waste.class.php',
            'ItemUnit.class.php',
            'Item.class.php',
            'Service.class.php'
        ));
    }



    function getQuery()
    {
        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
					' . $this->tableCustomer . '.name as customername,
					' . $this->tableService . '.name as servicename,
					' . $this->tableEmployee . '.name as salesname,
					' . $this->tableWasteCategory . '.name as categoryname,
                    ' . $this->tableCity . '.name as cityname
				from 
					' . $this->tableName . '
					left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                    left join ' . $this->tableCity . ' on ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey
                    left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.saleskey = ' . $this->tableEmployee . '.pkey
					left join ' . $this->tableService . ' on ' . $this->tableName . '.servicekey = ' . $this->tableService . '.pkey
                    left join ' . $this->tableWasteCategory . ' on ' . $this->tableName . '.wastecategorykey = ' .  $this->tableWasteCategory .'.pkey ,
                     ' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;
    }


   
    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);
        $waste = new Waste();
        $service = new Service();
        $customerKey = $arr['hidCustomerKey'];
        $serviceKey = $arr['hidServiceKey'];
        $salesKey = $arr['hidSalesKey'];
        $serviceDetailWasteKey = $arr['hidServiceDetailWasteKey'];
        $arrWasteKey = $arr['hidWasteKey'];
        $wasteCategoryKey = $arr['hidWasteCategoryKey'];
        $contractDuration = $this->unFormatNumber($arr['contractDuration']);
//        $name = $arr['name'];  
        
//        if(empty($name)){
//			$this->addErrorList($arrayToJs,false,$this->errorMsg['contract'][2]);
//		}else{    
//			$rContract = $this->isValueExisted($pkey,'name',$name,'4');	
//			if(count($rContract) <> 0) 
//			 $this->addErrorList($arrayToJs,false,$this->errorMsg['contract'][3]);
//        }
            
        if (empty($customerKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }
        if (empty($salesKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['commission'][3]);
        }

        if ($contractDuration <= 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['contract'][4]);
        }

        if (empty($serviceKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
        }
        $arrCheckWasteKey = array();
        for ($i = 0; $i < count($arrWasteKey); $i++) {

            
            
            if (empty($arrWasteKey[$i])) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['waste'][1]);
            } else {
                $rsWaste = $waste->getDataRowById($arrWasteKey[$i]);

                //cek ada limbah ada di layanan
                $rsServiceWaste = $service->getDetailWaste($serviceDetailWasteKey, 'refkey', $arrWasteKey[$i]);
                if (empty($rsServiceWaste)) { 
                    $this->addErrorList($arrayToJs, false,  $rsWaste[0]['code'].' - '.$rsWaste[0]['name'] . '. ' .$this->errorMsg['waste'][2]);
                }
                if ($rsWaste[0]['categorykey'] <> $wasteCategoryKey) {
                    $this->addErrorList($arrayToJs, false,  $rsWaste[0]['code'].' - '.$rsWaste[0]['name'] . '. ' .$this->errorMsg['waste'][3]);
                }
                // cek detail double waste 
                if (in_array($arrWasteKey[$i], $arrCheckWasteKey)) {
                    $this->addErrorList($arrayToJs, false, $rsWaste[0]['code'].' - '.$rsWaste[0]['name'] . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrCheckWasteKey, $arrWasteKey[$i]);
                }
            }
        }

        return $arrayToJs;
    }

    function validateConfirm($rsHeader)  { 

        $service = new Service();
        // cuma boleh 1 kontrak yg berjalan dalam satu tahun
        // $rsContract = $this->searchDataRow( array($this->tableName.'.code'), 
        //                                    ' and '.$this->tableName.'.customerkey = ' . $this->oDbCon->paramString($rsHeader[0]['customerkey']).' 
        //                                      and '.$this->tableName.'.statuskey = 2');
        // if (!empty($rsContract)) { 
        //     $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'</strong>. '. $this->errorMsg['contract'][7].' '.$rsContract[0]['code'].'.');
        // }
        $rsDetailAreaService = $service->getDetailArea($rsHeader[0]['servicekey'], $rsHeader[0]['areakey']);
        
       if ($rsDetailAreaService[0]['sellingprice'] > 0 && $rsHeader[0]['sellingprice'] <= 0) 
           $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' .$this->errorMsg['sellingPrice'][1]);  
       
            
    }
    
    function validateClose($rsHeader)  {
    
         if ($rsHeader[0]['statuskey'] <> 2)  
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] .' '. $this->errorMsg[204]);

    }

    function validateCancel($rsHeader, $autoChangeStatus = false)  {
		
		$pkey = $rsHeader[0]['pkey'];
		$disposalJobOrder = new DisposalJobOrder(); 
  
        $this->validateLinkedData($disposalJobOrder, array(   'linkedField' => array('field' => $disposalJobOrder->tableName.'.contractkey' ,'value' => $pkey),
                                                                'statuskey' => array(2,3,4,5), 
                                                                'errorCode' => 201, 
                                                                'errorDetailMsg' => $this->errorMsg['disposalJobOrder'][1],
                                                                'refCode' => $rsHeader[0]['code'], // KODE SPK LIST
                                                              )
                                  );
             
    }
    
    function cancelTrans($rsHeader, $copy)  {
        $disposalJobOrder = new DisposalJobOrder();

        $rsJobOrder = $disposalJobOrder->searchData('', '', true, ' and ' . $disposalJobOrder->tableName . '.contractkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $disposalJobOrder->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsJobOrder); $i++){
            $disposalJobOrder->changeStatus($rsJobOrder[$i]['pkey'], 6, '', false, true);
        }

        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);

    }


    function getItemDetail($pkey, $criteria = '', $orderby = '')
    {


        $sql = 'select
                ' . $this->tableDetailItem . '.*, 
                ' . $this->tableItem . '.name as itemname, 
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.sellingprice,
                ' . $this->tableItem . '.deftransunitkey,
                ' . $this->tableItem . '.isperiodically,
                ' . $this->tableItemUnit . '.name as unitname,
                 baseunit.name as baseunitname
              from
                ' . $this->tableDetailItem . ',
                ' . $this->tableItemUnit . ',
                ' . $this->tableItemUnit . ' baseunit,
                ' . $this->tableItem . '
              where
                ' . $this->tableDetailItem . '.itemkey = ' . $this->tableItem . '.pkey and
                  ' . $this->tableDetailItem . '.unitkey = ' . $this->tableItemUnit . '.pkey and
			  	' . $this->tableItem . '.baseunitkey = baseunit.pkey and
		        ' . $this->tableDetailItem . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function getAssetGroupDetail($pkey, $criteria = '', $orderby = '')
    {

        $sql = 'select
                ' . $this->tableDetailAssetGroup . '.*, 
                ' . $this->tableAssetGroup . '.name as assetgroupname, 
                ' . $this->tableAssetGroup . '.code as assetgroupcode
              from
                ' . $this->tableDetailAssetGroup . ',
                ' . $this->tableAssetGroup . '
              where
                ' . $this->tableDetailAssetGroup . '.assetgroupkey = ' . $this->tableAssetGroup . '.pkey and
		        ' . $this->tableDetailAssetGroup . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function getWasteDetail($pkey, $criteria = '', $orderby = '')
    {

        $sql = 'select
                ' . $this->tableDetailWaste . '.*, 
                '.$this->tableWaste. '.code as wastecode,
                '.$this->tableWaste. '.name as wastename,
                concat ('.$this->tableWaste. '.code, " - ", '.$this->tableWaste.'.name) as wastecodename
              from
                ' . $this->tableDetailWaste . ',
                ' . $this->tableWaste . '
              where
                ' . $this->tableDetailWaste . '.wastekey = ' . $this->tableWaste . '.pkey and
		        ' . $this->tableDetailWaste . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function generateDefaultQueryForAutoComplete($returnField) {

        $sql = 'select
					' . $returnField['key'] . ',
					' . $returnField['value'] . ' as value, 
                    ' . $this->tableName . '.*,
                    '.$this->tableCity.'.categorykey as citycategorykey,
                        concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as cityandcategoryname,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableWasteCategory . '.pkey as wastekey,
                    ' . $this->tableWasteCategory . '.name as wastecategoryname,
                    ' . $this->tableEmployee . '.name as salesname,
                    ' . $this->tableService . '.name as servicename
				from 
					' . $this->tableName . '
                    left join ' . $this->tableService . ' on ' . $this->tableName . '.servicekey = ' . $this->tableService . '.pkey
                    left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.saleskey = ' . $this->tableEmployee . '.pkey
                    left join ' . $this->tableWasteCategory . ' on ' . $this->tableName . '.wastecategorykey = ' . $this->tableWasteCategory . '.pkey
                    left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
				        left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
                    left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey,
                    ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
			';

        $sql .=  $this->getCompanyCriteria();
          
        return $sql;
    }

  function getBestServicesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5){
        // Service total
        
        $sql = 'select 
                    '.$this->tableName.'.itemkey,  
                    '.$this->tableItem.'.name as itemname,  
                sum('.$this->tableNameItemDetail.'.qtyinbaseunit * '.$this->tableNameItemDetail.'.priceafterdiscount) as total
                from 
                    '.$this->tableName.', 
                    '.$this->tableNameItemDetail.', 
                    '.$this->tableItem.'
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                    '.$this->tableName.'.pkey  = '.$this->tableNameItemDetail.'.refheaderkey and
                     '.$this->tableNameItemDetail.'.itemkey  = '.$this->tableItem.'.pkey and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')  
                 group by 
                    '.$groupBy.'
                 order by total desc limit ' . $limit;
      
        return $this->oDbCon->doQuery($sql); 
    }   

    function afterUpdateData($arrParam, $action){ 
		$disposalJobOrder = new DisposalJobOrder();
        $security = new Security();
        // khusus kalo edit
        if (isset($arrParam['hidId']) && !empty($arrParam['hidId'])) {
            $overwriteContractAllowed = $security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($this->overwriteContractSecurityObject),10);
            $pkey = $arrParam['hidId'];
            $rs = $this->getDataRowById($pkey);

            if ($rs[0]['statuskey'] == 2 && ($overwriteContractAllowed)) {

				// sementar update semua karena tidk ad patokan jon maana yg sudah selesai
				
					$sql = 'update ' . $disposalJobOrder->tableName .' set saleskey = ' . $this->oDbCon->paramString($rs[0]['saleskey']) . '
                            where contractkey = ' . $this->oDbCon->paramString($pkey);
                    $this->oDbCon->execute($sql);
				
//                $rsJobOrder = $disposalJobOrder->searchData('','',true,'  and '.$disposalJobOrder->tableName.'.contractkey = '.$this->oDbCon->paramString($pkey));
//                for ($i=0; $i<count($rsJobOrder); $i++) {
//                    $sql = 'update ' . $disposalJobOrder->tableName .' set saleskey = ' . $this->oDbCon->paramString($rs[0]['saleskey']) . '
//                            where pkey = ' . $this->oDbCon->paramString($rsJobOrder[$i]['pkey']);
//                    $this->oDbCon->execute($sql);
//                }
            }
        }
	}
    function normalizeParameter($arrParam, $trim = false){

        $security = new Security();
        $service = new Service();
        $customer = new Customer();
        $city = new City();
        $waste = new Waste();

        $rsCustomer = $customer->getLocationInformation($arrParam['hidCustomerKey']);
        $arrParam['hidCustomerKey'] = (!empty($rsCustomer)) ? $arrParam['hidCustomerKey'] : '';  
        $arrParam['hidCityKey'] = $rsCustomer[0]['citykey'];
        $arrParam['hidAreaKey'] = $rsCustomer[0]['citycategorykey'];
        
            
        $overwriteContractAllowed = $security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($this->overwriteContractSecurityObject),10);
        
        $arrParam['contractDuration'] = $this->unFormatNumber($arrParam['contractDuration']);
        if($arrParam['contractDuration'] < 0) $arrParam['contractDuration'] = 0;
        
        $pkey = $arrParam['hidId'];
        $trDate = $arrParam['trDate'];
        $contractDuration = $arrParam['contractDuration'];
           
        // sementara otomatis dulu, asumsi selalu per bulan. biar user gk salah input
        $arrParam['qtyJO'] = $arrParam['contractDuration'];
      
        
        $trDate = str_replace('\'','',$this->oDbCon->paramDate($arrParam['startingDate'],' / '));
         
        $date = new DateTime($trDate);
        $date->add(new DateInterval('P'.$contractDuration.'M')); 
        $arrParam['validDate'] =  $date->format('d / m / Y');


        $rsWasteCategory = $waste->getWasteCategory($arrParam['hidWasteCategoryKey']);
        $categoryCode = $rsWasteCategory[0]['code'];
        if (empty($arrParam['hidId'])) {
            $rsCity = $city->getDataRowById($arrParam['hidCityKey']);
            $cityCode =  $rsCity[0]['code'].$categoryCode;
            $code = $arrParam['code'];
            $arrParam['code'] = str_replace('{{REF}}',$cityCode,$code);
        }
        
        $rsHeader = array();

        // if (!$overwriteContractAllowed) {
            
        //     // agar yg tdk punya akses overwrite kontrak tdk ketimpa ulang
        //     if (isset($arrParam['hidId']) && !empty($arrParam['hidId'])) { 
        //         unset($this->arrData['extraprice']);
        //         unset($this->arrData['maximumweight']);
        //         unset($this->arrData['sellingprice']);
        //         unset($this->arrData['exceedprice']);
        //     } else {
        //         $serviceKey = $arrParam['hidServiceKey'];
        //         $areaKey = $arrParam['hidAreaKey'];
        //         $rsService = $service->getDataRowById($serviceKey);
        //         $rsArea = $service->getDetailArea($serviceKey, $areaKey);
        //         $arrParam['extraPrice'] = $rsArea[0]['extrapricearea'];
        //         $arrParam['sellingPrice'] = $rsArea[0]['sellingprice'];
        //         $arrParam['exceedSellingPriceArea'] = $rsArea[0]['exceedweightpriceaare'];
        //         $arrParam['maximumWeight'] = $rsService[0]['qtyweight'];
        //     }
        // }

        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
}
