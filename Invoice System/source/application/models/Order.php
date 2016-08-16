<?php

	/**
	 * @property int $id
     * @property int $contact_id
     * @property string $bill_company
	 * @property string $bill_firstname 	 
     * @property string $bill_lastname
     * @property string $bill_address
     * @property string $bill_postcode
     * @property string $bill_city
     * @property string $bill_country
     * @property string $delivery_company
     * @property string $delivery_firstname
     * @property string $delivery_lastname
     * @property string $delivery_address
     * @property string $delivery_postcode
     * @property string $delivery_city
     * @property string $delivery_country
     * @property float $subtotal
     * @property float $vat
     * @property float $total
     * @property string $delivery_method
     * @property string $payment_method
     * @property string $order_note
     * @property int $created_date
	 *  
	 * @author avladev
	 *
	 */
    class Order extends Core_ActiveRecord_Row {
    	public function __construct($id=null){
            $this->id = null;
            $this->contact_id = null;
            $this->bill_company = "";
            $this->bill_firstname = "";
            $this->bill_lastname = "";
            $this->bill_address = "";
            $this->bill_postcode = "";
            $this->bill_city = "";
            $this->bill_country = "";
            $this->delivery_company = "";
            $this->delivery_firstname = "";
            $this->delivery_lastname = "";
            $this->delivery_address = "";
            $this->delivery_postcode = "";
            $this->delivery_city = "";
            $this->delivery_country = "";
            $this->subtotal = 0.0;
            $this->vat = 0.0;
            $this->total = 0.0;
            $this->delivery_method = "";
            $this->payment_method = "";
            $this->order_note = "";

    		parent::__construct(new OrderModel(), $id);
    	}
    }
