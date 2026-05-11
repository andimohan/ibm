<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';  
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
            
                // Name of the file
                if (!isset($_FILES) || empty($_FILES['fileToUpload']['tmp_name']))
                    die ('<div class="text-red-cardinal" style="padding:1em">Missing File !</div>');

                // cek token
                if (!isset($_POST) || empty($_POST['token']))
                    die ('<div class="text-red-cardinal" style="padding:1em">Missing Token !</div>');

                //validasi token 
           
                require_once '../../assets/vendor/autoload.php';     
                $g = new \Google\Authenticator\GoogleAuthenticator();

                $userkey = base64_decode($_SESSION[$class->loginAdminSession]['id']); 
                $rsLogin = $employee->getDataRowById($userkey);
                if (!$g->checkCode($rsLogin[0]['secretAuth'], $_POST['token'])) 
                    die ('<div class="text-red-cardinal" style="padding:1em">Invalid Token !</div>'); 
               
                    
                $filename = $_FILES['fileToUpload']['tmp_name']; 

                $arrDomain = array();

                $sql = 'select * from customer_company';
            
                $licenseCon = $class->masterConn();
                $rs = $licenseCon->doQuery($sql); 
                $licenseCon = null;
             
                for ($i=0; $i<count($rs); $i++) { 
                        if($rs[$i]['name'] == 'test.wintera.co.id') continue;
                        
                        $name = $rs[$i]['name'];
                        $dbname = $rs[$i]['dbname'];
                        $userid = $rs[$i]['dbusername'];
                        $password = $rs[$i]['dbpass']; 
                            
                        $style = ($i==0) ? '' : 'padding-top:1em;';
                        
                        // Connect to MySQL server  
                        echo '<li class="text-black-jet" style="'.$style.'">connecting to <b>'. $name .'</b> / <b>'.$dbname.'</b>.</li>'; //, user <b>' .  $userid .'</b>, pass <b>'.$password.'</b><br>';
                    
                        $dbCon = newConnection($name);
                
                        try{
                            // Temporary variable, used to store current query
                            $templine = '';
                            // Read in entire file
                            $lines = file($filename);
                            // Loop through each line

                            // check database version

                            $sql = 'select value from  _user_setting 
                                    where settingkey = ( select pkey from _setting where code = \'databaseVersion\')
                                    ';
                            $rsVersion = $dbCon->doQuery($sql);
                            $dbVersion = $rsVersion[0]['value'];

                            $currVersion = $lines[0];

                            if (strpos($currVersion, '--version=') === false) { 
                                 echo '<li class="text-red-cardinal">Missing Version !</li>'; 
                                 die;
                            }

                            $currVersion = (int)str_replace('--version=','', $currVersion);


                           // echo '<li class="text-green-avocado">'.$currVersion.' == '.$dbVersion.'</li>'; 
                            if ($dbVersion == $currVersion){ 
                                echo '<li class="text-green-avocado" >Already latest version !</li>'; 
                                continue;
                            }

                            //die;

                            if(!$dbCon->startTrans(true))
                                throw new Exception($dbCon->errorMsg[100]);  

                            echo '<li class="text-black-jet">Updating <b>' .  $dbname .'.</b></li>'; //, user <b>' .  $userid .'</b>, pass <b>'.$password.'</b><br>';

                            foreach ($lines as $line){
                                // Skip it if it's a comment
                                if (substr($line, 0, 2) == '--' || $line == '')
                                  continue;

                                // Add this line to the current segment
                                $templine .= $line;
                                // If it has a semicolon at the end, it's the end of the query
                                if (substr(trim($line), -1, 1) == ';'){
                                    // Perform the query
                                    // mysql_query($templine) or print($dbname.': Error performing query <strong>' . $templine . ' : ' . mysql_error() . '</strong><br />');
                                   echo '<li class="text-green-avocado">'.$templine.'</li>'; //, user <b>' .  $userid .'</b>, pass <b>'.$password.'</b><br>';

                                    $result = $dbCon->execute($templine);
                                    if ($result  === false){ 
                                        $errinfo = $dbCon->con->errorInfo();
                                        
                                        echo '<li class="text-red-cardinal">'.var_dump($errinfo).'</li>';    
                                        //$msg = 'Invalid SQL command : ' . $varsql . chr(13) . $errinfo[2] .chr(13);  
                                        // throw new Exception($msg);
                                    }else{
                                        echo '<li class="text-blue-munsell">Update done.</li>';  
                                    }

                                    // Reset temp variable to empty
                                    $templine = '';
                                }
                            }

                            // update versi database 
                            $sql = 'update _user_setting set value = '.$dbCon->paramString($currVersion). '
                                    where settingkey = ( select pkey from _setting where code = \'databaseVersion\')
                                    ';
                            $dbCon->execute($sql);

                            $dbCon->endTrans();     

                        }catch(Exception $e){   
                            echo '<li class="text-red-cardinal">'.$e->getMessage().'<br>Transaction has been rollback !</li>';    
                            $dbCon->rollback();
                        }	 
                }
 
            ?> 
            
        </ul>
    </div>
</div>     
    
</body> 
</html> 