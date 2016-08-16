<?php

	/**
	 * @property int $id
	 * @property string $number 
	 * @property int $parent_invoice_id
	 * @property int $contact_id 
	 * @property float $total_sum
     * @property float $total_excl_vat
	 * @property float $vat_sum
	 * @property float $discount_sum
     * @property float $discount
	 * @property int $invoice_time
	 * @property string $paid_time
     * @property string $expire_time
     * @property string $reminder_time
     * @property string $notice
	 * @property string $notice_pattern
     * @property string $info
     * @property string $intro
     * @property string $intro_pattern
	 * @property int $credit
     * @property int $proforma
     * @property string $proforma_status
	 * @property string $status
     * @property string $from_webshop
     * @property int $created_by
     *
     * @property string $status_text
     * @property string $status_color
     * @property string $products_descriptions
     * @property float $unpaid_sum
     * @property string $signature
     * @proeprty string $signature_small
     *
	 * @property Contact $contact
     * @property Invoice $parent
	 * @property InvoiceProduct[] $products
     * @property InvoiceAnnex[] $annexes
     * @property Log[] $logs
     * @property InvoicePayment[] $payments
     * @property Receipt[] $receipts
	 *  
	 * @author avladev
	 *
	 */
    class Invoice extends Core_ActiveRecord_Row {

        private static $INVOICE_STATUS_TEXT = array('new' => 'Concept',
                                            'urgent' => 'Dringend',
                                            'outstanding' => 'Openstaand',
                                            'late' => 'Te laat',
                                            'part' => 'Deels voldaan',
                                            'paid' => 'Betaald',
                                            'judge' => 'Aanmaning');

        private static $INVOICE_STATUS_COLOR = array('new' => '',
                                             'urgent' => 'color',
                                             'outstanding' => 'color2',
                                             'late' => 'color1',
                                             'part' => 'color2',
                                             'paid' => 'color4',
                                             'judge' => 'color9');

        private static $PROFORMA_STATUS_TEXT = array(   'new' => 'Concept',
                                                        'unsent' => 'Niet verzonden',
                                                        'sent' => 'Verzonden');

        private static $PROFORMA_STATUS_COLOR = array(  'new' => '',
                                                        'unsent' => 'color',
                                                        'sent' => 'color4');

        private static $PROFORMA_PROFORMA_STATUS = array(   'new' => 'Concept',
                                                            'open' => 'Openstaand',
                                                            'accepted' => 'Geaccepteerd',
                                                            'denied' => 'Geweigerd',
                                                            'invoice' => 'Gefactureerd',
                                                            'archive' => 'Gearchiveerd',
                                                            'expired' => 'Verlopen',
                                                            'late' => 'Te laat',  
                                                            'urgent' => 'Dringend');

        private static $PROFORMA_PROFORMA_STATUS_COLOR = array(
                                                            'new' => '',
                                                            'open' => 'color2',
                                                            'accepted' => 'color4',
                                                            'denied' => 'color6',
                                                            'invoice' => 'color5',
                                                            'archive' => 'color7',
                                                            'expired' => 'color1',
                                                            'late'   => 'color1',
                                                            'urgent' => 'color');

    	public function __construct($id=null){
            $this->id = null;
            $this->number = '';
            $this->contact_id = null;
            $this->total_sum = 0.0;
            $this->vat_sum = 0.0;
            $this->discount_sum = 0.0;
            $this->discount = 0;
            $this->invoice_time = time();
            //$this->expire_time = strtotime('+ ' . Constants::INVOICE_PAY_DAYS . ' days', $this->invoice_time);
            $this->expire_time = strtotime('+ ' . SettingsModel::getInvoiceB2CPaymentTerm() . ' days', $this->invoice_time);
            $this->paid_time = 0;
            $this->reminder_time = 0;
/*           $settingModel = new SettingsModel();
            $empSettingModel = new EmpSettingModel();
            
            $user = new Zend_Session_Namespace('user');
            $emp_id = $user->id;

            if( !$this->notice ){    
                if ( $this->isProforma() ) {
                    $this->notice = $empSettingModel->getValue($emp_id, $settingModel->getProformaNoticeKey());
                    if ( $this->notice == null ) $this->notice = SettingsModel::getProformaDefaultNotice();
                } else {
                    $this->notice = $empSettingModel->getValue($emp_id, $settingModel->getInvoiceNoticeKey());
                    if ( $this->notice == null ) $this->notice = SettingsModel::getInvoiceDefaultNotice();
                }
            }
            
            if ( !$this->intro ){
                if ( $this->isProforma() ) {
                    $this->intro = $empSettingModel->getValue($emp_id, $settingModel->getProformaIntroKey());
                    if ( $this->intro == null ) $this->intro = SettingsModel::getProformaDefaultIntro();
                } else {
                    $this->intro = $empSettingModel->getValue($emp_id, $settingModel->getInvoiceIntroKey());
                    if ( $this->intro == null ) $this->intro = SettingsModel::getInvoiceIntro();
                }
            }*/
            $this->credit = 0;
            $this->status = InvoiceModel::STATUS_NEW;
            $this->from_webshop = InvoiceModel::NORMAL_INVOICE;
            $this->proforma_status = InvoiceModel::PROFORMA_STATUS_NEW;

    		parent::__construct(new InvoiceModel(), $id);
    	}

        public function relations(){
            return array(
		            	'contact' =>array('Contact', 'contact_id', self::HAS_ONE),
                        'parent' =>array('Invoice', 'parent_invoice_id', self::HAS_ONE),
		            	'products' => array('InvoiceProduct', 'invoice_id', self::HAS_MANY),
                        'annexes' => array('InvoiceAnnex', 'invoice_id', self::HAS_MANY),
                        'logs' => array('Log', 'source_id', self::HAS_MANY, array(array('source_type = ?', LogModel::SOURCE_TYPE_INVOICE)), array('id DESC')),
                        'payments' => array('InvoicePayment', 'invoice_id', self::HAS_MANY),
                        'receipts' => array('Receipt', 'invoice_id', self::HAS_MANY)
		            );
		}

        public function getInvoiceTime(){
            return $this->get('invoice_time') != 0 ? strtotime($this->get('invoice_time')) : 0;
        }

        public function setInvoiceTime($value){
            $this->set('invoice_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : 0);
        }

        public function getPaidTime(){
            return $this->get('paid_time') != 0 ? strtotime($this->get('paid_time')) : 0 ;
        }

        public function setPaidTime($value){
            $this->set('paid_time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : 0 );
        }

        public function getExpireTime(){
            return $this->get('expire_time') != 0 ? strtotime($this->get('expire_time')) : 0;
        }

        public function setExpireTime($value){
            $this->set('expire_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : 0);
        }

        public function isFinal(){
            return $this->status == InvoiceModel::STATUS_FINAL;
        }

        public function isNew(){
            return $this->status == InvoiceModel::STATUS_NEW;
        }

        public function isCredit(){
            return (bool) $this->credit;
        }

        public function isProforma(){
            return (bool) $this->proforma;
        }

        public function addProduct(InvoiceProduct $product){
            return $this->add('products', $product);
        }
        
        public function formatTmpNumber($number){
            if( $this->isProforma() ){
                $number = Constants::INVOICE_TMPNUM_PREFIX . 'O' . str_repeat("0", Constants::PROFORMA_NUMBER_PADDING - strlen($number)) . $number;
            }else{
                $number = Constants::INVOICE_TMPNUM_PREFIX . str_repeat("0", Constants::INVOICE_NUMBER_PADDING - strlen($number)) . $number;
            }

            return $number;
        }

        public function nextTmpNumber(){
            $invoiceModel = new InvoiceModel();
            $invoices = $invoiceModel->getAllInvoices($this->proforma, Constants::INVOICE_TMPNUM_PREFIX);        
            $idx = 1;          
            foreach ( $invoices as $invoice ) {
                $l = $this->proforma ? 4 : 3;
                $n = $this->getNumberFromString($invoice->number, $l);
                if ( $n > $idx ) {
                    break;    
                }
                $idx++;
            }         
            return $idx;
        }

        public function formatNumber($number){
            $ipf = SettingsModel::getInvoiceNumberFormat();
            $opf = SettingsModel::getProformaNumberFormat();    
            $cpf = SettingsModel::getCreditNumberFormat();    
            $prefix = "";
            
            if( $this->credit == 1 ){
                if ( $cpf != "" ) {
                    $prefix = Utils::getRealPrefix($cpf);       
                } else {
                    $prefix = Constants::INVOICE_CREDIT_PREFIX;
                }
            } else {
                if( $this->isProforma() ){
                    if ( $opf != "" ) {
                        $prefix = Utils::getRealPrefix($opf);       
                    } else {
                        $prefix = Constants::PROFORMA_NUMBER_PREFIX;
                    }
                } else {
                    if( $ipf != "" ){
                        $prefix = Utils::getRealPrefix($ipf);       
                    } else {
                        $prefix = Constants::INVOICE_NUMBER_PREFIX;
                    }
                }
            }
            $number = $prefix . str_repeat("0", Constants::PROFORMA_NUMBER_PADDING - strlen($number)) . $number;
            return $number;
        }    
        
        public function getFutureNumber(){
            if ( substr($this->number, 0, 3) == "CON" )
                return $this->formatNumber($this->currentNumber());
            return $this->number;
        }
        
        public function currentNumber(){
            return $this->isProforma() ? SettingsModel::getProformaCurrentNum() : SettingsModel::getInvoiceCurrentNum();
        }

        public function nextNumber(){
            return $this->isProforma() ? SettingsModel::getProformaNextNum() : SettingsModel::getInvoiceNextNum();
            /*$invoiceModel = new InvoiceModel();
            $invoices = $invoiceModel->getAllInvoices($this->proforma, Constants::INVOICE_NUMBER_PREFIX);
            $idx = 1;
            foreach ( $invoices as $invoice ) {
                $n = $this->getNumberFromString($invoice->number);
                if ( $n > $idx ) {
                    break;    
                }
                $idx++;
            }
            return $idx;*/
        }
        
        private function getNumberFromString($numStr, $len=3) {
            return (int)substr($numStr, $len);
        }

        public function getStatusText(){
            return $this->proforma ? $this->getProformaStatusName($this->proforma_status) : self::$INVOICE_STATUS_TEXT[$this->invoice_status_key];
        }

        public function getStatusColor(){
            return $this->proforma ? $this->proforma_status_color : self::$INVOICE_STATUS_COLOR[$this->invoice_status_key];
        }

        public function getProformaStatusKey(){
            if( $this->status == InvoiceModel::STATUS_NEW ){
                return 'new';
            }
            
            if( $this->paid_time ){
                return 'accepted';
            }
            
            if( $this->step == 2 ) {
                if ( (time() - $this->expire_time ) < Constants::INVOICE_JUDGE_DAYS * 24 * 60 * 60 ) {
                    return 'urgent';
                }
            }

            if( $this->step == 1 ) {
                if ( (time() - $this->expire_time) > Constants::INVOICE_URGENT_DAYS * 24 * 60 * 60 ) {
                    return 'urgent';
                } else {
                    return 'late';
                }
            }

            if( $this->step == 0 && $this->expire_time >= time() ){
                if ( $this->total_sum == $this->unpaid_sum ) {
                    return 'outstanding';
                } else {
                    return 'part';
                } 
            }
            
            return 'late';

            //return 'sent';
        }

        public function getInvoiceStatusKey(){
            if( $this->status == InvoiceModel::STATUS_NEW ){
                return 'new';
            }

            if( $this->paid_time ){
                return 'paid';
            }
            
            if( $this->step == 2 ) {
                if ( (time() - $this->expire_time ) > Constants::INVOICE_JUDGE_DAYS * 24 * 60 * 60 ) {
                    return 'judge';
                } else {
                    return 'urgent';
                }
            }

            if( $this->step == 1 ) {
                if ( (time() - $this->expire_time) > Constants::INVOICE_URGENT_DAYS * 24 * 60 * 60 ) {
                    return 'urgent';
                } else {
                    return 'late';
                }
            }

            if( $this->step == 0 && $this->expire_time >= time() ){
                if ( $this->total_sum == $this->unpaid_sum ) {
                    return 'outstanding';
                } else {
                    return 'part';
                } 
            }
            
            return 'late';
        }

        public function getProductsDescriptions(){
            $descriptions = array();
            foreach( $this->products as $product ){
                $description = $product->description;

                if( $product->product ){
                    $description = $product->product->short_description;
                }

                $descriptions[] = $description;
            }

            return array_unique($descriptions);
        }

        public function getUnpaidSum(){
            $paid = 0.0;
            foreach( $this->payments as $payment ){
                $paid += $payment->amount;
            }

            $unpaid = $this->total_sum - $paid;
            return $unpaid > 0 ? $unpaid : 0 ;
        }

        public static function getProformaStatusName($key){
            return array_key_exists($key, self::$PROFORMA_PROFORMA_STATUS) ? self::$PROFORMA_PROFORMA_STATUS[$key] : '';
        }

        public function finalize(){
            if( $this->status == InvoiceModel::STATUS_FINAL ){
                throw new Exception(_t("Invoice already final!"));
            }

            if( $this->proforma && $this->proforma_status == InvoiceModel::PROFORMA_STATUS_NEW ){
                $this->proforma_status = InvoiceModel::PROFORMA_STATUS_OPEN;
            }

            $this->model()->getAdapter()->beginTransaction();

            try {

                if( !$this->proforma && !$this->credit ){
                    $productModel = new ProductModel();
                    $removeFromStock = array();
                    $removeFromReservation = array();

                    foreach( $this->products as $invoiceProduct ){
                        if( $invoiceProduct->product && $invoiceProduct->qty ){
                            if( !array_key_exists($invoiceProduct->product->id, $removeFromStock) ){
                                $removeFromStock[$invoiceProduct->product->id] = 0;
                            }

                            $removeFromStock[$invoiceProduct->product->id] += $invoiceProduct->qty;
                        }
                    }

                    foreach( $this->receipts as $receipt ){

                        if( !$receipt->employee ){
                            continue;
                        }

                        foreach( $receipt->products as $receiptProduct ){
                            if( !$receiptProduct->product_id ){
                                continue;
                            }

                            if( !$receipt->pack || $receipt->pack->status != PackModel::STATUS_FINAL ){
                                // products are in reservation
                                // make them in transit
                                $receipt->employee->addStock($receiptProduct->product->id, $receiptProduct->qty, Employee::PRODUCT_TRANSIT);
                            }

                            $receipt->employee->removeStock($receiptProduct->product->id, $receiptProduct->qty);

                            if( array_key_exists($receiptProduct->product->id, $removeFromStock) ){
                                $removeFromStock[$receiptProduct->product->id] -= $receiptProduct->qty;
                            }
                        }
                    }

                    foreach( $removeFromStock as $productId => $qty ){
                        if( $productId > 0 && $qty > 0 ){
                            Utils::user()->removeStock($productId, $qty);
                        }
                    }
                }
                
                if ( !$this->proforma )
                    $this->number = $this->formatNumber($this->nextNumber());        
                $this->notice = str_replace('{invoice_number}', $this->number, $this->notice_pattern);
                $this->notice = str_replace('{offer_number}', $this->number, $this->notice);
                $this->notice = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->notice);
                $this->notice = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->notice);
                $this->notice = str_replace('{invoice_expiration_date}', date('d-m-Y', $this->expire_time), $this->notice);
                $this->notice = str_replace('{offer_expiration_date}', date('d-m-Y', $this->expire_time), $this->notice);
                $this->intro = str_replace('{invoice_number}', $this->number, $this->intro_pattern);
                $this->intro = str_replace('{offer_number}', $this->number, $this->intro);
                $this->intro = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->intro);
                $this->intro = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->intro);
                $this->intro = str_replace('{invoice_expiration_date}', date('d-m-Y', $this->expire_time), $this->intro);
                $this->intro = str_replace('{offer_expiration_date}', date('d-m-Y', $this->expire_time), $this->intro);
                $this->status = InvoiceModel::STATUS_FINAL;
                $this->save();

                $this->model()->getAdapter()->commit();
            }catch (Exception $e){
                $this->model()->getAdapter()->rollBack();
                throw $e;
            }

        }

        public function create(){
            if( $this->exists() ){
                throw new Exception(_t("Invoice already exists!"));
            }                 

            if( !$this->number ){ 
                if ( $this->proforma ) {
                    $this->number = $this->formatNumber($this->nextNumber());
                } else {
                    $this->number = $this->formatTmpNumber($this->nextTmpNumber());        
                }
            }                                                 

            if( !$this->invoice_time ){
                $this->invoice_time = time();
            }         
            
            $settingModel = new SettingsModel();
            //$empSettingModel = new EmpSettingModel();
            
            //$user = new Zend_Session_Namespace('user');
            //$emp_id = $user->id;

            if( !$this->notice ){    
                if ( $this->isProforma() ) {
                    //$this->notice = $empSettingModel->getValue($emp_id, $settingModel->getProformaNoticeKey());
                    //if ( $this->notice == null ) $this->notice = SettingsModel::getProformaDefaultNotice();
                    $this->notice = SettingsModel::getProformaDefaultNotice();
                } else {
                    //$this->notice = $empSettingModel->getValue($emp_id, $settingModel->getInvoiceNoticeKey());
                    //if ( $this->notice == null ) $this->notice = SettingsModel::getInvoiceDefaultNotice();
                    $this->notice = SettingsModel::getInvoiceDefaultNotice();
                }
            }
            
            $this->notice = str_replace('{invoice_creation_date}', date('d-m-Y', $this->invoice_time), $this->notice);
            $this->notice = str_replace('{offer_creation_date}', date('d-m-Y', $this->invoice_time), $this->notice);
            $this->notice = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $this->notice);
            $this->notice = str_replace('{bank_location}', SettingsModel::getInvoiceProviderBankloc(), $this->notice);
            $this->notice = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $this->notice);
            $this->notice = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $this->notice);
            $this->notice = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $this->notice);
            $this->notice_pattern = $this->notice;
            
            $this->notice = str_replace('{invoice_expiration_date}', date('d-m-Y', $this->expire_time), $this->notice);
            $this->notice = str_replace('{offer_expiration_date}', date('d-m-Y', $this->expire_time), $this->notice);
            $this->notice = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $this->notice);
            $this->notice = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->notice);
            $this->notice = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->notice);
            $this->notice = str_replace('{invoice_number}', $this->number, $this->notice);
            $this->notice = str_replace('{offer_number}', $this->number, $this->notice);
                        
            if ( !$this->intro ){
                if ( $this->isProforma() ) {
                    //$this->intro = $empSettingModel->getValue($emp_id, $settingModel->getProformaIntroKey());
                    //if ( $this->intro == null ) $this->intro = SettingsModel::getProformaDefaultIntro();
                    $this->intro = SettingsModel::getProformaDefaultIntro();
                } else {
                    //$this->intro = $empSettingModel->getValue($emp_id, $settingModel->getInvoiceIntroKey());
                    //if ( $this->intro == null ) $this->intro = SettingsModel::getInvoiceIntro();
                    $this->intro = SettingsModel::getInvoiceIntro();
                }
            }
            
            $this->intro = str_replace('{invoice_creation_date}', date('d-m-Y', $this->invoice_time), $this->intro);
            $this->intro = str_replace('{offer_creation_date}', date('d-m-Y', $this->invoice_time), $this->intro);
            $this->intro = str_replace('{bank_name}', SettingsModel::getInvoiceProviderBankname(), $this->intro);
            $this->intro = str_replace('{bank_location}', SettingsModel::getInvoiceProviderBankloc(), $this->intro);
            $this->intro = str_replace('{bank_number}', SettingsModel::getInvoiceProviderBankNumber(), $this->intro);
            $this->intro = str_replace('{bank_iban}', SettingsModel::getInvoiceProviderKVK(), $this->intro);
            $this->intro = str_replace('{bank_bic}', SettingsModel::getInvoiceProviderBIC(), $this->intro);
            
            $this->intro_pattern = $this->intro;
            
            $this->intro = str_replace('{invoice_expiration_date}', date('d-m-Y', $this->expire_time), $this->intro);
            $this->intro = str_replace('{company_name}', SettingsModel::getInvoiceProviderCompany(), $this->intro);
            $this->intro = str_replace('{offer_expiration_date}', date('d-m-Y', $this->expire_time), $this->intro);
            $this->intro = str_replace('{invoice_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->intro);
            $this->intro = str_replace('{offer_total_price}', '&euro; '.Utils::numberFormat($this->total_sum), $this->intro);
            $this->intro = str_replace('{invoice_number}', $this->number, $this->intro);
            $this->intro = str_replace('{offer_number}', $this->number, $this->intro);

            $this->created_by = Utils::user()->id;
            $this->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
            $log->source_id = $this->id;
            $log->event = LogModel::EVENT_INVOICE_CREATED;
            $log->save();
        }

        /**
         * @param float $amount
         * @param int $paid_time
         * @param string $payment_method
         * @return InvoicePayment
         */
        public function addPayment($amount, $paid_time, $payment_method){
            if( $this->paid_time ){
                throw new Exception(_t("Invoice already paid!"));
            }
            $amount = str_replace(".", "", $amount);   
            $amount = str_replace(",", ".", $amount);   

            if( !is_numeric($amount) ) {// || $amount < 0 ){
                throw new Exception(_t("Invalid amount!"));
            }

            if( $amount > $this->unpaid_sum ){
                throw new Exception(_t("Amount is greater than unpaid sum!"));
            }                                      

            $payment = new InvoicePayment();
            $payment->model()->getAdapter()->beginTransaction();

            try {
                $payment->invoice_id = $this->id;
                $payment->amount = $amount;
                $payment->paid_time = $paid_time;
                $payment->payment_method = $payment_method;   
                $payment->save();

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                $log->source_id = $this->id;
                $is_finish = "no";
                if ( $amount == $this->unpaid_sum ) {
                    $is_finish = "yes";
                }
                $log->data = $amount . ';' . $payment_method . ';' . $is_finish;
                $log->event = LogModel::EVENT_INVOICE_PAYMENT;
                $log->save();

                $this->clearRelations('payments');

                if( !$this->unpaid_sum ){
                    $this->paid_time = time();
                    $this->status = InvoiceModel::STATUS_FINAL;
                    $this->save('paid_time');

                    $log = new Log();
                    $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                    $log->source_id = $this->id;
                    $log->event = LogModel::EVENT_INVOICE_PAID;
                    $log->save();
                }

                $payment->model()->getAdapter()->commit();
            }catch( Exception $e ){
                $payment->model()->getAdapter()->rollBack();
                throw $e;
            }

            return $payment;
        }

        public function addAnnex($file){

            if( empty($file) || $file['error'] != UPLOAD_ERR_OK ){
                throw new Exception(_t('Fout bij het uploaden bestand!'));
            }

            if( !InvoiceAnnex::isAllowed($file['name']) ){
                throw new Exception(_t("Bestandstype niet toegestaan!"));
            }

            $this->model()->getAdapter()->beginTransaction();

            try {
                $annex = new InvoiceAnnex();
                $annex->invoice_id = $this->id;
                $annex->save();

                $annex->filename = $annex->createFilename($file['name']);
                $annex->name = $annex->filename;

                $dir = dirname($annex->toPath());

                if( !is_dir($dir) ){
                    if( !mkdir($dir, 0777, true) ){
                        throw new Exception(_t("Can't create invoice annex folder!"));
                    }
                }

                if( !is_file($dir . '/index.html') ){
                    file_put_contents($dir . '/index.html', '');
                }

                if( !move_uploaded_file($file['tmp_name'], $annex->toPath()) ){
                    throw new Exception(_t("Can't write annex file!"));
                }

                $annex->save();

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                $log->source_id = $this->id;
                $log->data = $annex->id;
                $log->event = LogModel::EVENT_INVOICE_ANNEX;
                $log->save();

                $this->model()->getAdapter()->commit();

            }catch( Exception $e ){
                $this->model()->getAdapter()->rollBack();
                throw $e;
            }
        }

        public function addSignature($file){
            require_once 'WideImage/WideImage.php';

            if( empty($file) || $file['error'] != UPLOAD_ERR_OK ){
                throw new Exception(_t('Error uploading file!'));
            }

            if( !in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), SettingsModel::getInvoiceSignatureTypes()) ){
                throw new Exception(_t("File type not allowed!"));
            }

            $path = $this->signaturePath();

            $dir = dirname($path);

            if( !is_dir($dir) ){
                if( !mkdir($dir, 0777, true) ){
                    throw new Exception(_t("Can't create invoice signatures folder!"));
                }
            }

            if( !is_file($dir . '/index.html') ){
                file_put_contents($dir . '/index.html', '');
            }


            $image = WideImage::loadFromFile($file['tmp_name']);
            $image->saveToFile($this->signaturePath());
            $image->resize(200, 67)->saveToFile($this->signaturePath('small'));
        }

        public function signaturePath($type=''){
            return dirname(APPLICATION_PATH) . "/public/" . SettingsModel::getInvoiceSignatureFolder() . "/" . $this->id . "/signature" . ($type ? ('.' . $type) : '') . ".jpg";
        }

        public function getSignature(){
            return file_exists($this->signaturePath()) ? ("/" . SettingsModel::getInvoiceSignatureFolder() . "/" . $this->id . "/" . basename($this->signaturePath())) : null ;
        }

        public function getSignatureSmall(){
            return file_exists($this->signaturePath('small')) ? ("/" . SettingsModel::getInvoiceSignatureFolder() . "/" . $this->id . "/" . basename($this->signaturePath('small'))) : null ;
        }

        public function getProformaStatusColor(){
            return array_key_exists($this->proforma_status, self::$PROFORMA_PROFORMA_STATUS_COLOR) ? self::$PROFORMA_PROFORMA_STATUS_COLOR[$this->proforma_status] : '' ;
        }
    }
