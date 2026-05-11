<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';  
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EmployeeCategory.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';
 
function getNewObj(){ 
    return  new Employee(); 
}

$OBJ = getNewObj();

//$warehouse = new Warehouse();
//$category = new EmployeeCategory();
//$cityCategory = new CityCategory();
//$city = new City();
//$chartOfAccount = new ChartOfAccount();

//$arrTempMaritalStatus = $OBJ->getMaritalStatus();
//$arrTempMaritalStatus= array_column($arrTempMaritalStatus, 'pkey','name');
//$arrMaritalStatus = array();
//foreach ($arrTempMaritalStatus as $key => $row) {
//    $arrMaritalStatus[strtolower($key)] = $row;
//}
//
//$arrTempSex = $OBJ->getSex();
//$arrTempSex= array_column($arrTempSex, 'pkey','name');
//$arrSex = array();
//foreach ($arrTempSex as $key => $row) {
//    $arrSex[strtolower($key)] = $row;
//}

    //$contactPersonDetail = array(
    //    'pkey' => array('paramName' => 'key'),
    //    'name' => array('paramName' => 'name', 'mandatory' => true),
    //    'position' => array('paramName' => 'position'),
    //    'phone' => array('paramName' => 'phone')
    //);


    $API_FIELDS = array_merge(array(
                'code' =>   array('paramName' => 'code'), 
                'attendanceid'  =>  array('paramName' => 'attendance_id'),
                'name'  =>  array('paramName' => 'name', 'mandatory' => true),    
                //'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
                //'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
                //'categorykey' => array('paramName' => 'category_id', 'mandatory' => true, 'ref' => array('obj' => $category, 'field' => 'code'), 'return' => array('paramName' => 'categorycode')),
                //'categoryname' => array('paramName' => 'category_name', 'updatable' => false, 'return' => array('paramName' => 'categoryname')),
                //'username'  =>  array('paramName' => 'username', 'mandatory' => true),
                //'email'  =>  array('paramName' => 'member_email', 'mandatory' => true),
                //'password'  =>  array('paramName' => 'password'),
                //'phone' => array('paramName' => 'member_phone'),
                //'mobile' => array('paramName' => 'member_mobile'),
                //'zipcode' => array('paramName' => 'member_zipcode'),
                //'livingaddress1' => array('paramName' => 'living_address1'),
                //'livingaddress2' => array('paramName' => 'living_address2'),
                'isdriver' => array('paramName' => 'is_driver','search' => array('field' => $OBJ->tableName.'.isdriver')),
                'issales' => array('paramName' => 'is_sales','search' => array('field' => $OBJ->tableName.'.issales')),
                //'position' => array('paramName' => 'position'),
                //'idnumber' => array('paramName' => 'id_number'),
                //'placeofbirth' => array('paramName' => 'place_of_birth_id', 'ref' => array('obj' => $city, 'field' => 'code'), 'return' => array('paramName' => 'placeofbirthcode')),
                //'placeofbirthname' => array('paramName' => 'place_of_birth_name', 'updatable' => false, 'return' => array('paramName' => 'placeofbirthname')),
                //'dateofbirth' => array('paramName' => 'date_of_birth'),
                //'address1' => array('paramName' => 'member_address1'),
                //'address2' => array('paramName' => 'member_address2'),
                //'citycategorykey' => array('paramName' => 'city_category_id', 'ref' => array('obj' => $cityCategory, 'field' => 'code'), 'return' => array('paramName' => 'citycategorycode')),
                //'citycategoryname' => array('paramName' => 'city_category_name', 'updatable' => false, 'return' => array('paramName' => 'citycategoryname')),
                //'citykey' => array('paramName' => 'city_id', 'ref' => array('obj' => $city, 'field' => 'code'), 'return' => array('paramName' => 'citycode')),
                //'cityname' => array('paramName' => 'city_name', 'updatable' => false, 'return' => array('paramName' => 'cityname')),
                //'maritalstatuskey' => array('paramName' => 'marital_status_id', 'ref' => array('dataset' => $arrMaritalStatus), 'return' => array('paramName' => 'maritalstatuscode')),
                //'maritalstatusname' => array('paramName' => 'marital_status_name','mandatory' => true , 'ref' => array('dataset' => $arrMaritalStatus), 'return' => array('paramName' => 'maritalstatusname')),
                //'nationality' => array('paramName' => 'nationality'),
                //'sexkey' => array('paramName' => 'sex_id', 'ref' => array('dataset' => $arrSex), 'return' => array('paramName' => 'sexcode')),
                //'sexname' => array('paramName' => 'sex_name','mandatory' => true , 'ref' => array('dataset' => $arrSex), 'return' => array('paramName' => 'sexname')),
                //'secretAuth' => array('paramName' => 'secret_auth'),
                //'photofile' => array('paramName' => 'photo_file'),
                //'signaturefile' => array('paramName' => 'signature_file'),
                //'needrealization' => array('paramName' => 'need_realization'),
                //'bankname' => array('paramName' => 'bank_name'),
                //'bankaccountname' => array('paramName' => 'bank_account_name'),
                //'bankaccountnumber' => array('paramName' => 'bank_account_number'),
                //'commissionpercentage' => array('paramName' => 'commission_percentage'),
                //'targetprofit' => array('paramName' => 'target_profit'),
                //'targetmonthperiod' => array('paramName' => 'target_month_period'),
                //'aroutstanding' => array('paramName' => 'ar_outstanding'),
                //'allwarehouseaccess' => array('paramName' => 'all_warehouse_access'),
                //'allcustomeraccess' => array('paramName' => 'all_customer_access'),
                //'allcoaaccess' => array('paramName' => 'all_coa_access'),
                //'allsalesaccess' => array('paramName' => 'all_sales_access'),
                //'allpaymentmethodaccess' => array('paramName' => 'all_payment_method_access'),
                //'cashbankcoakey' => array('paramName' => 'cash_bank_coa_id', 'ref' => array('obj' => $chartOfAccount, 'field' => 'code'), 'return' => array('paramName' => 'cashbankcoacode')),
                //'cashbankcoaname' => array('paramName' => 'cash_bank_coa_name', 'updatable' => false, 'return' => array('paramName' => 'cashbankcoaname')),
                //'commissionapcoakey' => array('paramName' => 'commission_ap_coa_id', 'ref' => array('obj' => $chartOfAccount, 'field' => 'code'), 'return' => array('paramName' => 'commissionapcoacode')),
                //'commissionapcoaname' => array('paramName' => 'commission_ap_coa_name', 'updatable' => false, 'return' => array('paramName' => 'commissionapcoaname')),
                //'arcoakey' => array('paramName' => 'ar_coa_id', 'ref' => array('obj' => $chartOfAccount, 'field' => 'code'), 'return' => array('paramName' => 'apcoacode')),
                //'arcoaname' => array('paramName' => 'ar_coa_name', 'updatable' => false, 'return' => array('paramName' => 'apcoaname')),
                //'apcoakey' => array('paramName' => 'ap_coa_id', 'ref' => array('obj' => $chartOfAccount, 'field' => 'code'), 'return' => array('paramName' => 'arcoacode')),
                //'apcoaname' => array('paramName' => 'ap_coa_name', 'updatable' => false, 'return' => array('paramName' => 'arcoaname')),

                //detail
                //'contact_person_detail' =>  array('paramName' => 'contact_person_detail', 'updatable' => false, 'detail' =>  $contactPersonDetail),
    ),$API_FIELDS);
    
require_once '_process.php';
     
?>