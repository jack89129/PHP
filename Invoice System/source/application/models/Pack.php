<?php

    /**
     * @property int $id
     * @property string $number
     * @property int $employee_id
     * @property int $status
     * @property int $created_time
     *
     * @property string $status_key
     * @property string $status_text
     * @property string $status_color
     *
     * @property Employee $employee
     * @property Contact $contact
     * @property PackProduct[] $products
     * @property Receipt[] $receipts
     */
    class Pack extends Core_ActiveRecord_Row {

        protected static $STATUS_TEXT = array(
            'new'       => 'Concept',
            'packed'    => 'In Transit',
            'invoiced'  => 'Gefactureerd'
        );

        protected static $STATUS_COLOR = array(
            'new'       => '',
            'packed'    => 'color5',
            'invoiced'  => 'color4'
        );


        public function relations(){
            return array(
                'employee' => array('Employee', 'employee_id', self::HAS_ONE),
                'products' => array('PackProduct', 'pack_id', self::HAS_MANY),
                'receipts' => array('Receipt', 'pack_id', self::HAS_MANY)
            );
        }

        public function __construct($id=null){
            parent::__construct(new PackModel(), $id);
        }

        public function addProduct(PackProduct $product){
            return $this->add('products', $product);
        }

        public function getCreatedTime(){
            return $this->get('created_time') != 0 ? strtotime($this->get('created_time')) : null;
        }

        public function setCreatedTime($value){
            $this->set('created_time', $value ? date(Constants::MYSQL_DAY_FORMAT, $value) : null);
        }

        public function formatNumber($number){
            return Constants::PACK_NUMBER_PREFIX . str_repeat("0", Constants::PACK_NUMBER_PADDING - strlen($number)) . $number;
        }

        public function nextNumber(){
            return SettingsModel::getPackNextNum();
        }

        public function create(){
            if( $this->exists() ){
                throw new Exception(_t("Pack already exists!"));
            }

            if( !$this->number ){
                $this->number = $this->formatNumber($this->nextNumber());
            }

            if( !$this->created_time ){
                $this->created_time = time();
            }
            
            if( !$this->delivery_date ){
                $this->delivery_date = date('Y-m-d');
            }

            $this->save();
        }

        public function getStatusKey(){
            if( $this->status == PackModel::STATUS_NEW ){
                return 'new';
            }

            foreach( $this->receipts as $receipt ){
                if( $receipt->invoice && $receipt->invoice->isFinal() ){
                    return 'invoiced';
                }
            }

            if( $this->status == PackModel::STATUS_FINAL ){
                return 'packed';
            }

            throw new Exception(_t("Unknown pack status!"));
        }

        public function getStatusText(){
            return self::$STATUS_TEXT[$this->status_key];
        }

        public function getStatusColor(){
            return self::$STATUS_COLOR[$this->status_key];
        }

    }