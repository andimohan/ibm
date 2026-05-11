<?php

class CarServiceMaintenanceRequest extends BaseClass{ 
  
   function __construct(){
		
		parent::__construct();


      $this->tableName = 'car_service_maintenance_request_header';
      $this->tableNameDetail = 'car_service_maintenance_request_detail';
      $this->tableMaintenanceType = 'car_service_maintenance_type';
      $this->tableCategory = 'car_service_maintenance_category';
      $this->tableCar = 'car';
      $this->tableChassis = 'chassis';
      $this->tableWarehouse = 'warehouse';
      $this->tableItem = 'item'; 	
      $this->tableStatus = 'transaction_status';

      $this->isTransaction = true;
      $this->securityObject = 'CarServiceMaintenanceRequest';


      $this->arrDataDetail = array();  
      $this->arrDataDetail['pkey'] = array('hidDetailKey');
      $this->arrDataDetail['refkey'] = array('pkey','ref');
      $this->arrDataDetail['trdesc'] = array('trDetailDesc');  

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataDetail));

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code'] = array('code');
      $this->arrData['trdate'] = array('trDate', 'date');
      $this->arrData['estdate'] = array('estDate', 'date');
      $this->arrData['warehousekey'] = array('selWarehouseKey');
      $this->arrData['categorykey'] = array('selCategory');
      $this->arrData['typekey'] = array('selType');
      $this->arrData['trdesc'] = array('trDesc');
      $this->arrData['mileage'] = array('mileage', 'number');
      $this->arrData['carkey'] = array('hidCarKey');
      $this->arrData['chassiskey'] = array('hidChassisKey');
      $this->arrData['statuskey'] = array('selStatus');

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
      array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
      array_push($this->arrSearchColumn, array('Nomor Polisi', $this->tableCar . '.policenumber'));
      array_push($this->arrSearchColumn, array('Nomor Polisi', $this->tableChassis . '.code'));
      array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 150, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber', 'title' => 'vehicle', 'dbfield' => 'policenumber', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'note', 'dbfield' => 'trnotes', 'width' => 200));

      $this->includeClassDependencies(
         array(
            'AP.class.php',
            'Brand.class.php',
            'Car.class.php',
            'Category.class.php',
            'CarCategory.class.php',
            'CarTurnover.class.php',
            'CashBank.class.php',
            'Chassis.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'Warehouse.class.php',
            'CarServiceMaintenance.class.php'
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {

      $sql =  '
         	SELECT 
               ' . $this->tableName . '.*,
               '. $this->tableWarehouse .'.name as warehousename,
               ' . $this->tableStatus . '.status as statusname ,
			      ' . $this->tableCategory . '.name as categoryname ,
               CONCAT_WS(\'\', ' . $this->tableCar . '.code , ' . $this->tableChassis . '.code) as vehiclecode ,
               CONCAT_WS(\'\', ' . $this->tableCar . '.policenumber , ' . $this->tableChassis . '.chassisnumber) as policenumber 
            FROM 
               '.$this->tableStatus.', 
               ' . $this->tableName . '
                  left join '.$this->tableWarehouse.' on  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
                  left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey
					   left join ' . $this->tableChassis . ' on ' . $this->tableName . '.chassiskey = ' . $this->tableChassis . '.pkey
					   left join ' . $this->tableCategory . ' on ' . $this->tableName . '.categorykey = ' . $this->tableCategory . '.pkey
            WHERE 
               ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
      ' . $this->criteria;
      return $sql;

   }

   function getDetailWithRelatedInformation($pkey, $criteria = '')
   {
      $sql = 'select
                ' . $this->tableNameDetail . '.*
              from
                ' . $this->tableNameDetail . '
              where
                ' . $this->tableNameDetail . '.refkey = (' . $this->oDbCon->paramString($pkey, ',') . ') ';

      $sql .= $criteria;

      return $this->oDbCon->doQuery($sql);
   }

   function getMaintenanceType($typekey = '')
   {

      $sql = 'select * from ' . $this->tableMaintenanceType . ' where 1 = 1';

      if (!empty($typekey))
         $sql .= ' and ' . $this->tableMaintenanceType . ' .pkey in (' . $typekey . ') ';

      return $this->oDbCon->doQuery($sql);
   }

   function getMaintenanceCategory($categorykey = '')
   {

      $sql = 'select * from ' . $this->tableCategory . ' where 1=1';

      if (!empty($categorykey))
         $sql .= ' and pkey = ' . $this->oDbCon->paramString($categorykey);

      return $this->oDbCon->doQuery($sql);

   }

   function validateForm($arr, $pkey = '')
   {

      $car = new Car();
      $chassis = new Chassis();

      $arrayToJs = parent::validateForm($arr, $pkey);

      $arrItemkey = $arr['hidItemKey'];
      $arrCarKey = $arr['hidCarKey'];
      $chassisKey = $arr['hidChassisKey'];

      $arrMileage = $this->unFormatNumber($arr['mileage']);


      if ($arr['selType'] == 1) {
         $rsCar = $car->getDataRowById($arrCarKey);
         if (empty($rsCar))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['car'][1]);
         else {
            $rsLastKm = $this->searchData('', '', true, ' and ' . $this->tableName . '.carkey = ' . $arrCarKey . ' and ' . $this->tableName . '.statuskey in(2,3)', 'order by trdate desc');
            $lastMileage = (isset($rsLastKm[0]['mileage'])) ? $rsLastKm[0]['mileage'] : 0;

            if ($arrMileage < $lastMileage)
               $this->addErrorList($arrayToJs, false, $this->errorMsg['car'][8]);
         }
      } else if ($arr['selType'] == 2) {
         $rsChassis = $chassis->getDataRowById($chassisKey);
         if (empty($rsChassis))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['chassis'][1]);
      }

      return $arrayToJs;
   }


   function validateConfirm($rsHeader)
   {
   

   }

   function confirmTrans($rsHeader)
   {
      $this->addCarServiceMaintenance($rsHeader);
   }


   
   function validateCancel($rsHeader, $autoChangeStatus = false)
   {

      $id = $rsHeader[0]['pkey'];

      $carServiceMaintenance = new CarServiceMaintenance();

      $rsCarServiceMaintenance = $carServiceMaintenance->searchData('', '', true, ' and ' . $carServiceMaintenance->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' and (' . $carServiceMaintenance->tableName . '.statuskey = 2 or ' . $carServiceMaintenance->tableName . '.statuskey = 3 ) ');

      if(!empty($rsCarServiceMaintenance))
      {
         $this->addErrorLog(false, ' <strong> ' . $rsCarServiceMaintenance[0]['code'] . '. </strong> ' . $this->errorMsg['carServiceMaintenance'][3]);
      }

   }


   function cancelTrans($rsHeader, $copy)
   {
      $id = $rsHeader[0]['pkey'];

      $this->cancelCarServiceMaintenance($rsHeader);

      if ($copy)
         $this->copyDataOnCancel($id);
   }

   function addCarServiceMaintenance($rsHeader)
   {
      $carServiceMaintenance = new CarServiceMaintenance();

      $arrParam = array();
      $arrParam['code'] = 'xxxxx';
      $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
      $arrParam['estDate'] = $this->formatDBDate($rsHeader[0]['estdate'], 'd / m / Y');
      $arrParam['hidCarMaintenanceRequestKey'] = $rsHeader[0]['pkey'];
      $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
      $arrParam['selCategory'] = $rsHeader[0]['categorykey'];
      $arrParam['selType'] = $rsHeader[0]['typekey'];
      $arrParam['mileage'] = $rsHeader[0]['mileage'];
      $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];
      $arrParam['hidChassisKey'] = $rsHeader[0]['chassiskey'];
      $arrParam['selStatus'] = TRANSACTION_STATUS['menunggu'];

      $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

      $arrComplaint = array();
      foreach($rsDetail as $detail)
      {
         array_push($arrComplaint, $detail['trdesc']);
      }

      $complaint = implode(" \n",  $arrComplaint);
      $arrParam['complaint'] = $complaint;

      $arrayToJs = $carServiceMaintenance->addData($arrParam);
      if(!$arrayToJs[0]['valid'])
         throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

   }

   function cancelCarServiceMaintenance($rsHeader)
   {
      $carServiceMaintenance = new CarServiceMaintenance();

      $rsCarServiceMaintenance = $carServiceMaintenance->searchData('', '', true, ' and ' . $carServiceMaintenance->tableName.'.refkey = '. $this->oDbCon->paramString($rsHeader[0]['pkey']) .' and '. $carServiceMaintenance->tableName.'.statuskey = 1 ' );
   
      for($i=0; $i < count($rsCarServiceMaintenance); $i++)
      {
         $carServiceMaintenance->changeStatus($rsCarServiceMaintenance[$i]['pkey'], 4, '', false, true);
      }
   
   }

   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = parent::normalizeParameter($arrParam);

      return $arrParam;

   }

}

 ?>