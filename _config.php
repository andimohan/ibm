<?php
 
	session_start();
	date_default_timezone_set('Asia/Jakarta');
 
	ini_set("zlib.output_compression", "On");
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
      
    define('CLASS_VERSION', 'class-2.12');
	define('ADMIN_CSS_VERSION', 'adminStyle-3.79.min.css');
	define('ADMIN_JS_VERSION', 'formJS-1.317.min.js');
	define('REPORT_JS_VERSION', 'report-2.12.min.js' );

	$WEB_FOLDER = '';
    $IS_HISTORY = false;

    $PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 
    define ('PROTOCOL',$PROTOCOL);

	$HTTP_HOST =  PROTOCOL . '://' .$_SERVER ['HTTP_HOST'] ;
	if(substr($HTTP_HOST,-1) <> "/") {
		$HTTP_HOST  .= '/';
	}

	$HTTP_HOST = $HTTP_HOST.$WEB_FOLDER; 
    define ('HTTP_HOST',$HTTP_HOST);
    define ('REQUEST_URI',$_SERVER['REQUEST_URI']);
 
	$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'] ;
	if(substr($DOC_ROOT,-1) <> "/") {
		$DOC_ROOT .= '/';
	} 
	
	$DOC_ROOT = $DOC_ROOT.$WEB_FOLDER;  
    define('DOC_ROOT',$DOC_ROOT);
   
    $patterns = array('www.',':');
    $replacements = array('','-');
    $DOMAIN_NAME = str_replace($patterns, $replacements, $_SERVER['HTTP_HOST']); 
    

    if(file_exists(DOC_ROOT.'_domain.php')) require_once '_domain.php';  

    // FOR DEVELOPMENT 
    $IS_DEVELOPMENT = false;
    if(file_exists(DOC_ROOT.'_development.php'))
        include '_development.php';  
  
    //echo $_SERVER ['HTTP_HOST'];
    

    // DEFINE  
    define('IS_DEVELOPMENT',$IS_DEVELOPMENT);
    define('DOMAIN_NAME',$DOMAIN_NAME); 
    //define('DOMAIN_COOKIES','_mnv'); // gk boleh ad titik, jd gk bisa pake nama domain
    define('PERSONALIZED_DOC_PATH',DOC_ROOT.'personalized/'.DOMAIN_NAME.'/');
    define('PERSONALIZED_URL_PATH','/personalized/'.DOMAIN_NAME.'/');
    define('DEBUG',false); 
  
    $apiVersion = 'v3/';
    if(in_array(DOMAIN_NAME,array('okl.wintera.co.id','eagle.wintera.co.id','trioeaglelogistic.wintera.co.id','marvel.wintera.co.id','airtel.wintera.co.id'))) 
        $apiVersion = 'v2/';
  
    define('API_URL', (IS_DEVELOPMENT) ? 'https://minerva.local/api/'.$apiVersion : HTTP_HOST.'api/'.$apiVersion );
 
    define('DOMAIN_FOLDER',strtolower(DOMAIN_NAME).'/');
    define('UPLOAD_TEMP_DOC', DOC_ROOT. '../_temp/' .DOMAIN_FOLDER);
    define('UPLOAD_TEMP_DOC_SHORT',  '/../_temp/' .DOMAIN_FOLDER); // untuk phpThumb, agar tidak terlihat path full.  
    define('UPLOAD_TEMP_URL', HTTP_HOST. '../_temp/' .DOMAIN_FOLDER); 
    define('DEFAULT_DOC_UPLOAD_PATH', DOC_ROOT. '../_upload/' .DOMAIN_FOLDER);
    define('DEFAULT_URL_UPLOAD_PATH', HTTP_HOST. '../_upload/' .DOMAIN_FOLDER); // harusnya gk bisa diakses karena naik 1 tingkat
    define('PHPTHUMB_URL_PATH', '/../_upload/' .DOMAIN_FOLDER);

    define('USER_SYSTEM',array('code' => '00000', 'name' => 'SYSTEM'));      


    // INI TIDAK/BUKAN MENGGAMBARKAN HAK AKSES STATUS, karena tidak ad akses update
    define('INSERT_DATA', 11);
    define('UPDATE_DATA', 12);
    define('DELETE_DATA', 13); 
    define('PRINT_DATA', 14);
    define('CHANGE_STATUS_DATA', 1);
    define('DEFAULT_EMPTY_DATE', '01 / 01 / 1970');

    define('ARR_DB_EMPTY_DATE', array('0000-00-00','0000-00-00 00:00:00','1970-01-01', '1970-01-01 00:00:00'));

    define('DECIMAL_SEPARATOR', '.'); 
 
    $arrTransactionStatus = array();     
    $arrTransactionStatus['menunggu'] = 1; 
    $arrTransactionStatus['konfirmasi'] = 2; 
    $arrTransactionStatus['selesai'] = 3; 
    $arrTransactionStatus['batal'] = 4; 
    define('TRANSACTION_STATUS',$arrTransactionStatus);   
  
    $apStatus = array();     
    $apStatus['open'] = 1; 
    $apStatus['partial'] = 2; 
    $apStatus['lunas'] = 3; 
    $apStatus['batal'] = 4; 
    define('AP_STATUS',$apStatus);   

    $financialReport = array();
    $financialReport['balanceSheet'] = 1;
    $financialReport['incomeStatement'] = 2; 
    define('FINANCIAL_REPORT',$financialReport);   
 
    $voucherCategory = array();
    $voucherCategory['registration'] = 1;
    $voucherCategory['sales'] = 2; 
    $voucherCategory['shipment'] = 3; 
    define('VOUCHER_CATEGORY',$voucherCategory);   
 
    $voucherType = array();
    $voucherType['regular'] = 1; // tuker point
    $voucherType['collectible'] = 2; // voucher umum, tp lupa kalo yg bisa diambil hanya sekali yg mana
    $voucherType['claim'] = 3; // claim berdasarkan kode voucher yang didapat
    define('VOUCHER_TYPE',$voucherType);   

    $customerType = array();
    $customerType['reseller'] = 1;
    $customerType['enduser'] = 2; 
    define('CUSTOMER_TYPE',$customerType);   

	$arrInvoiceType = array(); 
	$arrInvoiceType[1] = 'EMKL Order';
	$arrInvoiceType[2] = 'Biaya';
	$arrInvoiceType[3] = 'Faktur Penjualan'; 
    define('EMKL_INVOICE_TYPE',$arrInvoiceType);


    define('LOC_TYPE',array('origin' => 1, 'destination' => 2, 'freight' => 3));
    define('RATE_TYPE',array('rate' => 1, 'minimum' => 2, 'normal' => 3));
    define('NO_FREIGHT_TYPE',array(LOC_TYPE['origin'],LOC_TYPE['destination']));

	$arrTaxType = array(); 
	$arrTaxType['PPN'] = 1;
	$arrTaxType['PPH'] = 2;
    define('TAX_TYPE',$arrTaxType);

	$arrWitholdingCode = array(); 
	$arrWitholdingCode['art23'] = 'art23'; 
    define('WITHHOLDING_TAX',$arrWitholdingCode);

    // ITEM TYPE
    define('ITEM',1); 
    define('TRUCKING_SERVICE',2); 
    define('SERVICE',3); // service dan paket barang ??
    define('ITEM_DEPOT',4);

    // logistics => Bhuman
    define('COMPANY_TYPE', array('retail' => 1,
								 'trucking' => 2,
								 'workshop' => 3,
								 'tpamedical' => 4,
								 'forwarding' => 5,
								 'hospital' => 6,
								 'property' => 7,
								 'logistics' => 8, // bhuman,
								 'jewelry' => 9 // bhuman
								)); 
    
    // jenis2 piutang
    define('AP_TYPE', array('itemPurchase' => 1, 
                            'serviceOutsource' => 2, // bisa utk mobil luar atau bagi hasil ?
                            'driverCommission' => 3, // ritase
                            'salesCommission' => 4, 
                            'carServiceMaintenance' => 5, // DN utk pengurangan bagi hasil, 
                            'otherCost' => 6, // DN utk pengurangan bagi hasil, 
                            'downPayment' => 7, // DN utk pengurangan bagi hasil
                            'debitNote' => 8 // DN utk pengurangan bagi hasil
                            ));
    
    // jenis2 hutang
    define('AR_TYPE', array('salesOrder' => 1, 'serviceOrder' => 2, 'creditNote' => 3, 'downPayment' => 4, 'reimburse' => 5)); 
    define('AR_EMPLOYEE_TYPE', array('personalLoan' => 1, 'cashBankRealization' => 2)); 
    
    define('INVOICE_TYPE', array('sales' => 1, 'reimbursement' => 2)); 
    define('SYSTEM_UNIT', array('qty' => 1, 'weight' => 2,'length' => 3)); 
     
    $arrSupplier = array();     
    $arrSupplier[1] = 'Trucking'; 
    $arrSupplier[2] = 'Ocean Freight'; 
    $arrSupplier[3] = 'Others'; 
    define('EMKLSUPPLIERCATEGORY',$arrSupplier);


    $arrInputType = array();
    $arrInputType['text'] = 1; 
    $arrInputType['number'] = 2; 
    $arrInputType['textarea'] = 3; 
    $arrInputType['raw'] = 4; 
    $arrInputType['editor'] = 5; 
    $arrInputType['file'] = 6; 
    $arrInputType['select'] = 7; 
    $arrInputType['autocompletejs'] = 8; 
    $arrInputType['autocomplete'] = 9; 
    $arrInputType['date'] = 10; 
    $arrInputType['checkbox'] = 11; 
    $arrInputType['yesno'] = 12; 
    define('INPUT_TYPE',$arrInputType);   

  
    // SUPPLIER TYPE
