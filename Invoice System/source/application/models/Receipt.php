<?php

    /**
     * @property int $id
     * @property string $number
     * @property int $employee_id
     * @property int $contact_id
     * @property int $invoice_id
     * @property int $pack_id
     * @property string $status
     * @property int $created_time
     *
     * @property int $status_key
     * @property int $status_text
     * @property int $status_color
     *
     * @property Employee $employee
     * @property Contact $contact
     * @property ReceiptProduct[] $products
     * @property Invoice $invoice
     * @property Pack $pack
     */
    class Receipt extends Core_ActiveRecord_Row {


        protected static $STATUS_TEXT = array(
            'new'       => 'Concept',
            'reserved'  => 'Gereserveerd',
            'packed'    => 'In Transit',
            'invoiced'  => 'Gefactureerd'
        );

        protected static $STATUS_COLOR = array(
            'new'       => '',
            'reserved'  => 'color2',
            'packed'    => 'color',
            'invoiced'  => 'color4'
        );

        public function relations(){
            return array(
                'employee' => array('Employee', 'employee_id', self::HAS_ONE),
                'contact' => array('Contact', 'contact_id', self::HAS_ONE),
                'products' => array('ReceiptProduct', 'receipt_id', self::HAS_MANY),
                'invoice' => array('Invoice', 'invoice_id', self::HAS_ONE),
                'pack' => array('Pack', 'pack_id', self::HAS_ONE),
            );
        }

        public function __construct($id=null){
            parent::__construct(new ReceiptModel(), $id);
        }

        public function addProduct(ReceiptProduct $product){
            return $this->add('products', $product);
        }

        public function getCreatedTime(){
            return $this->get('created_time') != 0 ? strtotime($this->get('created_time')) : null;
        }

        public function setCreatedTime($value){
            $this->set('created_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : null);
        }

        public function formatNumber($number){
            return Constants::RECEIPT_NUMBER_PREFIX . str_repeat("0", Constants::RECEIPT_NUMBER_PADDING - strlen($number)) . $number;
        }

        public function nextNumber(){
            return SettingsModel::getReceiptNextNum();
        }

        /**
         * @param int $id
         */
        public static function receiptsToInvoice($ids){
            $contactId = null;
            $employeeId = null;
            $products = array();

            $receipt = new Receipt();
            $receipts = $receipt->findAll(array(array('id IN (?)', $ids)));

            foreach($receipts as $receipt){
                if( $contactId === null ){
                    $contactId = $receipt->contact_id;
                }

                if( !$receipt->employee_id ){
                    throw new Exception(_t("No employee selected for receipt %s!", $receipt->number));
                }

                if( $contactId != $receipt->contact_id ){
                    throw new Exception(_t("Different clients detected in selected receipts!"));
                }

                if( $receipt->invoice_id ){
                    throw new Exception(_t("Receipt already invoiced!"));
                }

                if( $receipt->status != ReceiptModel::STATUS_FINAL ){
                    throw new Exception(_t("Non final receipt %s!", $receipt->number));
                }

                if( !$receipt->pack || $receipt->pack->status != PackModel::STATUS_FINAL ){
                    throw new Exception(_t("Invoice can be only generated on final packing list!"));
                }

                foreach( $receipt->products as $product ){

                    if( $product->product_id ){
                        if( !array_key_exists($product->product_id, $products) ){
                            $products[$product->product_id] = array('product_id' => $product->product_id, 'description' => $product->description, 'qty' => 0);
                        }

                        $products[$product->product_id]['qty'] += $product->qty;
                    }else{
                        $products[] = array('product_id' => 0, 'description' => $product->description, 'qty' => $product->qty);
                    }
                }
            }

            $vatSum = 0;
            $totalSum = 0;

            foreach( $products as $key => $product ){
                $products[$key]['vat'] = reset(Constants::$VATS);
                $products[$key]['total_sum'] = 0;
                $products[$key]['price'] = 0;
                $products[$key]['tag_id'] = 0;

                if( $product['product_id'] ){
                    $p = new Product($product['product_id']);

                    if( !$p->exists() ){
                        throw new Exception(_t("Product not found!"));
                    }

                    $products[$key]['price'] = $p->price;
                    $products[$key]['tag_id'] = $p->income_tag_id;
                    $products[$key]['vat'] = $p->calculated_vat;
                }

                $products[$key]['total_sum'] = $products[$key]['qty'] * $products[$key]['price'];
                $vatSum += Utils::addVAT($products[$key]['total_sum'], $products[$key]['vat']) - $products[$key]['total_sum'];
                $totalSum += Utils::addVAT($products[$key]['total_sum'], $products[$key]['vat']);
            }

            $invoice = new Invoice();
            $invoice->model()->getAdapter()->beginTransaction();

            try {
                $invoice->contact_id = $contactId;
                $invoice->total_sum = $totalSum;
                $invoice->vat_sum = $vatSum;
                $invoice->total_excl_vat = $invoice->total_sum - $invoice->vat_sum;
                $invoice->create();

                foreach( $products as $product ){
                    $p = new InvoiceProduct();
                    $p->load($product);
                    $p->invoice_id = $invoice->id;
                    $p->save();
                }

                foreach( $receipts as $receipt ){
                    $receipt->invoice_id = $invoice->id;
                    $receipt->save();
                }

                $invoice->model()->getAdapter()->commit();
            }catch(Exception $e){
                $invoice->model()->getAdapter()->rollBack();
                throw $e;
            }

            return $invoice->id;
        }


        /**
         * @param int $id
         */
        public static function receiptsToPack($ids){
            $employeeId = null;
            $products = array();

            $receipt = new Receipt();
            $receipts = $receipt->findAll(array(array('id IN (?)', $ids)));

            foreach($receipts as $receipt){
                if( $employeeId === null ){
                    $employeeId = $receipt->employee_id;
                }

                if( !$receipt->employee_id ){
                    throw new Exception(_t("No employee selected for receipt %s!", $receipt->number));
                }

                if( $employeeId != $receipt->employee_id ){
                    throw new Exception(_t("Different employees detected in selected receipts!"));
                }

                if( $receipt->pack_id ){
                    throw new Exception(_t("Receipt already packed!"));
                }

                if( $receipt->status != ReceiptModel::STATUS_FINAL ){
                    throw new Exception(_t("Non final receipt %s!", $receipt->number));
                }
                
                $delivery_date = $receipt->delivery_date;

                foreach( $receipt->products as $product ){

                    if( $product->product_id ){
                        if( !array_key_exists($product->product_id, $products) ){
                            $products[$product->product_id] = array('product_id' => $product->product_id, 'description' => $product->description, 'qty' => 0);
                        }

                        $products[$product->product_id]['qty'] += $product->qty;
                    }else{
                        $products[] = array('product_id' => 0, 'description' => $product->description, 'qty' => $product->qty);
                    }
                }
            }

            $pack = new Pack();
            $pack->model()->getAdapter()->beginTransaction();

            try {
                $pack->employee_id = $employeeId;
                $pack->delivery_date = date('Y-m-d', strtotime($delivery_date));
                $pack->create();

                foreach( $products as $product ){
                    $p = new PackProduct();
                    $p->load($product);
                    $p->pack_id = $pack->id;
                    $p->save();
                }

                foreach( $receipts as $receipt ){
                    $receipt->pack_id = $pack->id;
                    $receipt->save();
                }

                $pack->model()->getAdapter()->commit();
            }catch(Exception $e){
                $pack->model()->getAdapter()->rollBack();
                throw $e;
            }

            return $pack->id;
        }

        public function getStatusKey(){
            if( $this->status == ReceiptModel::STATUS_NEW ){
                return 'new';
            }

            if( $this->invoice && $this->invoice->isFinal() ){
                return 'invoiced';
            }

            if( $this->pack && $this->pack->status == PackModel::STATUS_FINAL ){
                return 'packed';
            }

            if( $this->status == ReceiptModel::STATUS_FINAL ){
                return 'reserved';
            }

            throw new Exception(_t("Unknown receipt status!"));
        }

        public function getStatusText(){
            return self::$STATUS_TEXT[$this->status_key];
        }

        public function getStatusColor(){
            return self::$STATUS_COLOR[$this->status_key];
        }

    }