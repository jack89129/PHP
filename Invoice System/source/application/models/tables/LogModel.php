<?php

    class LogModel extends Jaycms_Db_Model {

        const SOURCE_TYPE_INVOICE = 'invoice';
        const SOURCE_TYPE_PURCHASE = 'purchase';
        const SOURCE_TYPE_RECEIPT = 'receipt';
        const SOURCE_TYPE_PACK = 'pack';

        const EVENT_INVOICE_CREATED = 'invoice_created';
        const EVENT_INVOICE_SENT_EMAIL = 'invoice_sent_email';
        const EVENT_INVOICE_SENT_PERSONAL = 'invoice_sent_personal';
        const EVENT_INVOICE_PAYMENT = 'invoice_payment';
        const EVENT_INVOICE_PAID = 'invoice_paid';
        const EVENT_INVOICE_UNPAID = 'invoice_unpaid';
        const EVENT_INVOICE_ANNEX = 'invoice_annex';
        const EVENT_INVOICE_ANNEX_EDIT = 'invoice_annex_edit';
        const EVENT_INVOICE_CREDIT = 'invoice_credit';
        const EVENT_INVOICE_PROFORMA_STATUS = 'invoice_proforma_status';
        
        const EVENT_INVOICE_LATE = 'invoice_late';
        const EVENT_INVOICE_URGENT = 'invoice_urgent';
        const EVENT_INVOICE_JUDGE = 'invoice_judge';

        const EVENT_PROFORMA_TO_INVOICE = 'proforma_to_invoice';
        const EVENT_INVOICE_FROM_PROFORMA = 'invoice_from_proforma';

        const EVENT_PURCHASE_CREATED = 'purchase_created';
        const EVENT_PURCHASE_PAYMENT = 'purchase_payment';
        const EVENT_PURCHASE_PAID = 'purchase_paid';
        const EVENT_PURCHASE_ATTACHMENT = 'purchase_attachment';
        const EVENT_PURCHASE_ATTACHMENT_EDIT = 'purchase_attachment_edit';

        const EVENT_RECEIPT_CREATED = 'receipt_created';

        const EVENT_PACK_CREATED = 'pack_created';
        const EVENT_PACK_DELETED = 'pack_deleted';
        const EVENT_PACK_FINAL = 'pack_final';
        const EVENT_PACK_UNFINAL = 'pack_unfinal';
        const EVENT_PACK_PRODUCT_ADDED = 'pack_product_added';
        const EVENT_PACK_PRODUCT_REMOVED = 'pack_product_removed';


        const EVENT_MANUAL = 'manual';

        protected $_name = 'log';
    }