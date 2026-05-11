<?php

class DisposalSalesWasteInvoice extends BaseClass
{

   function __construct()
   {

      parent::__construct();

      $this->tableName              = 'disposal_sales_waste_invoice_header';
      $this->tableNameDetail        = 'disposal_sales_waste_invoice_detail';
      $this->tablePayment           = 'disposal_sales_waste_invoice_payment';
      // $this->tableDownpaymentDetail = 'disposal_sales_waste_invoice_downpayment'; 
      $this->tableCustomer          = 'customer';
      $this->tableStatus            = 'transaction_status';
      $this->tableWarehouse         = 'warehouse';
      $this->tablePaymentMethod     = 'payment_method';
      $this->tableWaste             = 'waste';
      $this->tableItemUnit          = 'item_unit';
      $this->tableCustomer          = 'customer';
      $this->tableItem              = 'item';
      $this->tableWorkOrder         = 'disposal_work_order_header';
      $this->tableStatus            = 'transaction_status';
      $this->tableWarehouse         = 'warehouse';
      $this->tableSalesOrder        = 'disposal_job_order_header';
      $this->tablePaymentMethod     = 'payment_method';
      $this->tableDownpayment       = 'customer_downpayment';
      $this->tablePartialInvoice    = 'disposal_sales_invoice_partial';
      $this->tableAR                = 'ar';
      $this->tableWaste             = 'waste';
      $this->tableARStatus          = 'ar_status';
      $this->tableARPaymentHeader   = 'ar_payment_header';
      $this->tableARPaymentDetail   = 'ar_payment_detail';
      $this->tableCustomCode        = 'custom_code';
      $this->tableCOA               = 'chart_of_account';
      $this->isTransaction          = true;
      $this->newLoad                = true;
      $this->securityObject         = 'DisposalSalesWasteInvoice';

      $this->arrDataDetail         = array();
      //$this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItem, 'tableName' => $this->tableNameItemDetail)));
      $this->arrDataDetail['pkey'] = array('hidDetailKey');
      $this->arrDataDetail['refkey']               = array('pkey', 'ref');
      $this->arrDataDetail['wastekey']             = array('hidWasteKey');
      $this->arrDataDetail['unitkey']              = array('hidItemUnitKey');
      $this->arrDataDetail['quantity']             = array('quantity', 'number');
      $this->arrDataDetail['priceinunit']          = array('priceInUnit', 'number');
      $this->arrDataDetail['total']                = array('amount', 'number');

      $this->arrPaymentDetail = array(); 
      $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
      $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
      $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
      $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

      // $arrDownpaymentDetail = array(); 
      // $arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
      // $arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
      // $arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
      // $arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true));


      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
      array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
      // array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));

      $this->arrData                   = array();
      $this->arrData['pkey']           = array('pkey',array('dataDetail' => $arrDetails));
      $this->arrData['code']           = array('code');
      $this->arrData['trdate']         = array('trDate', 'date');
      $this->arrData['customerkey']    = array('hidCustomerKey');
      $this->arrData['warehousekey']   = array('selWarehouseKey');
      $this->arrData['trdesc']         = array('trDesc');
      $this->arrData['statuskey']      = array('selStatus');
      $this->arrData['grandtotal']     = array('grandTotal', 'number');
      $this->arrData['termofpaymentkey'] = array('selTermOfPayment'); 
      $this->arrData['subtotal']            = array('subtotal', 'number');
      $this->arrData['beforetaxtotal']      = array('beforeTaxTotal', 'number');
      $this->arrData['ispriceincludetax']   = array('isPriceIncludeTax');
      $this->arrData['totalpayment']        = array('totalPayment', 'number');
      $this->arrData['balance']             = array('balance', 'number');
      $this->arrData['finaldiscounttype']   = array('selFinalDiscountType');
      $this->arrData['finaldiscount']       = array('finalDiscount', 'number');
      $this->arrData['finaldiscounttype']   = array('selFinalDiscountType');
      $this->arrData['ispriceincludetax']   = array('isPriceIncludeTax');
      $this->arrData['taxpercentage']       = array('taxPercentage', 'number');
      $this->arrData['taxvalue']            = array('taxValue', 'number');
      $this->arrData['ispriceincludetax']   = array('chkIncludeTax');
      $this->arrData['tax23percentage']     = array('tax23Percentage', 'number');
      $this->arrData['tax23value']          = array('tax23Value', 'number');
      $this->arrData['usetax23']            = array('chkTax23');
      $this->arrData['donumber']            = array('doNumber');
      $this->arrData['shipmentnumber']      = array('shipmentNumber');
      $this->arrData['totaldownpayment']    = array('totalDownpayment', 'number');
      $this->arrData['outstanding']         = array('outstanding', 'number');
      $this->arrData['salesordercodecache'] = array('salesordercodecache');
      $this->arrData['isdownpayment']       = array('chkDownpayment');
      $this->arrData['invoiceto']           = array('selInvoiceTo');
      $this->arrData['currencykey']         = array('selCurrency');
      $this->arrData['rate']                = array('currencyRate', 'number');
      $this->arrData['vanumber']            = array('vanumber');
      $this->arrData['containernumber']     = array('containerNumber'); // taro di header saja biar bisa ditrim, kalo nanti perlu br detail ditambahin jg
      $this->arrData['requestid']           = array('requestId'); // taro di header saja biar bisa ditrim, kalo nanti perlu br detail ditambahin jg

      //$this->refAutoCode = array('param' => 'hidRefInvoiceKey', 'refField' => 'pkey');

      // perlu tambahin $this->tableNameItemDetail, tp harus manual, karena refkeynya beda
      // $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment, $this->tableDownpaymentDetail);

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 90, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'duedate', 'title' => 'duedate', 'dbfield' => 'duedate', 'width' => 90, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceAmount', 'title' => 'invoiceAmount', 'dbfield' => 'grandtotal', 'default' => true, 'align' => 'right', 'format' => 'integer', 'width' => 100));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'downpayment', 'title' => 'downpayment', 'dbfield' => 'totaldownpayment', 'default' => true, 'align' => 'right', 'format' => 'integer', 'width' => 90));
      array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceOutstanding', 'title' => 'invoiceOutstanding', 'dbfield' => 'outstanding', 'default' => true, 'align' => 'right', 'format' => 'integer', 'width' => 90));
      array_push($this->arrDataListAvailableColumn, array('code' => 'arOutstanding', 'title' => 'outstanding', 'dbfield' => 'aroutstanding', 'align' => 'right', 'format' => 'integer', 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'arstatusname', 'title' => 'arStatus', 'dbfield' => 'arstatusname', 'width' => 100, 'default' => true));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
      array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'width' => 120));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceType', 'title' => 'invoiceType', 'dbfield' => 'invoicetype', 'width' => 100));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'receiptDate', 'title' => 'receiptDate', 'dbfield' => 'receiptdt', 'width' => 100, 'align' => 'center', 'format' => 'date'));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'salesordercodecache', 'title' => 'JOCode', 'dbfield' => 'salesordercodecache', 'width' => 150));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'taxvalue', 'title' => 'tax', 'dbfield' => 'taxvalue', 'width' => 100, 'align' => 'right', 'format' => 'integer', ));
      // array_push($this->arrDataListAvailableColumn, array('code' => 'tax23value', 'title' => 'tax23', 'dbfield' => 'tax23value', 'width' => 100, 'align' => 'right', 'format' => 'integer'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'confirmedon', 'title' => 'confirmedDate', 'dbfield' => 'confirmedon', 'width' => 100, 'align' => 'center', 'format' => 'date'));

      $this->printMenu = array();
      array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/disposalSalesWasteInvoice'));
      //array_push($this->printMenu, array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'], 'icon' => 'print', 'url' => 'print/disposalSalesInvoice'));
      //array_push($this->printMenu, array('code' => 'printInvoiceKop', 'name' => $this->lang['printInvoice'] . ' (' . $this->lang['letterhead'] . ')', 'icon' => 'print', 'url' => 'print/disposalSalesInvoiceKop'));

      array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
      array_push($this->filterCriteria, array('title' => $this->lang['ar'], 'field' => $this->tableARStatus . '.pkey', 'alias' => 'arstatuskey', 'sql' => 'select pkey,status as name from ' . $this->tableARStatus . ' where 1=1'));

      $this->includeClassDependencies(
         array(
            'AP.class.php',
            'AR.class.php',
            'Customer.class.php',
            'Downpayment.class.php',
            'ServicePackage.class.php',
            'CustomerDownpayment.class.php',
            'GeneralJournal.class.php',
            'Item.class.php',
            'ItemUnit.class.php',
            'Supplier.class.php',
            'TermOfPayment.class.php',
            'Category.class.php',
            'DisposalJobOrder.class.php',
            'Warehouse.class.php',
            'DisposalContract.class.php',
            'DisposalWorkOrder.class.php',
            'APEmployeeCommission.class.php',
            'Service.class.php'
         )
      );
      $this->overwriteConfig();
   }

   function getQuery()
   {

      $rsKey = $this->getTableKeyAndObj($this->tableName, array('key'));

      $sql = '
            SELECT
                ' . $this->tableName . '.* ,
                ' . $this->tableWarehouse . '.name as warehousename, 
                ' . $this->tableCustomer . '.code as customercode, 
                ' . $this->tableCustomer . '.name as customername, 
                ' . $this->tableStatus . '.status as statusname,
                '.$this->tableAR.'.duedate,
                coalesce('.$this->tableAR.'.outstanding,0) as aroutstanding,
                '.$this->tableAR.'.statuskey as arstatuskey,
                '.$this->tableARStatus.'.status as arstatusname
            FROM ' . $this->tableStatus . ',
                 ' . $this->tableName . '
                 left join '.$this->tableAR.' on '.$this->tableAR.'.refheaderkey = '.$this->tableName.'.pkey and '.$this->tableAR.'.reftabletype = '. $this->oDbCon->paramString($rsKey['key']).'
                 left join '.$this->tableARStatus.' on '.$this->tableAR.'.statuskey = '.$this->tableARStatus.'.pkey and '.$this->tableAR.'.statuskey <> 4,
                 ' . $this->tableCustomer . ', 
                 ' . $this->tableWarehouse . ' 
            WHERE   
                  ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                  ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 
            ' . $this->criteria;

      $sql .= $this->getWarehouseCriteria();

      $sql .= ' group by ' . $this->tableName . '.pkey ';

      return $sql;
   }


   function reCountGrandTotal($arrParam)
   {
      $disposalJobOrder = new DisposalJobOrder();

      $usePPNDetail = $this->loadSetting('usePPNDetail');

      $grandtotal = 0;
      $subtotal   = 0;

      $isPriceIncludeTax = $arrParam['chkIncludeTax'];
      //$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
      $finalDiscount            = $this->unFormatNumber($arrParam['finalDiscount']);
      $finalDiscountType        = $arrParam['selFinalDiscountType'];
      $taxPercentage            = $this->unFormatNumber($arrParam['taxPercentage']);
      $taxValue            = $this->unFormatNumber($arrParam['taxValue']);
      $arrSalesOrderKey         = $arrParam['hidSalesOrderKey'];
      $arrSalesOrderDescription = $arrParam['detailNote'];
      $arrItemKey               = $arrParam['hidItemKey'];
      $arrSalesOrderDownpayment = $arrParam['salesOrderDownpayment'];
      $arrServicePrice          = $arrParam['servicePrice'];
      $arrAmount                = $arrParam['amount'];
      $arrPick                  = $arrParam['chkPick'];
      $arrPriceInUnit           = $arrParam['priceInUnit'];
      $arrWasteKey              = $arrParam['hidWasteKey'];
      $arrItemUnitKey           = $arrParam['hidItemUnitKey'];
      $arrQty                   = $arrParam['quantity'];

      $detailSubtotalTaxed  = 0;
      $detailBeforeTaxTotal = 0;
      $detailTaxTotal       = 0;
      $arrDetailInvoice     = array();

      for ($i = 0; $i < count($arrPick); $i++) {
         $arrServicePrice[$i]          = $this->unFormatNumber($arrServicePrice[$i]);
         $arrAmount[$i]                = $this->unFormatNumber($arrAmount[$i]);
         $arrSalesOrderDownpayment[$i] = $this->unFormatNumber($arrSalesOrderDownpayment[$i]);
         $arrPriceInUnit[$i]           = $this->unFormatNumber($arrPriceInUnit[$i]);
         $arrQty[$i]                   = $this->unFormatNumber($arrParam['quantity'][$i]);

         if ((empty($arrPriceInUnit[$i]) && empty($arrWasteKey[$i]) && empty($arrItemUnitKey[$i])) || empty($arrAmount[$i]) || empty($arrPick[$i]))
            continue;
 
         $subtotal += $arrQty[$i] * $arrPriceInUnit[$i];
         
         
      }
      $grandtotal += $subtotal;


         if ($finalDiscount != 0) {
            if ($finalDiscountType == 2)
               $finalDiscount = $finalDiscount / 100 * $subtotal;
         }

         $beforeTaxTotal = $grandtotal - $finalDiscount;
         $grandtotal = $beforeTaxTotal;

         $this->recalculateTaxAndValue($beforeTaxTotal, $grandtotal, $taxValue, $taxPercentage, $isPriceIncludeTax);
      

      $balance      = 0;
      $totalPayment = 0;

      $termOfPayment = new TermOfPayment();
      $rsTOP         = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);
      if ($rsTOP[0]['duedays'] == 0) {
         $payment = $arrParam['paymentMethodValue'];
         for ($i = 0; $i < count($payment); $i++) {
            $totalPayment += $this->unFormatNumber($payment[$i]);
         }
      }

      $totalDowpayment = 0;
      $downpayment     = $arrParam['downpaymentAmount'];
      $downpaymentKey  = $arrParam['hidDownpaymentKey'];
      for ($i = 0; $i < count($downpayment); $i++) {
         if (empty($downpaymentKey[$i]))
            continue;
         $totalDowpayment += $this->unFormatNumber($downpayment[$i]);
      }
      $tax23Percentage = $arrParam['tax23Percentage'];

      $outstanding = $grandtotal - $totalDowpayment;
      $balance     = $totalPayment - $outstanding;

      // hitung PPH 23  
      if ($isPriceIncludeTax)
         $detailSubtotalTaxed = $detailSubtotalTaxed - (round(($taxPercentage / (100 + $taxPercentage)) * $detailSubtotalTaxed));
      $tax23 = $tax23Percentage * $detailSubtotalTaxed / 100;


      $reCountResult                      = array();
      $reCountResult['subtotal']          = $subtotal;
      $reCountResult['beforeTaxTotal']    = $beforeTaxTotal;
      $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
      $reCountResult['grandTotal']        = $grandtotal;
      $reCountResult['totalPayment']      = $totalPayment;
      $reCountResult['totalDownpayment']  = $totalDowpayment;
      $reCountResult['outstanding']       = $outstanding;
      $reCountResult['balance']           = $balance;
      $reCountResult['tax23Value']        = $tax23;
      $reCountResult['taxValue']          = ($usePPNDetail) ? $detailTaxTotal : $taxValue;
      $reCountResult['recountDetail']     = ($usePPNDetail) ? $arrDetailInvoice : array();


      return $reCountResult;

   }

   function validateForm($arr, $pkey = '')
   {

      // $disposalJobOrder = new DisposalJobOrder();
      $arrayToJs        = parent::validateForm($arr, $pkey);
      $downpayment      = new CustomerDownpayment();

      $waste = new Waste();

      $customerkey              = $arr['hidCustomerKey'];
      $arrAmount                = $arr['amount'];
      $arrWasteKey              = $arr['hidWasteKey'];
      $arrQty                   = $arr['quantity'];
      $arrItemUnit                   = $arr['hidItemUnitKey'];


      $arrDetailKey = array();
      if (empty($customerkey))
         $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);

      // //validasi kalo status gk menunggu gk bisa edit 
      if (!empty($pkey)) {
         $rs = $this->getDataRowById($pkey);
         if ($rs[0]['statuskey'] <> 1) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[212]);
         }
      }
      
      for ($i = 0; $i < count($arrWasteKey); $i++)
      {

         if(!empty($arrWasteKey[$i]))
         {
            $rsWaste = $waste->getDataRowById($arrWasteKey[$i]);

            // $arrAmount[$i]  = $this->unformatNumber($arrAmount[$i]);
            $arrQty[$i]  = $this->unformatNumber($arrQty[$i]);

            if($arrQty[$i] <= 0)
            {
               $this->addErrorList($arrayToJs, false, '<strong>' . $rsWaste[0]['name'] . '.</strong> ' . $this->errorMsg['qty'][1]);
            }

            if ($arrAmount[$i] <= 0)
               $this->addErrorList($arrayToJs,false,$this->errorMsg[503]);

         } else {
            $this->addErrorList($arrayToJs,false, $arr['code'].'. '.$this->errorMsg['waste'][1]); 
         }

      }



      return $arrayToJs;
   }



   function recalculatePriceBeforeTaxAndFinalDiscount($rs, $amount)
   {
      // potong diskon proposional dan hitung harga sebelum pajak

      $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount'] / 100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount'];
      $total         = $rs[0]['subtotal'];
      $taxPercentage = $rs[0]['taxpercentage'];

      $discount = (!empty($finalDiscount)) ? $amount / $total * $finalDiscount : 0;

      $priceBeforeTax = $amount - $discount;

      if (!empty($rs[0]['taxvalue'])) {
         if ($rs[0]['ispriceincludetax']) {
            $taxValue       = round(($taxPercentage / (100 + $taxPercentage)) * $priceBeforeTax);
            $priceBeforeTax = $priceBeforeTax - $taxValue;
         }
      }


      return $priceBeforeTax;

   }

   function updateGL($rs, $arrShareProfit)
   {
      if (!USE_GL)
         return;

      $generalJournal   = new GeneralJournal();
      $disposalJobOrder = new DisposalJobOrder();
      $coaLink          = new COALink();
      $warehouse        = new Warehouse();
      $customer         = new Customer();
      $item             = new Item();
      $customCode       = new CustomCode();
      $service          = new Service();

      $rsCustomCode = $customCode->getDataRowById($rs[0]['customcodekey']);
      $isReimburse  = $rsCustomCode[0]['isreimburse'];

      // harusnya gk perlu, sebelum dipanggil sudah divalidasi
      //$nogl =  $rsCustomCode[0]['nogl']; 
      //kalo gk pake GL, biasanya utk invoice void
      //if($nogl == 1) return;

      $usePPNDetail = $this->loadSetting('usePPNDetail');
      $warehousekey = $rs[0]['warehousekey'];
      $id           = $rs[0]['pkey'];
      $rsDetail     = $this->getDetailById($id);


      //$invoiceType = $rs[0]['customcodekey'];

      $rsKey                  = $generalJournal->getTableKeyAndObj($this->tableName);
      $arr                    = array();
      $arr['pkey']            = $generalJournal->getNextKey($generalJournal->tableName);
      $arr['code']            = 'xxxxx';
      $arr['refkey']          = $rs[0]['pkey'];
      $arr['refTableType']    = $rsKey['key'];
      $arr['trDate']          = $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
      $arr['createdBy']       = 0;
      $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

      // desc
      $desc       = array();
      $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
      array_push($desc, $rsCustomer[0]['name']);
      if (!empty($rs[0]['trdesc']))
         array_push($desc, $rs[0]['trdesc']);
      $arr['trDesc'] = implode(chr(13), $desc);


      $temp      = -1;
      $totalDisc = 0;

      /*        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
              $totalDisc = $finalDiscount;*/

      $termOfPayment = new TermOfPayment();
      $rsTOP         = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
      $isCash        = ($rsTOP[0]['duedays'] == 0) ? true : false;

      if ($isCash) {
         $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);
         for ($i = 0; $i < count($rsPayment); $i++) {
            $rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $warehousekey, $rsPayment[$i]['paymentkey']);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp]     = $rsPayment[$i]['amount'];
            $arr['credit'][$temp]    = 0;
         }

         //selisih pembayaran   
         $temp++;
         if ($rs[0]['balance'] < 0) {
            $rsCOA                = $coaLink->getCOALink('othercost', $warehouse->tableName, $warehousekey, 0);
            $arr['debit'][$temp]  = abs($rs[0]['balance']);
            $arr['credit'][$temp] = 0;
         } else {
            $rsCOA                = $coaLink->getCOALink('otherrevenue', $warehouse->tableName, $warehousekey, 0);
            $arr['debit'][$temp]  = 0;
            $arr['credit'][$temp] = abs($rs[0]['balance']);
         }

         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];

      } else {
         $temp++;
         $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['customerkey'], $warehousekey);
         $arr['debit'][$temp]     = $rs[0]['grandtotal'] - $rs[0]['totaldownpayment'];
         $arr['credit'][$temp]    = 0;
      }
      // $this->setLog($arr, true);
      //downpayment 
      // $rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);
      // for ($i = 0; $i < count($rsDownpayment); $i++) {
      //    $temp++;
      //    $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'], $warehousekey);
      //    $arr['debit'][$temp]     = $rsDownpayment[$i]['amount'];
      //    $arr['credit'][$temp]    = 0;
      // }

      for ($i = 0; $i < count($rsDetail); $i++) {

         $coaKey = $coaLink->getCOALink('salesretail', $warehouse->tableName, $warehousekey, 0)[0]['coakey'];

         $temp++;
         $arr['hidCOAKey'][$temp] = $coaKey;
         $arr['debit'][$temp]     = 0;
         $arr['credit'][$temp]    = $rsDetail[$i]['total'];
      }


      /*        $rsCOA = $coaLink->getCOALink ('salesservicediscount', $warehouse->tableName,$warehousekey, 0);
              $temp++;
              $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
              $arr['debit'][$temp] = $totalDisc; 
              $arr['credit'][$temp] = 0; */

      $rsCOA = $coaLink->getCOALink('taxout', $warehouse->tableName, $warehousekey, 0);
      $temp++;
      $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
      $arr['debit'][$temp]     = 0;
      $arr['credit'][$temp]    = $rs[0]['taxvalue'];


      // kalo ad share profit
      $rsCOA = $coaLink->getCOALink('outsourcecost', $warehouse->tableName, $warehousekey);
      foreach ($arrShareProfit as $shareProfitRow) {

         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp]     = $shareProfitRow['amount'];
         $arr['credit'][$temp]    = 0;

         $temp++;
         $arr['hidCOAKey'][$temp] = $shareProfitRow['coakey'];
         $arr['debit'][$temp]     = 0;
         $arr['credit'][$temp]    = $shareProfitRow['amount'];
      }

      // kalo pph23 dibayar dimuka diakui
      if ($this->loadSetting('tax23GLInInvoice') == 1 && $rs[0]['tax23value'] > 0) {
         $rsCOA = $coaLink->getCOALink('prepaidTax23', $warehouse->tableName, $warehousekey);
         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp]     = $rs[0]['tax23value'];
         $arr['credit'][$temp]    = 0;

         $rsCOA = $coaLink->getCOALink('prepaidTax23Counter', $warehouse->tableName, $warehousekey);
         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp]     = 0;
         $arr['credit'][$temp]    = $rs[0]['tax23value'];
         // $this->setLog($rsCOA, true);
      }

      $arrayToJs = $generalJournal->addData($arr);


      if (!$arrayToJs[0]['valid'])
         throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);

   }

   function getDetailWithRelatedInformation($pkey, $criteria = '')
   {
      $sql = 'select
            ' . $this->tableNameDetail . '.*,
            '. $this->tableWaste .'.code as wastecode,
            '. $this->tableWaste .'.name as wastename,
            concat('. $this->tableWaste .'.code, " - ", '. $this->tableWaste .'.name) as wastecodename,   
            '. $this->tableItemUnit .'.name as unitname
         from
            ' . $this->tableNameDetail . '
            left join '. $this->tableWaste .' on '. $this->tableNameDetail .'.wastekey = '. $this->tableWaste .'.pkey  
            left join '. $this->tableItemUnit .' on '. $this->tableNameDetail .'.unitkey = '. $this->tableItemUnit .'.pkey
          where  
            ' . $this->tableNameDetail . '.refkey in  (' . $this->oDbCon->paramString($pkey, ',') . ') ';

      $sql .= $criteria;

      return $this->oDbCon->doQuery($sql);

   }

   function normalizeParameter($arrParam, $trim = false)
   {

      $disposalJobOrder = new DisposalJobOrder();
      $termOfPayment    = new TermOfPayment();
      $customCode       = new CustomCode();

      $fromAPI = (isset($arrParam['_mnv-api']) && $arrParam['_mnv-api'] == 1) ? true : false;

      // kalo invoice void 
      $rsCustomCode = $customCode->getDataRowById($arrParam['selCustomCode']);
      $nogl         = ($rsCustomCode[0]['nogl'] == 1) ? true : false;

      if ($nogl) {
         $arrParam['selTermOfPayment']        = 1; //tembak system variable
         $arrParam['hidDetailDownpaymentKey'] = array();
         $arrParam['downpaymentAmount']       = array();
         $arrParam['hidDownpaymentKey']       = array();
         $arrParam['hidDetailPaymentKey']     = array();
         $arrParam['paymentMethodValue']      = array();
         $arrParam['selPaymentMethod']        = array();
      }

      $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);

      $usePPNDetail = $this->loadSetting('usePPNDetail');

      $arrParam['chkIncludeTax'] = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;
      $arrParam['chkTax23']      = (!empty($arrParam['chkTax23'])) ? 1 : 0;
      $arrParam['currencyRate']  = (!isset($arrParam['currencyRate']) || $this->unFormatNumber($arrParam['currencyRate']) <= 0) ? 1 : $arrParam['currencyRate'];

      $WOKey = count($arrParam['hidWODetailKey']);

      // kalo detail ppn di setiap baris
      if ($usePPNDetail) {
         // header di nol kan
         $arrParam['chkIncludeTax'] = 0;
         $arrParam['taxPercentage'] = 0;
      }

      if ($rsTOP[0]['duedays'] != 0) {
         for ($i = 0; $i < count($arrParam['paymentMethodValue']); $i++) {
            $arrParam['paymentMethodValue'][$i]  = 0;
            $arrParam['hidDetailPaymentKey'][$i] = 0;
         }
      }

      $detail    = $arrParam['hidDetailKey'];
      $isPartial = $arrParam['chkDownpayment'];

      $arrDONumber       = array();
      $arrShipmentNumber = array();
      $arrJO             = array();



      // sementara, nanti dr recount dihitung ulang
      // $this->setLog('sementara, nanti dr recount dihitung ulang',true);
      $totalItemDetail = count($arrParam['exceedWeightPriceArea']);
      for ($i = 0; $i < $totalItemDetail; $i++) {

         $disposalWeight = $this->unFormatNumber($arrParam['disposalWeight'][$i]);
         $maximumWeight  = $this->unFormatNumber($arrParam['maximumWeight'][$i]);
         $chargedWeight  = $this->unFormatNumber($arrParam['chargedWeight'][$i]);
         // $chargedWeight = $disposalWeight - $maximumWeight;
         $chargedWeight = ceil($chargedWeight);

         if ($chargedWeight <= 0) {
            $arrParam['totalExceedWeightPriceArea'] = 0;
            continue;
         }

         $subtotalDetail                             = $chargedWeight * $this->unFormatNumber($arrParam['exceedWeightPriceArea'][$i]);
         $arrParam['totalExceedWeightPriceArea'][$i] = $subtotalDetail;
         $arrParam['subtotalDetail'][$i]             = $subtotalDetail;
         $arrParam['chargedWeight'][$i]              = $chargedWeight;
      }




      for ($i = 0; $i < count($detail); $i++) {
         $arrParam['selInvoiceType'][$i] = ($isPartial) ? 1 : $arrParam['selInvoiceType'][$i];

         if ($arrParam['chkPick'][$i] == 0 || (empty($arrParam['hidItemKey'][$i]) && empty($arrParam['hidSalesOrderKey'][$i]))) {
            $arrParam['selInvoiceType'][$i] = '';
            continue;
         }

         if ($arrParam['selInvoiceType'][$i] == 1) {

            $rsJO = $disposalJobOrder->getDataRowById($arrParam['hidSalesOrderKey'][$i]);
            if (!empty($rsJO)) {
               array_push($arrJO, $rsJO[0]['code']);
            }

         }
      }

      for ($i = 0; $i < $WOKey; $i++) {

         if ($isPartial) {
            $arrParam['hidWODetailKey'][$i] = '';
            continue;
         }

         if ($arrParam['chkService'][$i])
            continue;
         $arrParam['hidWODetailKey'][$i] = '';

      }
      $arrParam['salesordercodecache'] = (!empty($arrJO)) ? implode(', ', $arrJO) : '';

      if ($arrParam['chkDownpayment']) {
         $arrParam['finalDiscount']        = 0;
         $arrParam['selFinalDiscountType'] = 1;
      }

      $details = array();
      array_push($details, $this->arrItem);

      $arrParam      = $this->prepareMultiLevelDetail($arrParam, $details);
      $reCountResult = $this->reCountGrandTotal($arrParam);


      $arrParam['subtotal']          = $reCountResult['subtotal'];
      $arrParam['grandTotal']        = $reCountResult['grandTotal'];
      $arrParam['beforeTaxTotal']    = $reCountResult['beforeTaxTotal'];
      $arrParam['ispriceincludetax'] = $reCountResult['isPriceIncludeTax'];
      $arrParam['totalPayment']      = $reCountResult['totalPayment'];
      $arrParam['totalDownpayment']  = $reCountResult['totalDownpayment'];
      $arrParam['outstanding']       = $reCountResult['outstanding'];
      $arrParam['balance']           = $reCountResult['balance'];
      $arrParam['tax23Value']        = $reCountResult['tax23Value'];
      $arrParam['taxValue']          = $reCountResult['taxValue'];


      $recountDetail = $reCountResult['recountDetail'];
      for ($i = 0; $i < count($recountDetail); $i++) {
         $arrParam['amount'][$i]              = $recountDetail[$i]['amount'];
         $arrParam['detailOrderSubtotal'][$i] = $recountDetail[$i]['detailOrderSubtotal'];
      }


      // model logol
      if ($usePPNDetail) {

         for ($i = 0; $i < count($recountDetail); $i++) {
            $arrDetailValue  = $recountDetail[$i]['detailValue'];
            $countItemDetail = count($arrDetailValue);
            for ($j = 0; $j < $countItemDetail; $j++) {
               $arrParam['priceInUnitDetail'][$i][$j] = $arrDetailValue[$j]['priceInUnitDetail'];
               $arrParam['beforeTaxDetail'][$i][$j]   = $arrDetailValue[$j]['beforeTaxDetail'];
               $arrParam['subtotalDetail'][$i][$j]    = $arrDetailValue[$j]['subtotalDetail'];
               $arrParam['taxValueDetail'][$i][$j]    = $arrDetailValue[$j]['taxValueDetail'];
               $arrParam['afterTaxDetail'][$i][$j]    = $arrDetailValue[$j]['afterTaxDetail'];
            }
         }
      }


      if ($fromAPI) {
         // kalo jenis paymentnya cash, asumsi selalu dr VA, pasti sama angkanya
         if ($rsTOP[0]['duedays'] == 0) {
            $arrParam['paymentMethodValue'][0] = $arrParam['grandTotal'];
         }
      }

      $arrParam = parent::normalizeParameter($arrParam, true);


      return $arrParam;

   }




   function afterARStatusChanged($pkey)
   {
      // asumsi satu JO satu invoice

      $ar                   = new AR();
      $termOfPayment        = new TermOfPayment();
      $disposalJobOrder     = new DisposalJobOrder();
      $disposalWorkOrder    = new DisposalWorkOrder();
      $APEmployeeCommission = new APEmployeeCommission();

      $rs       = $this->getDataRowById($pkey); // rs = status 4
      $rsDetail = $this->getDetailById($pkey);
      $rsTOP    = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
      $isCash   = ($rsTOP[0]['duedays'] == 0) ? true : false;

      // invoice header (amount layanan > 0)
      // invice detail (amount header ==0)
      // invoice header + detail (amount layanan > 0)

      for ($i = 0; $i < count($rsDetail); $i++) {
         $JOKey           = $rsDetail[$i]['salesorderkey'];
         $rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);

         if ($rsDetail[$i]['serviceprice'] > 0) {

            // ubah status JO
            $rsJO = $disposalJobOrder->getDataRowById($JOKey);

            // hanya diubah jika status lebih kecil dr Aktif.
            // kalo sudah diaktif keatas harusny kena validasi di validate cancel misalnya
            if ($rsJO[0]['statuskey'] <= 4) {
               $statusKey = 2; // status default KO konfirmasi

               // kalo gk ad invoice sama sekali utk JO, 
               // karena kalo gk ad validsi ini, status JO gk blk ke konfirmasi kalo cancel invoice tempo
               // untuk sementara karena 1 JO pasti satu invoice, kita cek statusny saja
               if ($rs[0]['statuskey'] == 2) {
                  // kalo dikonfirmasi saja. karena kalo selesai gk boleh ganti lg. bisa saja sudah selesi proses SP Kjg di JO nya
                  // kalo cash
                  $this->addEmployeeCommission($rs);
                  if ($isCash) {
                     $statusKey = 4; // status aktif
                  } else {
                     // kalo tempo / AR 
                     $rsInvoiceKey = $this->getTableKeyAndObj($this->tableName, array('key'));
                     $rsAR         = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsInvoiceKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($pkey) . ' and (' . $ar->tableName . '.statuskey <> 4)');
                     $statusKey    = ($rsAR[0]['statuskey'] == 3) ? 4 : 3;
                  }

               }

               if ($statusKey <> $rsJO[0]['statuskey']) // kalo status sudah diatas / sama dengn AKTIF, jgn ubah balik ke status "Telah difaktur"
                  $disposalJobOrder->changeStatus($JOKey, $statusKey, '', false, true);
            }

            // update detail status
//                for ($j=0; $j<count($rsServiceDetail); $j++) 
//                    $disposalJobOrder->updateDetailStatus($JOKey);

         } else {
            //  $disposalJobOrder->updateDetailStatus($JOKey);
         }


      }


      // ubah status AR
      // berulang querynya dr atas gpp, nanti aj br dibenerin

      $rsInvoiceKey = $this->getTableKeyAndObj($this->tableName, array('key'));
      if ($isCash) {
         if ($rs[0]['statuskey'] == 4) {
            $arStatuskey = 1;
         } else {
            $arStatuskey = 3;
         }
      } else {
         $rsAR        = $ar->searchDataRow(
            array($ar->tableName . '.statuskey'),
            ' and reftabletype = ' . $this->oDbCon->paramString($rsInvoiceKey['key']) . ' 
            and refkey = ' . $this->oDbCon->paramString($pkey) . ' 
            and (' . $ar->tableName . '.statuskey <> 4)'
         );
         $arStatuskey = (!empty($rsAR)) ? $rsAR[0]['statuskey'] : 1;
      }


      $sql = 'update ' . $APEmployeeCommission->tableName . ' 
                set arstatuskey = ' . $this->oDbCon->paramString($arStatuskey) . ' 
                where refkey  = ' . $this->oDbCon->paramString($pkey) . ' and reftabletype = ' . $this->oDbCon->paramString($rsInvoiceKey['key']);
      $this->oDbCon->doQuery($sql);

      // update ar status untuk komisi employee per titik
      $rsWODetail = $this->getItemDetail($rs[0]['pkey'], 'refheaderkey');
      $arrWOKey   = array_column($rsWODetail, 'wokey');
      $rsWOKey    = $disposalWorkOrder->getTableKeyAndObj($disposalWorkOrder->tableName, array('key'));
      foreach ($arrWOKey as $WOKey) {
         $sql = 'update ' . $APEmployeeCommission->tableName . ' 
                set arstatuskey = ' . $this->oDbCon->paramString($arStatuskey) . ' 
                where refkey  = ' . $this->oDbCon->paramString($WOKey) . ' and reftabletype = ' . $this->oDbCon->paramString($rsWOKey['key']);
         $this->oDbCon->doQuery($sql);
      }

   }

   function addEmployeeCommission($rsHeader)
   {

      $APEmployeeCommission = new APEmployeeCommission();
      $disposalJobOrder     = new DisposalJobOrder();
      $service              = new Service();
      $employee             = new Employee();

      $pkey     = $rsHeader[0]['pkey'];
      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));
      $rsDetail = $this->getDetailById($pkey);

      $commissionDateType = $this->loadSetting('driverCommissionBasedOn');
      $commissionDate     = ($commissionDateType == 2) ? $this->formatDBDate($rsHeader[0]['trdate']) : date('d / m / Y');

      for ($i = 0; $i < count($rsDetail); $i++) {
         $JOKey = $rsDetail[$i]['salesorderkey'];
         $rsJO  = $disposalJobOrder->getDataRowById($JOKey);

         $rsAPCommission = $APEmployeeCommission->searchDataRow(array($APEmployeeCommission->tableName . '.pkey', $APEmployeeCommission->tableName . '.refkey2'), 'and ' . $APEmployeeCommission->tableName . '.statuskey in (1,2,3) and ' . $APEmployeeCommission->tableName . '.refkey2 in (' . $this->oDbCon->paramString($rsJO[0]['contractkey'], ',') . ') and ' . $APEmployeeCommission->tableName . '.reftabletype = ' . $tablekey['key']);

         if (empty($rsAPCommission)) {

            $rsService = $service->getDataRowById($rsJO[0]['servicekey']);

            $employeeCommission = $rsService[0]['firstemployeecommission'];
            $rsTeleSales        = $employee->getDetailCommission($rsJO[0]['saleskey'], $rsJO[0]['servicekey']);
            if (!empty($rsTeleSales) && $rsTeleSales[0]['commission'] > 0) {
               $employeeCommission = $rsTeleSales[0]['commission'];
            }
            $note = array();
            array_push($note, $rsHeader[0]['code']);

            $arrParam                    = array();
            $arrParam['code']            = 'xxxxxx';
            $arrParam['hidRefKey']       = $rsHeader[0]['pkey'];
            $arrParam['hidRefKey2']      = $rsJO[0]['contractkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode']      = $rsHeader[0]['code'];
            $arrParam['hidRefCode2']     = $rsJO[0]['code'];
            $arrParam['hidRefDate']      = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['hidRefTable']     = $tablekey['key'];
            $arrParam['trDesc']          = implode(chr(13), $note);
            $arrParam['trDate']          = $commissionDate;
            $date                        = new DateTime(date('Y-m-d'));
            $date->add(new DateInterval('P30D'));
            $arrParam['dueDate']        = $date->format('d / m / Y');
            $arrParam['createdBy']      = 0;
            $arrParam['overwriteGL']    = 1;
            $arrParam['islinked']       = 1;
            $arrParam['selAPType']      = AP_TYPE['driverCommission'];
            $arrParam['selWarehouse']   = $rsHeader[0]['warehousekey'];
            $arrParam['hidEmployeeKey'] = $rsJO[0]['saleskey'];
            $arrParam['hidARStatusKey'] = 1;

            $arrParam['amount'] = $employeeCommission;
            // $totalAP += $driverCommissionAmount;
            $arrayToJs = $APEmployeeCommission->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
               throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

            $this->updateGLCommission($rsHeader, $JOKey);
         }
      }


   }

   function updateGLCommission($rs, $JOKey)
   {
      if (!USE_GL)
         return;

      $disposalJobOrder = new DisposalJobOrder();
      $service          = new Service();

      $coaLink        = new COALink();
      $warehouse      = new Warehouse();
      $generalJournal = new GeneralJournal();
      $employee       = new Employee();
      $rsJO           = $disposalJobOrder->getDataRowById($JOKey);
      $rsService      = $service->getDataRowById($rsJO[0]['servicekey']);
      $rsEmployee     = $employee->getDataRowById($rsJO[0]['saleskey']);


      $employeeCommission = $rsService[0]['firstemployeecommission'];
      $rsTeleSales        = $employee->getDetailCommission($rsJO[0]['saleskey'], $rsJO[0]['servicekey']);
      if (!empty($rsTeleSales) && $rsTeleSales[0]['commission'] > 0) {
         $employeeCommission = $rsTeleSales[0]['commission'];
      }
      $warehousekey = $rs[0]['warehousekey'];

      $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));
      $rsCOA = $coaLink->getCOALink('commissioncost', $warehouse->tableName, $warehousekey);

      $arr                    = array();
      $arr['pkey']            = $generalJournal->getNextKey($generalJournal->tableName);
      $arr['code']            = 'xxxxx';
      $arr['refkey']          = $rs[0]['pkey'];
      $arr['refTableType']    = $rsKey['key'];
      $arr['trDate']          = $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
      $arr['refCode']         = $rs[0]['code'];
      $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

      $temp = -1;

      $temp++;
      $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
      $arr['debit'][$temp]     = $employeeCommission;
      $arr['credit'][$temp]    = 0;

      $coakey = $employee->getAPCommissionCOAKey($rsJO[0]['saleskey'], $warehousekey);

      //akun hutang 
      $temp++;
      $arr['hidCOAKey'][$temp] = $coakey;
      $arr['debit'][$temp]     = 0;
      $arr['credit'][$temp]    = $employeeCommission;
      $arr['trDesc']           = $this->lang['salesCommission'] . ' ' . $rsEmployee[0]['name'] . '. ' . $rsJO[0]['code'];

      $arrayToJs = $generalJournal->addData($arr);
      if (!$arrayToJs[0]['valid'])
         throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
   }

   function validateConfirm($rsHeader)
   {


      $id          = $rsHeader[0]['pkey'];
      $customerkey = $rsHeader[0]['customerkey'];
      $currencykey = $rsHeader[0]['currencykey'];

      $disposalJobOrder = new DisposalJobOrder();
      $termOfPayment    = new TermOfPayment();
      $customCode       = new CustomCode();

      $rsDetail      = $this->getDetailById($id);
      $rsPayment     = $this->getPaymentMethodDetail($id);
      // $rsDownpayment = $this->getDownpaymentDetail($id);

      // kalo invoice void, langsung retur
      $rsCustomCode = $customCode->getDataRowById($rsHeader[0]['customcodekey']);
      $nogl         = ($rsCustomCode[0]['nogl'] == 1) ? true : false;


      $balance          = 0;
      $totalPayment     = 0;
      $totalDownpayment = 0;

      for ($i = 0; $i < count($rsPayment); $i++)
         $totalPayment += $rsPayment[$i]['amount'];


      // for ($i = 0; $i < count($rsDownpayment); $i++)
      //    $totalDownpayment += $rsDownpayment[$i]['amount'];


      $rsTOP  = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
      $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;


      $balance = $totalPayment + $totalDownpayment - $rsHeader[0]['grandtotal'];

      if (!$nogl && $isCash) {
         $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
         if ($balance < ($thresholdDiscount * -1))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
         else if ($balance > $thresholdDiscount)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
      }


      // for ($i = 0; $i < count($rsDownpayment); $i++) {

      //    // validasi DP masi available gk 
      //    if ($rsDownpayment[$i]['downpaymentstatuskey'] <> 2) {
      //       $this->addErrorLog(false, $rsDownpayment[$i]['refcode'] . '. ' . $this->errorMsg['downpayment'][9]);
      //    } else {
      //       if ($customerkey <> $rsDownpayment[$i]['downpaymentcustomerkey'])
      //          $this->addErrorLog(false, $rsDownpayment[$i]['refcode'] . '. ' . $this->errorMsg['downpayment'][6]);

      //       if ($currencykey <> $rsDownpayment[$i]['downpaymentcurrencykey'])
      //          $this->addErrorLog(false, $rsDownpayment[$i]['refcode'] . '. ' . $this->errorMsg['downpayment'][10]);

      //       // validasi nilai DP masi mencukupi gk 
      //       if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'])
      //          $this->addErrorLog(false, $arrDownpaymentCode[$i] . '. ' . $this->errorMsg['downpayment'][8] . ' (' . $this->lang['outstanding'] . ': ' . $this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']['outstanding']) . ')');
      //    }

      // }




   }

   function confirmTrans($rsHeader)
   {

      $termOfPayment    = new TermOfPayment();
      $customCode       = new CustomCode();
      $warehouse        = new Warehouse();
      $disposalJobOrder = new DisposalJobOrder();

      // // kalo invoice void, langsung return aj
      $rsCustomCode = $customCode->getDataRowById($rsHeader[0]['customcodekey']);
      $nogl         = ($rsCustomCode[0]['nogl'] == 1) ? true : false;
      if ($nogl)
         return;


      $rsTOP  = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
      $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;


      // //update ar service
      if (!$isCash) {
         $ar       = new AR();
         $customer = new Customer();

         $topkey = $rsHeader[0]['termofpaymentkey'];
         $rsTOP  = $termOfPayment->getDataRowById($topkey);
         $top    = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

         $rsARKey  = $ar->getTableKeyAndObj($this->tableName);
         $arrParam = array();

         $arrParam['code']            = 'xxxxxx';
         $arrParam['hidCustomerKey']  = $rsHeader[0]['customerkey'];
         $arrParam['hidRefKey']       = $rsHeader[0]['pkey'];
         $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
         $arrParam['hidRefCode']      = $rsHeader[0]['code'];
         $arrParam['hidRefCode2']     = $rsHeader[0]['donumber'];
         $arrParam['hidRefDate']      = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
         $arrParam['hidRefTable']     = $rsARKey['key'];
         $arrParam['amount']          = $rsHeader[0]['grandtotal'] - $rsHeader[0]['totaldownpayment'];
         $arrParam['trDesc']          = $rsHeader[0]['code'];
         $arrParam['trDate']          = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
         $date                        = new DateTime($rsHeader[0]['trdate']);
         $date->add(new DateInterval('P' . $top . 'D'));
         $arrParam['dueDate']          = $date->format('d / m / Y'); // date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
         $arrParam['createdBy']        = 0;
         $arrParam['overwriteGL']      = 1;
         $arrParam['islinked']         = 1;
         $arrParam['selARType']        = AR_TYPE['serviceOrder'];
         $arrParam['selWarehouse']     = $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();
         $arrParam['tax23value']       = $rsHeader[0]['tax23value'];
         $arrParam['tax23outstanding'] = $rsHeader[0]['tax23value'];

         $returnVal = $ar->addData($arrParam, false);

         $rsHeader[0]['arKey']  = $returnVal[0]['data']['pkey'];
         $rsHeader[0]['arCode'] = $returnVal[0]['data']['code'];

      }

      // update invoicekey di SPK
      //cari berapa step utk jenis JO nya  

      // $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

      // $this->addPartialInvoice($rsHeader,$rsDetail);

      //update jurnal umum  
      $this->updateGL($rsHeader, $arrShareProfit);

   }

   function addPartialInvoice($rsHeader, $rsDetail)
   {

      $disposalJobOrder = new DisposalJobOrder();

      foreach ($rsDetail as $invoiceDetail) {
         if (empty($invoiceDetail['salesorderkey']))
            continue;

         if ($rsHeader[0]['isdownpayment']) {
            $amount = $invoiceDetail['amount'];
         } else {
            // $rsDP = $disposalJobOrder->getTotalInvoicedAndOutstanding($invoiceDetail['salesorderkey'],$rsHeader[0]['customcodekey']);

            // $amount = ($invoiceDetail['salesordergrandtotal']  >= $rsDP['outstanding'] ) ? $rsDP['outstanding'] : $invoiceDetail['salesordergrandtotal'];
            // $amount *= -1;

            // //update lock status
            // foreach($rsDP['rsTotalnvoiced'] as $row){
            //     $sql = 'update 
            //                     '.$this->tablePartialInvoice.'  
            //             set 
            //                     reflinkinvoiceheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).', 
            //                     reflinkinvoicedetailkey = '.$this->oDbCon->paramString($invoiceDetail['pkey']).' 
            //             where pkey = ' .$row['pkey'].' and reflinkinvoiceheaderkey = 0' ;
            //     $this->oDbCon->execute($sql);
            // }

         }

         if ($amount == 0)
            continue;

         $sql = 'insert into ' . $this->tablePartialInvoice . ' 
                       (refkey,refinvoicekey,customcodekey,amount) 
                    values(
                            ' . $this->oDbCon->paramString($invoiceDetail['salesorderkey']) . ',
                            ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ',
                            ' . $this->oDbCon->paramString($rsHeader[0]['customcodekey']) . ',
                            ' . $this->oDbCon->paramString($amount) . ' 
                        )
                    ';
         $this->oDbCon->execute($sql);
      }
   }

   function deletePartialInvoice($id)
   {

      //update lock status 
      $sql = 'update 
                            ' . $this->tablePartialInvoice . '  
                    set 
                            reflinkinvoiceheaderkey = 0, 
                            reflinkinvoicedetailkey = 0
                    where reflinkinvoiceheaderkey = ' . $this->oDbCon->paramString($id);
      $this->oDbCon->execute($sql);

      $sql = 'delete from ' . $this->tablePartialInvoice . ' where refinvoicekey = ' . $this->oDbCon->paramString($id);
      $this->oDbCon->execute($sql);
   }

   function validateCancel($rsHeader, $autoChangeStatus = false)
   {
      $id = $rsHeader[0]['pkey'];

      $ar      = new AR();
      $rsARKey = $ar->getTableKeyAndObj($this->tableName);

      //cek ad AR Service terbayar
      $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsARKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and (' . $ar->tableName . '.statuskey = 2 or ' . $ar->tableName . '.statuskey = 3)');
      if (!empty($rsAR))
         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ar'][2], true);

   }

   function cancelTrans($rsHeader, $copy)
   {

      $disposalJobOrder = new DisposalJobOrder();
      $ap               = new AP();

      $id       = $rsHeader[0]['pkey'];
      $rsDetail = $this->getDetailById($id);

      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));

      if ($rsHeader[0]['statuskey'] == 1)
         return;

      $ar       = new AR();
      $rsObjKey = $this->getTableKeyAndObj($this->tableName);

      $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsObjKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and ' . $ar->tableName . '.statuskey = 1');
      for ($i = 0; $i < count($rsAR); $i++) {
         $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'], 4, '', false, true);
         if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $arrayToJs[0]['message']);
      }

      if ($copy)
         $this->copyDataOnCancel($id);

      $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);

   }

   function validateBackConfirm($rsHeader)
   {
      $pkey = $rsHeader[0]['pkey'];
   }

   // function getInvoiceByMonth($startPeriod, $endPeriod)
   // {
   //    $sql = 'select 
   //                  month(trdate) as month,  
   //                  DATE_FORMAT(trdate, \'%b\')  as monthname, 
   //                  year(trdate) as year, 
   //                  sum(grandtotal) as total
   //              from 
   //                  ' . $this->tableName . '
   //              where (statuskey = 2 or statuskey = 3) and trdate between \'' . date("Y-m-d", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\')';

   //    $sql .= $this->getWarehouseCriteria();
   //    $sql .= ' group by year(trdate),month(trdate)';

   //    return $this->oDbCon->doQuery($sql);
   // }

   // function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5)
   // {
   //    // Sales Amount

   //    $sql = 'select 
   //                sum(' . $this->tableName . '.beforetaxtotal) as amount, 
   //                ' . $this->tableCustomer . '.name  as customername
   //              from 
   //                  ' . $this->tableName . ', 
   //                  ' . $this->tableCustomer . ' 
   //              where 
   //                  (' . $this->tableName . '.statuskey = 2 or ' . $this->tableName . '.statuskey = 3) and 
   //                   ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
   //                   trdate between \'' . date("Y-m-01 00:00", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\')  
   //               group by 
   //                  ' . $groupBy . '
   //               order by amount desc limit ' . $limit;

   //    return $this->oDbCon->doQuery($sql);
   // }

   // function generateDefaultQueryForAutoComplete($returnField)
   // {
   //    $sql = 'select
   //              ' . $returnField['key'] . ',
   //              ' . $returnField['value'] . ' as value,
   //              ' . $this->tableName . '.code,
   //              ' . $this->tableName . '.trdate,
   //              ' . $this->tableName . '.grandtotal
   //          from 
   //              ' . $this->tableName . ',
   //              ' . $this->tableStatus . '  
   //          where  		
   //              ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
   //      ';

   //    $sql .= $this->getCompanyCriteria();
   //    return $sql;

   // }

   function getPaymentDetail($invoicekey)
   {

      $objServiceOrder = $this->getSalesOrderObj();

      // diskon gk perlu dimasukan, jika memang perlu dibuat field terpisah saja
      $sql = 'select 
                     ' . $this->tableARPaymentHeader . '.pkey as paymentkey, 
                     ' . $this->tableAR . '.refheaderkey as invoicekey, 
                     ' . $this->tableARPaymentHeader . '.code as paymentcode, 
                     ' . $this->tableARPaymentHeader . '.trdate as paymentdate, 
                     ' . $this->tableARPaymentDetail . '.amount, 
                     ' . $this->tableARPaymentDetail . '.taxamount,  
                     ' . $this->tableARPaymentDetail . '.discount 
                from 
                    ' . $this->tableARPaymentHeader . ',
                    ' . $this->tableARPaymentDetail . ',
                    ' . $this->tableAR . '
                where   
                    ' . $this->tableARPaymentDetail . '.refkey = ' . $this->tableARPaymentHeader . '.pkey  and
                    ' . $this->tableARPaymentDetail . '.arkey = ' . $this->tableAR . '.pkey  and
                    ' . $this->tableAR . '.refheaderkey in (' . $this->oDbCon->paramString($invoicekey, ',') . ') and
                    ' . $this->tableARPaymentHeader . '.statuskey in (2,3) 
                ';

      return $this->oDbCon->doQuery($sql);
   }



   function afterUpdateData($arrParam, $action)
   {
   }


   function afterAddDataOnCopy($pkey, $oldkey)
   {
      $sql = 'update ' . $this->tableName . ' set receiptdt = \'' . DEFAULT_EMPTY_DATE . '\' where pkey  = ' . $this->oDbCon->paramString($pkey);
      $this->oDbCon->execute($sql);
   }


}
?>
