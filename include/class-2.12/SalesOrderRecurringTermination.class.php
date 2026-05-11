<?php

class SalesOrderRecurringTermination extends BaseClass
{

   function __construct()
   {

      parent::__construct();
      
      $this->tableName = 'sales_order_recurring_termination';
      $this->tableSalesOrderSubscription = 'sales_order_recurring_subscription_header';
      $this->tableCustomer = 'customer';
      $this->tableWarehouse = 'warehouse';
      $this->tableStatus = 'transaction_status';

      
      $this->isTransaction = true;
      $this->securityObject = 'SalesOrderRecurringTermination';
      $this->newLoad        = true;


      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['trdate'] = array('trDate', 'date');
      $this->arrData['refsubscriptionkey'] = array('hidSubscriptionKey');
      $this->arrData['warehousekey'] = array('selWarehouse');
      $this->arrData['trdesc'] = array('trDesc');
      $this->arrData['terminatedate'] = array('terminateDate', 'date');
      $this->arrData['statuskey'] = array('selStatus');

      // $this->newLoad = true;

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 120, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'refCode', 'title' => 'reference', 'dbfield' => 'sosubscriptioncode', 'default' => true,   'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 120));


      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
      array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
      array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));
      array_push($this->arrSearchColumn, array('Referensi Code', $this->tableSalesOrderSubscription . '.code'));

      $this->includeClassDependencies(
         array(
            'Customer.class.php',
            'Warehouse.class.php',
            'SalesOrderRecurringSubscription.class.php',
            'Supplier.class.php'
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {
      $sql = '
         select
            '. $this->tableName .'.*,
            ' . $this->tableStatus . '.status as statusname,
            '. $this->tableSalesOrderSubscription .'.code as sosubscriptioncode,
            '. $this->tableSalesOrderSubscription .'.customerkey as socustomerkey,
            '. $this->tableCustomer .'.name as customername,
            '. $this->tableWarehouse .'.name as warehousename
         from 
            '. $this->tableStatus .',
            '. $this->tableWarehouse .',
            '. $this->tableName .'
                  left join '. $this->tableSalesOrderSubscription .' on '. $this->tableName .'.refsubscriptionkey = '. $this->tableSalesOrderSubscription .'.pkey
                  left join '. $this->tableCustomer . ' on '. $this->tableSalesOrderSubscription .'.customerkey = '. $this->tableCustomer .'.pkey
         where
            '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey and
            '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey
      ' . $this->criteria;


      return $sql;
   }

   function validateForm($arr, $pkey = '')
   {
      $arrayToJs = parent::validateForm($arr, $pkey);

      $salesOrderSubscription = new SalesOrderRecurringSubscription();

      $warehouse = $arr['selWarehouse'];
      $subscriptionKey = $arr['hidSubscriptionKey'];
      $customerKey = $arr['hidCustomerKey'];

      if(empty($warehouse))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['warehouse'][1]);
      }

      if(empty($subscriptionKey))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['salesOrderRecurringSubscriptionTerminate'][1]);
      }

      $rsSOSubscription = $salesOrderSubscription->getDataRowById($subscriptionKey);

      if(empty($rsSOSubscription))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['salesOrderRecurringSubscriptionTerminate'][2]);
      }

      if(($warehouse <> $rsSOSubscription[0]['warehousekey']))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg[905]);
      }

      if(!empty($customerKey))
      {
         if(($customerKey <> $rsSOSubscription[0]['customerkey']))
         {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][4]);
         }

      } else {

         $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
      }

      return $arrayToJs;
   }


   function validateConfirm($rsHeader)
   {
      $id = $rsHeader[0]['pkey'];

      $salesOrderSubscription = new SalesOrderRecurringSubscription();

      $warehouse = $rsHeader[0]['warehousekey'];

      $rsSOSubscription = $salesOrderSubscription->getDataRowById($rsHeader[0]['refsubscriptionkey']);

       if(empty($rsSOSubscription))
      {
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>' . $this->errorMsg['salesOrderRecurringSubscriptionTerminate'][2]);
      }

      if(($warehouse <> $rsSOSubscription[0]['warehousekey']))
      {
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'. </strong>' . $this->errorMsg[905]);
      }

      if(($rsSOSubscription[0]['statuskey'] <> TRANSACTION_STATUS['konfirmasi']))
      {
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>. ' . $this->errorMsg[201] . '<br>' . $this->errorMsg[204]);
      }

      
   }  

   function confirmTrans($rsHeader)
   {
      $id = $rsHeader[0]['pkey'];
      $salesOrderSubscription = new SalesOrderRecurringSubscription();
      
      //Change status recurring ke terminate
      $this->updateSalesOrderSubscriptionStatus($rsHeader[0]['refsubscriptionkey'], 3);
	   
	   // harusnya gk perlu otomatis kecancel SO nya ketiak cancel subscription
//      $salesOrder = new SalesOrder();

//      $rsSalesOrderSubscription = $salesOrderSubscription->getDataRowById($rsHeader[0]['refsubscriptionkey']);
//      if (!empty($rsSalesOrderSubscription)) {
//         $rsSalesOrder = $salesOrder->searchData('', '', true, ' and ' . $salesOrder->tableName . '.refsubscriptionkey = ' . $this->oDbCon->paramString($rsSalesOrderSubscription[0]['pkey']) .
//            ' and ' . $salesOrder->tableName . '.statuskey = 1 ');
//
//         //ubah status menjadi batal, kalau ada yang menunggu
//         for ($i = 0; $i < count($rsSalesOrder); $i++) {
//            $salesOrder->changeStatus($rsSalesOrder[$i]['pkey'], 4,'',false,true);
//         }
//      }

   }

   function validateCancel($rsHeader, $autoChangeStatus = false)
   {
      $pkey = $rsHeader[0]['pkey'];
      
   }

   function cancelTrans($rsHeader, $copy)
   {
      $id = $rsHeader[0]['pkey'];

      $salesOrderSubscription = new SalesOrderRecurringSubscription();
      $salesOrder = new SalesOrder();

      //cek apakah ada SO yang terkonfirmasi / selesai
      $rsSalesOrder = $salesOrder->searchData('', '', true, ' and ' . $salesOrder->tableName . '.refsubscriptionkey = ' . $this->oDbCon->paramString($rsHeader[0]['refsubscriptionkey']) . ' and ' . $salesOrder->tableName . '.statuskey in (2,3) ');
      if (!empty($rsSalesOrder)) {
         //kembalikan status ke aktif kalau terminate di batalkan, jika ada so yang konfirmasi / selesai
         $this->updateSalesOrderSubscriptionStatus($rsHeader[0]['refsubscriptionkey'], 2);

      } else {
         //batalkan recurring jika tidak ada so yang konfirmasi / selesai
         $salesOrderSubscription->changeStatus($rsHeader[0]['refsubscriptionkey'], 4,'',false,true);
      }

      if ($copy)
      $this->copyDataOnCancel($id);
   
   }

   function updateSalesOrderSubscriptionStatus($pkey, $statusKey)
   { 
      $salesOrderSubscription = new SalesOrderRecurringSubscription();
 	  $salesOrderSubscription->changeStatus($pkey, $statusKey,'',false,true); 
   }

   function normalizeParameter($arrParam, $trim = false)
   {
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

}

?>