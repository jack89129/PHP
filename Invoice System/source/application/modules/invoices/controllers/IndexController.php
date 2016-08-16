<?php

class Invoices_IndexController extends Jaycms_Controller_Action
{

    private static $INVOICE_LIST_TABS = array();
    private static $INVOICE_TOTALS = array();

 	public function init()
    {
        parent::init();
        self::$INVOICE_LIST_TABS = array();

        $proforma = (int) $this->getRequest()->getParam('proforma', 0);

        if( $proforma ){
            self::$INVOICE_LIST_TABS['all'] = _t('Alle offertes');
            self::$INVOICE_LIST_TABS['unsent'] = _t('Concepten');
            //self::$INVOICE_LIST_TABS['sent'] = _t('Verzenden');
            self::$INVOICE_LIST_TABS['proforma_open'] = _t('Openstaand');
            self::$INVOICE_LIST_TABS['proforma_accepted'] = _t('Geaccepteerd');
            self::$INVOICE_LIST_TABS['proforma_denied'] = _t('Geweigerd');
            self::$INVOICE_LIST_TABS['proforma_invoice'] = _t('Gefactureerd');
            self::$INVOICE_LIST_TABS['proforma_archive'] = _t('Gearchiveerd');
            self::$INVOICE_LIST_TABS['late'] = _t('Te laat');
            self::$INVOICE_LIST_TABS['urgent'] = _t('Dringend');
            //self::$INVOICE_LIST_TABS['proforma_expired'] = _t('Verlopen');

            $this->view->page_title = _t("Offertes");
            $this->view->page_sub_title = _t("Overzicht, offertes maken en meer...");
            $this->view->current_module = "offers";
        }else{
            self::$INVOICE_LIST_TABS['all'] = _t('Alle %s', ($this->_getParam('credit') ? _t('credit-facturen') : _t('facturen')));
            self::$INVOICE_LIST_TABS['unsent'] = _t('Concepten');
            self::$INVOICE_LIST_TABS['outstanding'] = _t('Openstaand');
            self::$INVOICE_LIST_TABS['late'] = _t('Te laat');
            self::$INVOICE_LIST_TABS['urgent'] = _t('Dringend');
            self::$INVOICE_LIST_TABS['judge'] = _t('Aanmaning');
            self::$INVOICE_LIST_TABS['credit'] = _t('Credit Facturen');
            self::$INVOICE_LIST_TABS['paid'] = _t('Betaald');
            //self::$INVOICE_LIST_TABS['sent'] = _t('Verzenden');

            self::$INVOICE_TOTALS['outstanding'] = _t('Openstaand');
            self::$INVOICE_TOTALS['paid'] = _t('Betaald');
            self::$INVOICE_TOTALS['late'] = _t('Verlopen');

            $this->view->page_title = $this->_getParam('credit') ? _t('Credit-Facturen') : _t("Facturen");
            $this->view->page_sub_title = _t("Overzicht, %s maken en meer...", ($this->_getParam('credit') ? _t('credit-facturen') : _t('facturen')));
            $this->view->current_module = "invoices";
        }   
    }

    public function activeTabAction(){
    	$session = new Zend_Session_Namespace('invoices-tab');

    	$session->tab = $this->_getParam('tab');
    	$this->_helper->json(array('tab' => $session->tab));
    }
    
    public function indexAction(){               
        $contactId = (int) $this->_getParam('contact_id');
    	$pages = (array) $this->_getParam('pages', array());
        $proforma = (int) $this->getRequest()->getParam('proforma', 0);
        $credit = (int) $this->getRequest()->getParam('credit', 0);
        $date_from =  strtotime($this->_getParam('date_from', 0));
        $date_to =  strtotime($this->_getParam('date_to', 0));
        $date = (string) $this->_getParam('date', '');
        $is_from_contact = $this->_getParam('from_contact');
        $per_page = 20;

        Utils::activity('index', $proforma ? 'offer' : 'invoice');   

        if( ($proforma && !Utils::user()->can('offer_view')) || (!$proforma && !Utils::user()->can('invoice_view')) ){
            throw new Exception(_t("Access denied!"));
        }

        if( $date ){
            list($date_from, $date_to) = Utils::name2date($date);
        }    

    	$session = new Zend_Session_Namespace('invoices-tab');
    	$tab = $this->_getParam('tab');
    	$tab = $tab ? $tab : $session->tab;
        $tab = array_key_exists($tab, self::$INVOICE_LIST_TABS) ? $tab : '' ;
    	$tab = $tab ? $tab : reset(array_keys(self::$INVOICE_LIST_TABS));

    	
    	$contactModel = new ContactModel();
    	$contacts = $contactModel->findAll();
    	   	    	
    	$tabs = array();
        $cacheId = md5('invoice_list_' . $tab . $per_page . implode('', $pages));
    	$cache = Zend_Registry::get('cache');
    	$totals = array();

    	$cache->clean();
    	if( ($tabs = $cache->load($cacheId)) === false ){
    		$invoiceModel = new InvoiceModel();
    		$tabs = array();
            
            if ( empty($is_from_contact) ) {
                foreach( self::$INVOICE_LIST_TABS as $key => $label ){
                    $tabs[$key] = array( 'label' => $label, 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);
	    		    $page = (int) array_key_exists($key, $pages) ? $pages[$key] : 0 ;
                    $employeeId = Utils::user()->id;
                    if ( $key == 'credit' ) {
                        $tabs[$key]['result'] = $invoiceModel->findInvoices('all', $contactId, $employeeId, $proforma, 1, $date_from, $date_to, $tabs[$key]['total'], $tabs[$key]['sum'], $tabs[$key]['sum_no_vat'], $per_page, $page);
                    } else {
	    		        $tabs[$key]['result'] = $invoiceModel->findInvoices($key, $contactId, $employeeId, $proforma, 0, $date_from, $date_to, $tabs[$key]['total'], $tabs[$key]['sum'], $tabs[$key]['sum_no_vat'], $per_page, $page);
                    }
	    		    $tabs[$key]['page'] = $page;
	    		    $tabs[$key]['per_page'] = $per_page;
	    	    }    

                foreach( self::$INVOICE_TOTALS as $key => $label ){
                    if( array_key_exists($key, $tabs) ){
                        $totals[$key] = array('label' => $label, 'sum' => $tabs[$key]['sum'], 'sum_no_vat' => $tabs[$key]['sum_no_vat']);
                    }
                }
            } else {
                // should be consider offer 
                $tab_list = self::$INVOICE_LIST_TABS;
                $total_list = self::$INVOICE_TOTALS;
                unset($tab_list['all']);
                unset($tab_list['unsent']);
                if ( intval($is_from_contact) != 2 ) {
                    unset($tab_list['paid']);
                    unset($total_list['paid']);
                    $tab = 'sent';
                } else {
                    $tab_list['almost'] = 'Alle Facturen';
                    $tab = 'almost';
                }
                
                foreach( $tab_list as $key => $label ){
                    $tabs[$key] = array( 'label' => $label, 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);
                    $page = (int) array_key_exists($key, $pages) ? $pages[$key] : 0 ;
                    $employeeId = Utils::user()->id;
                    $tabs[$key]['result'] = $invoiceModel->findInvoices($key, $contactId, $employeeId, $proforma, $credit, $date_from, $date_to, $tabs[$key]['total'], $tabs[$key]['sum'], $tabs[$key]['sum_no_vat'], $per_page, $page);
                    $tabs[$key]['page'] = $page;
                    $tabs[$key]['per_page'] = $per_page;
                }

                foreach( $total_list as $key => $label ){
                    if( array_key_exists($key, $tabs) ){
                        $totals[$key] = array('label' => $label, 'sum' => $tabs[$key]['sum'], 'sum_no_vat' => $tabs[$key]['sum_no_vat']);
                    }
                }
            }
	    	
	    	$cache->save($tabs, null, array(), 60*2);
    	}
        
        $invoice_curnum = SettingsModel::getInvoiceCurrentNum();
        $offer_curnum = SettingsModel::getProformaCurrentNum();
        $result = $invoiceModel->fetchAll(array('proforma' => $proforma));
        $is_new = false;
        if ( ((!$proforma && $invoice_curnum == 1) || ($proforma && $offer_curnum == 1)) && count($result) == 0 ) $is_new = true;

    	$this->view->contacts = $contacts;
    	$this->view->tab = $tab;
    	$this->view->tabs = $tabs;
        $this->view->totals = $totals;
    	$this->view->contact_id = $contactId;
        $this->view->proforma = $proforma;
        $this->view->credit = $credit;
        $this->view->date_from = $date_from ? date(Constants::DATE_FORMAT, $date_from) : '';
        $this->view->date_to = $date_to ? date(Constants::DATE_FORMAT, $date_to) : '';
        $this->view->date = $date;
        $this->view->is_new = $is_new;
    }
    
