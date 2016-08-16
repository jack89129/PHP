<?php 

	/**
	 * @property int $id
	 * @property int $invoice_id
	 * @property int $product_id
     * @property int $tag_id
	 * @property string $description
	 * @property float $qty
	 * @property float $price
	 * @property int $vat
	 * @property float $total_sum
	 * 
	 * @property Invoice $invoice
	 * @property Product $product
     * @property Tag $tag
	 * 
	 * @author avladev
	 */
	class InvoiceProduct extends Core_ActiveRecord_Row {
		
		public function __construct($id=null){
            $this->id = null;
            $this->qty = 1;
            $this->invoice_id = null;
            $this->product_id = null;
            $this->tag_id = null;
            $this->description = '';
            $this->price = 0.0;
            $this->vat = SettingsModel::getInvoiceProductDefaultVAT();
            $this->total_sum = 0.0;
			parent::__construct(new InvoiceProductModel(), $id);
		}
		
		public function relations(){
			return array(
				'invoice' => array('Invoice', 'invoice_id', self::HAS_ONE),
				'product' => array('Product', 'product_id', self::HAS_ONE),
                'tag' => array('Tag', 'tag_id', self::HAS_ONE)
			);
		}
	}