<?php

class Reminder extends BaseClass{

    function __construct(){

        parent::__construct();

        $this->tableName = 'reminder';
        $this->tableStatus = 'transaction_status';
        $this->tableEmployee = 'employee';
        $this->tableMedicalRequestClaim = 'medical_request_claim_header';
        $this->tableMedicalPurchaseOrder = 'medical_purchase_order_header';
        $this->tableMedicalJobOrder = 'medical_job_order_header';
        $this->tableMedicalSalesOrderQuotation = 'medical_sales_order_quotation_header';
        $this->tableMedicalSalesInvoice = 'medical_sales_invoice_header';
        $this->securityObject = 'Reminder';
        $this->isTransaction = true;
        $this->newLoad = true;


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['refjobkey'] = array('hidMedicalJobOrderKey');
        $this->arrData['refrequestkey'] = array('hidMedicalRequestClaimKey');
        $this->arrData['refquotationkey'] = array('hidMedicalSalesOrderQuotationKey');
        $this->arrData['refinvoicekey'] = array('hidMedicalSalesInvoiceKey');
        $this->arrData['refpurchasekey'] = array('hidMedicalPurchaseKey');
        $this->arrData['module'] = array('selModule');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['code'] = array('code');
			
       

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sender', 'title' => 'sender', 'dbfield' => 'sendername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'toAccount', 'title' => 'toAccount', 'dbfield' => 'toaccountname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'transactionCode', 'title' => 'transactionCode', 'dbfield' => 'transactioncode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('transactionCode', $this->tableMedicalRequestClaim . '.code'));
        array_push($this->arrSearchColumn, array('toAccount', 'employee_toaccount.name'));
        array_push($this->arrSearchColumn, array('sender', 'employee_sender.name'));


        $this->includeClassDependencies(array(
            'MedicalRequestClaim.class.php',
            'MedicalJobOrder.class.php',
            'MedicalSalesOrderQuotation.class.php',
            'MedicalSalesInvoice.class.php',
            'MedicalPurchaseOrder.class.php',
            'Employee.class.php',
        ));