	public function viewAction()
    {   
    	$id = (int) $this->_getParam('id');
        $download = (int) $this->_getParam('download');

		$invoice = new Invoice($id);

        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        if( ($invoice->proforma && !Utils::user()->can('offer_view')) || (!$invoice->proforma && !Utils::user()->can('invoice_view')) ){
            throw new Exception(_t("Access denied!"));
        }

        if( $invoice->isNew() ){
            $this->_redirect('/' . ($invoice->proforma ? 'offers' : 'invoices') . '/index/new/id/' . $invoice->id);
            return;
        }

        Utils::activity('view', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

        if( !$invoice->contact ){
            throw new Exception(_t("Contact not found!"));
        }

        if( $invoice->parent_invoice_id && !$invoice->parent ){
            throw new Exception(_t("Parent invoice not found!"));
        }
        
        $intro = str_replace('{invoice_number}', $invoice->number, $invoice->intro_pattern);
        $intro = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
        $intro = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
        $intro = str_replace('{offer_number}', $invoice->number, $intro);        
        $intro = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
        $intro = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
        $intro = str_replace('{client_firstname}', $invoice->contact->firstname, $intro);
        $intro = str_replace('{client_lastname}', $invoice->contact->lastname, $intro);
        $invoice->intro = $intro;
        
        $notice = str_replace('{invoice_number}', $invoice->number, $invoice->notice_pattern);
        $notice = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $notice);
        $notice = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $notice);
        $notice = str_replace('{offer_number}', $invoice->number, $notice);        
        $notice = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $notice);
        $notice = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $notice);
        $notice = str_replace('{client_firstname}', $invoice->contact->firstname, $notice);
        $notice = str_replace('{client_lastname}', $invoice->contact->lastname, $notice);
        $invoice->notice = $notice;

		$this->view->invoice = $invoice;
        $this->view->download = $download;
        $this->view->hltc = (int) $this->_getParam('hltc', -1);
    }
    
    public function pdfstockAction(){
        $this->generateStockPDF('dagoverzicht.pdf', 'D');
        die();
    }
    
    protected function generateStockPDF($name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $today = date('Y-m-d');
        setlocale(LC_ALL, 'nl_NL');
        $this->view->curday = strftime('%d %B %Y', strtotime($today));
        $user = Utils::user();
        $this->view->emp_name = $user->firstname . ' ' . $user->lastname;
        
        $invoiceModel = new InvoiceModel();
        $packModel = new PackModel();
        $receiptModel = new ReceiptModel();
        
        // get paid invoice list
        $employeeId = $user->id;  
        $date_from = strtotime($today);
        $date_to = strtotime($today);
        
        $packlist = $packModel->findPacksForPDF($user->id, $today);
        $planlist = $receiptModel->findReceiptsForPDF($user->id, $today);
        $salelist = $invoiceModel->findInvoiceForPDF($user->id, $today);
        
        $result = array_merge($packlist, $planlist, $salelist);
        
        $list = array();
        foreach ( $result as $row ) {
            $a = $packlist["id$row[id]"];
            $b = $planlist["id$row[id]"];
            $c = $salelist["id$row[id]"];
            if ( !empty($a) ) {
                if ( !empty($b) ) {
                    if ( !empty($c) ) {
                        $list[] = $a + $b + $c;
                    } else {
                        $list[] = $a + $b;
                    }
                } else {
                    if ( !empty($c) ) {
                        $list[] = $a + $c;
                    } else {
                        $list[] = $a;
                    }
                }
            } else {
                if ( !empty($b) && !empty($c) ) {
                $list[] =  $b + $c;
                } else if ( !empty($b) ) {
                    $list[] = $b;
                } else {
                    $list[] = $c;
                }
            }
            
        }
        
        $speciallist = $receiptModel->findReceiptsForPDF($user->id, date('Y-m-d', strtotime($today)+86400));
        
        $this->view->generallist = $list;
        $this->view->speciallist = $speciallist;

        $content = $this->view->render('index/pdfstock.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        //$mpdf->SetHTMLHeader($header);
        //$mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);
        
        $name = 'laad-en-bestellijst_' . $this->view->emp_name . '_' . $today . '.pdf';
        
        $mpdf->Output($name, $destination);
    }
    
    public function pdfdayAction(){
        $this->generateDailyPDF('dagoverzicht.pdf', 'D');
        die();
    }
    
    protected function generateDailyPDF($name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $today = date('Y-m-d');
        setlocale(LC_ALL, 'nl_NL');
        $this->view->curday = strftime('%d %B %Y', strtotime($today));
        $user = Utils::user();
        $this->view->emp_name = $user->firstname . ' ' . $user->lastname;
        
        $invoiceModel = new InvoiceModel();
        
        // get paid invoice list
        $employeeId = $user->id;  
        $date_from = strtotime($today);
        $date_to = strtotime($today);
        $paid = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $paid['result'] = $invoiceModel->findPaidInvoicesForPDF('paid', $employeeId, $date_from, $date_to, $paid['total'], $paid['sum'], $paid['sum_no_vat']);
        $this->view->paidlist = $paid;
        
        // get open invoice list
        $opening = array( 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);     
        $opening['result'] = $invoiceModel->findInvoicesForPDF('outstanding', null, $employeeId, 0, 0, $date_from, $date_to, $opening['total'], $opening['sum'], $opening['sum_no_vat']);
        $this->view->openlist = $opening;

        $content = $this->view->render('index/pdfday.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        //$mpdf->SetHTMLHeader($header);
        //$mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);
        
        $name = 'dagoverzicht_' . $this->view->emp_name . '_' . $today . '.pdf';
        
        $mpdf->Output($name, $destination);
    }
    
    public function pdfAction(){
    	$id = $this->_getParam('id');
        $invoice = new Invoice($id);
        Utils::activity('pdf', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);
		$this->generatePDF($id, $invoice->getFutureNumber() . '.pdf', 'D');
    	die();
    }
    
    public function sendInvoiceFillAction(){
    	$invoice = $this->_getParam('invoice');
        $contactParam = $this->_getParam('contact');  
    	    	
		$email = '';
		$name  = '';
		$number = '';
		$date = '';
        $contactId = null;
        if ( !empty($invoice['contact_id']) ) {
            $contactId = $invoice['contact_id'];
        }

        $invoice = new Invoice($invoice['id']);

		if( !$invoice->exists() ){
			throw new Exception(_t("Invoice not found!"));
		}

        if( $invoice->contact_id && !$invoice->contact ){
            throw new Exception(_t("Contact not found!"));
        }
        
        $result = array();
        $result['company_name'] = "";
        if ( $invoice->contact_id == 0 && !$invoice->contact ) {
            $contact = new Contact($contactId);
            $contact->load($contactParam);
            $contact->id = null ;
            $contact->save();              
            $invoice->contact_id = intval($contact->id);
            $result['company_name'] = $contact->company_name;
            $invoice->save();
        }
        
        $result['contact_id'] = $invoice->contact_id;

    	if( $invoice->contact ){
	    	$email = $invoice->contact->email_address;
	    	$name = $invoice->contact->name;
    	}
    	
    	$number = $invoice->number;
    	$date = date(Constants::DATE_FORMAT, $invoice->expire_time);
    	
    	$result['email'] = $email;
        
        $settingModel = new SettingsModel();
        //$empSettingModel = new EmpSettingModel();
        
        $user = new Zend_Session_Namespace('user');
        $emp_id = $user->id;
        
    	$result['subject'] = '';
        if ( $invoice->isProforma() ) {
            $result['subject'] = SettingsModel::getProformaDefaultEmailSubject();
        } else {
            $status = $invoice->getInvoiceStatusKey();
            if ( $status == 'late' ) {
                $result['subject'] = SettingsModel::getInvoiceLateEmailSubject();
                $result['step'] = 1;
            } else if ( $status == 'urgent') {
                $result['subject'] = SettingsModel::getInvoiceUrgentEmailSubject();  
                $result['step'] = 2;
            } else if ( $status == 'judge') {
                $result['subject'] = SettingsModel::getInvoiceJudgeEmailSubject();  
                $result['step'] = 3;
            } else {
                $result['subject'] = SettingsModel::getInvoiceDefaultEmailSubject();
            }
        }
        if ( $result['subject'] == '' ) $result['subject'] = '&nbsp;';
        $result['subject'] = str_replace('{client_number}', $invoice->contact->number, $result['subject']);
        $result['subject'] = str_replace('{client_firstname}', $invoice->contact->firstname, $result['subject']);
        $result['subject'] = str_replace('{client_lastname}', $invoice->contact->lastname, $result['subject']);
        $result['subject'] = str_replace('{invoice_number}', $invoice->getFutureNumber(), $result['subject']);
        $result['subject'] = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $result['subject']);
        $result['subject'] = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $result['subject']);
        $result['subject'] = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $result['subject']);
        $result['subject'] = str_replace('{offer_number}', $invoice->getFutureNumber(), $result['subject']);
        $result['subject'] = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $result['subject']);
        $result['subject'] = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $result['subject']);
        $result['subject'] = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $result['subject']);
        $result['subject'] = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $result['subject']);
        $result['subject'] = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $result['subject']);
        $result['subject'] = str_replace('{bank_loc}', SettingsModel::getInvoiceProviderBankloc(), $result['subject']);
        $result['subject'] = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $result['subject']);
        $result['subject'] = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $result['subject']);
        $result['subject'] = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $result['subject']);
        $status = $invoice->getInvoiceStatusKey();
    	$result['body'] = $this->emailBody($name, $number, $date, $invoice->isProforma(), $status, $invoice->id);
    	$this->_helper->json($result);
    }
    
    protected function emailBody($name, $number, $date, $proforma, $status, $invoice_id){
        $settingModel = new SettingsModel();
        $user = new Zend_Session_Namespace('user');
        $emp_id = $user->id;
        
    	$template = $proforma ? SettingsModel::getProformaDefaultEmailBody() : SettingsModel::getInvoiceDefaultEmailBody();
        
        if ( $proforma ) {
            $template = SettingsModel::getProformaDefaultEmailBody();
        } else {
            if ( $status == 'late' ) {
                $template = SettingsModel::getInvoiceLateEmailBody();
            } else if ( $status == 'urgent' ) {
                $template = SettingsModel::getInvoiceUrgentEmailBody();
            } else if ( $status == 'judge' ) {
                $template = SettingsModel::getInvoiceJudgeEmailBody();
            } else {
                $template = SettingsModel::getInvoiceDefaultEmailBody();
            }
        }
        
        $invoice = new Invoice($invoice_id);
    	$template = str_replace('{name}', $name, $template);
    	$template = str_replace('{number}', $number, $template);
        $template = str_replace('{date}', $date, $template);
        $template = str_replace('{client_number}', $invoice->contact->number, $template);
        $template = str_replace('{client_firstname}', $invoice->contact->firstname, $template);
    	$template = str_replace('{client_lastname}', $invoice->contact->lastname, $template);
        $template = str_replace('{invoice_number}', $invoice->getFutureNumber(), $template);
        $template = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $template);
        $template = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $template);
        $template = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $template);
        $template = str_replace('{offer_number}', $invoice->getFutureNumber(), $template);
        $template = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $template);
        $template = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $template);
        $template = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $template);
        $template = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $template);
        $template = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $template);
        $template = str_replace('{bank_location}', SettingsModel::getInvoiceProviderBankloc(), $template);
        $template = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $template);
        $template = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $template);
        $template = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $template);
    	return $template;
    }
    
    public function finalAction(){
    	$invoice = $this->_getParam('invoice');
		$send = $this->_getParam('send', 0);

		$invoice = new Invoice($invoice['id']);
		
		if( !$invoice->exists() ){
			throw new Exception(_t("Invoice not found!"));
		}



        if( $invoice->status != InvoiceModel::STATUS_FINAL ){
            $this->finalize($invoice->id);
        }

        $download = '';

		if( $send ){
			$this->email($invoice->id, $this->_getParam('email'), $this->_getParam('subject'), $this->_getParam('body'), $this->pdf($invoice->id, $invoice->number));
		}else{
            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $invoice->id;
            $log->event = LogModel::EVENT_INVOICE_SENT_PERSONAL;
            $log->save();

            $download = '?download=1';
        }

        if( $invoice->isProforma() ){
		    $this->_helper->json(array('redirect' => $this->view->baseUrl() . "/offers/index/view/id/" . $invoice->id . $download));
        }else{
            $this->_helper->json(array('redirect' => $this->view->baseUrl() . "/invoices/index/view/id/" . $invoice->id . $download));
        }
    }

    private function finalize($id){
        $invoice = new Invoice($id);

        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        $invoice->finalize();         
    }
    
    public function emailAction(){
    	$invoice = $this->_getParam('invoice');
    	$email = $this->_getParam('email');
    	$subject = $this->_getParam('subject');
    	$body = $this->_getParam('body');              

        $invoice = new Invoice($invoice['id']);

        if( !$invoice->exists() ){
            throw new Exception(_t('Invoice not found!'));
        }                                
        if( $invoice->status != InvoiceModel::STATUS_FINAL ){
            $this->finalize($invoice->id);
        }
        
        $subject = str_replace('<br>', '', $subject);          
    	$this->email($invoice->id, $email, $subject, $body, $this->pdf($invoice->id));
        
        $status = $invoice->getInvoiceStatusKey();
        $step = 0;
        if ( $status == 'late' ) {
            $step = 1;
        } else if ( $status == 'urgent' ) {
            $step = 2;
        }
        
        if ( $step != 0 ) {
            $invoiceModel = new InvoiceModel();
            $invoiceModel->setStepInvoice($invoice->id, $step);
        }
        
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        if ( $status == 'late' ) {
            $log->event = LogModel::EVENT_INVOICE_LATE;
            $log->save();
        } else if ( $status == 'urgent' ) {
            $log->event = LogModel::EVENT_INVOICE_URGENT;
            $log->save();
        } else if ( $status == 'judge' ) {
            $log->event = LogModel::EVENT_INVOICE_JUDGE;
            $log->save();
        }
        
    	$this->_helper->json(array('success' => '1'));
    }
    
    protected function pdf($id){
        $invoice = new Invoice($id);
    	$name = $this->pdfName($id, $invoice->number);
    	$this->generatePDF($id, $name, 'F');
    	return $name;
    }
    
    protected function pdfName($id, $number){
   		$file = tempnam(sys_get_temp_dir(), 'factuur-');
    	//$name = dirname($file) . '/factuur-' . $id . '-' . time() . '.pdf';
        $name = dirname($file) . '/' . $number . '.pdf';
    	if( !rename($file, $name) ){
    		$name = $file;
    	}
    	
    	return $name;
    }
    
    protected function email($id, $email, $subject, $body, $pdf){
    	$invoice = new Invoice($id);
    	
    	if( !$invoice->exists() ){
    		throw new Exception(_t("Invoice not found!"));
    	}

        Utils::activity('email', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

        $validator = new Zend_Validate_EmailAddress();

        if( !$validator->isValid($email) ){
            throw new Exception(_t('Invalid email address!'));
        }

        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        $log->data = $email;
        $log->event = LogModel::EVENT_INVOICE_SENT_EMAIL;
        $log->save();
        
        $subject = str_replace('<br>', '', $subject);
    	
    	// email 
    	$mail = Mail::factory();
    	$mail->setSubject($subject);
    	$mail->addTo($email);
    	$mail->setBodyHtml($body);
    	$mail->createAttachment(	file_get_contents($pdf), 'application/pdf',
    								Zend_Mime::DISPOSITION_ATTACHMENT,
    								Zend_Mime::ENCODING_BASE64,
    								basename($pdf));

        foreach( $invoice->annexes as $annex ){
            $mail->createAttachment( file_get_contents($annex->toPath()), mime_content_type($annex->toPath()),
                                     Zend_Mime::DISPOSITION_ATTACHMENT,
                                     Zend_Mime::ENCODING_BASE64,
                                     basename($annex->toPath()));
        }

    	$mail->send();        
    }
    
    protected function generatePDF($id, $name, $destination){
    	require_once('MPDF/mpdf.php');
    	$this->_helper->layout()->disableLayout();

		$invoice = new Invoice($id);
				
		if( !$invoice->exists() ){
			throw new Exception(_t("Invoice not found!"));
		}

        if( $invoice->parent_invoice_id && !$invoice->parent ){
			throw new Exception(_t("Cannot find invoice parent invoice!"));
		}
                                                                                            
        $intro = str_replace('{invoice_number}', $invoice->number, $invoice->intro_pattern);
        $intro = str_replace('{offer_number}', $invoice->number, $intro);        
        $intro = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
        $intro = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
        $intro = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
        $intro = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
        $intro = str_replace('{client_firstname}', $invoice->contact->firstname, $intro);
        $intro = str_replace('{client_lastname}', $invoice->contact->lastname, $intro);
        $invoice->intro = $intro;
        
        $notice = str_replace('{invoice_number}', $invoice->number, $invoice->notice_pattern);
        $notice = str_replace('{offer_number}', $invoice->number, $notice); 
        $notice = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $notice);
        $notice = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $notice);       
        $notice = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $notice);
        $notice = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $notice);
        $notice = str_replace('{client_firstname}', $invoice->contact->firstname, $notice);
        $notice = str_replace('{client_lastname}', $invoice->contact->lastname, $notice);
        $invoice->notice = $notice;
		
		$this->view->invoice = $invoice;

    	$header = $this->view->render('index/pdf/header.phtml');
    	$footer = $this->view->render('index/pdf/footer.phtml');
    	$content = $this->view->render('index/pdf.phtml');   

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
    	//$mpdf->SetHTMLHeader($header);
    	$mpdf->SetHTMLFooter($footer);
		$mpdf->WriteHTML($content);
		
		$mpdf->Output($name, $destination);
    }
    
	public function newAction()
    {           
    	$id = (int) $this->_getParam('id');
        $contact_id = (int) $this->_getParam('contact_id');
        $proforma = (int) $this->_getParam('proforma', 0);
        $credit = (int) $this->_getParam('credit', 0);
        $force_edit = (int) $this->_getParam('force_edit', 0);

		$invoice = new Invoice($id);
        $products = new Product();
        $products = $products->findAll(array(array('deleted = ?', '0')));
        $contacts = new Contact();
        //$contacts = $contacts->findAll();

        if( ($proforma && !Utils::user()->can('offer_edit')) || (!$proforma && !Utils::user()->can('invoice_edit')) ){
            throw new Exception(_t("Access denied!"));
        }

		if( $invoice->exists() ){
            Utils::activity('edit', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);
			// old invoice   
			if( !$force_edit && $invoice->isFinal() ){
				throw new Exception(_t("Invoice is final and cannot be edited!"));
			}

			if( $invoice->parent_invoice_id && !$invoice->parent ){
				throw new Exception(_t("Cannot find parent invoice!"));
			}
			
		}else{                               
            $invoice->contact_id = $contact_id;
            $invoice->proforma = $proforma;
            $invoice->credit = $credit;
            $invoice->create();
            
            Utils::activity('new', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

            $this->_redirect('/' . ($invoice->isProforma() ? 'offers' : 'invoices') . '/index/edit/id/' . $invoice->id);
		}

        if( !$invoice->products ){
            $product = new InvoiceProduct();
            $invoice->addProduct($product);
        }                                          
        
        if ( !$invoice->intro ) {            
            $intro = "";
            if ( $invoice->isProforma() ) {
                $intro = SettingsModel::getProformaDefaultIntro();
            } else {
                $intro = SettingsModel::getInvoiceDefaultIntro();
            }
                                                                                                  
            $intro = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $intro);
            $intro = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $intro);
            $intro = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $intro);
            $intro = str_replace('{bank_location}', SettingsModel::getInvoiceProviderBankloc(), $intro);
            $intro = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $intro);
            $intro = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $intro);
            $intro = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $intro);
            $invoice->intro_pattern = $intro;
            
            $intro = str_replace('{offer_number}', $invoice->number, $intro);
            $intro = str_replace('{invoice_number}', $invoice->number, $intro);
            $intro = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
            $intro = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $intro);
            $intro = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $intro);
            $intro = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
            $intro = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $intro);
            //$contacts->intro = $intro;                                                                              
            $invoice->intro = $intro;
        }
		
		$this->view->invoice = $invoice;
		$this->view->contacts = $contacts;
		$this->view->products = $products;
        $this->view->force_edit = $force_edit;
        $this->view->b2b_end_date = date('d-m-Y', strtotime('+ ' . SettingsModel::getInvoiceB2BPaymentTerm() . ' DAYS', $invoice->invoice_time));
        $this->view->b2c_end_date = date('d-m-Y', strtotime('+ ' . SettingsModel::getInvoiceB2CPaymentTerm() . ' DAYS', $invoice->invoice_time));
    }
    
    public function contactChangedAction(){
    	$id = $this->_getParam('id');
    	$number = _t('');
        $contact = new Contact($id);

    	if( $contact->exists() ){
	    	$number = $contact->number;
    	}
    	
    	$this->_helper->json(array('number'=>$number));
    }
    
    public function productChangedAction(){
    	$id = $this->_getParam('id');
    	$index = $this->_getParam('index');

    	$product = new Product($id);

    	if( !$product->exists() ){
    		throw new Exception(_t("Product not found!"));
    	}
    	
    	$this->_helper->json(array('product' => (object) $product->data(), 'index' => $index));
    }
    
    public function addRowAction(){
    	$this->_helper->layout()->disableLayout();
    	$index = $this->_getParam('index');
        $intro = $this->_getParam('intro');
        $invoiceProduct = new InvoiceProduct();

    	$products = new Product();
		$products = $products->findAll(array(array('deleted=?', 0)));
				
		$this->view->product = $invoiceProduct;
		$this->view->products = $products;
		$this->view->invoice_row_index = $index;
        $this->view->contact_intro = $intro;
    	$this->renderScript('index/new/invoice-row.phtml');
    }
    
    public function productAutocompleteAction(){
    	$productModel = new ProductModel();
    	$products = $productModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
    	$this->_helper->json($products);
    }

    public function categoryAutocompleteAction(){
        $tagModel = new TagModel();
        $categoryModel = new TagCategoryModel();
        $tags = $tagModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'), TagCategoryModel::TYPE_PURCHASE);
        $this->_helper->json($tags);
    }

    public function saveAction(){
        $id = $this->_getParam('id');
    	$final = $this->_getParam('final');
    	$invoiceParam = $this->_getParam('invoice');
        $vatIncludedParam = $this->_getParam('vat_included', 0) ? true : false ;
    	$invoiceParam['invoice_time'] = date(Constants::MYSQL_DAY_FORMAT, strtotime($invoiceParam['invoice_time']));
        $invoiceParam['expire_time'] = date(Constants::MYSQL_DATE_FORMAT, strtotime($invoiceParam['expire_time']));
    	$productParam = $this->_getParam('product', array());
        $contactParam = $this->_getParam('contact', array());
        
    	$ordParam = $this->_getParam('ord', array());
        $force_edit = (int) $this->_getParam('force_edit', 0);
    	
    	$orderedProducts = array();
    	foreach( $ordParam as $ord ){
    		$orderedProducts[] = $productParam[$ord];
    	}

    	
    	$invoice = new Invoice($invoiceParam['id']);

    	if( !$force_edit && $invoice->exists() && $invoice->isFinal() ){
    		throw new Exception(_t("Invoice is final and it cannot be edited!"));
    	}
 
        $status = $invoice->status == InvoiceModel::STATUS_FINAL ? InvoiceModel::STATUS_FINAL : InvoiceModel::STATUS_NEW;
   		$invoice->load($invoiceParam);
   		$invoice->total_sum = 0;
   		$invoice->vat_sum = 0;
   		$invoice->status = $status;
   		
    	$newProducts = array();

        $invoice_total_excl_vat = 0.0;
        $invoice_total_incl_vat = 0.0;

    	foreach( $orderedProducts as $key => $product ){
            $product['price'] = str_replace('.', '', $product['price']);
            $product['price'] = str_replace(',', '.', $product['price']);
            $product['qty'] = str_replace('.', '', $product['qty']);
            $product['qty'] = str_replace(',', '.', $product['qty']);
            if( $product['qty'] < 1 || !$product['description'] ){
                continue;
            }

            if( $vatIncludedParam ){
                $product['price'] = Utils::removeVAT($product['price'], $product['vat']);
            }

    		$product['id'] = 0;
    		$product['total_sum'] = $product['qty'] * $product['price'];

            $invoice_total_incl_vat += $product['total_sum'] + $product['total_sum'] * ($product['vat']/100);
            $invoice_total_excl_vat += $product['total_sum'] - $product['total_sum'] * ($product['discount']/100) ;

            $invoice->vat_sum += $product['total_sum'] * ($product['vat']/100);
            $invoice->discount_sum += $product['total_sum'] * ($product['discount']/100);

            $product['total_sum'] -= $product['total_sum'] * ($product['discount']/100);
            $product['description'] = Utils::strip_bad_tags($product['description']);
            $product['description'] = str_replace('&nbsp;', '', $product['description']);
            $product['description'] = str_replace('<br>', '', $product['description']);

    	    $newProducts[] = (object) $product;
    	}


        $invoice->discount_sum += $invoice_total_excl_vat * ($invoice->discount/100);
        $invoice->total_sum = $invoice_total_incl_vat - $invoice->discount_sum;
        $invoice->total_excl_vat = $invoice_total_excl_vat;

        $products = $invoice->products;

    	foreach( $products as $index => $product ){
    		if( array_key_exists($index, $newProducts) ){
    			$newProducts[$index]->id = $product->id;
                $product = new InvoiceProduct();
                $product->load($newProducts[$index]);
    			$products[$index] = $product;
    			unset($newProducts[$index]);
    		}else{
    			$product->delete();
    			unset($products[$index]);
    		}
    	}
    	
    	foreach( $newProducts as $product ){
            $newProduct = new InvoiceProduct();
            $newProduct->load($product);
    		$products[] = $newProduct;
    	}

    	
    	$invoice->model()->getAdapter()->beginTransaction();

    	try {           

            if( ($this->_getParam('save_contact') && $invoice->contact_id) || !$invoice->contact_id ){
                $contact = new Contact($invoice->contact_id);
                $contact->load($contactParam);
                $contact->id = $invoice->contact_id ? $invoice->contact_id : null ;
                $contact->save();              
                $invoice->contact_id = $contact->id;
                if ( $contact->is_b2b == 1 ) {
                    $invoice->expire_time = date(Constants::MYSQL_DATE_FORMAT, strtotime('+ ' . SettingsModel::getInvoiceB2BPaymentTerm() . ' DAYS', time()));
                }
            }                    

            $new = !$invoice->id;
            if ( $invoice->contact ) {
                $intro = str_replace('{client_number}', $invoice->contact->number, $invoice->intro);
                $intro = str_replace('{client_firstname}', $invoice->contact->firstname, $intro);
                $intro = str_replace('{client_lastname}', $invoice->contact->lastname, $intro);
                $invoice->intro = $intro;
                $notice = str_replace('{client_number}', $invoice->contact->number, $invoice->notice);
                $notice = str_replace('{client_firstname}', $invoice->contact->firstname, $notice);
                $notice = str_replace('{client_lastname}', $invoice->contact->lastname, $notice);
                $invoice->notice = $notice;
            }
    		$invoice->save();
    		
    		foreach( $products as $product ){
    			$product->invoice_id = $invoice->id;
    			$product->save();
    		}

    		$invoice->model()->getAdapter()->commit();
    	}catch( Exception $e ){
    		$invoice->model()->getAdapter()->rollBack();
    		throw $e;
    	}
    	
    	if( $final ){
   			$this->_forward('final');
   			return;
   		}
    	
    	
    	$this->_helper->json((object) $invoice->data());
    }
    
	public function editAction(){
		$this->_forward('new');   	
    }
    
    public function payAction(){
    	$invoice = $this->_getParam('invoice');
    	$this->pay($invoice['id']);
    	$this->_helper->json(array('success' => '1'));
    }
    
    public function payCreditAction(){
        $invoice = $this->_getParam('invoice');
        $parentid = $this->_getParam('parentid');
        $this->pay($invoice['id']);
        $invoice = new Invoice($parentid);
        if ( $invoice->paid_time == '0000-00-00 00:00:00' )
            $this->pay($parentid);
        $this->_helper->json(array('success' => '1'));
    }
    
    protected function payCredit($id){
        $invoice = new Invoice($id);

        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }
        
        if( $invoice->paid_time ){
            throw new Exception(_t("Invoice already paid!"));
        }

        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->paid_time = time();
        $invoice->save('paid_time');

        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        $log->event = LogModel::EVENT_INVOICE_PAID;
        $log->save();
    }
    
    protected function pay($id){
    	$invoice = new Invoice($id);

    	if( !$invoice->exists() ){
    		throw new Exception(_t("Invoice not found!"));
    	}
    	
    	if( $invoice->paid_time ){
    		throw new Exception(_t("Invoice already paid!"));
    	}

        if( $invoice->status != InvoiceModel::STATUS_FINAL ){
            throw new Exception(_t("Invoice is not sent to client you cannot pay it!"));
        }

        $invoice->paid_time = time();
        $invoice->save('paid_time');

        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        $log->event = LogModel::EVENT_INVOICE_PAID;
        $log->save();
    }
    
    public function creditAction(){
    	$id = $this->_getParam('id');
    	$newId = $this->credit($id);
    	$this->_helper->json(array('redirect' => $this->view->baseUrl() . '/invoices/index/new/id/' . $newId));
    }
    
    protected function credit($id){
    	$invoice = new Invoice($id);

    	if( !$invoice->exists() ){
    		throw new Exception(_t("Invoice not found!"));
    	}

    	if( !$invoice->isFinal() ){
    		throw new Exception(_t("Cannot create credit invoice for non final invoice!"));
    	}
    	
    	if( $invoice->isCredit() ){
    		throw new Exception(_t("Cannot create credit invoice for credit invoice!"));
    	}

        Utils::activity('credit', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

    	$invoice->model()->getAdapter()->beginTransaction();
    	
    	try {
	    	$credit = clone $invoice;
	    	//$credit->number = SettingsModel::getInvoiceNextNum();
            $credit->number = $invoice->formatTmpNumber($invoice->nextTmpNumber());
            $credit->total_sum = -1 * $invoice->total_sum;
            $credit->total_excl_vat = -1 * $invoice->total_excl_vat;
            $credit->vat_sum = -1 * $invoice->vat_sum;
            /*$cf = SettingsModel::getCreditNumberFormat();
            if ( $cf != "" ) {
                $credit->number = Utils::getRealPrefix($cf) . str_repeat("0", Constants::INVOICE_NUMBER_PADDING - strlen($credit->number)) . $credit->number;
            } else {
	  		    $credit->number = Constants::INVOICE_NUMBER_PREFIX . str_repeat("0", Constants::INVOICE_NUMBER_PADDING - strlen($credit->number)) . $credit->number;
            }*/
	  		$credit->parent_invoice_id = $invoice->id;
	  		$credit->paid_time = 0;
	  		$credit->status = InvoiceModel::STATUS_NEW;
	  		$credit->credit = 1;      
	  		
	  		$credit->save();

	  		foreach( $invoice->products as $product ){
	  			$product = clone $product;
	  			$product->invoice_id = $credit->id;
                $product->price *= -1;
                $product->total_sum *= -1;
	  			$product->save();
	  		}

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $invoice->id;
            $log->data = $credit->id;
            $log->event = LogModel::EVENT_INVOICE_CREDIT;
            $log->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $credit->id;
            $log->event = LogModel::EVENT_INVOICE_CREATED;
            $log->save();
	  		
	  		$invoice->model()->getAdapter()->commit();
	  		
    	}catch(Exception $e){
    		$invoice->model()->getAdapter()->rollBack();
    		throw $e;
    	}
    	
    	return $credit->id;
    }
    
    public function duplicateAction(){
    	$id = $this->_getParam('id');
    	$invoice = $this->duplicate($id);
    	$this->_helper->json(array('redirect' => $this->view->baseUrl() . '/invoices/index/new/id/' . $invoice->id));
    }
    
    protected function duplicate($id){
    	$invoiceModel = new InvoiceModel();
    	$invoice = $invoiceModel->findById($id);
    	
    	if( !$invoice ){
    		throw new Exception(_t("Invoice not found!"));
    	}

        Utils::activity('duplicate', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);
    	
    	$invoiceProductModel = new InvoiceProductModel();
    	$invoiceProducts = $invoiceProductModel->findAllByColumn('invoice_id', $invoice->id);
    	
    	$invoiceModel->getAdapter()->beginTransaction();
        $tmp = new Invoice();
        $tmp->proforma = $invoice->proforma;      
    	try {

    		unset($invoice->id);
    		$invoice->number = $tmp->formatTmpNumber($tmp->nextTmpNumber());
    		//$invoice->number = Constants::INVOICE_NUMBER_PREFIX . str_repeat("0", Constants::INVOICE_NUMBER_PADDING - strlen($invoice->number)) . $invoice->number;
    		$invoice->status = InvoiceModel::STATUS_NEW;
            $invoice->proforma_status = InvoiceModel::PROFORMA_STATUS_NEW;
    		$invoice->paid_time = '0000-00-00 00:00:00';
    		
	    	$invoice->id = $invoiceModel->insert((array) $invoice);
	    	
	    	foreach( $invoiceProducts as $product ){
	    		unset($product->id);
	    		$product->invoice_id = $invoice->id;
	    		$invoiceProductModel->insert((array) $product);
	    	}
	    	
	    	$invoiceModel->getAdapter()->commit();
            
            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $invoice->id;
            $log->data = $id;
            $log->event = LogModel::EVENT_INVOICE_DUPLICATE;
            $log->save();
    	}catch(Exception $e){
    		$invoiceModel->getAdapter()->rollBack();
            throw $e;
    	}
    	
    	return $invoice;    	
    }
    
    public function deleteAction(){
    	$id = $this->_getParam('id');
        $invoice = new Invoice($id);
        $link = $invoice->proforma ? 'offers' : 'invoices';
        
    	$this->delete($id);
    	$this->_helper->json(array('redirect' => '/'.$link.'/'));
    }
    
    public function unpaidAction(){
        $id = $this->_getParam('id');
        $this->unpaid($id);
        $this->_helper->json(array('reload' => 1));
    }
    
    protected function unpaid($id){
        
        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->findById($id);
        
        if( !$invoice ){
            throw new Exception(_t("Invoice not found!"));
        }

        if( ($invoice->proforma && !Utils::user()->can('offer_delete')) && (!$invoice->proforma && !Utils::user()->can('invoice_delete')) ){
            throw new Exception(_t('Access denied!'));
        }
        
        if( ((int) $invoice->paid_time) == 0){
            throw new Exception(_t("This is not paid invoice!"));
        }

        Utils::activity('unpaid', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);
        
        
        $invoiceModel->getAdapter()->beginTransaction();
        try {            
            $invoiceModel->setUnPaidInvoice($invoice->id);
            $invoiceModel->getAdapter()->commit();
            
            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $invoice->id;
            $log->event = LogModel::EVENT_INVOICE_UNPAID;
            $log->save();
        }catch(Exception $e){
            $invoiceModel->getAdapter()->rollBack();
            throw $e;
        }
    }
    
    protected function delete($id){
    	
    	$invoiceModel = new InvoiceModel();
    	$invoice = $invoiceModel->findById($id);
    	
    	if( !$invoice ){
    		throw new Exception(_t("Invoice not found!"));
    	}

        if( ($invoice->proforma && !Utils::user()->can('offer_delete')) && (!$invoice->proforma && !Utils::user()->can('invoice_delete')) ){
            throw new Exception(_t('Access denied!'));
        }
    	
    	if( $invoice->status != InvoiceModel::STATUS_NEW ){
    		throw new Exception(_t("You can't delete final invoices!"));
    	}
    	
    	if( (!$invoice->proforma && ((int) $invoice->paid_time)) || ($invoice->proforma && ($invoice->proforma_status == 'accepted' || $invoice->proforma_status == 'invoice')) ){
            $tmp = $invoice->proforma ? 'offertes' : 'facturen';
            throw new Exception("Je kunt geen bevestigde $tmp verwijderen!");
    		//throw new Exception(_t("You can't delete paid invoice!"));
    	}

        Utils::activity('delete', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);
    	
    	$invoiceProductModel = new InvoiceProductModel();
    	$invoiceProducts = $invoiceProductModel->findAllByColumn('invoice_id', $invoice->id);
    	
    	
    	$invoiceModel->getAdapter()->beginTransaction();
    	try {
    		
    		foreach( $invoiceProducts as $product ){
    			$invoiceProductModel->deleteById($product->id);
    		}
    		
    		$invoiceModel->deleteById($invoice->id);
    		$invoiceModel->getAdapter()->commit();
    		
    	}catch(Exception $e){
    		$invoiceModel->getAdapter()->rollBack();
    		throw $e;
    	}
    }
    
    public function bulkPayAction(){
    	$invoices = $this->_getParam('invoices', array());
    	
    	$invoiceModel = new InvoiceModel();
    	foreach( $invoices as $id ){
    		$invoice = $invoiceModel->findById($id);
    		
    		if( !$invoice || ((int) $invoice->paid_time) || $invoice->status != InvoiceModel::STATUS_FINAL ){
    			continue;
    		}
    		
    		$this->pay($id);
    	}
    	
    	$this->_helper->json(array('reload' => 1));
    }
    
    public function bulkEmailAction(){
        Utils::activity('bulk-email', 'invoice');
    	$invoices = $this->_getParam('invoices', array());
    	
    	$invoiceModel = new InvoiceModel();
    	$contactModel = new ContactModel();
    	foreach( $invoices as $id ){
    		$invoice = new Invoice($id);
    		
    		if( !$invoice->exists() || !$invoice->contact ){
    			continue;
    		}                  

    		if( !$invoice->contact->email_address ){
    			continue;
    		}

    		$name = $invoice->contact->name;
    		$number = $invoice->number;
    		$date = date(Constants::DATE_FORMAT, $invoice->expire_time);
    		
    		$email = $invoice->contact->email_address;
    		$subject = $invoice->isProforma() ? SettingsModel::getProformaDefaultEmailSubject() : SettingsModel::getInvoiceDefaultEmailSubject();
            $subject = str_replace('{client_number}', $invoice->contact->number, $subject);
            $subject = str_replace('{client_firstname}', $invoice->contact->firstname, $subject);
            $subject = str_replace('{client_lastname}', $invoice->contact->lastname, $subject);
            $subject = str_replace('{invoice_number}', $invoice->getFutureNumber(), $subject);
            $subject = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $subject);
            $subject = str_replace('{invoice_total_price}', $invoice->total_sum, $subject);
            $subject = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $subject);
            $subject = str_replace('{offer_number}', $invoice->getFutureNumber(), $subject);
            $subject = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $subject);
            $subject = str_replace('{offer_total_price}', $invoice->total_sum, $subject);
            $subject = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $subject);
            $subject = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $subject);
            $subject = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $subject);
            $subject = str_replace('{bank_location}', SettingsModel::getInvoiceProviderBankloc(), $subject);
            $subject = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $subject);
            $subject = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $subject);
            $subject = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $subject);
    		$body = $this->emailBody($name, $number, $date, $invoice->isProforma(), $invoice->status, $invoice->id);
            try {
                if( $invoice->status != InvoiceModel::STATUS_FINAL ){
                    $this->finalize($invoice->id);
                }
            }catch(Exception $e){
                continue;
            }                           
    		$pdf = $this->pdf($id);
    		
    		$this->email($id, $email, $subject, $body, $pdf);
    	}
    	
    	$this->_helper->json(array('reload' => 1));
    }
    
    public function bulkPdfAction(){
        Utils::activity('bulk-pdf', 'invoice');
    	$invoices = $this->_getParam('invoices', array());

    	$file = tempnam(sys_get_temp_dir(), 'factuur-archive-');
    	$name = dirname($file) . '/factuur-archive-' . date('d-m-Y') . '.zip';
    	if( !rename($file, $name) ){
    		$name = $file;
    	}
    	

    	$files = array();
    	
    	$invoiceModel = new InvoiceModel();
    	foreach( $invoices as $id ){
    		$invoice = new Invoice($id);
    		
    		if( !$invoice->exists() ){
    			continue;
    		}
    		
    		$pdfFilename = $this->pdfName($id, $invoice->number);
    		$this->generatePDF($id, $pdfFilename, 'F');
    		$files[$invoice->number . '.pdf'] = $pdfFilename;

            $index = 0;
            foreach( $invoice->annexes as $annex ){
                $index++;
                $files[ $invoice->number . '_BIJLAGE' . $index . '_' . pathinfo(preg_replace('@[^a-z0-9\.\-\_]@i', '', $annex->name), PATHINFO_FILENAME) . '.' . pathinfo($annex->toPath(), PATHINFO_EXTENSION)] = $annex->toPath();
            }
    	}

    	$zip = new ZipArchive();
    	
    	if( $zip->open($name, ZIPARCHIVE::CREATE) !== true ) {
    		throw new Exception(_t("Can't create archive!"));
    	}
    	
    	foreach ($files as $filename => $file) {
		    if( !$zip->addFile($file, $filename) ){
				throw new Exception(_t("Can't add file to archive!"));
		    }
		}
		
		$zip->close();
    	
		if( headers_sent() ){
			throw new Exception(_t("Can't send archive. Headers already sent!"));
		}
		
		header('Content-Type: application/zip');
		header('Content-Length: ' . filesize($name));
		header('Content-Disposition: attachment; filename=' . basename($name));
    			     
		fpassthru(fopen($name, 'rb'));
		die();
    }
    
    public function bulkCreditAction(){
    	$invoices = $this->_getParam('invoices', array());
    	
    	foreach( $invoices as $id ){
    		$this->credit($id);
    	}
    	
    	$this->_helper->json(array('reload' => 1));
    }
    
	public function bulkDuplicateAction(){
    	$invoices = $this->_getParam('invoices', array());
    	
    	foreach( $invoices as $id ){
    		$this->duplicate($id);
    	}
    	
    	$this->_helper->json(array('reload' => 1));
    }
    
	public function bulkDeleteAction(){
    	$invoices = $this->_getParam('invoices', array());
    	
    	foreach( $invoices as $id ){
    		$this->delete($id);
    	}
    	
    	$this->_helper->json(array('reload' => 1));
    }

    public function validateAnnexFileAction(){
        $filename = $this->_getParam('filename');

        if( !InvoiceAnnex::isAllowed($filename) ){
            throw new Exception(_t("Bestandstype niet toegestaan!"));
        }

        $this->_helper->json(array('success'=>1));
    }

    public function createAnnexInvoiceAction(){
        Zend_Registry::set('force-json', true);
        $proforma = (int) $this->_getParam('proforma', 0);
        $invoiceId = (int) $this->_getParam('id', 0);

        $invoice = new Invoice($invoiceId);
        //$invoice->model()->getAdapter()->beginTransaction();

        //try {
            if( !$invoice->exists() ){
                $invoice->proforma = $proforma;
                $invoice->create();
            }      

            $invoice->addAnnex($_FILES['attachment']);
            $invoice->save();

            //$invoice->model()->getAdapter()->commit();
        /*}catch(Exception $e){
            $invoice->model()->getAdapter()->rollBack();
            throw $e;
        } */


        Utils::activity('annex', $proforma ? 'offer' : 'invoice', $invoice->id);

        $response = array();
        $response['success'] = 1;
        $response['message'] = _t('Succes!');

        if( !$invoiceId ){
            $response['redirect'] = ($invoice->proforma ? '/offers' : '/invoices') . '/index/view/id/' . $invoice->id;
        }

        echo '<div id="response">' . Zend_Json::encode($response) . '</div>';
        die();
    }

    public function bulkProformaStatusAction(){;
        $invoices = $this->_getParam('invoices', array());
        $status = $this->_getParam('status', '');

        foreach( $invoices as $id ){
            $this->proformaStatus($id, $status);
        }

        $this->_helper->json(array('reload' => 1));
    }

    public function proformaStatusAction(){
        $id = $this->_getParam('id');
        $status = $this->_getParam('status');
        $this->proformaStatus($id, $status);

        $result = array();
        $result['proforma_status'] = $this->view->partial('index/_partials/proforma-status-sidebar.phtml', array('invoice' => new Invoice($id)));

        $this->_helper->json($result);
    }

    public function proformaStatus($id, $status){
        $proforma = new Invoice($id);

        if( !$proforma->exists() ){
            throw new Exception(_t('Offer not found!'));
        }

        if( !$proforma->proforma ){
            throw new Exception(_t('Invoice is not an offer!'));
        }

        if( $proforma->proforma_status == InvoiceModel::PROFORMA_STATUS_INVOICE ){
            throw new Exception(_t("Offer is already invoiced you cannot invoice it again!"));
        }

        Utils::activity('proforma-status', $proforma->proforma ? 'offer' : 'invoice', $proforma->id);

        $proforma->model()->getAdapter()->beginTransaction();

        try {

            $proforma->proforma_status = $status;
            if ( $status == 'accepted' ) {
                $proforma->paid_time = time();
            }
            $proforma->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $proforma->id;
            $log->data = $status;
            $log->event = LogModel::EVENT_INVOICE_PROFORMA_STATUS;
            $log->save();                               

            if( $proforma->proforma_status == InvoiceModel::PROFORMA_STATUS_INVOICE ){
                $invoice = new Invoice();
                $invoice->proforma = 0;
                $invoice->contact_id = $proforma->contact_id;
                $invoice->total_sum = $proforma->total_sum;
                $invoice->total_excl_vat = $proforma->total_excl_vat;
                $invoice->vat_sum = $proforma->vat_sum;
                $invoice->discount = $proforma->discount;
                $invoice->discount_sum = $proforma->discount_sum;
                $invoice->create();

                foreach( $proforma->products as $product ){
                    $invoiceProduct = new InvoiceProduct();
                    $invoiceProduct->load($product->data());
                    $invoiceProduct->id = null;
                    $invoiceProduct->invoice_id = $invoice->id;
                    $invoiceProduct->save();
                }

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                $log->source_id = $proforma->id;
                $log->data = $invoice->id;
                $log->event = LogModel::EVENT_PROFORMA_TO_INVOICE;
                $log->save();

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                $log->source_id = $invoice->id;
                $log->data = $proforma->id;
                $log->event = LogModel::EVENT_INVOICE_FROM_PROFORMA;
                $log->save();
            }

            $proforma->model()->getAdapter()->commit();

            if( isset($invoice) ){
                $this->_helper->json(array('redirect' => $this->view->baseUrl() . '/invoices/index/edit/id/' . $invoice->id));
            }
            $this->_helper->json(array('reload' => 1));

        }catch(Exception $e){
            $proforma->model()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function paymentDialogInvoicesListAction(){
        $invoicesParam = $this->_getParam('invoices');
        $explicitInvoices = !empty($invoicesParam);

        if( !$invoicesParam ){
            $invoiceModel = new InvoiceModel();
            $invoicesParam = array();
            $invoices = $invoiceModel->findUnpaidInvoices();

            foreach( $invoices as $invoice ){
                $invoicesParam[] = $invoice->id;
            }
        }

        $invoices = array();
        foreach( $invoicesParam as $id ){
            $invoice = new Invoice($id);

            if( !$invoice->exists() || $invoice->paid_time || (!$explicitInvoices && $invoice->status != InvoiceModel::STATUS_FINAL) ){
                continue;
            }

            $invoices[] = $invoice;
        }
        
        $thx_subject = SettingsModel::getInvoiceThanksEmailSubject();
        $thx_body = SettingsModel::getInvoiceThanksEmailBody();
        if ( substr($thx_body, -4) == '<br>' ) $thx_body = substr($thx_body, 0, -4);
        
        $thx_body = str_replace('{client_number}', $invoice->contact->number, $thx_body);
        $thx_body = str_replace('{client_firstname}', $invoice->contact->firstname, $thx_body);
        $thx_body = str_replace('{client_lastname}', $invoice->contact->lastname, $thx_body);
        $thx_body = str_replace('{invoice_number}', $invoice->getFutureNumber(), $thx_body);
        $thx_body = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $thx_body);
        $thx_body = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $thx_body);
        $thx_body = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $thx_body);
        $thx_body = str_replace('{offer_number}', $invoice->getFutureNumber(), $thx_body);
        $thx_body = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $thx_body);
        $thx_body = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $thx_body);
        $thx_body = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $thx_body);
        $thx_body = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $thx_body);
        $thx_body = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $thx_body);
        $thx_body = str_replace('{bank_loc}', SettingsModel::getInvoiceProviderBankloc(), $thx_body);
        $thx_body = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $thx_body);
        $thx_body = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $thx_body);
        $thx_body = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $thx_body);

        $thx_subject = str_replace('{client_number}', $invoice->contact->number, $thx_subject);
        $thx_subject = str_replace('{client_firstname}', $invoice->contact->firstname, $thx_subject);
        $thx_subject = str_replace('{client_lastname}', $invoice->contact->lastname, $thx_subject);
        $thx_subject = str_replace('{invoice_number}', $invoice->getFutureNumber(), $thx_subject);
        $thx_subject = str_replace('{invoice_expiration_date}', date('d-m-Y', $invoice->expire_time), $thx_subject);
        $thx_subject = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $thx_subject);
        $thx_subject = str_replace('{invoice_creation_date}', date('d-m-Y', $invoice->invoice_time), $thx_subject);
        $thx_subject = str_replace('{offer_number}', $invoice->getFutureNumber(), $thx_subject);
        $thx_subject = str_replace('{offer_expiration_date}', date('d-m-Y', $invoice->expire_time), $thx_subject);
        $thx_subject = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($invoice->total_sum), $thx_subject);
        $thx_subject = str_replace('{offer_creation_date}', date('d-m-Y', $invoice->invoice_time), $thx_subject);
        $thx_subject = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $thx_subject);
        $thx_subject = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $thx_subject);
        $thx_subject = str_replace('{bank_loc}', SettingsModel::getInvoiceProviderBankloc(), $thx_subject);
        $thx_subject = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $thx_subject);
        $thx_subject = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $thx_subject);
        $thx_subject = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $thx_subject);

        $result = array();
        $invoice = reset($invoices);
        $result['invoices_list'] = $this->view->partial('index/_partials/invoice-payment/invoices-list.phtml', array('invoices' => $invoices, 'invoice' => $invoice));
        $result['invoice'] = $invoice ? $this->view->partial('index/_partials/invoice-payment/invoice.phtml', array('invoice' => $invoice, 'subject' => $thx_subject, 'body' => $thx_body)) : '';
        $this->_helper->json($result);
    }

    public function paymentDialogInvoiceLoadAction(){
        $id = (int) $this->_getParam('id', 0);

        $invoice = new Invoice($id);

        if( !$invoice->exists() ){
            throw new Exception(_t('Invoice not found!'));
        }

        if( $invoice->paid_time ){
            throw new Exception(_t('Invoice already paid!'));
        }

        $result = array();
        $result['invoice'] = $this->view->partial('index/_partials/invoice-payment/invoice.phtml', array('invoice' => $invoice));
        $this->_helper->json($result);
    }

    public function invoicePaymentPayAction(){
        $invoiceParam = $this->_getParam('invoice');
        $paymentParam = $this->_getParam('payment');
        $emailParam = $this->_getParam('email');         

        $invoice = new Invoice($invoiceParam['id']);

        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        //$invoice->model()->getAdapter()->beginTransaction();

        try {
            $invoice->addPayment($paymentParam['amount'], strtotime($paymentParam['paid_time']), $paymentParam['payment_method']);
            Utils::activity('payment', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

            if( !empty($emailParam['send']) ){
                $emailValidator = new Zend_Validate_EmailAddress();

                if( !$emailValidator->isValid($emailParam['email']) ){
                    throw new Exception(_t('Invalid email address!'));
                }


                // email
                $mail = Mail::factory();
                $mail->setSubject(_t('Received payment'));
                $mail->addTo($emailParam['email']);
                $mail->setBodyHtml($emailParam['body']);
                $mail->send();
            }
            
            //$invoice->paid_time = strtotime($paymentParam['paid_time']);
            //$invoice->save('paid_time');

            //$invoice->model()->getAdapter()->commit();
        }catch(Exception $e){
            //$invoice->model()->getAdapter()->rollBack();
            throw $e;
        }

        $this->_helper->json(array('success' => 1));
    }

    public function contactAutocompleteAction(){
        $contactModel = new ContactModel();
        $contacts = $contactModel->autocomplete($this->_getParam('field'), $this->_getParam('term'), $this->_getParam('limit'));
        foreach ( $contacts as $key => $val ) {
            $contact = new Contact($val->id);
            $contacts[$key]->formated_number = $contact->getNumber();
        }
        $this->_helper->json($contacts);
    }

    public function testAction(){
        $text = 'asdf <mark>aasdf</mark> & <link>https://core.bg/asdf?query=asdf&asdf</link> <lt>asdf</lt>';
        echo Utils::strip_bad_tags($text);
        die();
    }

    protected function pdfTestAction(){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $invoice = new Invoice($this->_getParam('id'));

        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        if( $invoice->parent_invoice_id && !$invoice->parent ){
            throw new Exception(_t("Cannot find invoice parent invoice!"));
        }

        $this->view->invoice = $invoice;

        $header = $this->view->render('index/pdf/header.phtml');
        $footer = $this->view->render('index/pdf/footer.phtml');
        $content = $this->view->render('index/pdf.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 20, 30, 9, 9, 'P');
        //$mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);

        $mpdf->Output();
        die();
    }

    public function annexesReloadAction(){
        $id = (int) $this->_getParam('id', 0);
        $invoice = new Invoice($id);

        if( !$invoice->exists() ){
            throw new Exception(_t('Invoice not found!'));
        }

        $result = array();
        $result['annexes'] = $this->view->partial('index/_partials/invoice-annex-sidebar.phtml', array('invoice' => $invoice));
        $this->_helper->json($result);
    }

    public function annexEditAction(){
        $id = (int) $this->_getParam('id', 0);
        $invoiceAnnex = new InvoiceAnnex($id);

        if( !$invoiceAnnex->exists() ){
            throw new Exception(_t("Invoice annex not found!"));
        }

        $result = array();
        $result['id'] = $invoiceAnnex->id;
        $result['annex_edit'] = $this->view->partial('index/_partials/invoice-annex-edit.phtml', array('annex' => $invoiceAnnex));
        $this->_helper->json($result);
    }

    public function annexSaveAction(){
        $id = (int) $this->_getParam('id', 0);
        $name = $this->_getParam('name', '');
        $invoiceAnnex = new InvoiceAnnex($id);

        if( !$invoiceAnnex->exists() ){
            throw new Exception(_t("Invoice annex not found!"));
        }

        if( !$name ){
            throw new Exception(_t('Fill name field!'));
        }

        if( $invoiceAnnex->name != $name ){
            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $invoiceAnnex->invoice_id;
            $log->data = $name;
            $log->event = LogModel::EVENT_INVOICE_ANNEX_EDIT;
            $log->save();
        }

        $invoiceAnnex->name = $name;
        $invoiceAnnex->annexRename($name);
        $invoiceAnnex->save();
        $this->_helper->json(array('success' => '1'));
    }

    public function annexDeleteAction(){
        $id = (int) $this->_getParam('id', 0);
        $invoiceAnnex = new InvoiceAnnex($id);

        if( !$invoiceAnnex->exists() ){
            throw new Exception(_t("Invoice annex not found!"));
        }
        $invoice_id = $invoiceAnnex->invoice_id;
        $name = $invoiceAnnex->name;

        $invoiceAnnex->delete();
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice_id;
        $log->data = $name;
        $log->event = LogModel::EVENT_INVOICE_ANNEX_DELETE;
        $log->save();
        $this->_helper->json(array('success' => 1));
    }
    
	public function preDispatch() {
		$results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
		
		if ($results) {
			$products = array();
			$minstock_products = new Zend_Session_Namespace('min_stock_products');
			if (!$minstock_products->id_list) $minstock_products->id_list = array();
			//var_dump($minstock_products->id_list);
			foreach ($results as $product) {
				if (in_array($product->id, $minstock_products->id_list)) continue;
				$products[] = $product;
				//array_push($minstock_products->id_list, $product->id);
			}
			//var_dump($minstock_products->id_list);die();
			if ($products) $this->view->show_box = true;
			
			$this->view->minstock_products = $results;
		}
	}
}
