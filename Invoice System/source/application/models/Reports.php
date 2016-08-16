<?php

    class Reports {

        public static function getEarnings($dateFrom, $dateTo, Tag $tag, $group){
            $invoiceModel = new InvoiceModel();
            return $invoiceModel->getTotalByTag($dateFrom, $dateTo, $tag, $group);
        }

        public static function getExpenses($dateFrom, $dateTo, Tag $tag, $group){
            $purchaseModel = new PurchaseModel();
            return $purchaseModel->getTotalByTag($dateFrom, $dateTo, $tag, $group);
        }

        public static function getTagTotals($dateFrom, $dateTo, Tag $tag){
            if( $tag->category->type == TagCategoryModel::TYPE_INVOICE ){
                $invoiceModel = new InvoiceModel();
                return $invoiceModel->getTagTotals($dateFrom, $dateTo, $tag);
            }

            if( $tag->category->type == TagCategoryModel::TYPE_PURCHASE ){
                $purchaseModel = new PurchaseModel();
                return $purchaseModel->getTagTotals($dateFrom, $dateTo, $tag);
            }

            return array();
        }

        public static function getContactsUnpaidInvoicesTotal($dateFrom, $dateTo){
            $invoiceModel = new InvoiceModel();
            return $invoiceModel->getContactsUnpaidInvoicesTotal($dateFrom, $dateTo);
        }

        public static function getContactsUnpaidPurchasesTotal($dateFrom, $dateTo){
            $purchaseModel = new PurchaseModel();
            return $purchaseModel->getContactsUnpaidPurchasesTotal($dateFrom, $dateTo);
        }

        public static function getUnpaidInvoices($dateFrom, $dateTo, $contactId=0){
            $invoiceModel = new InvoiceModel();
            return $invoiceModel->getUnpaidInvoices($dateFrom, $dateTo, $contactId);
        }

        public static function getUnpaidPurchases($dateFrom, $dateTo, $contactId=0){
            $invoiceModel = new PurchaseModel();
            return $invoiceModel->getUnpaidPurchases($dateFrom, $dateTo, $contactId);
        }

        public static function vatInvoicesOverview($dateFrom, $dateTo){
            $invoiceModel = new InvoiceModel();
            return $invoiceModel->vatOverview($dateFrom, $dateTo);
        }

        public static function vatPurchasesOverview($dateFrom, $dateTo){
            $purchaseModel = new PurchaseModel();
            return $purchaseModel->vatOverview($dateFrom, $dateTo);
        }

        public static function vatInvoicesGovernment($dateFrom, $dateTo){
            $invoiceModel = new InvoiceModel();
            return $invoiceModel->vatGovernment($dateFrom, $dateTo);
        }

        public static function vatPurchasesGovernment($dateFrom, $dateTo){
            $purchaseModel = new PurchaseModel();
            return $purchaseModel->vatGovernment($dateFrom, $dateTo);
        }
        
        public static function vatOthersGovernment($dateFrom, $dateTo){
            $vatCategoryModel = new VatCategoryModel();
            $cats = $vatCategoryModel->getCategories("other");
            $result = array();
            foreach( $cats as $cat ) {
                $row = array();
                $row['category'] = "(" . $cat->code . ") " . $cat->name;
                $row['code'] = $cat->code;
                $row['total_excl_vat'] = 0;
                $row['vat_sum'] = 0;
                $result[] = $row;
            }
            
            return $result;
        }
    }