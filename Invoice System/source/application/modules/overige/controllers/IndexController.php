<?php

class Overige_IndexController extends Jaycms_Controller_Action
{     
    private static $INVOICE_LIST_TABS = array();
    private static $INVOICE_TOTALS = array();
    
	public function init()
	{	
		parent::init();
		$this->view->page_title = _t("Bankboek");
		$this->view->page_sub_title = _t("Hier kunt u uw afschriften ingeven ...");
		$this->view->current_module = "overige";   
        
        self::$INVOICE_LIST_TABS = array();
        self::$INVOICE_LIST_TABS['all'] = _t('Alle %s', ($this->_getParam('credit') ? _t('credit-facturen') : _t('facturen')));
        self::$INVOICE_LIST_TABS['urgent'] = _t('Dringend');
        self::$INVOICE_LIST_TABS['outstanding'] = _t('Openstaand');
        self::$INVOICE_LIST_TABS['late'] = _t('Te laat');
        self::$INVOICE_LIST_TABS['unsent'] = _t('Niet verzonden');
        self::$INVOICE_LIST_TABS['paid'] = _t('Betaald');
        self::$INVOICE_LIST_TABS['sent'] = _t('Verzonden');
	}
	
	public function indexAction()
	{   
        $date = $this->_getParam('kas_date', date('Y-m-d'));
        $kas_date =  strtotime($date);
        $today = $kas_date;        
        $yesterday = strtotime(date('Y-m-d', $kas_date - 86400));       
        $tabs = array();
        $totals = array();
                                                             
        $invoiceModel = new InvoiceModel();
        $purchaseModel = new PurchaseModel();
        $kasModel = new BankboekModel();
        $employeeId = Utils::user()->id;   
        
        $date_to = $yesterday;
        $start_invoice = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $start_purchase = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $start_invoice['result'] = $invoiceModel->findInvoicesForOverige('paid', $employeeId, $date_from, $date_to, $start_invoice['total'], $start_invoice['sum'], $start_invoice['sum_no_vat']);
        $start_purchase['result'] = $purchaseModel->findPurchasesForOverige('paid', $date_from, $date_to, $start_purchase['total'], $start_purchase['sum'], $start_invoice['sum_no_vat']);
        
        $date_from = $today;
        $date_to = $today;
        $end_invoice = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $end_purchase = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $end_invoice['result'] = $invoiceModel->findInvoicesForOverige('paid', $employeeId, $date_from, $date_to, $end_invoice['total'], $end_invoice['sum'], $end_invoice['sum_no_vat']);
        $end_purchase['result'] = $purchaseModel->findPurchasesForOverige('paid', $date_from, $date_to, $end_purchase['total'], $end_purchase['sum'], $end_purchase['sum_no_vat']);
        
        $this->view->start_invoice = $start_invoice;
        $this->view->end_invoice = $end_invoice;    
        $this->view->start_purchase = $start_purchase;
        $this->view->end_purchase = $end_purchase;
        setlocale(LC_ALL, 'nl_NL');
        $this->view->start_date_str = strftime('%A %d %B %Y', $today);
        $this->view->start_date = date("Y-m-d", $today);  
        $this->view->start_balance = $kasModel->getStartBalance($date);
        $this->view->balance = $kasModel->getEndBalance($date);       
        $this->view->afsch_num = $kasModel->getAfschrift($date);   
        $this->view->page_idx = 'bank';
	}
    
    public function saveAfschAction() {
        $kasModel = new BankboekModel();
        $kas_date = $this->_getParam('kas_date');
        $afsch = $this->_getParam('afsch');
        $data = $kasModel->saveAfsch($kas_date, $afsch);
        $this->_helper->json($data);
    }
    
    public function searchAfschAction() {
        $kasModel = new BankboekModel();
        $afsch = $this->_getParam('afsch');
        $data = $kasModel->searchKasboek($afsch);
        $this->_helper->json($data);
    }
    
