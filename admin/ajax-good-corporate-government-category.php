<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodCorporateGovernmentCategory.class.php'));
$goodCorporateGovernmentCategory = createObjAndAddToCol(new GoodCorporateGovernmentCategory());

$obj = $goodCorporateGovernmentCategory;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

die;

?>