<?php

class EmployeeAttendanceImport extends BaseClass{
	
    function __construct(){
 
    parent::__construct();

    $this->tableName = 'employee_attendance_import';
    $this->tableNameDetail = 'employee_attendance_import_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableEmployee = 'employee';    
    $this->tableStatus = 'transaction_status';
	$this->uploadFolder = 'employee-attendance/'; 
    $this->isTransaction = true;    
        
    $this->securityObject = 'EmployeeAttendance';

    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey');  
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouse');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['trdate'] = array('trDate','date'); 
    $this->arrData['totalworkdays'] = array('totalWorkDays');
	$this->arrData['totalabsencedays'] = array('totalAbsenceDays');
	$this->arrData['totallatedays'] = array('totalLateDays'); 
	$this->arrData['totalunpaidleave'] = array('totalUnpaidLeave');
	$this->arrData['trdesc'] = array('trDesc'); 
	$this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
    $this->arrData['statuskey'] = array('selStatus');
 
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'description','dbfield' => 'trdesc','default'=>true, 'width' => 200 ));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
   	 
    $this->arrSearchColumn = array(); 
	array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
	array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));  

	$this->newLoad = true;
		
	array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashAdvance'));
         
    $this->includeClassDependencies(array(
           'Warehouse.class.php',
           'Employee.class.php',
           'GeneralJournal.class.php',
           'EmployeeAttendance.class.php'
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.', 
                 '.$this->tableName.'
      	        WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;       
        
        $sql .=  $this->getWarehouseCriteria() ;
                                
        return $sql;
    }
    
   
    
    function validateForm($arr,$pkey = ''){  
        $arrayToJs = parent::validateForm($arr,$pkey);  
 		 
        return $arrayToJs;
    }
      
	function closeTrans($rsHeader){
		
		// sementara disini 
		$id = $rsHeader[0]['pkey'];
		$this->calculateLateFine($id);
		
	}
	
	function calculateLateFine($id){
		
		// bisa dicustom per client nanti 
		$employee = new Employee();
		
		$rsHeader = $this->getDataRowById($id);
		
		$emplyoeeAttendance = new EmployeeAttendance();
		
		$lateFine = array(0,15000,30000,60000,120000,240000,480000);
		$lateLimitInSecond = 1 * 3600; // batas telat 1 jam
		$halfDay = 13 * 3600; // sampe jam 1 siang,
			
		$rsEmployee = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.attendanceid'),
											  ' and '.$employee->tableName.'.attendanceid <> \'\' 
											    and '.$employee->tableName.'.warehousekey = ' .$this->oDbCon->paramString($rsHeader[0]['warehousekey'])
											  );
		
		
		// TELAT
		$arrTotalLateDay = array();
		$arrTotalLateAmount = array();
		$arrLateDetail = array();
			
		// loop per employee
		// cari yang telatnya kurang dari 09.30
		$rsDetail = $this->getDetailById($id,' and latesecond <= ' . $this->oDbCon->paramString($lateLimitInSecond) ,' order by trdate asc');
		$rsDetail = $this->reindexDetailCollections($rsDetail,'employeekey');  
		
		foreach($rsDetail as $employeekey => $emplyoeeRow){
			if (!isset($arrTotalLateAmount[$employeekey])) $arrTotalLateAmount[$employeekey] = 0;
			
			$fineDayIndex = 0;
			$prevDayIndex = 0;
			
			$dateInSecond = 0;
			$prevDateInSecond = 0;
			
			$arrTotalLateDay[$employeekey] = 0; 
			  
			$arrLateDetail[$employeekey] = array();
			$arrLateDetail[$employeekey]['hidDetailKey'] = array();
			$arrLateDetail[$employeekey]['lateDate'] = array();
			$arrLateDetail[$employeekey]['late'] = array();
			$arrLateDetail[$employeekey]['lateSecond'] = array();
			$arrLateDetail[$employeekey]['lateFine'] = array();
				
			foreach($emplyoeeRow as $row){
				
				$dayIndex = date_format(date_create($row['trdate']), 'w');
				
				$dateInSecond = date_format(date_create($row['trdate']), 'U');
				
				// utk pertama 
				if ($prevDateInSecond == 0) $prevDateInSecond = $dateInSecond;
				
				// start pertama kali
				if ($prevDayIndex == 0) $prevDayIndex = $dayIndex - 1;
				 
				$fineDayIndex++;
				
				// 6 -> sabtu
				// hati2 kalo sudah selang seminggu dan next absennya di hari yang kebetulan sama hari selanjutnya
				// contoh tgl 30 nov 2023 ke 8 dec 2023
				
//				if ($fineDayIndex == 7 || ($prevDayIndex + 1) != $dayIndex)
//					$fineDayIndex = 1;
				
				if ($fineDayIndex == 7 || $dateInSecond-$prevDateInSecond > 86400 )
					$fineDayIndex = 1;
					
				$prevDayIndex = $dayIndex;
					
				$arrTotalLateAmount[$employeekey] += $lateFine[$fineDayIndex];
				
				$arrTotalLateDay[$employeekey]++;
				  
				array_push($arrLateDetail[$employeekey]['hidDetailKey'],0);
				array_push($arrLateDetail[$employeekey]['lateDate'],$this->formatDBDate($row['trdate']));
				array_push($arrLateDetail[$employeekey]['late'], $row['late']);
				array_push($arrLateDetail[$employeekey]['lateSecond'], $row['latesecond']);
				array_push($arrLateDetail[$employeekey]['lateFine'], $lateFine[$fineDayIndex]);
				
				$prevDateInSecond = $dateInSecond;
				
			}
			 
			
		}
		
		// HALFDAY
		$rsDetailHalfDay = $this->getDetailById($id,' and latesecond > ' . $this->oDbCon->paramString($lateLimitInSecond) .' and clockinsecond <= '  . $this->oDbCon->paramString($halfDay),' order by trdate asc');
		
			$this->setLog(' and latesecond > ' . $this->oDbCon->paramString($lateLimitInSecond) .' and clockinsecond <= '  . $this->oDbCon->paramString($halfDay) ,true);
		
		$rsDetailHalfDay = $this->reindexDetailCollections($rsDetailHalfDay,'employeekey');  
		
//		$this->setLog(' and latesecond > ' . $this->oDbCon->paramString($lateLimitInSecond) .' and clockinsecond <= '  . $this->oDbCon->paramString($halfDay),true);
//		$this->setLog($rsDetailHalfDay,true);
//		
//		 
		$arrTotalHalfDay = array();
		$arrHalfDayDetail = array();
			
			
		foreach($rsDetailHalfDay as $employeekey => $emplyoeeRow){
			   
			$arrHalfDayDetail[$employeekey] = array();
			$arrHalfDayDetail[$employeekey]['hidHalfDayDetailKey'] = array();
			$arrHalfDayDetail[$employeekey]['halfDayLateDate'] = array();
			$arrHalfDayDetail[$employeekey]['halfDayLate'] = array();
			$arrHalfDayDetail[$employeekey]['halfDayLateSecond'] = array();
			$arrHalfDayDetail[$employeekey]['halfDayLateFine'] = array();
				
			foreach($emplyoeeRow as $row){ 
				array_push($arrHalfDayDetail[$employeekey]['hidHalfDayDetailKey'],0);
				array_push($arrHalfDayDetail[$employeekey]['halfDayLateDate'],$this->formatDBDate($row['trdate']));
				array_push($arrHalfDayDetail[$employeekey]['halfDayLate'], $row['late']);
				array_push($arrHalfDayDetail[$employeekey]['halfDayLateSecond'], $row['latesecond']);
				array_push($arrHalfDayDetail[$employeekey]['halfDayLateFine'], 0); // nanti diupdate
//				array_push($arrHalfDayDetail[$employeekey]['isHalfDay'], 1); // nanti diupdate 
			} 
			
			
			$arrTotalHalfDay[$employeekey] = count($emplyoeeRow);
		}
		
		
		// LOOP PER KARYAWAN
		foreach($rsEmployee as $employeeRow){
			
			$employeekey = 	$employeeRow['pkey'];
			
			$arrParam = array(); 
			
			$arrParam['hidDetailKey'] = array();
			$arrParam['lateDate'] = array();
			$arrParam['late'] = array();
			$arrParam['lateSecond'] = array();
			$arrParam['lateFine'] = array();
			
			$arrParam['hidHalfDayDetailKey'] = array();
			$arrParam['halfDayLateDate'] = array();
			$arrParam['halfDayLate'] = array();
			$arrParam['halfDayLateSecond'] = array();
			$arrParam['halfDayLateFine'] = array();
			
			$arrParam['code'] = 'xxxx';
			$arrParam['selWarehouse'] =  $rsHeader[0]['warehousekey'];
			$arrParam['hidEmployeeKey'] =  $employeekey;
			$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate']);
			$arrParam['totalWorkDays'] =  0; // nanti baru diupdate
			$arrParam['totalAbsenceDays'] = 0; // nanti baru diupdate
			$arrParam['totalLateDays'] = $arrTotalLateDay[$employeekey]; // nanti baru diupdate
			$arrParam['totalUnpaidLeave'] = 0; // nanti baru diupdate
			$arrParam['totalLateFine'] = $arrTotalLateAmount[$employeekey];
			$arrParam['totalHalfDay'] = $arrTotalHalfDay[$employeekey]; // nanti baru diupdate 
			$arrParam['selStatus'] = 1;

			if(!empty($arrLateDetail[$employeekey]['hidDetailKey'])){
				$arrParam['hidDetailKey'] = $arrLateDetail[$employeekey]['hidDetailKey'];
				$arrParam['lateDate'] = $arrLateDetail[$employeekey]['lateDate'];
				$arrParam['late'] = $arrLateDetail[$employeekey]['late'];
				$arrParam['lateSecond'] = $arrLateDetail[$employeekey]['lateSecond'];
				$arrParam['lateFine'] = $arrLateDetail[$employeekey]['lateFine']; 
			}
			
			if(!empty($arrHalfDayDetail[$employeekey]['hidHalfDayDetailKey'])){
				$arrParam['hidHalfDayDetailKey'] = $arrHalfDayDetail[$employeekey]['hidHalfDayDetailKey'];
				$arrParam['halfDayLateDate'] = $arrHalfDayDetail[$employeekey]['halfDayLateDate'];
				$arrParam['halfDayLate'] = $arrHalfDayDetail[$employeekey]['halfDayLate'];
				$arrParam['halfDayLateSecond'] = $arrHalfDayDetail[$employeekey]['halfDayLateSecond'];
				$arrParam['halfDayLateFine'] = $arrHalfDayDetail[$employeekey]['halfDayLateFine'];
			}
				
//			$this->setLog($arrParam,true);
			$response = $emplyoeeAttendance->addData($arrParam);
			$this->setLog($response,true);
		}
		
	}
	
	function confirmTrans($rsHeader){
		 
		require_once DOC_ROOT.'assets/vendor/autoload.php';  
		
		$employee = new Employee();
		
		$id = $rsHeader[0]['pkey'];
         
		
		// ambil file, baca per baris
		// example
		
		$inputFileType = 'Xlsx';   
		$inputFileName = $rsHeader[0]['file'];
		$uploadPath = DEFAULT_DOC_UPLOAD_PATH.$this->uploadFolder.$id.'/';
			
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
		$reader->setReadDataOnly(true); 

		$spreadsheet = $reader->load($uploadPath.$inputFileName);
		$worksheet = $spreadsheet->getActiveSheet(); 
		$arrAttendance = $this->importFromExcel($worksheet);
		 
		// ambil data karyawan
		$rsEmployee = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.code',$employee->tableName.'.attendanceid'),
											  ' and ' . $employee->tableName.'.attendanceid in ('.$this->oDbCon->paramString(array_keys($arrAttendance),',').')' );
		
		
