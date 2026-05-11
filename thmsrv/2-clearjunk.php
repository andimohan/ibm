<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';

if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;


//SLEEP
// [done] DATA AP EMPLOYEE COMISSION MASIH PAKE SO
//UPDATE NO JO DI
//    [done] AP Komisi
//    [done] CAR TURNOVER
//    [done] INVOICE
// [done] CEK AD GK CREATEDON <  2021 disetiap table

// AKSES CONSIGNEE harus dibuka
// [done] HAPUS SEMUA AR AP < 2021 yg tdk ad di payment

    
$sql = array();

//array_push($sql, "delete from security_access where userkey not in (252)"); 
//array_push($sql, "delete from security_object where modulecode not in ('SecurityPrivileges','Employee','Consignee', 'Item','COGS','Banner','CashIn','City','CashOut','CashBankTransfer','Setting','PaymentConfirmation','CityCategory','ItemUnit','Warehouse','Supplier','TermOfPayment','Customer','PurchaseOrder','CurrencyRate','Currency','PaymentMethod','ItemIn','ItemOut','WarehouseTransfer','Brand','AutoCode','ChartOfAccount','AR','AP','APPayment','ARPayment','GeneralJournal','ReportGeneralJournal','ReportIncomeStatement','ItemAdjustment','BillingStatement','COALink','ReportGeneralLedger','RoleTemplate','CustomerCategory','TruckingService','Car','Terminal','Chassis','ChassisCategory','TruckingSellingRate','ServiceCategory','TruckingServiceOrderCategory','TruckingServiceOrder','TruckingServiceWorkOrder','TruckingServiceWorkOrderCost','overwriteContract','CarCategory','SellingPrice','TruckingServiceOrderInvoice','CostRate','TruckingJob','TruckingCostCashOut','location','CustomerDownpayment','CarSeries','BillOfMaterials','customCode','TruckingPurchase','TruckingCost','Service','SupplierDownpayment','APEmployeeCommission','APEmployeeCommissionPayment','CostCashOut','TimeUnit','RevenueCashIn','ReportTrialBalance','AutoReverseGL','APEmployee','CancelReason','APCommission','APCommissionPayment','SupplierDownpaymentSettlement','updateItemSellingPrice','TruckingPurchaseRefund','SupplierCategory')"); 
//array_push($sql, "delete from user_security_object where security_object_key not in (select pkey from security_object)"); 
//array_push($sql, "delete from security_access where objectkey not in (select pkey from security_object)"); 

array_push($sql, "update employee set password = 'cdf4a007e2b02a0c49fc9b7ccfbb8a10c644f635e1765dcf2a7ab794ddc7edac' where pkey = 1");

// invoice receipt
array_push($sql, "insert into user_security_object (security_object_key,statuskey) values (215,1)");

array_push($sql, "insert into user_security_object (security_object_key,statuskey) values (215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,2)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,3)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,4)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,10)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,11)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (1,215,12)");


array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,2)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,3)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,4)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,10)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,11)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (3,215,12)");

array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,1)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,2)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,3)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,4)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,10)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,11)");
array_push($sql, "insert into security_access (userkey,objectkey,statuskey) values (131,215,12)");


$regex = '\\b\\w*SO0\\w*\\b';
// '\\b\\w*\\w*\\b'
array_push($sql, "UPDATE trucking_cost_cash_out_header SET jobdescription = REGEXP_REPLACE(jobdescription, '".$regex."' , '')");
array_push($sql, "UPDATE general_journal_header SET trdesc = REGEXP_REPLACE(trdesc,'".$regex."' , '')");
//array_push($sql, "UPDATE ap_employee_commission SET refcode2 = REGEXP_REPLACE(trdesc,'".$regex."' , '')");
array_push($sql, "UPDATE ap_employee_commission SET refcode2 = ''");



// ud dihapus dibawah
//array_push($sql, "UPDATE car_turnover SET refcode2 = ''");

array_push($sql, "update general_journal_detail set coakey = 21 where coakey in (select pkey from chart_of_account where code in ('2.1.1.01','2.1.1.02','2.1.1.03','2.1.1.04','2.1.1.05','2.1.3'))");
array_push($sql, "update cash_out_header, cash_out_detail  set cash_out_detail.coakey = 21  where  cash_out_header.pkey = cash_out_detail.refkey and cash_out_detail.coakey not in (select pkey from chart_of_account)");
array_push($sql, "update coa_link set coakey = 21 where coakey in (select pkey from chart_of_account where code in ('2.1.1.01','2.1.1.02','2.1.1.03','2.1.1.04','2.1.1.05','2.1.3'))");
 

// gabungin coa hutang, open semua closingan dari awal
array_push($sql, "delete from chart_of_account where code in ('2.1.1.01','2.1.1.02','2.1.1.03','2.1.1.04','2.1.1.05','2.1.3')");
array_push($sql, "update chart_of_account set isleaf = 1 where pkey = 21");
array_push($sql, "delete from chart_of_account_active_period where pkey <> 1");
array_push($sql, "update chart_of_account_active_period set isclosed =  0");
array_push($sql, "truncate chart_of_account_amount"); 
//array_push($sql, "INSERT INTO `security_access` (`userkey`, `objectkey`, `statuskey`) VALUES ( 252, 107, 1), (252, 107, 2),	( 252, 107, 3),	( 252, 107, 10),	(252, 107, 11),	( 252, 107, 12)");



