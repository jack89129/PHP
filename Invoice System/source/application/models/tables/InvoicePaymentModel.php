<?php

    class InvoicePaymentModel extends Jaycms_Db_Model {
        protected $_name = 'invoice_payment';

        const PAYMENT_METHOD_BANK = 'bank';
        const PAYMENT_METHOD_CASH = 'cash';
    }