//		$this->setLog(json_encode($arrAttendance),true);
//		$this->setLog($rsEmployee,true);
		
		$arrNotLate = array('00:00');
		
		foreach($rsEmployee as $employeeRow){
			if(!isset(  $arrAttendance[$employeeRow['attendanceid']] )) continue;
			
			$employeeAttendance = $arrAttendance[$employeeRow['attendanceid']];
			 
			$employeekey = $employeeRow['pkey'];
			
			foreach($employeeAttendance as $row){
				// hanya catat jika telat dan tidak masuk
				if(!empty($row['late']) && !in_array($row['late'],$arrNotLate)){
					$sql = 'insert into '.$this->tableNameDetail.' 
								(refkey,trdate,employeekey,onduty,offduty,clockin,clockinsecond,clockout,clockoutsecond,late,latesecond) 
							values (
								'.$this->oDbCon->paramString($id).',
								'.$this->oDbCon->paramString($row['date']).',
								'.$this->oDbCon->paramString($employeekey).',
								'.$this->oDbCon->paramString($row['on_duty']).',
								'.$this->oDbCon->paramString($row['off_duty']).',
								'.$this->oDbCon->paramString($row['clock_in']).',
								'.$this->oDbCon->paramString($this->convertToSecond($row['clock_in'])).',
								'.$this->oDbCon->paramString($row['clock_out']).',
								'.$this->oDbCon->paramString($this->convertToSecond($row['clock_out'])).',
								'.$this->oDbCon->paramString($row['late']).', 
								'.$this->oDbCon->paramString($this->convertToSecond($row['late'])).'
							) ';

					$this->oDbCon->execute($sql);
				}
			} 
			
		}
		
	} 
	
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey']; 
		 
		$inputFileName = $rsHeader[0]['file'];
		$uploadPath = DEFAULT_DOC_UPLOAD_PATH.$this->uploadFolder.$id.'/';
		
