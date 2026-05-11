<?php 
include ('exportdb.php'); 
  
$arrdbname =array();
$host = '127.0.0.1';

array_push($arrdbname, array('host' => $host ,
                             'dbname' => 'programs_00035-28',
                             'username' => 'programs_00035-u',
                             'password' =>'123'));  
 

 
    
try {
    
    for($i=0;$i<count($arrdbname);$i++){
        $world_dumper = Shuttle_Dumper::create(array( 
            'host' => $arrdbname[$i]['host'],
            'db_name' => $arrdbname[$i]['dbname'],
            'username' =>  $arrdbname[$i]['username'],
            'password' => $arrdbname[$i]['password'],
        ));
 
        // dump the database to gzipped file
        $world_dumper->dump($arrdbname[$i]['dbname'].'.sql');
        
        echo $arrdbname[$i]['dbname'] . ' done !<br>';
    }
	 
    echo 'All Done !';
    
} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}