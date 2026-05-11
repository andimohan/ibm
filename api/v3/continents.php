<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Continent.class.php';

function getNewObj()
{
    return new Continent();
}

$OBJ = getNewObj();


$API_FIELDS = array_merge(array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'statuskey' => array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status'), 'return' => array('isReturn' => false)),
), $API_FIELDS);

require_once '_process.php';

?>