//	    if(!is_file($uploadPath.$inputFileName))
//			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[216]);
             
 	 }
    
	
	function importFromExcel($worksheet){
		// nanti bisa dioverwrite
			
		$arrAttendance = array();
		 
		// Get the highest row and column numbers referenced in the worksheet
		$highestRow = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

		
		for ($row = 2; $row <= $highestRow; ++$row) { 

			$attendanceID = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); 
			$name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
			$trdate = $worksheet->getCellByColumnAndRow(3, $row)->getValue();    
			$onDuty = $worksheet->getCellByColumnAndRow(4, $row)->getValue();  
			$offDuty = $worksheet->getCellByColumnAndRow(5, $row)->getValue();   
			$clockIn = $worksheet->getCellByColumnAndRow(6, $row)->getValue();    
			$clockOut = $worksheet->getCellByColumnAndRow(7, $row)->getValue();   
			$late = $worksheet->getCellByColumnAndRow(8, $row)->getValue();   
  
			if(!isset($arrAttendance[$attendanceID])) $arrAttendance[$attendanceID] = array();
				 
			array_push($arrAttendance[$attendanceID], array( 
				'attendance_id' => $attendanceID,
				'date' => str_replace('\'','',$this->oDbCon->paramDate($trdate)),
				'on_duty' =>  $onDuty,
				'off_duty' => $offDuty,
				'clock_in' => $clockIn,
				'clock_out' => $clockOut,
				'late' => $late,
			)); 


		}
		
		return $arrAttendance;
		
	}
 
	function convertToSecond($time){
		if(empty($time)) return 0;
		 
		$arrTime = explode(':',$time); 
//		$this->setLog($time . ' ==> ' .mktime($arrTime[0], $arrTime[1], 0, 0, 0, 0),true); 

		return  ($arrTime[0] * 3600) + ($arrTime[1] * 60);
	}
	
    function normalizeParameter($arrParam, $trim=false){
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }
     
}

?>