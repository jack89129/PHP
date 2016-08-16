<?php
    /**
     * @property int $id
     * @property int $pack_id
     * @property int $product_id
     * @property string $description
     * @property int $qty
     *
     * @property Pack $pack
     * @property Product $product
     */

    class PackProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            parent::__construct(new PackProductModel(), $id);
        }

        public function relations(){
            return array(
                'pack' => array('Pack', 'pack_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE)
            );
        }
    }