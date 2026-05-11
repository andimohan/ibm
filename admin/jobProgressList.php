<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('JobProgress.class.php');
$jobProgress = createObjAndAddToCol(new JobProgress());

$obj = $jobProgress;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'jobProgressForm';

function generateQuickView($obj, $id)
{
    $detail = '';
     
    $rs = $obj->searchData('','',true,  ' and ' . $obj->tableName.'.pkey = '.$obj->oDbCon->paramString($id).' ');
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

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
                                <div class="div-table-col">'.ucwords($obj->lang['category']).'</div> 
                                <div class="div-table-col">'.$rs[0]['categoryname'].'</div> 
                            </div>
							
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
      
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                <div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
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
									<div class="div-table-col detail-col-header" >'.ucwords($obj->lang['jobProgress']).'</div>  
                                </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			$detailInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col">'.$rsDetail[$i]['name'].'</div>     
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		

		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$detailInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail; 
}


include('dataList.php');

?>