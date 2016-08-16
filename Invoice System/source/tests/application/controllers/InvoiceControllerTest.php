<?php

class IndexControllerTest extends ControllerTestCase  
{

    private $mc = '/invoices/index/';

    private function checkMC(){
        list($module, $controller) = explode("/", trim($this->mc, "/\\"));
        $this->assertModule($module);
        $this->assertController($controller);
    }

	public function testIndexAction()
	{
        $this->dispatch($this->mc);
        $this->checkMC();
        $this->assertAction("index");
	}

    public function testValidActiveTab(){
        $this->dispatch( $this->mc . 'active-tab/tab/urgent');
        $json = json_decode($this->getResponse()->getBody());
        $this->assertSame('urgent', $json->tab);
    }

    public function testInvalidActiveTab(){
        $this->dispatch('/invoices/index/active-tab/tab/' . md5(time()));
        $this->assertController('error');
    }

    public function testPdfAction(){
//        $invoice = $this->createInvoice();
//        $this->dispatch('/invoices/index/pdf/id/' . $invoice->id);
//        $this->checkMC();
//        $this->assertHeader('Content-type', 'application/pdf');
    }

    public function testContactChangedAction(){
        $contact = $this->createContact();

        $this->dispatch($this->mc . 'contact-changed/id/' . $contact->id);
        $contact->delete();

        $json = json_decode($this->getResponse()->getBody());
        $this->assertEquals($contact->number, $json->number);
    }

    public function testProductChangedAction(){
        $product = $this->createProduct();
        $index = 2;

        $this->dispatch($this->mc . 'product-changed/id/' . $product->id . '/index/' . $index);
        $product->delete();
        $this->checkMC();

        $json = $this->json();
        $this->assertEquals($product->id, $json->product->id);
        $this->assertEquals($index, $json->index);
    }

    public function testProductChangedActionInvalidProduct(){
        $product = $this->createProduct();
        $product->delete();
        $index = 2;

        $this->dispatch($this->mc . 'product-changed/id/' . $product->id . '/index/' . $index);
        $this->assertController('error');
    }

    public function testNewAction(){
        $this->dispatch( $this->mc . 'new');

        $this->checkMC();
        $this->assertAction('new');
        $this->assertResponseCode(200);
    }

    public function testNewActionFinalInvoice(){
        $invoice = $this->createInvoice();
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->save();
        $this->dispatch($this->mc . 'new/id/' . $invoice->id);
        $this->assertController('error');
    }

    public function testNewActionInvalidInvoiceParent(){
        $parent = $this->createInvoice();
        $invoice = $this->createInvoice();
        $invoice->parent_invoice_id = $parent->id;
        $invoice->save();

        $parent->delete();
        $this->dispatch($this->mc . 'new/id/' . $invoice->id);
        $invoice->delete();

        $this->assertController('error');
    }

    public function testViewActionExistingInvoice(){
        $contact = $this->createContact();
        $invoice = $this->createInvoice();
        $invoice->contact_id = $contact->id;
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->save();

        $this->dispatch( $this->mc . 'view/id/' . $invoice->id);
        $invoice->delete();
        $contact->delete();

        $this->checkMC();
        $this->assertAction('view');
        $this->assertResponseCode(200);
    }

    public function testViewActionExistingInvoiceWithoutParent(){
        $parent = $this->createInvoice();

        $invoice = $this->createInvoice();
        $invoice->contact_id = 1;
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->parent_invoice_id = $parent->id;
        $invoice->save();

        $parent->delete();

        $this->dispatch('/invoices/index/view/id/' . $invoice->id);
        $invoice->delete();

        $this->assertController('error');
    }

    public function testViewActionUnexistingInvoiceContact(){
        $contact = $this->createContact();
        $invoice = $this->createInvoice();
        $invoice->contact_id = $contact->id;
        $invoice->number = SettingsModel::getInvoiceNextNum();
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->save();

        $contact->delete();

        $this->dispatch( $this->mc . 'view/id/' . $invoice->id);
        $invoice->delete();

        $this->assertController('error');
    }

