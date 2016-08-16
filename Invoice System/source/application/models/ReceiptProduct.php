<?php
    /**
     * @property int $id
     * @property int $receipt_id
     * @property int $product_id
     * @property string $description
     * @property int $qty
     *
     * @property Receipt $receipt
     * @property Product $product
     */

    class ReceiptProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            parent::__construct(new ReceiptProductModel(), $id);
        }

        public function relations(){
            return array(
                'receipt' => array('Receipt', 'receipt_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE)
            );
        }
    }