// update ar ap hanya outstanding saja 
// harus diatas
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR00762'");
array_push($sql, "update ar set amount ='200000', amountidr = '200000' where code = 'AR00802'");
array_push($sql, "update ar set amount ='650000', amountidr = '650000' where code = 'AR00813'");
array_push($sql, "update ar set amount ='200000', amountidr = '200000' where code = 'AR00853'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR00899'");
array_push($sql, "update ar set amount ='15000000', amountidr = '15000000' where code = 'AR00900'");
array_push($sql, "update ar set amount ='16500000', amountidr = '16500000' where code = 'AR00901'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR00961'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR01076'");
array_push($sql, "update ar set amount ='3649993', amountidr = '3649993' where code = 'AR01170'");
array_push($sql, "update ar set amount ='385000', amountidr = '385000' where code = 'AR01189'");
array_push($sql, "update ar set amount ='65000000', amountidr = '65000000' where code = 'AR01616'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR02666'");
array_push($sql, "update ar set amount ='4500000', amountidr = '4500000' where code = 'AR02714'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR02716'");
array_push($sql, "update ar set amount ='500000', amountidr = '500000' where code = 'AR03141'");
array_push($sql, "update ar set amount ='5500000', amountidr = '5500000' where code = 'AR03203'");
array_push($sql, "update ar set amount ='1439998', amountidr = '1439998' where code = 'AR03204'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03211'");
array_push($sql, "update ar set amount ='385000', amountidr = '385000' where code = 'AR03212'");
array_push($sql, "update ar set amount ='16250000', amountidr = '16250000' where code = 'AR03227'");
array_push($sql, "update ar set amount ='6567000', amountidr = '6567000' where code = 'AR03228'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03229'");
array_push($sql, "update ar set amount ='615000', amountidr = '615000' where code = 'AR03230'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03231'");
array_push($sql, "update ar set amount ='385000', amountidr = '385000' where code = 'AR03232'");
array_push($sql, "update ar set amount ='3750000', amountidr = '3750000' where code = 'AR03233'");
array_push($sql, "update ar set amount ='2590500', amountidr = '2590500' where code = 'AR03234'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03236'");
array_push($sql, "update ar set amount ='599500', amountidr = '599500' where code = 'AR03237'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR03246'");
array_push($sql, "update ar set amount ='891000', amountidr = '891000' where code = 'AR03247'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR03279'");
array_push($sql, "update ar set amount ='2028000', amountidr = '2028000' where code = 'AR03282'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03283'");
array_push($sql, "update ar set amount ='1452000', amountidr = '1452000' where code = 'AR03284'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR03296'");
array_push($sql, "update ar set amount ='1199999', amountidr = '1199999' where code = 'AR03297'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR03314'");
array_push($sql, "update ar set amount ='939999', amountidr = '939999' where code = 'AR03315'");
array_push($sql, "update ar set amount ='9000000', amountidr = '9000000' where code = 'AR03324'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03345'");
array_push($sql, "update ar set amount ='719999', amountidr = '719999' where code = 'AR03346'");
array_push($sql, "update ar set amount ='6250000', amountidr = '6250000' where code = 'AR03347'");
array_push($sql, "update ar set amount ='4672965', amountidr = '4672965' where code = 'AR03348'");
array_push($sql, "update ar set amount ='5950000', amountidr = '5950000' where code = 'AR03367'");
array_push($sql, "update ar set amount ='3789500', amountidr = '3789500' where code = 'AR03368'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR03374'");
array_push($sql, "update ar set amount ='913000', amountidr = '913000' where code = 'AR03375'");
array_push($sql, "update ar set amount ='18750000', amountidr = '18750000' where code = 'AR03389'");
array_push($sql, "update ar set amount ='7661500', amountidr = '7661500' where code = 'AR03390'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR03402'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03403'");
array_push($sql, "update ar set amount ='564999', amountidr = '564999' where code = 'AR03404'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03490'");
array_push($sql, "update ar set amount ='1546000', amountidr = '1546000' where code = 'AR03493'");
array_push($sql, "update ar set amount ='1820000', amountidr = '1820000' where code = 'AR03513'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR03521'");
array_push($sql, "update ar set amount ='1246500', amountidr = '1246500' where code = 'AR03523'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03524'");
array_push($sql, "update ar set amount ='1727000', amountidr = '1727000' where code = 'AR03525'");
array_push($sql, "update ar set amount ='10500000', amountidr = '10500000' where code = 'AR03526'");
array_push($sql, "update ar set amount ='4378000', amountidr = '4378000' where code = 'AR03527'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR03528'");
array_push($sql, "update ar set amount ='2879996', amountidr = '2879996' where code = 'AR03529'");
array_push($sql, "update ar set amount ='47500000', amountidr = '47500000' where code = 'AR03530'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR03533'");
array_push($sql, "update ar set amount ='5639994', amountidr = '5639994' where code = 'AR03534'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR03556'");
array_push($sql, "update ar set amount ='691000', amountidr = '691000' where code = 'AR03565'");
array_push($sql, "update ar set amount ='13750000', amountidr = '13750000' where code = 'AR03584'");
array_push($sql, "update ar set amount ='5472500', amountidr = '5472500' where code = 'AR03585'");
array_push($sql, "update ar set amount ='4850000', amountidr = '4850000' where code = 'AR03599'");
array_push($sql, "update ar set amount ='3268500', amountidr = '3268500' where code = 'AR03600'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR03605'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR03691'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03718'");
array_push($sql, "update ar set amount ='1094500', amountidr = '1094500' where code = 'AR03719'");
array_push($sql, "update ar set amount ='42000', amountidr = '42000' where code = 'AR03759'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR03778'");
array_push($sql, "update ar set amount ='2310000', amountidr = '2310000' where code = 'AR03779'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR03792'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR03793'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR03794'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR03795'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR03796'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03801'");
array_push($sql, "update ar set amount ='456500', amountidr = '456500' where code = 'AR03802'");
array_push($sql, "update ar set amount ='336000', amountidr = '336000' where code = 'AR03803'");
array_push($sql, "update ar set amount ='84000', amountidr = '84000' where code = 'AR03805'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR03807'");
array_push($sql, "update ar set amount ='25000000', amountidr = '25000000' where code = 'AR03808'");
array_push($sql, "update ar set amount ='5816657', amountidr = '5816657' where code = 'AR03809'");
array_push($sql, "update ar set amount ='38000', amountidr = '38000' where code = 'AR03827'");
array_push($sql, "update ar set amount ='18000', amountidr = '18000' where code = 'AR03829'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR03832'");
array_push($sql, "update ar set amount ='9000000', amountidr = '9000000' where code = 'AR03833'");
array_push($sql, "update ar set amount ='2159997', amountidr = '2159997' where code = 'AR03834'");
array_push($sql, "update ar set amount ='12500000', amountidr = '12500000' where code = 'AR03837'");
array_push($sql, "update ar set amount ='2479995', amountidr = '2479995' where code = 'AR03838'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03846'");
array_push($sql, "update ar set amount ='668085', amountidr = '668085' where code = 'AR03847'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR03848'");
array_push($sql, "update ar set amount ='8065959', amountidr = '8065959' where code = 'AR03849'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03851'");
array_push($sql, "update ar set amount ='1434188', amountidr = '1434188' where code = 'AR03852'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03854'");
array_push($sql, "update ar set amount ='719999', amountidr = '719999' where code = 'AR03855'");
array_push($sql, "update ar set amount ='40000000', amountidr = '40000000' where code = 'AR03856'");
array_push($sql, "update ar set amount ='11519985', amountidr = '11519985' where code = 'AR03857'");
array_push($sql, "update ar set amount ='25000000', amountidr = '25000000' where code = 'AR03858'");
array_push($sql, "update ar set amount ='6479991', amountidr = '6479991' where code = 'AR03859'");
array_push($sql, "update ar set amount ='9000000', amountidr = '9000000' where code = 'AR03860'");
array_push($sql, "update ar set amount ='12500000', amountidr = '12500000' where code = 'AR03861'");
array_push($sql, "update ar set amount ='2479995', amountidr = '2479995' where code = 'AR03862'");
array_push($sql, "update ar set amount ='27500000', amountidr = '27500000' where code = 'AR03865'");
array_push($sql, "update ar set amount ='12705200', amountidr = '12705200' where code = 'AR03866'");
array_push($sql, "update ar set amount ='374000', amountidr = '374000' where code = 'AR03867'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03883'");
array_push($sql, "update ar set amount ='522500', amountidr = '522500' where code = 'AR03884'");
array_push($sql, "update ar set amount ='84000', amountidr = '84000' where code = 'AR03889'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR03901'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR03902'");
array_push($sql, "update ar set amount ='40800000', amountidr = '40800000' where code = 'AR03903'");
array_push($sql, "update ar set amount ='27500000', amountidr = '27500000' where code = 'AR03904'");
array_push($sql, "update ar set amount ='7919989', amountidr = '7919989' where code = 'AR03905'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR03906'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR03911'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03912'");
array_push($sql, "update ar set amount ='1800000', amountidr = '1800000' where code = 'AR03916'");
array_push($sql, "update ar set amount ='550000', amountidr = '550000' where code = 'AR03917'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR03918'");
array_push($sql, "update ar set amount ='1011000', amountidr = '1011000' where code = 'AR03919'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03920'");
array_push($sql, "update ar set amount ='660000', amountidr = '660000' where code = 'AR03921'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR03955'");
array_push($sql, "update ar set amount ='451000', amountidr = '451000' where code = 'AR03956'");
array_push($sql, "update ar set amount ='10799985', amountidr = '10799985' where code = 'AR03959'");
array_push($sql, "update ar set amount ='37500000', amountidr = '37500000' where code = 'AR03960'");
array_push($sql, "update ar set amount ='86000', amountidr = '86000' where code = 'AR03961'");
array_push($sql, "update ar set amount ='2750000', amountidr = '2750000' where code = 'AR03965'");
array_push($sql, "update ar set amount ='1094500', amountidr = '1094500' where code = 'AR03966'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR03967'");
array_push($sql, "update ar set amount ='750090', amountidr = '750090' where code = 'AR03968'");
array_push($sql, "update ar set amount ='30000000', amountidr = '30000000' where code = 'AR03975'");
array_push($sql, "update ar set amount ='13134000', amountidr = '13134000' where code = 'AR03976'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR03977'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR03999'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR04007'");
array_push($sql, "update ar set amount ='665000', amountidr = '665000' where code = 'AR04008'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04009'");
array_push($sql, "update ar set amount ='605000', amountidr = '605000' where code = 'AR04010'");
array_push($sql, "update ar set amount ='7000000', amountidr = '7000000' where code = 'AR04020'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04021'");
array_push($sql, "update ar set amount ='40000000', amountidr = '40000000' where code = 'AR04022'");
array_push($sql, "update ar set amount ='18345984', amountidr = '18345984' where code = 'AR04023'");
array_push($sql, "update ar set amount ='25000000', amountidr = '25000000' where code = 'AR04024'");
array_push($sql, "update ar set amount ='11391990', amountidr = '11391990' where code = 'AR04025'");
array_push($sql, "update ar set amount ='17000000', amountidr = '17000000' where code = 'AR04026'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04028'");
array_push($sql, "update ar set amount ='1094500', amountidr = '1094500' where code = 'AR04029'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR04030'");
array_push($sql, "update ar set amount ='882490', amountidr = '882490' where code = 'AR04031'");
array_push($sql, "update ar set amount ='24000000', amountidr = '24000000' where code = 'AR04034'");
array_push($sql, "update ar set amount ='35000000', amountidr = '35000000' where code = 'AR04035'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR04036'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR04037'");
array_push($sql, "update ar set amount ='16000000', amountidr = '16000000' where code = 'AR04038'");
array_push($sql, "update ar set amount ='14000000', amountidr = '14000000' where code = 'AR04039'");
array_push($sql, "update ar set amount ='18000000', amountidr = '18000000' where code = 'AR04045'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04063'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04065'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04066'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04067'");
array_push($sql, "update ar set amount ='12000000', amountidr = '12000000' where code = 'AR04088'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR04089'");
array_push($sql, "update ar set amount ='25000000', amountidr = '25000000' where code = 'AR04092'");
array_push($sql, "update ar set amount ='11589990', amountidr = '11589990' where code = 'AR04093'");
array_push($sql, "update ar set amount ='35000000', amountidr = '35000000' where code = 'AR04094'");
array_push($sql, "update ar set amount ='5500000', amountidr = '5500000' where code = 'AR04095'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04102'");
array_push($sql, "update ar set amount ='456500', amountidr = '456500' where code = 'AR04103'");
array_push($sql, "update ar set amount ='9000000', amountidr = '9000000' where code = 'AR04108'");
array_push($sql, "update ar set amount ='2788500', amountidr = '2788500' where code = 'AR04109'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04114'");
array_push($sql, "update ar set amount ='300000', amountidr = '300000' where code = 'AR04116'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04117'");
array_push($sql, "update ar set amount ='1700000', amountidr = '1700000' where code = 'AR04118'");
array_push($sql, "update ar set amount ='3600000', amountidr = '3600000' where code = 'AR04119'");
array_push($sql, "update ar set amount ='1639000', amountidr = '1639000' where code = 'AR04120'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04121'");
array_push($sql, "update ar set amount ='15000000', amountidr = '15000000' where code = 'AR04137'");
array_push($sql, "update ar set amount ='6359994', amountidr = '6359994' where code = 'AR04138'");
array_push($sql, "update ar set amount ='21500000', amountidr = '21500000' where code = 'AR04139'");
array_push($sql, "update ar set amount ='9172992', amountidr = '9172992' where code = 'AR04140'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04141'");
array_push($sql, "update ar set amount ='2317998', amountidr = '2317998' where code = 'AR04142'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04143'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04144'");
array_push($sql, "update ar set amount ='1740000', amountidr = '1740000' where code = 'AR04145'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04146'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04147'");
array_push($sql, "update ar set amount ='1155000', amountidr = '1155000' where code = 'AR04148'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR04149'");
array_push($sql, "update ar set amount ='3600000', amountidr = '3600000' where code = 'AR04150'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04151'");
array_push($sql, "update ar set amount ='13500000', amountidr = '13500000' where code = 'AR04166'");
array_push($sql, "update ar set amount ='1751002', amountidr = '1751002' where code = 'AR04167'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04168'");
array_push($sql, "update ar set amount ='870000', amountidr = '870000' where code = 'AR04169'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04170'");
array_push($sql, "update ar set amount ='2178000', amountidr = '2178000' where code = 'AR04171'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04176'");
array_push($sql, "update ar set amount ='1540000', amountidr = '1540000' where code = 'AR04177'");
array_push($sql, "update ar set amount ='28000000', amountidr = '28000000' where code = 'AR04178'");
array_push($sql, "update ar set amount ='7000000', amountidr = '7000000' where code = 'AR04179'");
array_push($sql, "update ar set amount ='19000000', amountidr = '19000000' where code = 'AR04180'");
array_push($sql, "update ar set amount ='30000000', amountidr = '30000000' where code = 'AR04181'");
array_push($sql, "update ar set amount ='24000000', amountidr = '24000000' where code = 'AR04182'");
array_push($sql, "update ar set amount ='5250000', amountidr = '5250000' where code = 'AR04183'");
array_push($sql, "update ar set amount ='3197000', amountidr = '3197000' where code = 'AR04184'");
array_push($sql, "update ar set amount ='5700000', amountidr = '5700000' where code = 'AR04193'");
array_push($sql, "update ar set amount ='25000000', amountidr = '25000000' where code = 'AR04195'");
array_push($sql, "update ar set amount ='11355000', amountidr = '11355000' where code = 'AR04196'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR04197'");
array_push($sql, "update ar set amount ='1569672', amountidr = '1569672' where code = 'AR04198'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04199'");
array_push($sql, "update ar set amount ='2218998', amountidr = '2218998' where code = 'AR04200'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04201'");
array_push($sql, "update ar set amount ='2119998', amountidr = '2119998' where code = 'AR04202'");
array_push($sql, "update ar set amount ='4950000', amountidr = '4950000' where code = 'AR04207'");
array_push($sql, "update ar set amount ='1749000', amountidr = '1749000' where code = 'AR04208'");
array_push($sql, "update ar set amount ='3300000', amountidr = '3300000' where code = 'AR04209'");
array_push($sql, "update ar set amount ='1980000', amountidr = '1980000' where code = 'AR04210'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04211'");
array_push($sql, "update ar set amount ='3775000', amountidr = '3775000' where code = 'AR04212'");
array_push($sql, "update ar set amount ='3200000', amountidr = '3200000' where code = 'AR04213'");
array_push($sql, "update ar set amount ='1510000', amountidr = '1510000' where code = 'AR04214'");
array_push($sql, "update ar set amount ='1650000', amountidr = '1650000' where code = 'AR04215'");
array_push($sql, "update ar set amount ='990000', amountidr = '990000' where code = 'AR04216'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04217'");
array_push($sql, "update ar set amount ='495999', amountidr = '495999' where code = 'AR04218'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04219'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04220'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR04221'");
array_push($sql, "update ar set amount ='3377997', amountidr = '3377997' where code = 'AR04222'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04223'");
array_push($sql, "update ar set amount ='2288000', amountidr = '2288000' where code = 'AR04224'");
array_push($sql, "update ar set amount ='12500000', amountidr = '12500000' where code = 'AR04225'");
array_push($sql, "update ar set amount ='5794995', amountidr = '5794995' where code = 'AR04226'");
array_push($sql, "update ar set amount ='15000000', amountidr = '15000000' where code = 'AR04227'");
array_push($sql, "update ar set amount ='3831300', amountidr = '3831300' where code = 'AR04228'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR04229'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04230'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04231'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04232'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR04234'");
array_push($sql, "update ar set amount ='792000', amountidr = '792000' where code = 'AR04236'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04238'");
array_push($sql, "update ar set amount ='456500', amountidr = '456500' where code = 'AR04239'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04252'");
array_push($sql, "update ar set amount ='8400000', amountidr = '8400000' where code = 'AR04254'");
array_push($sql, "update ar set amount ='2068000', amountidr = '2068000' where code = 'AR04256'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04263'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04265'");
array_push($sql, "update ar set amount ='440000', amountidr = '440000' where code = 'AR04266'");
array_push($sql, "update ar set amount ='12000000', amountidr = '12000000' where code = 'AR04267'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR04268'");
array_push($sql, "update ar set amount ='6400000', amountidr = '6400000' where code = 'AR04269'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR04270'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR04271'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04272'");
array_push($sql, "update ar set amount ='1023000', amountidr = '1023000' where code = 'AR04273'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04274'");
array_push($sql, "update ar set amount ='1800000', amountidr = '1800000' where code = 'AR04275'");
array_push($sql, "update ar set amount ='550000', amountidr = '550000' where code = 'AR04276'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04277'");
array_push($sql, "update ar set amount ='1740000', amountidr = '1740000' where code = 'AR04278'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04279'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR04281'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04284'");
array_push($sql, "update ar set amount ='704000', amountidr = '704000' where code = 'AR04285'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04286'");
array_push($sql, "update ar set amount ='1540000', amountidr = '1540000' where code = 'AR04287'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04288'");
array_push($sql, "update ar set amount ='1740000', amountidr = '1740000' where code = 'AR04289'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04290'");
array_push($sql, "update ar set amount ='4000000', amountidr = '4000000' where code = 'AR04291'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04292'");
array_push($sql, "update ar set amount ='1000000', amountidr = '1000000' where code = 'AR04294'");
array_push($sql, "update ar set amount ='35100000', amountidr = '35100000' where code = 'AR04308'");
array_push($sql, "update ar set amount ='1701986', amountidr = '1701986' where code = 'AR04310'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04311'");
array_push($sql, "update ar set amount ='870000', amountidr = '870000' where code = 'AR04312'");
array_push($sql, "update ar set amount ='87000000', amountidr = '87000000' where code = 'AR04315'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04316'");
array_push($sql, "update ar set amount ='1740000', amountidr = '1740000' where code = 'AR04317'");
array_push($sql, "update ar set amount ='21600000', amountidr = '21600000' where code = 'AR04318'");
array_push($sql, "update ar set amount ='3480000', amountidr = '3480000' where code = 'AR04319'");
array_push($sql, "update ar set amount ='1000000', amountidr = '1000000' where code = 'AR04320'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR04324'");
array_push($sql, "update ar set amount ='1000000', amountidr = '1000000' where code = 'AR04326'");
array_push($sql, "update ar set amount ='12000000', amountidr = '12000000' where code = 'AR04328'");
array_push($sql, "update ar set amount ='4437996', amountidr = '4437996' where code = 'AR04329'");
array_push($sql, "update ar set amount ='12000000', amountidr = '12000000' where code = 'AR04331'");
array_push($sql, "update ar set amount ='1250000', amountidr = '1250000' where code = 'AR04332'");
array_push($sql, "update ar set amount ='715000', amountidr = '715000' where code = 'AR04333'");
array_push($sql, "update ar set amount ='11000000', amountidr = '11000000' where code = 'AR04338'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04339'");
array_push($sql, "update ar set amount ='5500000', amountidr = '5500000' where code = 'AR04340'");
array_push($sql, "update ar set amount ='10000000', amountidr = '10000000' where code = 'AR04341'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04342'");
array_push($sql, "update ar set amount ='2708200', amountidr = '2708200' where code = 'AR04343'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04345'");
array_push($sql, "update ar set amount ='550000', amountidr = '550000' where code = 'AR04346'");
array_push($sql, "update ar set amount ='7000000', amountidr = '7000000' where code = 'AR04347'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04348'");
array_push($sql, "update ar set amount ='10200000', amountidr = '10200000' where code = 'AR04349'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04350'");
array_push($sql, "update ar set amount ='25500000', amountidr = '25500000' where code = 'AR04352'");
array_push($sql, "update ar set amount ='13500000', amountidr = '13500000' where code = 'AR04353'");
array_push($sql, "update ar set amount ='2722500', amountidr = '2722500' where code = 'AR04354'");
array_push($sql, "update ar set amount ='13500000', amountidr = '13500000' where code = 'AR04355'");
array_push($sql, "update ar set amount ='1925000', amountidr = '1925000' where code = 'AR04356'");
array_push($sql, "update ar set amount ='13500000', amountidr = '13500000' where code = 'AR04357'");
array_push($sql, "update ar set amount ='1925000', amountidr = '1925000' where code = 'AR04358'");
array_push($sql, "update ar set amount ='1800000', amountidr = '1800000' where code = 'AR04359'");
array_push($sql, "update ar set amount ='495000', amountidr = '495000' where code = 'AR04360'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04361'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR04362'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04366'");
array_push($sql, "update ar set amount ='2200000', amountidr = '2200000' where code = 'AR04367'");
array_push($sql, "update ar set amount ='14000000', amountidr = '14000000' where code = 'AR04368'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04369'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04370'");
array_push($sql, "update ar set amount ='5000000', amountidr = '5000000' where code = 'AR04371'");
array_push($sql, "update ar set amount ='3279493', amountidr = '3279493' where code = 'AR04372'");
array_push($sql, "update ar set amount ='1100000', amountidr = '1100000' where code = 'AR04373'");
array_push($sql, "update ar set amount ='451000', amountidr = '451000' where code = 'AR04374'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR04375'");
array_push($sql, "update ar set amount ='3283500', amountidr = '3283500' where code = 'AR04376'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR04377'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR04378'");
array_push($sql, "update ar set amount ='8283385', amountidr = '8283385' where code = 'AR04379'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR04380'");
array_push($sql, "update ar set amount ='3179997', amountidr = '3179997' where code = 'AR04381'");
array_push($sql, "update ar set amount ='600000', amountidr = '600000' where code = 'AR04382'");
array_push($sql, "update ar set amount ='13500000', amountidr = '13500000' where code = 'AR04384'");
array_push($sql, "update ar set amount ='1745001', amountidr = '1745001' where code = 'AR04385'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR04386'");
array_push($sql, "update ar set amount ='7550010', amountidr = '7550010' where code = 'AR04387'");
array_push($sql, "update ar set amount ='7600000', amountidr = '7600000' where code = 'AR04388'");
array_push($sql, "update ar set amount ='3800000', amountidr = '3800000' where code = 'AR04389'");
array_push($sql, "update ar set amount ='15200000', amountidr = '15200000' where code = 'AR04390'");
array_push($sql, "update ar set amount ='18900000', amountidr = '18900000' where code = 'AR04391'");
array_push($sql, "update ar set amount ='2970000', amountidr = '2970000' where code = 'AR04392'");
array_push($sql, "update ar set amount ='1400000', amountidr = '1400000' where code = 'AR04393'");
array_push($sql, "update ar set amount ='328000', amountidr = '328000' where code = 'AR04394'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04395'");
array_push($sql, "update ar set amount ='2610001', amountidr = '2610001' where code = 'AR04396'");
array_push($sql, "update ar set amount ='8500000', amountidr = '8500000' where code = 'AR04397'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04398'");
array_push($sql, "update ar set amount ='990000', amountidr = '990000' where code = 'AR04399'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04400'");
array_push($sql, "update ar set amount ='1053001', amountidr = '1053001' where code = 'AR04401'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04402'");
array_push($sql, "update ar set amount ='870000', amountidr = '870000' where code = 'AR04403'");
array_push($sql, "update ar set amount ='2100000', amountidr = '2100000' where code = 'AR04404'");
array_push($sql, "update ar set amount ='1094500', amountidr = '1094500' where code = 'AR04405'");
array_push($sql, "update ar set amount ='35000000', amountidr = '35000000' where code = 'AR04411'");
array_push($sql, "update ar set amount ='40000000', amountidr = '40000000' where code = 'AR04412'");
array_push($sql, "update ar set amount ='4400000', amountidr = '4400000' where code = 'AR04418'");
array_push($sql, "update ar set amount ='15400000', amountidr = '15400000' where code = 'AR04419'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04427'");
array_push($sql, "update ar set amount ='1627000', amountidr = '1627000' where code = 'AR04428'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04429'");
array_push($sql, "update ar set amount ='1158999', amountidr = '1158999' where code = 'AR04430'");
array_push($sql, "update ar set amount ='18000000', amountidr = '18000000' where code = 'AR04431'");
array_push($sql, "update ar set amount ='3300000', amountidr = '3300000' where code = 'AR04432'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04433'");
array_push($sql, "update ar set amount ='171774000', amountidr = '171774000' where code = 'AR04436'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04438'");
array_push($sql, "update ar set amount ='2725003', amountidr = '2725003' where code = 'AR04439'");
array_push($sql, "update ar set amount ='2700000', amountidr = '2700000' where code = 'AR04440'");
array_push($sql, "update ar set amount ='550000', amountidr = '550000' where code = 'AR04441'");
array_push($sql, "update ar set amount ='500000', amountidr = '500000' where code = 'AR04442'");
array_push($sql, "update ar set amount ='27000000', amountidr = '27000000' where code = 'AR04443'");
array_push($sql, "update ar set amount ='4950000', amountidr = '4950000' where code = 'AR04445'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04446'");
array_push($sql, "update ar set amount ='2100002', amountidr = '2100002' where code = 'AR04447'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04448'");
array_push($sql, "update ar set amount ='2970000', amountidr = '2970000' where code = 'AR04449'");
array_push($sql, "update ar set amount ='1400000', amountidr = '1400000' where code = 'AR04450'");
array_push($sql, "update ar set amount ='328000', amountidr = '328000' where code = 'AR04451'");
array_push($sql, "update ar set amount ='5940000', amountidr = '5940000' where code = 'AR04453'");
array_push($sql, "update ar set amount ='12000000', amountidr = '12000000' where code = 'AR04454'");
array_push($sql, "update ar set amount ='2100000', amountidr = '2100000' where code = 'AR04455'");
array_push($sql, "update ar set amount ='3600000', amountidr = '3600000' where code = 'AR04458'");
array_push($sql, "update ar set amount ='1330000', amountidr = '1330000' where code = 'AR04459'");
array_push($sql, "update ar set amount ='1800000', amountidr = '1800000' where code = 'AR04460'");
array_push($sql, "update ar set amount ='842800', amountidr = '842800' where code = 'AR04461'");
array_push($sql, "update ar set amount ='8000000', amountidr = '8000000' where code = 'AR04462'");
array_push($sql, "update ar set amount ='10500000', amountidr = '10500000' where code = 'AR04463'");
array_push($sql, "update ar set amount ='35000000', amountidr = '35000000' where code = 'AR04464'");
array_push($sql, "update ar set amount ='5500000', amountidr = '5500000' where code = 'AR04465'");
array_push($sql, "update ar set amount ='1600000', amountidr = '1600000' where code = 'AR04466'");
array_push($sql, "update ar set amount ='440000', amountidr = '440000' where code = 'AR04467'");
array_push($sql, "update ar set amount ='160000', amountidr = '160000' where code = 'AR04468'");
array_push($sql, "update ar set amount ='35000000', amountidr = '35000000' where code = 'AR04469'");
array_push($sql, "update ar set amount ='6107750', amountidr = '6107750' where code = 'AR04470'");
array_push($sql, "update ar set amount ='16000000', amountidr = '16000000' where code = 'AR04471'");
array_push($sql, "update ar set amount ='18000000', amountidr = '18000000' where code = 'AR04472'");
array_push($sql, "update ar set amount ='21600000', amountidr = '21600000' where code = 'AR04473'");
array_push($sql, "update ar set amount ='3080000', amountidr = '3080000' where code = 'AR04475'");
array_push($sql, "update ar set amount ='40500000', amountidr = '40500000' where code = 'AR04480'");
array_push($sql, "update ar set amount ='7125000', amountidr = '7125000' where code = 'AR04481'");
array_push($sql, "update ar set amount ='1400000', amountidr = '1400000' where code = 'AR04482'");
array_push($sql, "update ar set amount ='328000', amountidr = '328000' where code = 'AR04483'");
array_push($sql, "update ar set amount ='7000000', amountidr = '7000000' where code = 'AR04484'");
array_push($sql, "update ar set amount ='35100000', amountidr = '35100000' where code = 'AR04485'");
array_push($sql, "update ar set amount ='5554002', amountidr = '5554002' where code = 'AR04486'");
array_push($sql, "update ar set amount ='5100000', amountidr = '5100000' where code = 'AR04489'");
array_push($sql, "update ar set amount ='1567500', amountidr = '1567500' where code = 'AR04490'");
array_push($sql, "update ar set amount ='1800000', amountidr = '1800000' where code = 'AR04491'");
array_push($sql, "update ar set amount ='825000', amountidr = '825000' where code = 'AR04492'");
array_push($sql, "update ar set amount ='3900000', amountidr = '3900000' where code = 'AR04493'");
array_push($sql, "update ar set amount ='2500000', amountidr = '2500000' where code = 'AR04494'");
array_push($sql, "update ar set amount ='18000000', amountidr = '18000000' where code = 'AR04495'");
array_push($sql, "update ar set amount ='5830000', amountidr = '5830000' where code = 'AR04496'");
array_push($sql, "update ar set amount ='10200000', amountidr = '10200000' where code = 'AR04497'");
array_push($sql, "update ar set amount ='8130600', amountidr = '8130600' where code = 'AR04498'");
array_push($sql, "update ar set amount ='2800000', amountidr = '2800000' where code = 'AR04499'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04500'");
array_push($sql, "update ar set amount ='2000000', amountidr = '2000000' where code = 'AR04501'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04502'");
array_push($sql, "update ar set amount ='550000', amountidr = '550000' where code = 'AR04503'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04504'");
array_push($sql, "update ar set amount ='10800000', amountidr = '10800000' where code = 'AR04506'");
array_push($sql, "update ar set amount ='1980000', amountidr = '1980000' where code = 'AR04507'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04508'");
array_push($sql, "update ar set amount ='1053001', amountidr = '1053001' where code = 'AR04509'");
array_push($sql, "update ar set amount ='8100000', amountidr = '8100000' where code = 'AR04510'");
array_push($sql, "update ar set amount ='1485000', amountidr = '1485000' where code = 'AR04511'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04512'");
array_push($sql, "update ar set amount ='1500000', amountidr = '1500000' where code = 'AR04513'");
array_push($sql, "update ar set amount ='58000000', amountidr = '58000000' where code = 'AR04514'");
array_push($sql, "update ar set amount ='4200000', amountidr = '4200000' where code = 'AR04515'");
array_push($sql, "update ar set amount ='1034000', amountidr = '1034000' where code = 'AR04517'");
array_push($sql, "update ar set amount ='6300000', amountidr = '6300000' where code = 'AR04518'");
array_push($sql, "update ar set amount ='1497000', amountidr = '1497000' where code = 'AR04519'");
array_push($sql, "update ar set amount ='5200000', amountidr = '5200000' where code = 'AR04520'");
array_push($sql, "update ar set amount ='50400000', amountidr = '50400000' where code = 'AR04522'");
array_push($sql, "update ar set amount ='35600000', amountidr = '35600000' where code = 'AR04523'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR04524'");
array_push($sql, "update ar set amount ='8400000', amountidr = '8400000' where code = 'AR04525'");
array_push($sql, "update ar set amount ='4806200', amountidr = '4806200' where code = 'AR04526'");
array_push($sql, "update ar set amount ='2100000', amountidr = '2100000' where code = 'AR04527'");
array_push($sql, "update ar set amount ='22500000', amountidr = '22500000' where code = 'AR04528'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR04529'");
array_push($sql, "update ar set amount ='13000000', amountidr = '13000000' where code = 'AR04531'");
array_push($sql, "update ar set amount ='660000', amountidr = '660000' where code = 'AR04532'");
array_push($sql, "update ar set amount ='12500000', amountidr = '12500000' where code = 'AR04533'");
array_push($sql, "update ar set amount ='2750000', amountidr = '2750000' where code = 'AR04534'");
array_push($sql, "update ar set amount ='24000000', amountidr = '24000000' where code = 'AR04535'");
array_push($sql, "update ar set amount ='17200000', amountidr = '17200000' where code = 'AR04536'");
array_push($sql, "update ar set amount ='660000', amountidr = '660000' where code = 'AR04537'");
array_push($sql, "update ar set amount ='9817993', amountidr = '9817993' where code = 'AR04538'");
array_push($sql, "update ar set amount ='13300000', amountidr = '13300000' where code = 'AR04539'");
array_push($sql, "update ar set amount ='4081000', amountidr = '4081000' where code = 'AR04540'");
array_push($sql, "update ar set amount ='6650000', amountidr = '6650000' where code = 'AR04541'");
array_push($sql, "update ar set amount ='26700000', amountidr = '26700000' where code = 'AR04542'");
array_push($sql, "update ar set amount ='30000000', amountidr = '30000000' where code = 'AR04543'");
array_push($sql, "update ar set amount ='3000000', amountidr = '3000000' where code = 'AR04544'");
array_push($sql, "update ar set amount ='6000000', amountidr = '6000000' where code = 'AR04545'");
array_push($sql, "update ar set amount ='7200000', amountidr = '7200000' where code = 'AR04546'");
array_push($sql, "update ar set amount ='3960000', amountidr = '3960000' where code = 'AR04547'");
array_push($sql, "update ar set amount ='3600000', amountidr = '3600000' where code = 'AR04548'");
array_push($sql, "update ar set amount ='19200000', amountidr = '19200000' where code = 'AR04549'");
array_push($sql, "update ar set amount ='7505000', amountidr = '7505000' where code = 'AR04550'");
array_push($sql, "update ar set amount ='2350000', amountidr = '2350000' where code = 'AR04554'");
array_push($sql, "update ar set amount ='935000', amountidr = '935000' where code = 'AR04555'");
array_push($sql, "update ar set amount ='16000000', amountidr = '16000000' where code = 'AR04556'");
array_push($sql, "update ar set amount ='6040000', amountidr = '6040000' where code = 'AR04557'");
array_push($sql, "update ar set amount ='2000000', amountidr = '2000000' where code = 'AR04558'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04559'");
array_push($sql, "update ar set amount ='2970000', amountidr = '2970000' where code = 'AR04560'");
array_push($sql, "update ar set amount ='18900000', amountidr = '18900000' where code = 'AR04561'");
array_push($sql, "update ar set amount ='2455002', amountidr = '2455002' where code = 'AR04562'");
array_push($sql, "update ar set amount ='2970000', amountidr = '2970000' where code = 'AR04563'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04564'");
array_push($sql, "update ar set amount ='990000', amountidr = '990000' where code = 'AR04566'");
array_push($sql, "update ar set amount ='5400000', amountidr = '5400000' where code = 'AR04567'");
array_push($sql, "update ar set amount ='13650000', amountidr = '13650000' where code = 'AR04568'");
array_push($sql, "update ar set amount ='13650000', amountidr = '13650000' where code = 'AR04569'");
array_push($sql, "update ar set amount ='20000000', amountidr = '20000000' where code = 'AR04579'");
array_push($sql, "update ar set amount ='22000000', amountidr = '22000000' where code = 'AR04580'");
array_push($sql, "update ar set amount ='23000000', amountidr = '23000000' where code = 'AR04586'");
array_push($sql, "update ar set amount ='500000', amountidr = '500000' where code = 'AR04587'");
array_push($sql, "update ar set amount ='40000000', amountidr = '40000000' where code = 'AR04590'");
array_push($sql, "update ar set amount ='18543984', amountidr = '18543984' where code = 'AR04591'");
array_push($sql, "update ar set amount ='2100000', amountidr = '2100000' where code = 'AR04594'");
array_push($sql, "update ar set amount ='13824800', amountidr = '13824800' where code = 'AR04595'");
array_push($sql, "update ar set amount ='25450000', amountidr = '25450000' where code = 'AR04596'");
array_push($sql, "update ar set amount ='1300000', amountidr = '1300000' where code = 'AR04597'");
array_push($sql, "update ar set amount ='660000', amountidr = '660000' where code = 'AR04598'");
array_push($sql, "update ar set amount ='150000', amountidr = '150000' where code = 'AR04599'");
array_push($sql, "update ar set amount ='7500000', amountidr = '7500000' where code = 'AR04600'");
array_push($sql, "update ar set amount ='2042417', amountidr = '2042417' where code = 'AR04601'");
array_push($sql, "update ar set amount ='400000', amountidr = '400000' where code = 'AR04602'");
array_push($sql, "update ar set amount ='7600000', amountidr = '7600000' where code = 'AR04603'");
array_push($sql, "update ar set amount ='2332000', amountidr = '2332000' where code = 'AR04604'");
array_push($sql, "update ar set amount ='2850000', amountidr = '2850000' where code = 'AR04605'");
array_push($sql, "update ar set amount ='16200000', amountidr = '16200000' where code = 'AR04606'");
array_push($sql, "update ar set amount ='2310000', amountidr = '2310000' where code = 'AR04607'");
array_push($sql, "update ar set amount ='18900000', amountidr = '18900000' where code = 'AR04608'");
array_push($sql, "update ar set amount ='3025000', amountidr = '3025000' where code = 'AR04609'");
array_push($sql, "update ar set amount ='32500000', amountidr = '32500000' where code = 'AR04610'");
array_push($sql, "update ar set amount ='9359987', amountidr = '9359987' where code = 'AR04611'");
//

