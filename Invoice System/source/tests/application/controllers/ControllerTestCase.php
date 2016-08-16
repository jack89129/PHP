<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
	protected $application;
    private $fast = false;
	
 	public function setUp()
    {  	
		$this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
	
	public function appBootstrap()
	{
	  	$this->application = new Zend_Application(APPLICATION_ENV,APPLICATION_PATH . '/configs/application.ini');
	  	$this->application->bootstrap();
        $config = $this->application->getOption('phpunit');

        if( !empty($config['fast']) ){
            $this->fast(true);
        }
    }

    protected function createInvoice(){
        $invoice = new Invoice();
        $invoice->number = SettingsModel::getInvoiceNextNum();
        $invoice->save();
        return new Invoice($invoice->id);
    }

    protected function createContact(){
        $contact = new Contact();
        $contact->firstname = md5(time());
        $contact->lastname = md5(time());
        $contact->address = md5(time());
        $contact->city = md5(time());
        $contact->email_address  = md5(time()) . '@gmail.com';
        $contact->save();
        return new Contact($contact->id);
    }

    protected function createInvoiceProduct($invoice_id=null, $product_id=null){
        $product = new InvoiceProduct();
        $product->description = md5(time());
        $product->price = 10;
        $product->total_sum = $product->qty * $product->price;
        $product->vat = SettingsModel::getInvoiceProductDefaultVAT();
        $product->invoice_id = $invoice_id;
        $product->product_id = $product_id;
        $product->save();
        return new InvoiceProduct($product->id);
    }

    protected function createProduct(){
        $product = new Product();
        $product->article_code = strtoupper(md5(time()));
        $product->save();

        return new Product($product->id);
    }

    protected function json(){
        return json_decode($this->getResponse()->getBody());
    }

    public function error(){
        if( $this->getResponse() && $this->getResponse()->isException() ){
            return $this->getResponse()->getBody();
        }

        return null;
    }

    public function fast($fast=null){
        if( $fast !== null ){
            $this->fast = (bool) $fast;
        }

        return $this->fast;
    }


	
    
}