    public function setUnpaidInvoiceAction(){
        $invoiceModel = new InvoiceModel(); 
        $id = $this->_getParam('id');
        $invoice_number = $this->_getParam('invoice_number');
        $is_email = $this->_getParam('is_email');
        $mail_addr = $this->_getParam('mail_addr');
        $message = $this->_getParam('message');
        $kas_date = $this->_getParam('kas_date');
        $payment_method = $this->_getParam('payment_method');
        $invoice = $invoiceModel->getInvoices($invoice_number);
        $contactModel = new ContactModel();
        $invoice[0]->owner = $contactModel->getContactName($invoice[0]->contact_id);
        $invoiceModel->setPaidInvoice($invoice_number, $kas_date);
        if ( $is_email == "1" ) {
            $subject = "Invoice paid notice";
            mail($mail_addr, $mail_subject, $message,
            "From: admin@avaxo.be\r\n" .
            "Reply-To: webmaster@avaxo.be\r\n" .
            "X-Mailer: PHP/" . phpversion());
        }
        $payment = new InvoicePayment();
        $payment->model()->getAdapter()->beginTransaction();
        
        try {
            $payment->invoice_id = $id;
            $payment->amount = $invoice[0]->total_sum;
            $payment->paid_time = date("Y-m-d H:i:s", strtotime($kas_date));
            $payment->payment_method = $payment_method;
            $payment->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $id;
            $log->data = $invoice[0]->total_sum;
            $log->event = LogModel::EVENT_INVOICE_PAYMENT;
            $log->save();

            $payment->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $payment->model()->getAdapter()->rollBack();
            throw $e;
        }
        
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $id;
        $log->event = LogModel::EVENT_INVOICE_PAID;
        $log->save();
        $this->_helper->json($invoice);
    }
    
    public function setUnpaidPurchaseAction(){
        $purchaseModel = new PurchaseModel();
        $id = $this->_getParam('id');
        $purchase_number = $this->_getParam('purchase_number');
        $kas_date = $this->_getParam('kas_date');
        $purchase = $purchaseModel->getPurchase($purchase_number);
        $contactModel = new ContactModel();
        $purchase[0]->owner = $contactModel->getContactName($purchase[0]->contact_id);
        $purchaseModel->setPaidPurchase($purchase_number, $kas_date);
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
        $log->source_id = $id;
        $log->event = LogModel::EVENT_PURCHASE_PAID;
        $log->save();
        $this->_helper->json($purchase);
    }
    
    public function saveBalanceAction() {
        $date = $this->_getParam('kas_date');
        $amount = $this->_getParam('amount');
        $kasModel = new BankboekModel();
        $kasModel->setEndBalance($date, $amount);
        $this->_helper->json($date);
    }
    
    public function searchUnpaidAction(){
        $amount = $this->_getParam('amount');
        $number = $this->_getParam('number');
        if ( $amount > 0 ) {
            $invoiceModel = new InvoiceModel(); 
            $unpaid = $invoiceModel->findUnpaidAllInvoices($amount, $number);
            /*if ( count($unpaid) == 1 ) {
                $invoiceModel->setPaidInvoice($unpaid[0]->number);
            }*/
        } else if ( $amount < 0 ) {
            $purchaseModel = new PurchaseModel(); 
            $unpaid = $purchaseModel->findUnpaidAllPurchases(-1 * $amount, $number);
            /*if ( count($unpaid) == 1 ) {
                $purchaseModel->setPaidPurchase($unpaid[0]->number);
            }*/
        } else {
            $invoiceModel = new InvoiceModel(); 
            $unpaid = $invoiceModel->findUnpaidAllInvoices($amount, $number);
            $purchaseModel = new PurchaseModel(); 
            $punpaid = $purchaseModel->findUnpaidAllPurchases(-1 * $amount, $number);
            $unpaid = array_merge($unpaid, $punpaid);
        }
        $this->_helper->json($unpaid);
    }
	
	public function preDispatch() {
		$results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
		
		if ($results) {
			$products = array();
			$minstock_products = new Zend_Session_Namespace('min_stock_products');
			if (!$minstock_products->id_list) $minstock_products->id_list = array();
			
			foreach ($results as $product) {
				if (in_array($product->id, $minstock_products->id_list)) continue;
				$products[] = $product;
			}
			
			if ($products) $this->view->show_box = true;
			
			$this->view->minstock_products = $results;
		}
	}
}

