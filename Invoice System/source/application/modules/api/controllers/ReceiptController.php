<?php

    class Api_ReceiptController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();
        }

        public function getReceiptAction(){
            $receipt = new Receipt((int) $this->_getParam('id'));

            if( !$receipt->exists() ){
                throw new Exception(_t("Receipt not found!"));
            }

            $this->_helper->json(stringify($receipt->data()));
        }

        public function getReceiptProductsAction(){
            $receipt = new Receipt((int) $this->_getParam('id'));

            if( !$receipt->exists() ){
                throw new Exception(_t("Invoice not found!"));
            }

            $products = array();
            foreach( $receipt->products as $product ){
                $products[] = $product->data();
            }

            $this->_helper->json(stringify($products));
        }

        public function getReceiptContactAction(){
            $receipt = new Receipt((int) $this->_getParam('id'));

            if( !$receipt->exists() ){
                throw new Exception(_t("Receipt not found!"));
            }

            $this->_helper->json($receipt->contact ? stringify($receipt->contact->data()) : null );
        }

        public function getReceiptEmployeeAction(){
            $receipt = new Receipt((int) $this->_getParam('id'));

            if( !$receipt->exists() ){
                throw new Exception(_t("Receipt not found!"));
            }

            $result = null;
            if( $receipt->employee ){
                $result = $receipt->employee->data();
                unset($result['password']);
                unset($result['username']);
            }

            $this->_helper->json(stringify($result));
        }
        
        public function saveReceiptAction(){
            $receiptParam = $this->_getParam('receipt');
            $productParam = $this->_getParam('receipt_product', array());

            $orderedProducts = array();
            foreach( $productParam as $ord ){
                $orderedProducts[] = $ord;
            }
            
            $receipt = new Receipt();
            $receipt->number = $receipt->formatNumber($receipt->nextNumber());
            $receipt->employee_id = Utils::user()->id;
            $receipt->contact_id = $receiptParam["contact_id"] ? $receiptParam["contact_id"] : 0;
            $receipt->created_time = time();
            $receipt->created_by = Utils::user()->id;
            $receipt->status = ReceiptModel::STATUS_FINAL;
            $receipt->delivery_date = date('Y-m-d', time() + 7 * 24 * 60 * 60);
            if ( !empty($receiptParam['delivery_date']) ) {
                $receipt->delivery_date = date('Y-m-d', $receiptParam['delivery_date']);
            }

            if( !empty($receiptParam['employee_id']) ){
                $receipt->employee_id = $receiptParam['employee_id'];
            }

            $receipt->info = $receiptParam['info'] ? $receiptParam["info"] : "";
            $receipt->save();

            $newProducts = array();

            foreach( $productParam as $key => $product ){
                if( $product['qty'] <= 0 && !$product['product_id'] ){
                    continue;
                }
                
                $prod = new Product($product['product_id']);
                if ( !$prod->exists() ) {
                    continue;
                }
                
                $product['description'] = $prod->name;
                $newProducts[] = (object) $product;
            }


            $products = $receipt->products;

            foreach( $products as $index => $product ){
                if( array_key_exists($index, $newProducts) ){
                    $newProducts[$index]->id = $product->id;
                    $product = new ReceiptProduct();
                    $product->load($newProducts[$index]);
                    $products[$index] = $product;
                    unset($newProducts[$index]);
                }else{
                    $product->delete();
                    unset($products[$index]);
                }
            }

            foreach( $newProducts as $product ){
                $newProduct = new ReceiptProduct();
                $newProduct->load($product);
                $products[] = $newProduct;
            }

            $receipt->model()->getAdapter()->beginTransaction();

            try {
                $receipt->save();

                foreach( $products as $product ){
                    $product->receipt_id = $receipt->id;
                    $product->save();
                }

                $receipt->model()->getAdapter()->commit();
            }catch( Exception $e ){
                $receipt->model()->getAdapter()->rollBack();
                throw $e;
            }

            $this->_helper->json((object) $receipt->data());
        }

        public function listAction(){
            $page = (int) $this->_getParam('page', 0);
            $page = $page > 0 ? $page : 0 ;

            $per_page = (int) $this->_getParam('per_page', 5);
            $per_page = $per_page > 0 ? $per_page : 5;

            $contact = (int) $this->_getParam('contact', 0);
            $contact = $contact > 0 ? $contact : 0 ;

            $total = 0;
            $sum = 0;
            $sum_no_vat = 0;
            $receiptModel = new ReceiptModel();
            $receipts = $receiptModel->findEmployeeReceipts(Utils::user()->id, $contact, $total, $per_page, $page);

            $result = array();
            $result['per_page'] = $per_page;
            $result['page'] = $page;
            $result['total'] = $total;
            $result['sum'] = $sum;
            $result['sum_no_vat'] = $sum_no_vat;
            $result['receipts'] = array();

            foreach( $receipts as $receipt ){
                $result['receipts'][] = $receipt->data();
            }

            $this->_helper->json(stringify($result));
        }
    }