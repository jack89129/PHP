<?php
    /**
     * @property int $id
     * @property int $wholesaler_id
     * @property int $product_id
     * @property int $tag_id
     * @property string $description
     * @property float $total_sum
     * @property float $total_excl_vat
     * @property float $vat_sum
     * @property int $vat
     *
     * @property Wholesaler $wholesaler
     * @property Product $product
     * @property Tag $tag
     */

    class WholesalerProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            parent::__construct(new WholesalerProductModel(), $id);
        }

        public function relations(){
            return array(
                'wholesaler' => array('Wholesaler', 'wholesaler_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE),
                'tag' => array('Tag', 'tag_id', self::HAS_ONE)
            );
        }
    }