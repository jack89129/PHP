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

    class SettingPackProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            parent::__construct(new SettingPackProductModel(), $id);
        }

        public function relations(){
            return array(
                'pack' => array('SettingPack', 'pack_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE)
            );
        }
    }