/*    $arrSupplierType = array();
    $arrSupplierType['supplier'] = 1;
    $arrSupplierType['carrier'] = 2;
    $arrSupplierType['agent'] = 3;
    define('SUPPLIER_TYPE',$arrSupplierType);   */

 
    define('VEHICLE_PARTNERSHIP_TYPE', array('oncall' => 1, 'contract' => 2));
 
    define('TIME_STAMP_TYPE', array('trdate' => 1, 'confirmeddate' => 2));


    $arrEMKL = array();
    $arrEMKL['jobType']['import'] = 1;
    $arrEMKL['jobType']['export'] = 2; 
	$arrEMKL['jobType']['domestic'] = 3;
	$arrEMKL['jobType']['warehouse'] = 4;
	$arrEMKL['jobType']['trucking'] = 5;
 

    $arrEMKL['volume']['cbm'] = 1;
    $arrEMKL['volume']['kg'] = 2; 

    $arrEMKL['container']['fcl'] = 1;
    $arrEMKL['container']['lcl'] = 2;
    $arrEMKL['container']['trucking'] = 3;
    $arrEMKL['container']['document'] = 4;
    $arrEMKL['container']['lclnc'] = 5;

    // sementara khusus CIF, kedepan harus tambahin jenis FCL / LCL saja
    $arrEMKL['container']['freightcustomfcl'] = 6;
    $arrEMKL['container']['freightcustomlcl'] = 7;
    $arrEMKL['container']['customfcl'] = 8;
    $arrEMKL['container']['customlcl'] = 9;
    $arrEMKL['container']['warehouse'] = 10;

	$arrEMKL['shipping']['sea'] = 1;
    $arrEMKL['shipping']['air'] = 2;
    
	$arrEMKL['emklType']['fcl'] = 1;
    $arrEMKL['emklType']['lcl'] = 2;
    $arrEMKL['emklType']['trucking'] = 3;
    $arrEMKL['emklType']['document'] = 4;
    $arrEMKL['emklType']['lclnc'] = 5;

    define('EMKL',$arrEMKL);   

	define('FCL_CONTAINER_TYPE', array(EMKL['container']['fcl'], EMKL['container']['trucking'], EMKL['container']['airfreight'], EMKL['container']['ftl'], EMKL['container']['document'], EMKL['container']['fclclearance'],EMKL['container']['fcltrucking']));   
    define('LCL_CONTAINER_TYPE', array(EMKL['container']['lcl'], EMKL['container']['lclnc'], EMKL['container']['lclclearance'], EMKL['container']['ltl'], EMKL['container']['lcltrucking']));   


    define('CURRENCY',array('idr' => 1));   
    define('CLAIM_TYPE',array('repair' => 1, 'replace' => 2, 'upgrade' => 3, 'CN' => 4, 'void' => 5));   
    define('UNIT',array('kg' => 2, 'gram' => 3));
      
    define('CURRENCY_PREFERENCE',array('auto' => 1, 'idr' => 2));   

    define('ARAP_BALANCE_ROUNDING', array('idr' => 1, 'currency' => 0.01));

    $arrAPIAction = array();
    $arrAPIAction['update'] = 'update';
    $arrAPIAction['get'] = 'get';
    $arrAPIAction['delete'] = 'delete';
    define('API_ACTION',$arrAPIAction);   
  
    $importTemplate = array();
    $importTemplate['item'] = 'Template Import - Barang';
    $importTemplate['itemIn'] = 'Template Import - Pemasukaan Barang';
    $importTemplate['itemOut'] = 'Template Import - Pengeluaran Barang';
    $importTemplate['customer'] = 'Template Import - Pelanggan';
    $importTemplate['EMKLPurchaseOrderExport'] = 'Template Import - Purchase Order Export';
    $importTemplate['EMKLPurchaseOrderImport'] = 'Template Import - Purchase Order Import';
    $importTemplate['EMKLJobOrderExport'] = 'Template Import - Job Order Export';
    $importTemplate['salesOrderSubscription'] = 'Template Import - Sales Order Subscription';
    $importTemplate['cashBankIn'] = 'Template Import - Kas Bank Masuk';
    $importTemplate['cashOut'] = 'Template Import - Kas Keluar'; 
    $importTemplate['city'] = 'Template Import - Kota';
    $importTemplate['cityCategory'] = 'Template Import - Kategori Kota'; 
    $importTemplate['services'] = 'Template Import - Layanan';
    $importTemplate['supplier'] = 'Template Import - Pemasok';  
    $importTemplate['consignee'] = 'Template Import - Consignee';  
    $importTemplate['ap'] = 'Template Import - Hutang (AP)';
    $importTemplate['ar'] = 'Template Import - Piutang (AR)';
    $importTemplate['employee'] = 'Template Import - Karyawan'; // problem kalo gk pake class baru
    $importTemplate['location'] = 'Template Import - Lokasi'; 
    $importTemplate['car'] = 'Template Import - Mobil';
    $importTemplate['brand'] = 'Template Import - Merk';
    $importTemplate['itemCategory'] = 'Template Import - Kategori Barang';
    $importTemplate['asset'] = 'Template Import - Asset';
    $importTemplate['buildingUnit'] = 'Template Import - Unit Bangunan';
    $importTemplate['salesOrder'] = 'Template Import - Order Penjualan';
    $importTemplate['coa'] = 'Template Import - COA';
    $importTemplate['arPayment'] = 'Template Import - Pembayaran Piutang'; 
    $importTemplate['purchaseOrder'] = 'Template Import - Order Pembelian'; 
    $importTemplate['itemAdjustment'] = 'Template Import - Penyesuaian Stok Barang'; 
	$importTemplate['truckingServiceOrder'] = 'Template Import - Job Order'; 
	$importTemplate['continent'] = 'Template Import - Benua';
	$importTemplate['country'] = 'Template Import - Negara';
	$importTemplate['city'] = 'Template Import - Kota';
	$importTemplate['port'] = 'Template Import - Port';
	$importTemplate['vessel'] = 'Template Import - Vessel';
	$importTemplate['commodity'] = 'Template Import - Commodity';
	$importTemplate['itemUnits'] = 'Template Import - Item Unit';
    $importTemplate['generalJournal'] = 'Template Import - Jurnal Umum';
 	$importTemplate['carServiceMaintenance'] = 'Template Import - Perawatan Mobil';
	$importTemplate['costRatePraja'] = 'Template Import - Tarif Biaya';
	$importTemplate['purchasePricing'] = 'Template Import - Purchase Pricing';
	$importTemplate['salesPricing'] = 'Template Import - Sales Pricing';


    define('IMPORT_TEMPLATE',$importTemplate);
 
    $defaultCost = array();
    $defaultCost['outsourceDownpayment'] = '1';
    define('DEFAULT_COST',$defaultCost);   

    $arrCOA = array();
    $arrCOA['type']['assets'] = 1;
    $arrCOA['type']['liability'] = 2;
    $arrCOA['type']['equity'] = 3;
    $arrCOA['type']['revenue'] = 4;
    $arrCOA['type']['cogs'] = 5;
    $arrCOA['type']['expense'] = 6; 
    define('COA',$arrCOA);   
    

    $arrMP = array();     
    $arrMP['lazada'] = 1; 
    $arrMP['shopee'] = 2; 
    $arrMP['tokopedia'] = 3; 
    define('MARKETPLACE',$arrMP);   

    $cronRepeat = array();
    $cronRepeat['daily'] = '1';
    $cronRepeat['monthly'] = '2';
    $cronRepeat['annually'] = '3';
    define('CRON_REPEAT',$cronRepeat);   

    $nettingPayment = array();
    array_push($nettingPayment,array( 'pkey'=> '-1', 'name' => 'Netting')); 
    define('NETTING_PAYMENT', $nettingPayment);

    define('COMMISSION_PAYMENT', array(array('pkey' => '-2', 'name' => 'COMMISSION PAYMENT')));


    $cashAdv = array();
    array_push($cashAdv,array( 'pkey'=> '-1', 'name' => 'CASH ADV.')); 
    define('CASH_ADVANCE', $cashAdv);

	define('LOGIN_TYPE',array('customer' => 1, 'finance' => 8001, 'operational' => 8002, 'purchasing' => 8003));
	define('RECIPIENT_TYPE',array('customer' => 1,'supplier' => 2,'employee' => 3));   

    $arrCashBankType = array();     
    $arrCashBankType['temporary'] = 1; 
    $arrCashBankType['isnottemporary'] = 2; 
    define('CASH_BANK_TYPE',$arrCashBankType);

	define('TIN_TYPE',array('TIN' => 1,
                           'NIK' => 2,
                           'Passport' => 3,
                           'Others' => 4, 
                        )); 


    $vatOutType = array();
    $vatOutType['ASSIGN'] = 1;
    //$vatOutType['REVOKE'] = 2; 
    //$vatOutType['REVISION'] = 3; 
    define('VAT_OUT_TYPE',$vatOutType);   

    $arrSparePartType = array();
    $arrSparePartType['tire'] = 1;
    $arrSparePartType['sidemirror'] = 2;
    $arrSparePartType['battery'] = 3;
    $arrSparePartType['filter'] = 4;
    define('SPARE_PART_TYPE', $arrSparePartType);

    define('DN_TYPE',array('ap' => 1,
                          'cash' => 2,
                          'debitMemo'=> 3
                        ));

    define('CN_TYPE',array('ar' => 1,
                           'withoutAR'=> 2,
                        ));

	$path = DOC_ROOT.'log/'; 
	if (!file_exists($path))  mkdir($path, 0755, true);   
	ini_set('error_log', $path.'['.date('d-m-Y') .'] - '.md5(DOC_ROOT).'.txt' ); 


    // REDIRECT ON CUSTOM .....
