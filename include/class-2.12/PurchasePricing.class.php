<?php  
class PurchasePricing extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'purchase_pricing_header';
		$this->tableNameDetail = 'purchase_pricing_detail';
        $this->tableSupplier = 'supplier';
        $this->tableItem = 'item';

        $this->tableStatus = 'transaction_status';
        $this->isTransaction = true;

        $this->securityObject = 'PurchasePricing';

        $this->importUrl = 'import/purchasePricing';

        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['price'] = array('price','number');
    
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['notes'] = array('trDesc');


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array($this->lang['code'], $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array($this->lang['date'], $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array($this->lang['supplier'], $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array($this->lang['status'], $this->tableStatus . '.status'));

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->includeClassDependencies(array(
            'Item.class.php',
            'Supplier.class.php'
        ));

		$this->overwriteConfig();
    }


    function getQuery()
    {

        $sql =  '
				select
					' . $this->tableName . '.*,
                    ' . $this->tableSupplier . '.name as suppliername,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName . ',
                    ' . $this->tableSupplier . ', 
                    ' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and  
                    ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey
 	    '; 
        
        $sql .=  $this->criteria;

        return $sql;
    }


    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
        $sql = 'select
	   			    '.$this->tableNameDetail .'.*,
                    '.$this->tableItem.'.code as itemcode,
                    '.$this->tableItem.'.barcode as itembarcode,
                    '.$this->tableItem.'.name as itemname
			    from
			  	    '. $this->tableNameDetail .',
                    '.$this->tableItem.'
			    where 
                    '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
			  	    '.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }

    function getLatestPurchasePricing($supplierkey, $itemkey, $trdate = '')
    {
        if(empty($supplierkey)) return [];

        $criteriaDate = '';
        if (!empty($trdate)) {
            $criteriaDate = ' and ' . $this->tableName . '.trdate <= ' . $this->oDbCon->paramDate($trdate, ' / ');
        }

        $sql = 'select * from 
                    '.$this->tableName.' 
                where 
                    ' . $this->tableName . '.supplierkey = '.$this->oDbCon->paramString($supplierkey).' and
                    ' . $this->tableName . '.statuskey = 2  
                    '.$criteriaDate.'  
                order by trdate desc, 
                '.$this->tableName.'.pkey desc limit 1
        ';

        $rs = $this->oDbCon->doQuery($sql);	 


        if (empty($rs)) return [];

        if (!empty($rs)){ 
            $sql = ' 
                select  
                    itemkey, 
                    coalesce('.$this->tableNameDetail.'.price,0) as price
                from  
                    '.$this->tableNameDetail.' 
                where   
                    '.$this->tableNameDetail.'.refkey =  '.$this->oDbCon->paramString($rs[0]['pkey']).' and
                    '.$this->tableNameDetail.'.itemkey in ('.$this->oDbCon->paramString($itemkey,',').');
            ';
        }

        $rs = $this->oDbCon->doQuery($sql);

        return  $rs;
    }

    function validateForm($arr, $pkey = '')
    {

        $item = new Item();

        $arrayToJs = parent::validateForm($arr, $pkey);
        
        $arrSupplierKey = $arr['hidSupplierKey'];
        $arrItemKey = $arr['hidItemKey'];
        $arrPrice = $arr['price'];
    
        if(empty($arrSupplierKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
        }

        $rsItem = $item->searchData('','',true, ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').') ');
        $rsItemCol = $this->reindexDetailCollections($rsItem,'pkey');

        
        $arrItemDetailKeys = array();
        for($i=0; $i < count($arrItemKey); $i++) {

            if(empty($arrItemKey[$i])) {
                $this->addErrorList($arrayToJs, false,$this->errorMsg['item'][1]);
            } else {

                $rsItem = $rsItemCol[$arrItemKey[$i]];
                $price = $this->unFormatNumber($arrPrice[$i]);
                if(in_array($arrItemKey[$i], $arrItemDetailKeys)) {
                    $this->addErrorList($arrayToJs, false, '<strong>'.$rsItem[0]['name'].'. </strong>'. $this->errorMsg[215]);
                } else {
                    array_push($arrItemDetailKeys, $arrItemKey[$i]);
                }

                if($price <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>'.$rsItem[0]['name'].'. </strong>'. $this->errorMsg['price'][1]);
                }

            }
        }


        return $arrayToJs;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);


        return $arrParam;
    }

}

?>