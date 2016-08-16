<?php

    class Api_InvoiceController extends Jaycms_Controller_Action {


        public function init(){
            parent::init();
        }

        public function getInvoiceAction(){
            $invoice = new Invoice((int) $this->_getParam('id'));

            if( !$invoice->exists() ){
                throw new Exception(_t("Invoice not found!"));
            }

            $this->_helper->json(stringify($invoice->data()));
        }

        public function getInvoiceProductsAction(){
            $invoice = new Invoice((int) $this->_getParam('id'));

            if( !$invoice->exists() ){
                throw new Exception(_t("Invoice not found!"));
            }

            $products = array();
            foreach( $invoice->products as $product ){
                $products[] = $product->data();
            }

            $this->_helper->json(stringify($products));
        }

        public function listAction(){
            $page = (int) $this->_getParam('page', 0);
            $page = $page > 0 ? $page : 0 ;

            $per_page = (int) $this->_getParam('per_page', 5);
            $per_page = $per_page > 0 ? $per_page : 5;

            $credit = (int) $this->_getParam('credit', 0);
            $credit = $credit ? 1 : 0;

            $contact = (int) $this->_getParam('contact', 0);
            $contact = $contact > 0 ? $contact : 0 ;

            $total = 0;
            $sum = 0;
            $sum_no_vat = 0;
            $invoiceModel = new InvoiceModel();
            $invoices = $invoiceModel->findInvoices('all', $contact, Utils::user()->id, 0, $credit, 0, 0, $total, $sum, $sum_no_vat, $per_page, $page);

            $result = array();
            $result['per_page'] = $per_page;
            $result['page'] = $page;
            $result['total'] = $total;
            $result['sum'] = $sum;
            $result['sum_no_vat'] = $sum_no_vat;
            $result['invoices'] = array();

            foreach( $invoices as $invoice ){
                $result['invoices'][] = $invoice->data();
            }

            $this->_helper->json(stringify($result));
        }

        public function getInvoiceContactAction(){
            $invoice = new Invoice((int) $this->_getParam('id'));

            if( !$invoice->exists() ){
                throw new Exception(_t("Invoice not found!"));
            }

            $this->_helper->json($invoice->contact ? stringify($invoice->contact->data()) : null );
        }

        public function getTagsAction(){
            $tagCategory = new TagCategoryModel();
            $categories = $tagCategory->getCategoriesByType(TagCategoryModel::TYPE_INVOICE);

            $result = array();
            foreach( $categories as $category ){
                $r = array();
                $r['category'] = $category->data();
                $r['tags'] = array();
                foreach( $category->tags as $tag ){
                    $r['tags'][] = $tag->data();
                }
                $result[] = $r;
            }

            $this->_helper->json(stringify($result));
        }

        public function saveInvoiceAction(){
            $final = 1;

            $invoiceParam = $this->_getParam('invoice');
            $invoiceParam['id'] = !empty($invoiceParam['id']) ? $invoiceParam['id'] : null ;
            $invoiceParam['invoice_time'] = date(Constants::MYSQL_DAY_FORMAT, time());
            //$invoiceParam['expire_time'] = date(Constants::MYSQL_DATE_FORMAT, strtotime('+ ' . Constants::INVOICE_PAY_DAYS . ' DAYS', time()));
            $invoiceParam['expire_time'] = date(Constants::MYSQL_DATE_FORMAT, strtotime('+ ' . SettingsModel::getInvoiceB2CPaymentTerm() . ' DAYS', time()));

            $productParam = $this->_getParam('invoice_product', array());
            $orderedProducts = $productParam;


            $invoice = new Invoice($invoiceParam['id']);

            if( $invoice->exists() && $invoice->isFinal() ){
                throw new Exception(_t("Invoice is final and it cannot be edited!"));
            }

            $invoice->load($invoiceParam);

            $newProducts = array();

            $invoice_total_excl_vat = 0.0;
            $invoice_total_incl_vat = 0.0;

            foreach( $orderedProducts as $key => $product ){
                if( !isset($product['qty']) || empty($product['product_id']) || !isset($product['price']) || !isset($product['discount']) || empty($product['vat']) ){
                    throw new Exception(_t("Invalid product. Information missing!"));
                }

                if( $product['qty'] < 1 ){
                    continue;
                }


                $p = new Product($product['product_id']);

                if( !$p->exists() ){
                    throw new Exception(_t("Product not found!"));
                }

                $product['tag_id'] = $p->income_tag ? $p->income_tag->id : 0;
                if( empty($product['description']) ){
                    $product['description'] = $p->name;
                }


                $product['id'] = 0;
                $product['total_sum'] = $product['qty'] * $product['price'];

                $invoice_total_incl_vat += $product['total_sum'] + $product['total_sum'] * ($product['vat']/100);
                $invoice_total_excl_vat += $product['total_sum'] - $product['total_sum'] * ($product['discount']/100) ;

                $invoice->vat_sum += $product['total_sum'] * ($product['vat']/100);
                $invoice->discount_sum += $product['total_sum'] * ($product['discount']/100);

                $product['total_sum'] -= $product['total_sum'] * ($product['discount']/100);
                $product['description'] = Utils::strip_bad_tags($product['description']);

                $newProducts[] = (object) $product;
            }


            $invoice->discount_sum += $invoice_total_excl_vat * ($invoice->discount/100);
            $invoice->total_sum = $invoice_total_incl_vat - $invoice->discount_sum;
            $invoice->total_excl_vat = $invoice_total_excl_vat;

            $products = $invoice->products;

            foreach( $products as $index => $product ){
                if( array_key_exists($index, $newProducts) ){
                    $newProducts[$index]->id = $product->id;
                    $product = new InvoiceProduct();
                    $product->load($newProducts[$index]);
                    $products[$index] = $product;
                    unset($newProducts[$index]);
                }else{
                    $product->delete();
                    unset($products[$index]);
                }
            }

            foreach( $newProducts as $product ){
                $newProduct = new InvoiceProduct();
                $newProduct->load($product);
                $products[] = $newProduct;
            }

            if( !$invoice->contact_id ){
                throw new Exception(_t("Contact missing!"));
            }

            if( !$invoice->contact ){
                throw new Exception(_t("Contact not found!"));
            }
            
            if ( $invoice->contact->is_b2b == 1 ) {
                $invoice->expire_time = date(Constants::MYSQL_DATE_FORMAT, strtotime('+ ' . SettingsModel::getInvoiceB2BPaymentTerm() . ' DAYS', time()));
            }

            if( $invoice->total_sum <= 0 ){
                throw new Exception(_t("Zero invoice!"));
            }


            $invoice->model()->getAdapter()->beginTransaction();

            try {
                if( $invoice->exists() ){
                    $invoice->save();
                }else{
                    $invoice->create();
                }

                foreach( $products as $product ){
                    $product->invoice_id = $invoice->id;
                    $product->save();
                }

                $invoice->finalize();

                $invoice->model()->getAdapter()->commit();
            }catch( Exception $e ){
                $invoice->model()->getAdapter()->rollBack();
                throw $e;
            }

            $this->_helper->json(array('id' => stringify($invoice->id)));
        }

        public function addPaymentAction(){
            $invoiceId = (int) $this->_getParam('id', 0);
            $amount = $this->_getParam('amount', 0);

            $invoice = new Invoice($invoiceId);

            if( !$invoice->exists() ){
                throw new Exception(_t('Invoice not found!'));
            }


            $payment = $invoice->addPayment($amount ? $amount : $invoice->unpaid_sum, time(), InvoicePaymentModel::PAYMENT_METHOD_CASH);
            $this->_helper->json(array('id' => stringify($payment->data())));
        }

        public function getUnpaidSumAction(){
            $invoiceId = (int) $this->_getParam('id', 0);

            $invoice = new Invoice($invoiceId);

            if( !$invoice->exists() ){
                throw new Exception(_t('Invoice not found!'));
            }

            $this->_helper->json(array('sum' => stringify($invoice->unpaid_sum)));
        }

        public function pdfAction(){
            $type = $this->_getParam('type');
            if ( $type == 'day' ) {
                $this->_forward('pdfday', 'index', 'invoices'); 
            } else if ( $type == 'stock' ) {
                $this->_forward('pdfstock', 'index', 'invoices');
            } else {
                $this->_forward('pdf', 'index', 'invoices');
            }
        }

        public function signatureAction(){
            $invoiceId = $this->_getParam('id');

            $invoice = new Invoice($invoiceId);

            if( !$invoice->exists() ){
                throw new Exception(_t('Invoice not found!'));
            }

            $invoice->addSignature($_FILES['signature']);

            $this->_helper->json(array());
        }
    }