    public function testViewActionExistingInvoiceRedirectToNew(){
        $invoice = $this->createInvoice();
        $invoice->status = InvoiceModel::STATUS_NEW;
        $invoice->save();

        $this->dispatch($this->mc . 'view/id/' . $invoice->id);
        $invoice->delete();

        $this->checkMC();
        $this->assertAction('view');
        $this->assertRedirect();
    }

    public function testViewActionUnexistingInvoice(){
        $this->dispatch($this->mc . 'view/id/-1');
        $this->assertController('error');
    }

    public function testSendInvoiceFillAction(){
        $contact = $this->createContact();
        $invoice = $this->createInvoice();
        $invoice->contact_id = $contact->id;
        $invoice->save();


        $this->dispatch($this->mc . 'send-invoice-fill/?invoice[id]=' . $invoice->id);
        $invoice->delete();
        $contact->delete();

        $this->checkMC();
        $this->assertAction('send-invoice-fill');
        $json = $this->json();

        $this->assertEquals($contact->email_address, $json->email);
        $this->assertEquals(SettingsModel::getInvoiceDefaultEmailSubject(), $json->subject);
        $this->assertContains($contact->name, $json->body);
        $this->assertContains($invoice->number, $json->body);
    }

    public function testSendInvoiceFillActionInvalidInvoice(){
        $this->dispatch($this->mc . 'send-invoice-fill/?invoice[id]=-1');
        $this->assertController('error');
    }

    public function testSendInvoiceFillActionIvalidContact(){
        $contact = $this->createContact();
        $invoice = $this->createInvoice();
        $invoice->contact_id = $contact->id;
        $invoice->save();
        $contact->delete();

        $this->dispatch($this->mc . 'send-invoice-fill/?invoice[id]=' . $invoice->id);
        $invoice->delete();

        $this->assertController('error');
    }

    public function testFinalAction(){
        $contact = $this->createContact();
        $invoice = $this->createInvoice();
        $invoice->contact_id = $contact->id;
        $invoice->save();

        $this->getRequest()->setParams(array(
            'invoice' => array('id' => $invoice->id),
            'email' => 'avladev@gmail.com',
            'send' => $this->fast() ? 0 : 1,
            'subject' => 'Test subject',
            'body' => 'Test body'
        ));
        $this->dispatch($this->mc . 'final');
        echo $this->error();
        $invoice->delete();
        $contact->delete();

        $this->checkMC();
        $this->assertAction('final');
        $json = $this->json();
        $this->assertContains((string) $invoice->id, $json->redirect);
    }

    public function testFinalActionInvalidInvoice(){
        $invoice = $this->createInvoice();
        $invoice->delete();

        $this->dispatch($this->mc . 'final/?invoice[id]=' . $invoice->id);
        $this->assertController('error');
    }

    public function testEmailAction(){
        if( $this->fast() ){
            return;
        }

        $invoice = $this->createInvoice();
        $this->getRequest()->setParams(array(
                'invoice' => array('id' => $invoice->id),
                'email' => 'avladev@gmail.com',
                'subject' => 'Test subject',
                'body' => 'Test body'
            )
        );

        $this->dispatch($this->mc . 'email');
        $invoice->delete();

        $this->checkMC();
        $json = $this->json();
        $this->assertEquals(1, $json->success);
    }

    public function testAddRowAction(){
        $this->dispatch($this->mc . 'add-row');
        $this->assertAction('add-row');
        $this->assertResponseCode(200);
    }

    public function testProductAutocompleteAction(){
        $product = $this->createProduct();

        $this->dispatch($this->mc . 'product-autocomplete?term=' . $product->name);
        $product->delete();

        $this->checkMC();
        $json = $this->json();
        $this->assertEquals(1, count($json));
    }

}
