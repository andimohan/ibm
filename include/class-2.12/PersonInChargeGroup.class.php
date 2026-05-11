<?php

class PersonInChargeGroup extends BaseClass
{
   function __construct()
   {

      parent::__construct();

      $this->tableName = 'person_in_charge_group_header';
      $this->tableNameDetail = 'person_in_charge_group_detail';
      $this->tableSupplier = 'supplier';
      $this->tableStatus = 'master_status';

      $this->securityObject = 'PersonInChargeGroup';

      $this->arrDataDetail = array();
      $this->arrDataDetail['pkey'] = array('hidDetailKey');
      $this->arrDataDetail['refkey'] = array('pkey', 'ref');
      $this->arrDataDetail['supplierkey'] = array('hidSupplierKey');

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataDetail));

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code'] = array('code');
      $this->arrData['name'] = array('name');
      $this->arrData['trdesc'] = array('trDesc');
      $this->arrData['statuskey'] = array('selStatus');

      $this->newLoad = true;

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

      $this->includeClassDependencies(
         array(
            'Supplier.class.php'
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {
      $sql = '
            SELECT
                ' . $this->tableName . '.*,
                ' . $this->tableStatus . '.status as statusname
                
            FROM ' . $this->tableStatus . ',  
                 ' . $this->tableName . '
            WHERE   
                  ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 

            ' . $this->criteria;

      return $sql;
   }

   function getDetailWithRelatedInformation($pkey, $criteria = '')
   {

      $sql = 'select
	   			' . $this->tableNameDetail . '.*,
               '. $this->tableSupplier .'.name as suppliername
            from
               ' . $this->tableNameDetail . '
                  left join '. $this->tableSupplier .' on '.$this->tableNameDetail.'.supplierkey = '. $this->tableSupplier .'.pkey
            where
               ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') 
            ';

      $sql .= $criteria;

      return $this->oDbCon->doQuery($sql);
   }

   function validateForm($arr,$pkey = '')
   {
      $arrayToJs = parent::validateForm($arr, $pkey);

      $supplier = new Supplier();

      $name = $arr['name'];
      $arrSupplierKey = $arr['hidSupplierKey'];

		$rsPIC = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rsPIC) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		}
 
      $arrDetailKeys = array();

      for ($i = 0; $i < count($arrSupplierKey); $i++) {
         if (empty($arrSupplierKey[$i])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
         } else {

            if (in_array($arrSupplierKey[$i], $arrDetailKeys)) {
               $rsSupplier = $supplier->getDataRowById($arrSupplierKey[$i]);
               $this->addErrorList($arrayToJs, false, $rsSupplier[0]['name'] . '. ' . $this->errorMsg[215]);
            } else {
               array_push($arrDetailKeys, $arrSupplierKey[$i]);
            }
         }
      } 

      return $arrayToJs;
   }  

   function normalizeParameter($arrParam, $trim = false)
   {
      $arrParam = parent::normalizeParameter($arrParam);

      return $arrParam;
   }

}

?>