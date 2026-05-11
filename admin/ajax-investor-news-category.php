<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Category.class.php', 'InvestorNewsCategory.class.php'));
$investorNewsCategory = createObjAndAddToCol(new InvestorNewsCategory());

$obj = $investorNewsCategory;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

die;

?>