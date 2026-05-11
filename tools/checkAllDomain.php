<?php

// die("die, comment open for reset transaction");
// JANGAN ADA UPDATE / INSERT DI FILE INI

require_once '../_config.php';
require_once '../_include-v2.php';


$licenseCon = $class->masterConn();
$sql = 'select * from customer_company';
$rs = $licenseCon->doQuery($sql); 
$licenseCon = null;


for ($i=0; $i<count($rs); $i++) { 
     
    // if($rs[$i]['name'] == 'nuansa.wintera.co.id' || $rs[$i]['name'] == 'bcl.wintera.co.id' || $rs[$i]['name'] == 'mhk.wintera.co.id') continue;
    
    $name = $rs[$i]['name'];
    echo $name.'<br>';
    $dbCon = newConnection($name); 
   
    // $dbCon->startTrans(true);

    try{
        $sql = 'SHOW TABLES LIKE \'car_service_maintenance_header\'';
        $result = $dbCon->con->query($sql); 
        
        if ($result->rowCount() == 0){
            echo '<span style="color:#f00">tidak ad table</span><br>';
            continue; 
        }else{
            
//              $sql = '
// CREATE TABLE IF NOT EXISTS `car_service_maintenance_file` (
//   `pkey` int(11) NOT NULL AUTO_INCREMENT,
//   `refkey` int(11) DEFAULT NULL,
//   `file` varchar(255) DEFAULT \'\',
//   `size` int(11) DEFAULT 0,
//   `isprimary` int(11) DEFAULT 0,
//   `width` int(11) DEFAULT 0,
//   `height` int(11) DEFAULT 0,
//   `orderlist` int(11) NOT NULL,
//   PRIMARY KEY (`pkey`)
// ) ENGINE=InnoDB AUTO_INCREMENT=24396 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
// $dbCon->con->exec($sql); 
             echo '>>>>>>>>>>>>>>>>>> '.$name;
        }
        
        
    // $dbCon->endTrans(true);


        // $sql = 'select * from debit_note_header';
        // $result = $dbCon->doQuery($sql); 
        
        // if (count($result) > 0){
        //     echo '<span style="color:#f00"> >>>>>>>>>>>>>>> '.$rs[$i]['name'].'</span><br>';
        // }
        
        
     }catch(Exception $e){
           
        echo 'ERROR';
        continue;
   
      }

    
    echo '<br>';
 }

echo 'done';
die;


    //
    //$tableName = 'tax';
    //$sql = 'SHOW TABLES LIKE ' .$dbCon->paramString($tableName);
    //$rsTable = $dbCon->doQuery($sql); 
    //
    //if(empty($rsTable)){
    //    //echo ' tidak ad table ' .$tableName.'<br>';
    //    continue;
    //}else{
    //    
    //}
    // 
    //
    //$sql = 'select * from ' .$tableName.' where typekey = 2';
    //$rsData =  $dbCon->doQuery($sql); 
    //
    //if(empty($rsData)) continue;
    //
    //echo $name.'<br>'; 
    //foreach($rsData as $dataRow){
    //    echo $dataRow['name'] .'-' . $dataRow['namecode'].'<br>';
    //}
   
    
    //  $sql = 'select * from _plan_type';
    //  $rsDomain = $dbCon->doQuery($sql); 
      
    //   try{
    //       if($rsDomain[0]['categorykey'] == 2 || $rsDomain[0]['categorykey'] == 5){
          
    //     $sql = 'select * from emkl_purchase_order_header where isreimburse = 1';
    //     $rsDomain = $dbCon->doQuery($sql); 
    //     if(!empty($rsDomain)) echo $name.'<br>';
        
    //     $sql = 'select * from prepaid_expense where isreimburse = 1';
    //     $rsDomain = $dbCon->doQuery($sql);
    //     if(!empty($rsDomain)) echo $name.'<br>';
      
    //  }
       
    //   }catch(Exception $e){
          
    //   }
    
    
   
    
    // cek penggunana upload file JO
    //
    // $sql = 'select _user_setting.value from _user_setting, _setting where _user_setting.settingkey = _setting.pkey and   _setting.code =  \'companyPPNType\'  ';
    // $rsSetting = $dbCon->doQuery($sql); 
    //if( $rsSettings[0]['value']==1) 
    //     echo   $rs[$i]['name'] .'<br>'; 
         
     //$sql = 'select * from debit_note_header ';
     //$rsFile = $dbCon->doQuery($sql);
    //
     //if(count($rsFile) > 1 ){ 
     //    echo $name.' ' .count($rsFile). ' ' .$rsFile[0]['trdate'].' <br>';
     //}
    
    // cek yg pake advance Finance
    // $sql = ' select * from _user_setting where settingkey in  ( select pkey from _setting where code like \'advancedFinance\') ';
    // $rsSettings = $dbCon->doQuery($sql);
    
    // if( $rsSettings[0]['value']==1) 
    //     echo   $rs[$i]['name'] .'<br>'; 
           
           
    // cek yg pake maintenance
    // $sql = 'select count(pkey) as total from general_journal_header where reftabletype like (select pkey from tablekey where tablename like \'car_service_maintenance_header\')';
    // $rsResult = $dbCon->doQuery($sql); 
         
    //  if( $rsResult[0]['total'] > 0) 
    //      echo   $rs[$i]['name'] .'<br>'; 
	  
     
     
//     $dbCon->endTrans();
?>