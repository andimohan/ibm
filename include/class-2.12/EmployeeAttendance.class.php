<?php

class EmployeeAttendance extends BaseClass{
	
    function __construct(){
 
    parent::__construct();

    $this->tableName = 'employee_attendance';
    $this->tableNameDetail = 'employee_attendance_late';
    $this->tableNameDetailHalfDay = 'employee_attendance_halfday';
    $this->tableWarehouse = 'warehouse';   
    $this->tableEmployee = 'employee';    
    $this->tableStatus = 'transaction_status';
    $this->isTransaction = true;    
        
    $this->securityObject = 'EmployeeAttendance';

		
	$this->arrDataDetail = array(); 
	$this->arrDataDetail['pkey'] = array('hidDetailKey');
	$this->arrDataDetail['refkey'] = array('pkey','ref');
	$this->arrDataDetail['trdate'] = array('lateDate','date');
	$this->arrDataDetail['late'] = array('late');
	$this->arrDataDetail['latesecond'] = array('lateSecond','number');
	$this->arrDataDetail['latefine'] = array('lateFine','number');
		
	$this->arrDataDetailHalfDay = array(); 
	$this->arrDataDetailHalfDay['pkey'] = array('hidHalfDayDetailKey');
	$this->arrDataDetailHalfDay['refkey'] = array('pkey','ref');
	$this->arrDataDetailHalfDay['trdate'] = array('halfDayLateDate','date');
	$this->arrDataDetailHalfDay['late'] = array('halfDayLate');
	$this->arrDataDetailHalfDay['latesecond'] = array('halfDayLateSecond','number');
	$this->arrDataDetailHalfDay['latefine'] = array('halfDayLateFine','number');
//	$this->arrDataDetailHalfDay['ishalfday'] = array('isHalfDay','number');

	$arrDetails = array(); 
	array_push($arrDetails, array('dataset' => $this->arrDataDetail)); 
	array_push($arrDetails, array('dataset' => $this->arrDataDetailHalfDay, 'tableName' => $this->tableNameDetailHalfDay));
  

	$this->arrData = array(); 
	$this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
	$this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouse');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['trdate'] = array('trDate','date'); 
    $this->arrData['totalworkdays'] = array('totalWorkDays','number');
	$this->arrData['totalabsencedays'] = array('totalAbsenceDays','number');
	$this->arrData['totallatedays'] = array('totalLateDays','number');
	$this->arrData['totalunpaidleave'] = array('totalUnpaidLeave','number');
	$this->arrData['totallatefine'] = array('totalLateFine','number');
	$this->arrData['totalhalfday'] = array('totalHalfDay','number'); 
	$this->arrData['trdesc'] = array('trDesc'); 
    $this->arrData['statuskey'] = array('selStatus');
 
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'attendanceid','title' => 'attendanceID','dbfield' => 'attendanceid','default'=>true, 'width' => 90));
    array_push($this->arrDataListAvailableColumn, array('code' => 'employeecode','title' => 'employeeCode','dbfield' => 'employeecode', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename','default'=>true, 'width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'late','title' => 'lateDays','dbfield' => 'totallatedays','default'=>true, 'width' => 80, 'align' =>'right', 'format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'latecut','title' => 'cut','dbfield' => 'totallatefine','default'=>true, 'width' => 80,  'align' =>'right','format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'halfday','title' => 'halfDay','dbfield' => 'totalhalfday','default'=>true, 'width' => 80, 'align' =>'right', 'format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
   	
		   
    $this->arrSearchColumn = array(); 
	array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
	array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
	array_push($this->arrSearchColumn, array('Karyawan', $this->tableEmployee. '.name'));  

		
	$this->newLoad = true;
		
	array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashAdvance'));
         
    $this->includeClassDependencies(array(
           'Warehouse.class.php',
           'Employee.class.php',
		   'GeneralJournal.class.php'
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableEmployee.'.name as employeename, 
                '.$this->tableEmployee.'.code as employeecode, 
                '.$this->tableEmployee.'.attendanceid as attendanceid, 
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableEmployee.', 
                 '.$this->tableName.'
      	        WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;       
        
        $sql .=  $this->getWarehouseCriteria() ;
                                
        return $sql;
    }
    
   
    
    function validateForm($arr,$pkey = ''){  
        $arrayToJs = parent::validateForm($arr,$pkey);  
 		$arrEmployeeKey = $arr['hidEmployeeKey']; 
 
		if(empty($arrEmployeeKey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]); 
	  
        return $arrayToJs;
    }
      
	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey'];
         
       
	} 
	
	
	function getHalfDayDetail($id){
		$sql = 'select * from '. $this->tableNameDetailHalfDay.' where refkey = ' . $this->oDbCon->paramString($id);
		return  $this->oDbCon->doQuery($sql);
	}
 
    function normalizeParameter($arrParam, $trim=false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam; 
    }
     
}

?>