array_push($sql, "update ap set amount ='20000000', amountidr = '20000000' where code = 'AP00025'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00404'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00405'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00406'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00407'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00408'");
array_push($sql, "update ap set amount ='2300000', amountidr = '2300000' where code = 'AP00409'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00410'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00411'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00412'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00413'");
array_push($sql, "update ap set amount ='2500000', amountidr = '2500000' where code = 'AP00414'");


// clear data
$cuttoffYear = 2021;
array_push($sql, "truncate login_log"); 
array_push($sql, "truncate transaction_log"); 
array_push($sql, "delete from _widget where pkey not in (8,9)"); 

//array_push($sql, "delete from ap where year(trdate) < " . $year); // kalo bisa oper kartu AP
array_push($sql, "delete from ap_payment_header where year(trdate) < " . $cuttoffYear);
array_push($sql, "delete from ap_payment_detail where refkey not in (select pkey from ap_payment_header)"); 
array_push($sql, "delete from ap_downpayment where refkey not in (select pkey from ap_payment_header)"); 

array_push($sql, "delete from ar_payment_header where year(trdate) < " . $cuttoffYear); 
array_push($sql, "delete from ar_payment_detail where refkey not in (select pkey from ar_payment_header)"); 
array_push($sql, "delete from ar_downpayment where refkey not in (select pkey from ar_payment_header)"); 

