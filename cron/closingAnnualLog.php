<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 


$lastYear = date('Y') - 1;
$arrTable = array('transaction_log','login_log');


$licenseCon = $class->masterConn();
$sql = 'select * from customer_company';
$rsAccount = $licenseCon->doQuery($sql); 
$licenseCon = null;
 

foreach($rsAccount as $accountRow){
     
    $domainName =  $accountRow['name'];
    // if( $domainName == 'praja.wintera.co.id') break;
     
    $connectionFile = DOC_ROOT. 'connections/'.$domainName.'.php';
    if(!is_file($connectionFile))  die ("account error");
     
    require_once $connectionFile;      

    echo '<b>'.$domainName.'</b><br>';
    $rsDatabaseInfo = $rs;
    
    
    $dbCon = newConnection($domainName); 
    
    $hasArchive = false;
    $sql = 'SHOW DATABASES LIKE \''.$rsDatabaseInfo[0]['dbname'].'_arc\'';
    // echo $sql.'<br>';
    $rsDBExists =  $dbCon->doQuery($sql);
    if (!empty($rsDBExists)) 
        $hasArchive = true;
    
 
    $dbCon->startTrans(true);
    foreach($arrTable as $tableName){

        for($i=$lastYear;$i>2010;$i--){
            $year = $i;

            // echo $tableName. ' : ' .$year.'<br>';

            // kalo gk ad data, continue....
            $sql = 'select count(pkey) as total from '.$tableName.' where year(createdon) = '.$year;
            $rs =  $dbCon->doQuery($sql);
            if($rs[0]['total'] == 0) break;

            //create table log
            $newTable = $tableName.'_'.$year;
            $sql = 'CREATE TABLE '.$newTable.' LIKE '.$tableName;
            $dbCon->execute($sql);

            // insert log ke histori
            $sql = 'INSERT INTO '.$newTable.' SELECT * FROM '.$tableName.' where year(createdon) = '.$year;
            $dbCon->execute($sql);

            // hapus log yg skrg
            $sql = 'DELETE FROM '.$tableName.' where year(createdon) = '.$year;
            $dbCon->execute($sql); 
            
                       
            // nanti tambahin pindahin database ke archive
            
            if(  $tableName=='transaction_log' && $hasArchive){
                // HARUS TEMBAK MATI, karena ad table selain transaction_log
                $sql = 'CREATE TABLE IF NOT EXISTS '.$rsDatabaseInfo[0]['dbname'].'_arc.transaction_log_'.$year.' AS
                          SELECT * FROM transaction_log_'.$year;
                          
                echo $sql.'<br>';
                $dbCon->execute($sql); 
                
                $sql = 'DROP TABLE transaction_log_'.$year; 
                echo $sql.'<br>';
                $dbCon->execute($sql); 
                
                
// RENAME TABLE wintera_cif.transaction_log_2022 TO wintera_cif_arc.transaction_log_2022;
// RENAME TABLE wintera_cif.transaction_log_2023 TO wintera_cif_arc.transaction_log_2023;
// RENAME TABLE wintera_cif.transaction_log_2024 TO wintera_cif_arc.transaction_log_2024;

            } 
         
            
        }


        // set ulang auto increment 
        $sql = 'select max(pkey) as maxpkey, min(pkey) as minpkey,count(pkey) as totalrows from '.$tableName;
        $rs = $dbCon->doQuery($sql); 
        $lastPkey = $rs[0]['maxpkey'];
        $firstPkey = $rs[0]['minpkey'];
        $totalRows = $rs[0]['totalrows'];


        if($firstPkey > 1){
            $selisih = $firstPkey - 1; 
            $sql = 'update  '.$tableName.' set pkey = pkey - '.$selisih;
            $dbCon->execute($sql);

            $sql ='ALTER TABLE  '.$tableName.' AUTO_INCREMENT =' . ($totalRows +1) ;
            $dbCon->execute($sql); 
        }


    }
 
    echo '<br>';
    $dbCon->endTrans();
     
}

die("done");
?>