        $this->overwriteConfig();
    }

    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*,  
                     ' . $this->tableMedicalRequestClaim . '.code as requestcode,
                     ' . $this->tableMedicalJobOrder . '.code as jocode,
                     ' . $this->tableMedicalPurchaseOrder . '.code as purchasecode,
                     ' . $this->tableMedicalSalesOrderQuotation . '.code as quotationcode,
                     ' . $this->tableMedicalSalesInvoice . '.code as invoicecode,
                    CASE
                        WHEN ' . $this->tableName . '.module = "request" THEN ' . $this->tableMedicalRequestClaim . '.code
                        WHEN ' . $this->tableName . '.module = "quotation" THEN ' . $this->tableMedicalSalesOrderQuotation . '.code
                        WHEN ' . $this->tableName . '.module = "job" THEN ' . $this->tableMedicalJobOrder . '.code
                        WHEN ' . $this->tableName . '.module = "guaranteeLetter" THEN ' . $this->tableMedicalPurchaseOrder . '.code
                        WHEN ' . $this->tableName . '.module = "invoice" THEN ' . $this->tableMedicalSalesInvoice . '.code
                    END AS transactioncode,
                        employee_toaccount.name as toaccountname, 
                        employee_sender.name as sendername, 
                     ' . $this->tableStatus . '.status as statusname
                 from 
                     ' . $this->tableName . '
						 left join ' . $this->tableMedicalJobOrder . ' on ' . $this->tableName . '.refjobkey = ' . $this->tableMedicalJobOrder . '.pkey  
						 left join ' . $this->tableMedicalRequestClaim . ' on ' . $this->tableName . '.refrequestkey = ' . $this->tableMedicalRequestClaim . '.pkey  
						 left join ' . $this->tableMedicalPurchaseOrder . ' on ' . $this->tableName . '.refpurchasekey = ' . $this->tableMedicalPurchaseOrder . '.pkey  
						 left join ' . $this->tableMedicalSalesOrderQuotation . ' on ' . $this->tableName . '.refquotationkey = ' . $this->tableMedicalSalesOrderQuotation . '.pkey  
						 left join ' . $this->tableMedicalSalesInvoice . ' on ' . $this->tableName . '.refinvoicekey = ' . $this->tableMedicalSalesInvoice . '.pkey  
						 left join ' . $this->tableEmployee . ' employee_toaccount on ' . $this->tableName . '.employeekey = employee_toaccount.pkey 
						 left join ' . $this->tableEmployee . ' employee_sender on ' . $this->tableName . '.createdby = employee_sender.pkey,  
                     ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
          ' . $this->criteria;
		 
        return $sql;
    }

    function validateForm($arr, $pkey = '') {
		
        $arrayToJs = parent::validateForm($arr, $pkey);

        switch ($arr['selModule']) {
            case 'request' :
                if (empty($arr['hidMedicalRequestClaimKey'])){
			        $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
                }
                break;
            case 'job' :
                if (empty($arr['hidMedicalJobOrderKey'])) {
			        $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
                }  
                break;
            case 'guaranteeLetter' :
                if (empty($arr['hidMedicalPurchaseKey'])) {
			        $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
                }
                break;
            case 'quotation' :
                if (empty($arr['hidMedicalSalesOrderQuotationKey'])) {
			        $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
                }
                break;
            case 'invoice' :
                if (empty($arr['hidMedicalSalesInvoiceKey'])) {
			        $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
                }
                break;
        }
        return $arrayToJs;
    }

    
    function afterUpdateData($arrParam, $action)  {
        switch ($arrParam['selModule']) {
            case 'request' :
                $medicalRequestClaim = new MedicalRequestClaim();
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalRequestClaim, $arrParam['hidMedicalRequestClaimKey']);
                break;
            case 'job' :
                $medicalJobOrder = new MedicalJobOrder();
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalJobOrder, $arrParam['hidMedicalJobOrderKey']);
                break;
            case 'guaranteeLetter' :
                $medicalPurchaseOrder = new MedicalPurchaseOrder();
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalPurchaseOrder, $arrParam['hidMedicalPurchaseKey']);
                break;
            case 'quotation' :
                $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalSalesOrderQuotation, $arrParam['hidMedicalSalesOrderQuotationKey']);
                break;
            case 'invoice' :
                $medicalSalesInvoice = new MedicalSalesInvoice();
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalSalesInvoice, $arrParam['hidMedicalSalesInvoiceKey']);
                break;
        }
    }

    function afterStatusChanged($rsHeader){ 
        switch ($rsHeader[0]['module']) {
            case 'request' :
                $medicalRequestClaim = new MedicalRequestClaim();
                $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalRequestClaim, $rsHeader[0]['refrequestkey']);
                break;
            case 'job' :
                $medicalJobOrder = new MedicalJobOrder();
                $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalJobOrder, $rsHeader[0]['refjobkey']);
                break;
            case 'guaranteeLetter' :
                $medicalPurchaseOrder = new MedicalPurchaseOrder();
                $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalPurchaseOrder, $rsHeader[0]['refpurchasekey']);
                break;
            case 'quotation' :
                $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
                $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalSalesOrderQuotation, $rsHeader[0]['refquotationkey']);
                break;
            case 'invoice' :
                $medicalSalesInvoice = new MedicalSalesInvoice();
                $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalSalesInvoice, $rsHeader[0]['refinvoicekey']);
                break;
        }
    }


    function normalizeParameter($arrParam, $trim = false){
  
        $medicalRequestClaimKey = $arrParam['hidMedicalRequestClaimKey'];
        $medicalJobOrderKey = $arrParam['hidMedicalJobOrderKey'];
        $medicalSalesOrderQuotationKey = $arrParam['hidMedicalSalesOrderQuotationKey'];
        $medicalPurchaseKey = $arrParam['hidMedicalPurchaseKey'];
        $medicalSalesInvoiceKey = $arrParam['hidMedicalSalesInvoiceKey'];

        $arrParam['hidMedicalRequestClaimKey'] = 0;
        $arrParam['hidMedicalJobOrderKey'] = 0;
        $arrParam['hidMedicalSalesOrderQuotationKey'] = 0;
        $arrParam['hidMedicalPurchaseKey'] = 0;
        $arrParam['hidMedicalSalesInvoiceKey'] = 0;

        switch ($arrParam['selModule']) {
            case 'request' :
                $arrParam['hidMedicalRequestClaimKey'] = $medicalRequestClaimKey;
                break;
            case 'job' :
                $arrParam['hidMedicalJobOrderKey'] = $medicalJobOrderKey;
                break;
            case 'guaranteeLetter' :
                $arrParam['hidMedicalPurchaseKey'] = $medicalPurchaseKey;
                break;
            case 'quotation' :
                $arrParam['hidMedicalSalesOrderQuotationKey'] = $medicalSalesOrderQuotationKey;
                break;
            case 'invoice' :
                $arrParam['hidMedicalSalesInvoiceKey'] = $medicalSalesInvoiceKey;
                break;

        }
		
		$arrParam = parent::normalizeParameter($arrParam, true);
 
        return $arrParam;
    }

}
