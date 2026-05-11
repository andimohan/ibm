<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/GeneralJournal.class.php';
$generalJournal = new GeneralJournal();

$url = API_URL . 'general-journal';


echo $generalJournal->executeImportAPI($url, $_POST['data'], 'code');

?>