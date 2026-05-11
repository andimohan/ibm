<?php

function exportCSVFile($title, $data='') {
     
    	$data = array(
	     '0' => array('Name'=> 'user1', 'Status' =>'complete', 'Priority'=>'Low', 'Salary'=>'001'),
	     '1' => array('Name'=> 'user2', 'Status' =>'inprogress', 'Priority'=>'Low', 'Salary'=>'111'),
	     '2' => array('Name'=> 'user3', 'Status' =>'hold', 'Priority'=>'Low', 'Salary'=>'333'),
	     '3' => array('Name'=> 'user4', 'Status' =>'pending', 'Priority'=>'Low', 'Salary'=>'444'),
	     '4' => array('Name'=> 'user5', 'Status' =>'pending', 'Priority'=>'Low', 'Salary'=>'777'),
	     '5' => array('Name'=> 'user6', 'Status' =>'pending', 'Priority'=>'Low', 'Salary'=>'777')
	    );
	     
	     if(!empty($data)){

	     	$csvTitle = $title;
			$titleArray = array_keys($data[0]);
			$delimiter = "\t";
		 
            $title = 'test'; 
             
			$filename = $title.".xls";
			     
			//Send headers
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename");
			header("Pragma: no-cache");
			header("Expires: 0");
			  
			//print the title to the first cell
			print $csvTitle . "\r\n";
			  
			//Separate each column title name with the delimiter
			$titleString = implode($delimiter, $titleArray);
			print $titleString . "\r\n";
			  
			//Loop through each subarray, which are our data sets
			foreach ($data as $subArrayKey => $subArray) {
			    //Separate each datapoint in the row with the delimiter
			    $dataRowString = implode($delimiter, $subArray);
			    print $dataRowString . "\r\n";
			}

	     }
	    
	    

	}

    exportCSVFile('test');

?>