array_push($sql, "update ap set refheaderkey = 0, refkey = 0, refkey2 = 0, refcode='', refcode2='' where year(trdate) < " . $cuttoffYear); 
array_push($sql, "update ar set refheaderkey = 0, refkey = 0, refcode='', refcode2='' where year(trdate) < " . $cuttoffYear);  
 

array_push($sql, "delete from ap_employee_commission_payment_header where year(trdate) < " . $cuttoffYear);
array_push($sql, "delete from ap_employee_commission_payment_detail where refkey not in (select pkey from ap_employee_commission_payment_header)"); 
array_push($sql, "update ap_employee_commission set refheaderkey = 0, refkey = 0, refcode='', refcode2='' where year(trdate) < " . $cuttoffYear);  
array_push($sql, "update ap,trucking_service_order_header set ap.refcode2 = trucking_service_order_header.code where ap.refkey2 = trucking_service_order_header.pkey");   
 

//array_push($sql, "delete from general_journal_header where code != 'GJ29782' and (statuskey = 4 or year(trdate) < " . $cuttoffYear.')'); 
array_push($sql, "delete from general_journal_header where  (statuskey = 4 or statuskey = 1 or year(trdate) < " . $cuttoffYear.')'); 
array_push($sql, "delete from general_journal_header where  annualclosingjournal = 1"); 
array_push($sql, "delete from general_journal_detail where refkey not in (select pkey from general_journal_header)"); 
//array_push($sql, "update  general_journal_header set statuskey = 2 where code = 'GJ29782'"); 