//    function getPersonalizedFiles($fileName,$ext=''){     
//        global $class;
//        
//        $ext = (empty($ext)) ? 'php' : ''; 
//        $ext = (!empty($ext)) ? '.'.$ext : '';
//        
//        $path = 'admin/'.$fileName.$ext;
//        $docPersonalizedFile = PERSONALIZED_DOC_PATH.$path;   
//        $urlPersonalizedFile = PERSONALIZED_URL_PATH.$path;
//        
//        return (is_file($docPersonalizedFile)) ? $urlPersonalizedFile : $fileName; 
//    }
 
    $testVoucher = (in_array(DOMAIN_NAME, array('wintera.co.id','amt.wintera.co.id' ,'logol.wintera.co.id' , 'yellowegg.wintera.co.id' , 'thomastrans.wintera.co.id' ))) ? true : false; 
    define('TEST_VOUCHER',$testVoucher);  

    define ('TEMP_TABLE_S3' , array('item','trucking_service_order_invoice_header', 'trucking_service_order_header',
                                    'ap_payment_header','ar_payment_header', 'invoice_tax', 'trucking_service_work_order',
                                    'emkl_job_order_header', 'trucking_cost_cash_out_header', 'cash_out_header',
                                    'customer','disposal_contract', 'disposal_work_order_header',
                                    'ap_payable_23_payment_header','general_journal_header','cash_bank_transfer_header',
								    'sales_order_header','emkl_purchase_order_header','car_service_maintenance_header'));

	//define ('TEST_IP',array('180.252.120.28'));

?>