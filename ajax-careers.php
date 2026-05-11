<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

includeClass(array('JobOpportunities.class.php'));  

$obj = new JobOpportunities();

if (isset($_POST) && !empty($_POST['action'])) {
 
	$arrReturn = array();
	switch ($_POST['action']) {
		case 'loadData':
            $pageIndex = ( isset($_POST) && !empty($_POST['page']) ) ? (int) $_POST['page'] : 0; 
            $sortCriteria = (isset($_POST) && in_array($_POST['orderBy'], array('featured','newest'))) ? $_POST['orderBy'] : 'featured';  
            $orderby = ($sortCriteria == 'featured') ? 'order by '.$obj->tableName.'.isfeatured asc, '.$obj->tableName.'.createdon desc' :  'order by '.$obj->tableName.'.createdon desc';
 
            $totalrowsperpage = $obj->loadSetting('careersTotalRowsPerPage'); 
            $now = $pageIndex * $totalrowsperpage; 

            $limit = ' limit ' . $now . ', ' . $totalrowsperpage;
            
            // nanti akan tambah keyword dan kota
            $criteria = '';
            
            if(!empty($_POST['keyword'])){ 
                $keyword = $_POST['keyword'];
                $criteria .=  ' and ('.$obj->tableName.'.title like '.$obj->oDbCon->paramString('%'.$keyword.'%').' or
                 '.$obj->tableDepartment.'.name like '.$obj->oDbCon->paramString('%'.$keyword.'%').' or
                 '.$obj->tableExperience.'.name like '.$obj->oDbCon->paramString('%'.$keyword.'%').'  or
                 '.$obj->tableCity.'.name like '.$obj->oDbCon->paramString('%'.$keyword.'%').'  or
                 '.$obj->tableCityCategory.'.name like '.$obj->oDbCon->paramString('%'.$keyword.'%').' 
                )'; 

            }
            $arrReturn = $obj->searchData('','',true,$criteria,$orderby,$limit);  
            $arrReturn = $obj->updateContentLang($arrReturn);
  
            $totalPages = ceil( $obj->getTotalRows($criteria) / $totalrowsperpage);
             
            $lastPage = ($pageIndex >= $totalPages - 1) ? 1 : 0;

			break;
	}
	echo json_encode(array('data' => $arrReturn, 'lastpage' => $lastPage));
	die;
}


?>