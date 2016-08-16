<?php

    class PurchasePaymentModel extends Jaycms_Db_Model {
        protected $_name = 'purchase_payment';

        const PAYMENT_METHOD_BANK = 'bank';
        const PAYMENT_METHOD_CASH = 'cash';
    }