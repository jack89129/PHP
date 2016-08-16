<?php
    /**
     * @property int $id
     * @property int $purchase_id
     * @property int $product_id
     * @property int $tag_id
     * @property string $description
     * @property float $total_sum
     * @property float $total_excl_vat
     * @property float $vat_sum
     * @property int $vat
     *
     * @property Purchase $purchase
     * @property Product $product
     * @property Tag $tag
     */

    class PurchaseProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            parent::__construct(new PurchaseProductModel(), $id);
        }

        public function relations(){
            return array(
                'purchase' => array('Purchase', 'purchase_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE),
                'tag' => array('Tag', 'tag_id', self::HAS_ONE)
            );
        }
    }