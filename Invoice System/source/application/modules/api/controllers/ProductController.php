<?php

    class Api_ProductController extends Jaycms_Controller_Action {


        public function init(){
            parent::init();
        }

        public function getProductAction(){
            $product = new Product((int) $this->_getParam('id'));

            if( !$product->exists() ){
                throw new Exception(_t("Product not found!"));
            }

            $result = $product->data();
            $result['vat'] = $product->calculated_vat;
            $this->_helper->json(stringify($result));
        }

        public function getProductsAction(){
            $deleted = (int) $this->_getParam('deleted', 0);
            $products = Product::all();

            foreach( $products as $key => $product ){
                $products[$key] = $product->data();
                $products[$key]['vat'] = $product->calculated_vat;
            }

            $this->_helper->json(stringify($products));
        }

        public function getTopProductsAction(){
            $contact = (int) $this->_getParam('contact', 0);
            $limit   = (int) $this->_getParam('limit', 10);
            $productModel = new ProductModel();
            $products = $productModel->topProducts($contact, $limit);

            foreach( $products as $key => $product ){
                $products[$key] = $product->data();
            }

            $this->_helper->json(stringify($products));
        }
    }