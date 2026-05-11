<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array("Quiz.class.php"));
$quiz = new Quiz();

$obj = $quiz;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'quizForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['name'], $obj->tableName . '.name')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 

function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    
    $obj->setLog($rsAnswer,true);
	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                            </div>
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['date']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['description']).'</div> 
								<div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['description']).'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['detail']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							     <div class="div-table-row">  
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['question']).'</div>  
                                </div>';
    $rsAnswer = array();
        for ($i=0;$i<count($rsDetail);$i++){
             	$rsAnswer = $obj->getItemDetail($rsDetail[$i]['pkey']);

			$detailInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['question'].'</div> 
				</div>
			';
		}
    
    	$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
    
    $detailAnswersInformation = ' <div class="data-card no-border">
						<div class="content">
						<div class="div-table  quick-view-table">
							     <div class="div-table-row">  
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['answer']).'</div>  
                                </div>';
    
           for ($j=0;$j<count($rsAnswer);$j++){
            
			$detailAnswersInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col" style=" ">'.$rsAnswer[$j]['answers'].'</div> 
				</div>
			';
		}
    
    
    	$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$detailInformation.'
								 '.$detailAnswersInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';
    
    
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
