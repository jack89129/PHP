<?php

class InvoiceTest extends ControllerTestCase {

    public function testInvoiceDefaults(){
        $invoice = new Invoice();
        $this->assertSame(null, $invoice->id);
        $this->assertSame('', $invoice->number);
        $this->assertSame(null, $invoice->contact_id);
        $this->assertSame(0.0, $invoice->total_sum);
        $this->assertSame(0.0, $invoice->vat_sum);
        $this->assertSame(0.0, $invoice->discount_sum);
        $this->assertSame(null, $invoice->paid_time);
        $this->assertSame(0, $invoice->credit);
        $this->assertSame(SettingsModel::getInvoiceDefaultNotice(), $invoice->notice);
        $this->assertSame(InvoiceModel::STATUS_NEW, $invoice->status);
    }

    public function testInvoiceTime(){
        $invoice = new Invoice();
        $time = time();
        $date = strtotime(date('Y-m-d', $time));
        $invoice->invoice_time = $time;
        $this->assertEquals($date, $invoice->invoice_time);
    }

    public function testPaidTime(){
        $invoice = new Invoice();
        $time = time();
        $invoice->paid_time = $time;
        $this->assertEquals($time, $invoice->paid_time);
    }

    public function testIsFinal(){
        $invoice = new Invoice();
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $this->assertTrue($invoice->isFinal());
    }

    public function testIsNew(){
        $invoice = new Invoice();
        $invoice->status = InvoiceModel::STATUS_NEW;
        $this->assertTrue($invoice->isNew());
    }

    public function testIsCredit(){
        $invoice = new Invoice();
        $invoice->credit = 1;
        $this->assertTrue($invoice->isCredit());
    }

    public function testAddProduct(){
        $invoice = new Invoice();
        $invoice->addProduct(new InvoiceProduct());
        $this->assertEquals(1, count($invoice->products));
        $this->assertInstanceOf('InvoiceProduct', $invoice->products[0]);
    }
}