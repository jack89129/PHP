<?php

class Overige_PrintController extends Jaycms_Controller_Action
{     
    
    public function init()
    {    
        // Optional added for consistency
        parent::init();
    }
    
    public function reportAction()
    {   
        $this->view->page_title = _t("Printen");
        $this->view->page_sub_title = _t("Hier kunt u een printoverzicht vinden ...");
        $date = $this->_getParam('kas_date', date('Y-m-d'));
        $quarter0 = date('Y').'-01-01';
        $quarter1 = date('Y').'-03-31';
        $quarter2 = date('Y').'-06-30';
        $quarter3 = date('Y').'-09-31';
        $quarter4 = date('Y').'-12-31';
        /*$from = $quarter0;
        $to = $quarter1;*/
        $from = $date;
        $to = $date;
        if ( $date < $quarter1 ) { 
            $period = "Kwartaal1";
        } else if ( $date < $quarter2 ) {
            $period = "Kwartaal2";
        } else if ( $date < $quarter2 ) {
            $period = "Kwartaal3";
        } else {
            $period = "Kwartaal4";
        }
        
        $kas_date =  strtotime($date);
        $tabs = array();
        $totals = array();
                                                             
        $invoiceModel = new InvoiceModel();
        $purchaseModel = new PurchaseModel();
        $kasModel = new KasboekModel();
        $employeeId = Utils::user()->id;   
        
        $date_from = strtotime($from);
        $date_to = strtotime($to);
        $invoices = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $purchases = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $invoices['result'] = $invoiceModel->findInvoicesForOverige('paid', $employeeId, $date_from, $date_to, $invoices['total'], $invoices['sum'], $invoices['sum_no_vat'], InvoicePaymentModel::PAYMENT_METHOD_CASH);
        $purchases['result'] = $purchaseModel->findPurchasesForOverige('paid', $date_from, $date_to, $purchases['total'], $purchases['sum'], $purchases['sum_no_vat'], PurchasePaymentModel::PAYMENT_METHOD_CASH);
        
        $this->view->invoices = $invoices;    
        $this->view->purchases = $purchases;
        setlocale(LC_ALL, 'nl_NL');
        $this->view->kas_date = strftime('%d %B %Y', $kas_date);
        $this->view->startsaldo = $kasModel->getStartBalance($from);
        $this->view->endsaldo = $kasModel->getEndBalance($to);       
        $this->view->afsch = $kasModel->getAfschrift($date);     
        $this->view->period = $period;  
        //Bankboek-Kwartaal1-23-01-2013.pdf
        $this->generatePDF('Bankboek-' . $period . '-' . date('d-m-Y', $kas_date) . '.pdf', 'D');
        die();                                  
    }
    
    protected function generatePDF($name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $content = $this->view->render('excel/report.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'L');
        $mpdf->WriteHTML($content);
        
        $mpdf->Output($name, $destination);
    }
    
    public function monthAction()
    {   
        $this->view->page_title = _t("Printen");
        $this->view->page_sub_title = _t("Hier kunt u een printoverzicht vinden ...");
        $date = $this->_getParam('kas_date', date('Y-m-d'));
        $quarter0 = date('Y').'-01-01';
        $quarter1 = date('Y').'-03-31';
        $quarter2 = date('Y').'-06-30';
        $quarter3 = date('Y').'-09-31';
        $quarter4 = date('Y').'-12-31';
        if ( $date < $quarter1 ) { 
            $period = "Kwartaal1";
        } else if ( $date < $quarter2 ) {
            $period = "Kwartaal2";
        } else if ( $date < $quarter2 ) {
            $period = "Kwartaal3";
        } else {
            $period = "Kwartaal4";
        }
        
        /*$from = date('Y-m', strtotime($date)).'-01';
        $to = date('Y-m', strtotime($date)).'-31';;
        
        $kas_date =  strtotime($date);
        $tabs = array();
        $totals = array();
                                                             
        $invoiceModel = new InvoiceModel();
        $purchaseModel = new PurchaseModel();
        $kasModel = new KasboekModel();
        $employeeId = Utils::user()->id;   
        
        $date_from = strtotime($from);
        $date_to = strtotime($to);
        $invoices = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $purchases = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $invoices['result'] = $invoiceModel->findInvoicesForOverige('paid', $employeeId, $date_from, $date_to, $invoices['total'], $invoices['sum'], $invoices['sum_no_vat'], InvoicePaymentModel::PAYMENT_METHOD_CASH);
        $purchases['result'] = $purchaseModel->findPurchasesForOverige('paid', $date_from, $date_to, $purchases['total'], $purchases['sum'], $purchases['sum_no_vat'], PurchasePaymentModel::PAYMENT_METHOD_CASH);
        
        $this->view->invoices = $invoices;    
        $this->view->purchases = $purchases;*/
        
        $kasModel = new KasboekModel();
        $employeeId = Utils::user()->id;
        
        $total=0;
        $sum=0.0;
        $sum_no_vat=0.0;
        $year_month = date('Y-m', strtotime($date));
        $kas_date =  strtotime($date);
        $from = date('Y-m', strtotime($date)).'-01';
        $to = date('Y-m', strtotime($date)).'-31';
        
        $result = $kasModel->getMonthKasboek($year_month, $total, $sum, $sum_no_vat);
        
        setlocale(LC_ALL, 'nl_NL');
        $this->view->result = $result;
        $this->view->kas_date = strftime('%B %Y', $kas_date);
        $this->view->startsaldo = $kasModel->getLatestBalance($from);
        $this->view->endsaldo = $kasModel->getLatestBalance($to);    
        $this->view->afsch = $kasModel->getAfschrift($date);     
        $this->view->period = $period;  
        
        $this->generateMonthPDF('Kasboek-maandafschriften-' . date('m-Y', $kas_date) . '.pdf', 'D');
        die();                                  
    }
    
    protected function generateMonthPDF($name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $content = $this->view->render('print/month.phtml');

        $mpdf = new mPDF('utf-8', array(297, 210));
        $mpdf->WriteHTML($content);
        
        $mpdf->Output($name, $destination);
    }
    
}

