<?php
include_once '../_config.php'; 
include_once '../_include.php';
include_once '../_global.php';  
?> 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<title>Updating SQL</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating SQL...</h1>
        <ul class="progress-list"> 
        
            <?php
            
               

                $arrDomain = array();

                $sql = 'select * from customer_company';
            
                $licenseCon = $class->masterConn();
                $rs = $licenseCon->doQuery($sql); 
                $licenseCon = null;
             
                for ($i=0; $i<count($rs); $i++) { 
                        if($rs[$i]['name'] == 'test.wintera.co.id') continue;
                        if($rs[$i]['name'] == 'mhk.wintera.co.id') continue;
                        
                        
                        $name = $rs[$i]['name'];
                        $dbname = $rs[$i]['dbname'];
                        $userid = $rs[$i]['dbusername'];
                        $password = $rs[$i]['dbpass']; 
                            
                        $style = ($i==0) ? '' : 'padding-top:1em;';
                        
                        // Connect to MySQL server  
                        echo '<li class="text-black-jet" style="'.$style.'">connecting to <b>'. $name .'</b> / <b>'.$dbname.'</b>.</li>'; //, user <b>' .  $userid .'</b>, pass <b>'.$password.'</b><br>';
                    
                        $dbCon = newConnection($name);
                
//                        try{
//                    
//                            if(!$dbCon->startTrans())
//                                throw new Exception($dbCon->errorMsg[100]);  

                            $sql = 'select 
                                            general_journal_header.code ,
                                            general_journal_header.trdate ,
                                            general_journal_header.refcode,
                                            general_journal_detail.debit,
                                            general_journal_detail.credit,
                                            general_journal_detail.trdesc ,
                                            general_journal_header.createdon 
                                            
                                        from general_journal_detail,general_journal_header
                                        where 
<<<<<<< HEAD
                                        general_journal_detail.refkey = general_journal_header.pkey and general_journal_detail.trdesc = \'penyesuaian\' and year(trdate) = 2025
                                        and general_journal_header.statuskey in (2,3)
                                   ';
=======
                                            general_journal_detail.refkey = general_journal_header.pkey and 
                                            general_journal_detail.trdesc = \'Penyesuaian\' and
                                            year(trdate) = 2025 and
                                            (debit > 100 or credit > 100) and
                                            general_journal_header.statuskey in (2,3)
                                   '; 
>>>>>>> 50aed49da6b00b648aa0fe7c581340f459046064
                            
                            $rsData = $dbCon->doQuery($sql);
                            
                            echo '<table border="1">';
                            foreach($rsData as $dataRow){
                                echo '<tr>';
                                echo '<td>' .  $dataRow['code'] .'</td>'; 
                                echo '<td>' .  $dataRow['refcode'] .'</td>'; 
                                echo '<td>' .  $dataRow['trdate'] .'</td>'; 
                                echo '<td>' .  $dataRow['createdon'] .'</td>'; 
                                echo '<td>' .  $dataRow['debit'] .'</td>'; 
                                echo '<td>' .  $dataRow['credit'] .'</td>'; 
                                echo '<td>' .  $dataRow['trdesc'] .'</td>'; 
                                echo '</tr>';
                            }
                            echo '</table>';
                                

                         
//                            $dbCon->endTrans();     
//
//                        }catch(Exception $e){   
//                            echo '<li class="text-red-cardinal">'.$e->getMessage().'<br>Transaction has been rollback !</li>';    
//                            $dbCon->rollback();
//                        }	 
                }
 
            ?> 
            
        </ul>
    </div>
</div>     
    
</body> 
</html> 