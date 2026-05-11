<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Continent.class.php';

function getNewObj()
{
    return new Country();
}

$OBJ = getNewObj();


$API_FIELDS = array_merge(array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'continentname' => array('paramName' => 'continent_name', 'updatable' => false, 'return' => array('paramName' => 'continentname')),
    'continentkey' => array('paramName' => 'continent_id', 'mandatory' => true, 'ref' => array('obj' => new Continent(), 'field' => 'code'), 'return' => array('paramName' => 'continentcode')),
    'statuskey' => array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status'), 'return' => array('isReturn' => false)),
), $API_FIELDS);

require_once '_process.php';

?>