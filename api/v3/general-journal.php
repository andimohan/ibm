<?php

require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/GeneralJournal.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ChartOfAccount.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';


function getNewObj()
{
    return new GeneralJournal();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$chartOfAccount = new ChartOfAccount();
$currency = new Currency();

$journalDetail = array(
    'pkey' => array('paramName' => 'pkey'),
    'coakey' => array('paramName' => 'coa_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCOA . '.code'), 'ref' => array('obj' => $chartOfAccount, 'field' => 'code'), 'return' => array('paramName' => 'coacode'),
    'coacode' => array('paramName' => 'coa_code', 'updatable' => false, 'return' => array('paramName' => 'coacode'))),
    'currencykey' => array('paramName' => 'currency_id', 'mandatory' => true, 'ref' => array('obj' => $currency, 'field' => 'name'), 'return' => array('paramName' => 'currencyname')),
    'currencyname' => array('paramName' => 'currency_name', 'updatable' => false, 'return' => array('paramName' => 'currencyname')),
    'debitsource' => array('paramName' => 'debit_source'),
    'creditsource' => array('paramName' => 'credit_source'),
    'rate' => array('paramName' => 'rate'),
    'debit' => array('paramName' => 'debit'),
    'credit' => array('paramName' => 'credit'),
    'trdesc' => array('paramName' => 'notes'),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' => $journalDetail)
));

// $obj->setLog($API_FIELDS, true);

require_once '_process.php';

?>