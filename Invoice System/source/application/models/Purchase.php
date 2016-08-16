<?php

    /**
     * @property int $id
     * @property string $number
     * @property int $contact_id
     * @property float $total_sum
     * @property float $total_excl_vat
     * @property float $vat_sum
     * @property float $discount_sum
     * @property int $discount
     * @property string $invoice_time
     * @property string $expire_time
     * @property string $paid_time
     *
     * @property Wholesaler $contact
     * @property PurchaseProduct[] $products
     * @property PurchaseAttachment[] $attachments
     * @property Log[] $logs
     * @property PurchasePayment[] $purchases
     */
    class Purchase extends Core_ActiveRecord_Row {

        private static $STATUS_TEXT = array('new' => 'Concept',
            'urgent' => 'Dringend',
            'outstanding' => 'Openstaand',
            'late' => 'Te laat',
            'paid' => 'Betaald');

        private static $STATUS_COLOR = array('new' => '',
            'urgent' => 'color2',
            'outstanding' => 'color',
            'late' => 'color1',
            'paid' => 'color4');

        public function relations(){
            return array(
                'contact' => array('Wholesaler', 'contact_id', self::HAS_ONE),
                'products' => array('PurchaseProduct', 'purchase_id', self::HAS_MANY),
                'attachments' => array('PurchaseAttachment', 'purchase_id', self::HAS_MANY),
                'logs' => array('Log', 'source_id', self::HAS_MANY, array(array('source_type = ?', LogModel::SOURCE_TYPE_PURCHASE)), array('id DESC')),
                'payments' => array('PurchasePayment', 'purchase_id', self::HAS_MANY)
            );
        }

        public function __construct($id=null){
            parent::__construct(new PurchaseModel(), $id);
        }

        public function create(){
            if( !$this->number ){
                $this->number = $this->formatNumber($this->nextNumber());
            }

            if( !$this->invoice_time ){
                $this->invoice_time = time();
            }

            if( !$this->expire_time ){
                //$this->expire_time = strtotime('+' . Constants::INVOICE_PAY_DAYS . ' day', $this->invoice_time);
                $this->expire_time = strtotime('+' . SettingsModel::getInvoiceB2CPaymentTerm() . ' day', $this->invoice_time);
            }

            $this->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
            $log->source_id = $this->id;
            $log->event = LogModel::EVENT_PURCHASE_CREATED;
            $log->save();
        }

        public function addProduct(PurchaseProduct $product){
            return $this->add('products', $product);
        }

        public function addPayment($amount, $paid_time, $payment_method){
            if( $this->paid_time ){
                throw new Exception(_t("Purchase already paid!"));
            }

            if( !is_numeric($amount) || $amount < 0 ){
                throw new Exception(_t("Invalid amount!"));
            }

            if( $amount > $this->unpaid_sum ){
                throw new Exception(_t("Amount is greater than unpaid sum!"));
            }

            $payment = new PurchasePayment();
            $payment->model()->getAdapter()->beginTransaction();

            try {
                $payment->purchase_id = $this->id;
                $payment->amount = $amount;
                $payment->paid_time = $paid_time;
                $payment->payment_method = $payment_method;
                $payment->save();

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
                $log->source_id = $this->id;
                $log->data = $amount;
                $log->event = LogModel::EVENT_PURCHASE_PAYMENT;
                $log->save();

                $this->clearRelations('payments');

                if( !$this->unpaid_sum ){
                    $this->paid_time = time();
                    $this->save('paid_time');

                    $log = new Log();
                    $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
                    $log->source_id = $this->id;
                    $log->event = LogModel::EVENT_PURCHASE_PAID;
                    $log->save();
                }

                $payment->model()->getAdapter()->commit();
            }catch( Exception $e ){
                $payment->model()->getAdapter()->rollBack();
                throw $e;
            }

            return $payment;
        }

        public function addAttachment($file){

            if( empty($file) || $file['error'] != UPLOAD_ERR_OK ){
                throw new Exception(_t('Error uploading file!'));
            }

            if( !PurchaseAttachment::isAllowed($file['name']) ){
                throw new Exception(_t("File type not allowed!"));
            }

            $this->model()->getAdapter()->beginTransaction();

            try {
                $attachment = new PurchaseAttachment();
                $attachment->purchase_id = $this->id;
                $attachment->save();

                $attachment->filename = $attachment->createFilename($file['name']);
                $attachment->name = $attachment->filename;

                $dir = dirname($attachment->toPath());

                if( !is_dir($dir) ){
                    if( !mkdir($dir, 0777, true) ){
                        throw new Exception(_t("Can't create purchase attachment folder!"));
                    }
                }

                if( !is_file($dir . '/index.html') ){
                    file_put_contents($dir . '/index.html', '');
                }

                if( !move_uploaded_file($file['tmp_name'], $attachment->toPath()) ){
                    throw new Exception(_t("Can't write attachment file!"));
                }

                $attachment->save();

                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
                $log->source_id = $this->id;
                $log->data = $attachment->id;
                $log->event = LogModel::EVENT_PURCHASE_ATTACHMENT;
                $log->save();

                $this->model()->getAdapter()->commit();

            }catch( Exception $e ){
                $this->model()->getAdapter()->rollBack();
                throw $e;
            }
        }

        public function getInvoiceTime(){
            return $this->get('invoice_time') != 0 ? strtotime($this->get('invoice_time')) : null;
        }

        public function setInvoiceTime($value){
            $this->set('invoice_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : null);
        }

        public function getExpireTime(){
            return $this->get('expire_time') != 0 ? strtotime($this->get('expire_time')) : null;
        }

        public function setExpireTime($value){
            $this->set('expire_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : null);
        }

        public function getPaidTime(){
            return $this->get('paid_time') != 0 ? strtotime($this->get('paid_time')) : null ;
        }

        public function setPaidTime($value){
            $this->set('paid_time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : null );
        }

        public function formatNumber($number){
            $pf = SettingsModel::getPurchaseNumberFormat();
            if ( $pf == "" ){
                return Constants::PURCHASE_NUMBER_PREFIX . str_repeat("0", Constants::PURCHASE_NUMBER_PADDING - strlen($number)) . $number;
            } else {
                return Utils::getRealPrefix($pf) . str_repeat("0", Constants::PURCHASE_NUMBER_PADDING - strlen($number)) . $number;
            }
        }

        public function nextNumber(){
            return SettingsModel::getPurchaseNextNum();
        }

        public function getUnpaidSum(){
            $paid = 0.0;
            foreach( $this->payments as $payment ){
                $paid += $payment->amount;
            }

            $unpaid = $this->total_sum - $paid;
            return $unpaid > 0 ? $unpaid : 0 ;
        }

        public function getStatusText(){
            return self::$STATUS_TEXT[$this->status_key];
        }

        public function getStatusColor(){
            return self::$STATUS_COLOR[$this->status_key];
        }

        public function getStatusKey(){
            if( $this->paid_time ){
                return 'paid';
            }

            if( !$this->paid_time &&
                ($this->expire_time - time()) >= 0 &&
                ($this->expire_time - time()) <= Constants::INVOICE_URGENT_DAYS * 24 * 60 * 60 ){
                return 'urgent';
            }

            if( !$this->paid_time && $this->expire_time < time() ){
                return 'late';
            }

            if( !$this->paid_time ){
                return 'outstanding';
            }

            return 'outstanding';
        }

    }