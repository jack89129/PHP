<?php

	/**
	 * @property int $id
	 * @property int $order_id
	 * @property int $product_id
     * @property int $quantity
	 *  
	 * @author avladev
	 *
	 */
    class OrderItem extends Core_ActiveRecord_Row {

    	public function __construct($id=null){
            $this->id = null;
            $this->order_id = null;
            $this->product_id = null;
            $this->quantity = 0;

    		parent::__construct(new OrderItemModel(), $id);
    	}

    }