// saldo awal
array_push($sql,"INSERT INTO `general_journal_header` (`pkey`, `code`, `refkey`, `reftabletype`, `warehousekey`, `reftable`, `refcode`, `trdesc`, `trdate`, `statuskey`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `confirmedby`, `confirmedon`, `tagkey`, `totaldebit`, `totalcredit`, `closed`, `cancelforperiod`, `annualclosingjournal`, `reversefor`, `isbalancing`, `monthlyclosingkey`, `isreval`) VALUES 	(290338, 'GJ29782', 0, 0, 1, '', '', 'Saldo awal', '2020-12-31', 1, '0000-00-00 00:00:00', 0, '2025-04-27 16:29:32', 131, 0, '0000-00-00 00:00:00', 0, 31850956543.0000000, 31850956543.0000000, 0, 0, 0, 0, 0, 0, 0)");
array_push($sql,"INSERT INTO `general_journal_detail` (`pkey`, `refkey`, `refcashbankkey`, `coakey`, `debitsource`, `creditsource`, `currencykey`, `rate`, `debit`, `credit`, `trdesc`, `refcode`)
VALUES
	(370606, 290338, 0, 103, 90325447.0000000, 0.0000000, 1, 1.0000000, 90325447.0000000, 0.0000000, '', ''),
	(370607, 290338, 0, 104, 1836267023.0000000, 0.0000000, 1, 1.0000000, 1836267023.0000000, 0.0000000, '', ''),
	(370608, 290338, 0, 105, 2570962.0000000, 0.0000000, 1, 1.0000000, 2570962.0000000, 0.0000000, '', ''),
	(370609, 290338, 0, 128, 4224315566.0000000, 0.0000000, 1, 1.0000000, 4224315566.0000000, 0.0000000, '', ''),
	(370610, 290338, 0, 146, 45439000.0000000, 0.0000000, 1, 1.0000000, 45439000.0000000, 0.0000000, '', ''),
	(370611, 290338, 0, 147, 21895935.0000000, 0.0000000, 1, 1.0000000, 21895935.0000000, 0.0000000, '', ''),
	(370612, 290338, 0, 109, 61507000.0000000, 0.0000000, 1, 1.0000000, 61507000.0000000, 0.0000000, '', ''),
	(370613, 290338, 0, 111, 1717375000.0000000, 0.0000000, 1, 1.0000000, 1717375000.0000000, 0.0000000, '', ''),
	(370614, 290338, 0, 113, 23851260610.0000000, 0.0000000, 1, 1.0000000, 23851260610.0000000, 0.0000000, '', ''),
	(370615, 290338, 0, 112, 0.0000000, 351890735.0000000, 1, 1.0000000, 0.0000000, 351890735.0000000, '', ''),
	(370616, 290338, 0, 110, 0.0000000, 36585999.0000000, 1, 1.0000000, 0.0000000, 36585999.0000000, '', ''),
	(370617, 290338, 0, 114, 0.0000000, 9889032161.0000000, 1, 1.0000000, 0.0000000, 9889032161.0000000, '', ''),
	(370618, 290338, 0, 21, 0.0000000, 5526313505.0000000, 1, 1.0000000, 0.0000000, 5526313505.0000000, '', ''),
	(370619, 290338, 0, 119, 0.0000000, 4042000.0000000, 1, 1.0000000, 0.0000000, 4042000.0000000, '', ''),
	(370620, 290338, 0, 121, 0.0000000, 13896667.0000000, 1, 1.0000000, 0.0000000, 13896667.0000000, '', ''),
	(370621, 290338, 0, 116, 0.0000000, 3173994600.0000000, 1, 1.0000000, 0.0000000, 3173994600.0000000, '', ''),
	(370622, 290338, 0, 117, 0.0000000, 600000000.0000000, 1, 1.0000000, 0.0000000, 600000000.0000000, '', ''),
	(370623, 290338, 0, 118, 0.0000000, 3940500000.0000000, 1, 1.0000000, 0.0000000, 3940500000.0000000, '', ''),
	(370624, 290338, 0, 15, 0.0000000, 8314700876.0000000, 1, 1.0000000, 0.0000000, 8314700876.0000000, '', '')");


  
array_push($sql, "delete from cash_bank_transfer_header where statuskey = 4 or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from cash_bank_transfer_detail  where refkey not in (select pkey from cash_bank_transfer_header)");
 
array_push($sql, "delete from cash_in_header where statuskey = 4 or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from cash_in_detail  where refkey not in (select pkey from cash_in_header)"); 

array_push($sql, "delete from cash_out_header where statuskey = 4  or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from cash_out_detail  where refkey not in (select pkey from cash_out_header)"); 

array_push($sql, "delete from trucking_cost_cash_out_header where statuskey = 4   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from trucking_cost_cash_out_detail  where refkey not in (select pkey from trucking_cost_cash_out_header)"); 

array_push($sql, "delete from trucking_cost_cash_out_header where statuskey = 5   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from trucking_cost_cash_out_detail  where refkey not in (select pkey from trucking_cost_cash_out_header)"); 

array_push($sql, "delete from trucking_service_order_header where statuskey = 7   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from trucking_service_order_detail  where refkey not in (select pkey from trucking_service_order_header)"); 
array_push($sql, "delete from trucking_service_order_selling_cost  where refkey not in (select pkey from trucking_service_order_header)"); 
 
    
array_push($sql, "delete from trucking_service_order_invoice_header where statuskey = 4   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from trucking_service_order_invoice_detail  where refkey not in (select pkey from trucking_service_order_invoice_header)"); 

array_push($sql, "delete from trucking_service_work_order where statuskey = 4   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from trucking_service_work_order_cost  where refkey not in (select pkey from trucking_service_work_order)"); 

 
array_push($sql, "delete from sales_order_invoice_receipt_header  where statuskey = 4   or year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from sales_order_invoice_receipt_detail  where refkey not in (select pkey from sales_order_invoice_receipt_header)"); 
array_push($sql, "update sales_order_invoice_receipt_header set invoicecodecache = null " ); 

array_push($sql, "delete from ap where statuskey = 4"); 
array_push($sql, "delete from ap_payment_header where statuskey = 4"); 
array_push($sql, "delete from ap_payment_detail where refkey not in (select pkey from ap_payment_header)"); 
array_push($sql, "delete from ap_employee_commission_payment_header where statuskey  = 4"); 
array_push($sql, "delete from ap_employee_commission_payment_detail where refkey not in (select pkey from ap_employee_commission_payment_header)"); 
array_push($sql, "delete from ap_payable_23 where statuskey = 4"); 
array_push($sql, "delete from ap_payable_23_payment_header where statuskey = 4"); 
array_push($sql, "delete from ap_payable_23_payment_detail where refkey not in (select pkey from ap_payable_23_payment_header)"); 

array_push($sql, "delete from ap_employee_commission where statuskey = 4"); 
array_push($sql, "delete from ap_employee_commission_payment_header where statuskey = 4"); 
array_push($sql, "delete from ap_employee_commission_payment_detail where refkey not in (select pkey from ap_employee_commission_payment_header)"); 


array_push($sql, "delete from ar where statuskey = 4"); 
array_push($sql, "delete from ar_payment_header where statuskey = 4"); 
array_push($sql, "delete from ar_payment_detail where refkey not in (select pkey from ar_payment_header)"); 
array_push($sql, "delete from ar_prepaid_23 where statuskey = 4"); 
array_push($sql, "delete from ar_prepaid_23_payment_header where statuskey = 4"); 
array_push($sql, "delete from ar_prepaid_23_payment_detail  where refkey not in (select pkey from ar_prepaid_23_payment_header)"); 
   
array_push($sql, "delete from customer_downpayment where statuskey = 4"); 
array_push($sql, "delete from customer_downpayment_settlement_header where statuskey = 4"); 
array_push($sql, "delete from customer_downpayment_settlement_detail  where refkey not in (select pkey from customer_downpayment_settlement_header)"); 
array_push($sql, "delete from supplier_downpayment where statuskey = 4"); 
array_push($sql, "delete from supplier_downpayment_settlement_header where statuskey = 4"); 
array_push($sql, "delete from supplier_downpayment_settlement_detail  where refkey not in (select pkey from supplier_downpayment_settlement_header)"); 
array_push($sql, "delete from car_turnover"); 
array_push($sql, "update trucking_service_order_header set codectr = code"); 
array_push($sql, "update general_journal_header, cash_out_header  set general_journal_header.refcode = cash_out_header.code where general_journal_header.reftabletype = 67 and general_journal_header.refkey = cash_out_header.pkey"); 
array_push($sql, "update cash_out_header set recipientname = '' where recipientname = 'Hutang Lama'");

// hapus data lama yg masih ad outstanding ketika cutoff
array_push($sql, "delete from ap where outstanding <=0 and pkey not in (select apkey from ap_payment_detail) and year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from ar where outstanding <=0 and pkey not in (select arkey from ar_payment_detail) and year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from ap_employee_commission where outstanding <=0 and pkey not in (select apkey from ap_employee_commission_payment_detail)  and year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from customer_downpayment where outstanding <=0 and pkey not in (select downpaymentkey from ar_downpayment) and year(trdate) < " . $cuttoffYear ); 
array_push($sql, "delete from supplier_downpayment where outstanding <=0 and pkey not in (select downpaymentkey from ap_downpayment) and year(trdate) < " . $cuttoffYear ); 

// dihapuis karena simpen cache no inv, bisa beda
array_push($sql, "update ar set trdesc='' ");
array_push($sql, "update ap set trdesc='', refcode2 = '' ");
array_push($sql, "update ap_employee_commission set trdesc='', refcode2='' ");
  

array_push($sql, "update cash_out_detail set trdesc = 'PAJAK ATAS BUNGA' where pkey = 25746"); 
array_push($sql, "update cash_out_detail set trdesc = 'PAJAK ATAS BUNGA' where pkey = 25023"); 
array_push($sql, "DROP TABLE IF EXISTS `transaction_log_detail`");  
array_push($sql, "DROP TABLE IF EXISTS `transaction_log_action`"); 
array_push($sql, "DROP TABLE IF EXISTS `read_log`"); 
array_push($sql, "DROP TABLE IF EXISTS `activity_log`");  
array_push($sql, "ALTER TABLE `trucking_service_order_invoice_item_detail` ADD `isreimburse` TINYINT(1)  NULL  DEFAULT '0'  AFTER `requestid`"); 
array_push($sql, "ALTER TABLE `trucking_service_order_invoice_item_detail` ADD `currencykey` INT  NULL  DEFAULT '0'  AFTER `isreimburse`"); 
 
array_push($sql, "update supplier set name = replace(name, ' (ELNUSA)','') "); 


// GJ78601
array_push($sql, "update general_journal_header set totaldebit = 296550567, totalcredit = 296550567 where pkey = 173969"); 
array_push($sql, "delete from general_journal_detail where pkey = 229893"); 
array_push($sql, "delete from general_journal_detail where pkey = 229894"); 
array_push($sql, "delete from general_journal_detail where pkey = 229896"); 
array_push($sql, "delete from general_journal_detail where pkey = 229897"); 
array_push($sql, "update general_journal_detail set debit =296550567 where pkey = 229892 "); 
array_push($sql, "update general_journal_detail set credit =296550567 where pkey = 229895 "); 

// GJ78600
array_push($sql, "update general_journal_header set totaldebit =  100797195 , totalcredit = 100797195 where pkey = 173968"); 
array_push($sql, "delete from general_journal_detail where pkey = 229888"); 
array_push($sql, "update  general_journal_detail set debit =  100797195  where pkey = 229887"); 

//GJ84673
array_push($sql, "update general_journal_header set totaldebit =  7500000 , totalcredit = 7500000 where pkey = 169216"); 
array_push($sql, "update general_journal_detail set credit =  7500000  where pkey = 224878"); 
array_push($sql, "update general_journal_detail set coakey = 103, debit = 244814,credit = 0 where pkey = 224878");

// tembak mati pkey agar tdk keinsert 2x
array_push($sql, "insert into general_journal_detail (pkey, refkey,refcashbankkey, coakey, debitsource,creditsource,currencykey,rate,debit,credit ) values (370625,169216,0,199,0,0,1,1,0,7500000 )"); 

//GJ84672
array_push($sql, "update general_journal_header set totaldebit =  307043651 , totalcredit = 307043651 where pkey = 169214"); 
array_push($sql, "delete from general_journal_detail where pkey = 224868"); 
array_push($sql, "delete from general_journal_detail where pkey = 224869"); 
array_push($sql, "update general_journal_detail set debit =  307043651,credit =0  where pkey = 224867"); 
array_push($sql, "update general_journal_detail set credit =  307043651,debit =0  where pkey = 224870"); 

array_push($sql, "delete from chart_of_account where code = '6.1.1.01' ");


//array_push($sql, ""); 

//array_push($sql, ""); 



try{ 
    $class->oDbCon->startTrans();

    $class->setLog('>>>>>>>>> start',true);
    
    foreach($sql as $row){
        $class->setLog($row,true);
        $class->oDbCon->execute($row);
    }

    // reordering number
    $sql = 'select * from cash_out_header where statuskey <> 4 order by trdate asc, pkey asc';
    $rs = $class->oDbCon->doQuery($sql);
    
    $currMonth = 0 ;
    foreach($rs as $row){ 
        
        if ($class->formatDBDate($row['trdate'],'m') <> $currMonth){
                $ctr = 1;
                $currMonth = $class->formatDBDate($row['trdate'],'m');
        }
            
        
        $number = sprintf('%03d',$ctr++);
        $month = sprintf('%02d', $currMonth);
        $year =  $class->formatDBDate($row['trdate'],'Y');
         
        $code = 'CBO-'.$number.'/'.$month.'/'.$year;
        $sql = 'update cash_out_header set code = '.$class->oDbCon->paramString($code).' where pkey = ' . $class->oDbCon->paramString($row['pkey']);
        $class->oDbCon->execute($sql);
    }

    
    // sort ulang kode  mundur 
    
    //CDP
    $sql = 'select code from customer_downpayment where year(trdate) = '.$cuttoffYear.' order by code asc limit 1';
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    $lastAPMumber = intval(str_replace('CDP','',$rsAP[0]['code']));
   
    $sql = 'select pkey from customer_downpayment where year(trdate) < '.$cuttoffYear.' order by trdate desc'; 
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    foreach($rsAP as $apRow){
        $lastAPMumber--;
        $newCode = 'CDP' .  sprintf('%05d', $lastAPMumber);
        $sql  = 'update customer_downpayment set code = ' .$class->oDbCon->paramString($newCode).' where pkey = ' . $class->oDbCon->paramString($apRow['pkey']) ;
        $class->oDbCon->execute($sql);
    }
    
    //SDP
    
    $sql = 'select code from supplier_downpayment where year(trdate) = '.$cuttoffYear.' order by code asc limit 1';
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    $lastAPMumber = intval(str_replace('SDP','',$rsAP[0]['code']));
   
    $sql = 'select pkey from supplier_downpayment where year(trdate) < '.$cuttoffYear.' order by trdate desc'; 
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    foreach($rsAP as $apRow){
        $lastAPMumber--;
        $newCode = 'SDP' .  sprintf('%05d', $lastAPMumber);
        $sql  = 'update supplier_downpayment set code = ' .$class->oDbCon->paramString($newCode).' where pkey = ' . $class->oDbCon->paramString($apRow['pkey']) ;
        $class->oDbCon->execute($sql);
    }
    
    
    //AP
    $sql = 'select code from ap where year(trdate) = '.$cuttoffYear.' order by code asc limit 1';
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    $lastAPMumber = intval(str_replace('AP','',$rsAP[0]['code']));
   
    $sql = 'select pkey from ap where year(trdate) < '.$cuttoffYear.' order by trdate desc'; 
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    foreach($rsAP as $apRow){
        $lastAPMumber--;
        $newCode = 'AP' .  sprintf('%05d', $lastAPMumber);
        $sql  = 'update ap set code = ' .$class->oDbCon->paramString($newCode).' where pkey = ' . $class->oDbCon->paramString($apRow['pkey']) ;
        $class->oDbCon->execute($sql);
    }
    
    //AR
    $sql = 'select code from ar where year(trdate) = '.$cuttoffYear.' order by code asc limit 1';
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    $lastAPMumber = intval(str_replace('AR','',$rsAP[0]['code']));
   
    $sql = 'select pkey from ar where year(trdate) < '.$cuttoffYear.' order by trdate desc'; 
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    foreach($rsAP as $apRow){
        $lastAPMumber--;
        $newCode = 'AR' .  sprintf('%05d', $lastAPMumber);
        $sql  = 'update ar set code = ' .$class->oDbCon->paramString($newCode).' where pkey = ' . $class->oDbCon->paramString($apRow['pkey']) ;
        $class->oDbCon->execute($sql);
    }
    
    
    //AP EMPLOYEE COMMISSION
    $sql = 'select code from ap_employee_commission where year(trdate) = '.$cuttoffYear.' order by code asc limit 1';
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    $lastAPMumber = intval(str_replace('APCOM','',$rsAP[0]['code']));
   
    $sql = 'select pkey from ap_employee_commission where year(trdate) < '.$cuttoffYear.' order by trdate desc'; 
    $rsAP =  $class->oDbCon->doQuery($sql);
    
    foreach($rsAP as $apRow){
        $lastAPMumber--;
        $newCode = 'APCOM' .  sprintf('%05d', $lastAPMumber);
        $sql  = 'update ap_employee_commission set code = ' .$class->oDbCon->paramString($newCode).' where pkey = ' . $class->oDbCon->paramString($apRow['pkey']) ;
        $class->oDbCon->execute($sql);
    }
    
    
    $sql = "update general_journal_header, cash_out_header set general_journal_header.refcode = cash_out_header.code where general_journal_header.refkey = cash_out_header.pkey and general_journal_header.reftabletype = 67 ";
    $class->oDbCon->execute($sql);
    
    
    
    includeClass(array('GeneralJournal.class.php'));
    $generalJournal = new GeneralJournal();
    $generalJournal->changeStatus(290338,2);
        
    $class->oDbCon->endTrans(); 
} catch(Exception $e){
    $class->oDbCon->rollback();
    var_dump($e->getMessage()); 
}		


echo 'done';


?>