<?php

    class InvoiceProductTest extends ControllerTestCase {

        public function testProductDefaults(){
            $product = new InvoiceProduct();
            $this->assertSame(null, $product->id);
            $this->assertSame(null, $product->invoice_id);
            $this->assertSame(null, $product->product_id);
            $this->assertSame(0.0, $product->total_sum);
            $this->assertSame(0.0, $product->price);
            $this->assertSame(SettingsModel::getInvoiceProductDefaultVAT(), $product->vat);
            $this->assertSame(1, $product->qty);
            $this->assertSame('', $product->description);
        }
    }