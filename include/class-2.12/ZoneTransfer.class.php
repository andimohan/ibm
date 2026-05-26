<?php 
class ZoneTransfer extends BaseClass
{
    
    function __construct()
    {
        parent::__construct();

        $this->tableName = 'zone_transfer_header';
        $this->tableNameDetail = 'zone_transfer_detail';
        $this->tablePallet = 'pallet';
        $this->tableWarehouse = 'warehouse';
        $this->tableWarehouseLayout = 'warehouse_layout';
        $this->tableItem = 'item';

        $this->tableStatus = 'transaction_status';

        $this->isTransaction = true;

        $this->securityObject = 'ZoneTransfer'; 


        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty','number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['warehouselayoutoriginkey'] = array('hidWarehouseLayoutOriginKey');
        $this->arrData['warehouselayoutdestinationkey'] = array('hidWarehouseLayoutDestinationKey');
        $this->arrData['submissionnumber'] = array('submissionNumber');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 120, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'locationOrigin','title' => 'origin','dbfield' => 'warehouselayoutoriginname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'locationDestination','title' => 'destination','dbfield' => 'warehouselayoutdestinationname','default'=>true, 'width' => 150));
                array_push($this->arrDataListAvailableColumn, array('code' => 'submissionNumber','title' => 'submissionNumber','dbfield' => 'submissionnumber','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pallet','title' => 'pallet','dbfield' => 'palletname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 100));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'WarehouseLayout.class.php',
            'Pallet.class.php',
            'ItemMovement.class.php',
            'ItemReceiving.class.php' 
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
			SELECT '.$this->tableName.'.* ,
                layoutorigin.name as warehouselayoutoriginname,
                layoutdestination.name as warehouselayoutdestinationname,
                '.$this->tablePallet.'.name as palletname,
			    '.$this->tableStatus.'.status as statusname,
                '.$this->tableWarehouse.'.name as warehousename 
			FROM 
                '.$this->tableName.'
                        left join '.$this->tableWarehouseLayout.' layoutorigin on '.$this->tableName.'.warehouselayoutoriginkey = layoutorigin.pkey
                        left join '.$this->tableWarehouseLayout.' layoutdestination on '.$this->tableName.'.warehouselayoutdestinationkey = layoutdestination.pkey
                        left join '.$this->tablePallet.' on '.$this->tableName.'.palletkey = '.$this->tablePallet.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ;  
		  
        $sql .=  $this->getCompanyCriteria() ;
      
      return $sql;

    }

    function getDetailWithRelatedInformation($pkey,$criteria = ''){
        
	   $sql = 'select
	   			'.$this->tableNameDetail.'.*,
                '.$this->tableItem.'.name as itemname,
                '.$this->tablePallet.'.code as palletcode,
                '.$this->tablePallet.'.name as palletname
			  from
			  	'.$this->tableNameDetail.'
                    left join '.$this->tablePallet.' on '.$this->tableNameDetail.'.palletkey = '.$this->tablePallet.'.pkey,
                '.$this->tableItem.'
			  where
                '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria;
              
		return $this->oDbCon->doQuery($sql);
	
    }
            
    function validateForm($arr,$pkey = ''){
        
		$arrayToJs = parent::validateForm($arr,$pkey); 

        $itemReceiving = new ItemReceiving();

        $warehouseOriginKey = $arr['hidWarehouseLayoutOriginKey'];
        $warehouseDestinationKey = $arr['hidWarehouseLayoutDestinationKey'];
        $refkey = $arr['hidRefKey'];

        $arrItemKey = $arr['hidItemKey'];
        $arrQty = $arr['qty'];

        if(empty($warehouseOriginKey) || empty($warehouseDestinationKey)) {
             $this->addErrorList($arrayToJs,false, $this->errorMsg['zoneTransfer'][1]); 
        }
          
        if($warehouseOriginKey == $warehouseDestinationKey) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg['zoneTransfer'][2]); 
        } 

        if(empty($refkey)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['zoneTransfer'][3]);
        } else {
            $rsReceiving = $itemReceiving->getDataRowById($refkey);
            if(empty($rsReceiving)) {
                $this->addErrorList($arrayToJs,false,$this->errorMsg['zoneTransfer'][4]);
            }
        }

        if(empty($arrItemKey[0])) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg[501]); 
        } else {
            for($i=0; $i<count($arrItemKey); $i++) {

                $qty = $this->unFormatNumber($arrQty[$i]);

                $itemName = $arr['itemName'][$i];
                if(empty($arrItemKey[$i])) {
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]); 
                } else if($qty <= 0) {
                    $this->addErrorList($arrayToJs,false,'<strong>'.$itemName.'.</strong> '.$this->errorMsg[510]); 
                }

            }
        }
      
 		  
		return $arrayToJs;
    }

    function validateConfirm($rsHeader)
{
        $id = $rsHeader[0]['pkey'];

        $itemMovement = new itemMovement();
        $itemReceiving = new ItemReceiving();

        $rsReceiving = $itemReceiving->getDataRowById($rsHeader[0]['refkey']);

        if(empty($rsReceiving)) {
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.$this->errorMsg['zoneTransfer'][4]);
        }

        $rsDetail = $this->getDetailById($id);

        //validasi stock
        for($i=0;$i<count($rsDetail);$i++){

            $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], '', $rsHeader[0]['warehouselayoutoriginkey']);  
            
            $totalqty = $saldoakhir - $rsDetail[$i]['qty'];  
            
            if($totalqty<0){
                $item = new Item();
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }
        }

    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey']; 

        $itemMovement = new ItemMovement();  
		$warehouseLayout = new WarehouseLayout();

        $rsWarehouseLayoutFrom = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutoriginkey']);
		$rsWarehouseLayoutTo = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutdestinationkey']);
	 	$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);  

        $note = $rsHeader[0]['code'] .'. Perpindahan Zona dari '.$rsWarehouseLayoutFrom[0]['name'].' ke ' .$rsWarehouseLayoutTo[0]['name'];

        for($i=0;$i<count($rsDetail); $i++){	 
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qty'], 0, $this->tableName, array(
                'warehousekey' => $rsHeader[0]['warehousekey'],
                'warehouselayoutkey' => $rsHeader[0]['warehouselayoutoriginkey']
            ) , $note, $rsHeader[0]['trdate']);
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qty'], 0 ,$this->tableName, array(
                'warehousekey' => $rsHeader[0]['warehousekey'],
                'warehouselayoutkey' => $rsHeader[0]['warehouselayoutdestinationkey']
            ), $note, $rsHeader[0]['trdate']);
        }

    }

    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 

        $itemMovement = new ItemMovement();  
		$itemMovement->cancelMovement($id,$this->tableName);

        if ($copy)
            $this->copyDataOnCancel($id);	  


    }  

    function afterStatusChanged($rsHeader){  
       
    }

    function normalizeParameter($arrParam, $trim = false){ 

        $arrParam['submissionNumber'] = $arrParam['refCode'];
         
        $arrParam = parent::normalizeParameter($arrParam); 
       
        return $arrParam;
        
    }
    
} 