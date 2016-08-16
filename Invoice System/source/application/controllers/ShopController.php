<?php

class ShopController extends Jaycms_Controller_Action
{

    public function init()
    {    
        //parent::init();
        $this->view->page_title = _t("Webshop");
        $this->view->page_sub_title = _t("demo...");
        $this->view->current_module = "webshop";
        $has_webshop = SettingsModel::getWebshopActivation();
        if ( $has_webshop != 'on' ) {
            $this->_redirect('/');
        }
        $contact = Utils::contact();
        $is_logged_in = $contact!=null ? true : false;
        
        $this->view->is_intro = 0;
        if ( $is_logged_in ) {
            $usrId = Utils::contact()->id;
            $contact = new Contact($usrId);
            $this->view->is_intro = $contact->is_intro;
        }
        
        $cart = Utils::cart();
        $result = array();
        $total = 0;
        $full_total = 0;
        $discount = 0;
        if ( !empty($cart) ) {
            foreach ( $cart as $row ) {
                $product = new Product($row['pid']);
                $product->qty = $row['qty'];
                $result[] = $product;
                if ( $product->has_new_price ) {
                    $total += $product->new_price * (100 - $product->new_discount) / 100 * $row['qty'];
                    $discount += $product->new_price * $product->new_discount / 100 * $row['qty'];
                    $full_total += $product->new_price * $row['qty'];
                } else {
                    $total += $product->price * (100 - $product->discount) / 100 * $row['qty'];
                    $discount += $product->price * $product->discount / 100 * $row['qty'];
                    $full_total += $product->price * $row['qty'];
                }
            }
        }
        
        $contactInfo = array();
        $addr_street = SettingsModel::getInvoiceProviderAddressStreet();
        $addr_num = SettingsModel::getInvoiceProviderAddressNum();
        $addr_post = SettingsModel::getInvoiceProviderAddressPost();
        $addr_city = SettingsModel::getInvoiceProviderAddressCity();
        $contactInfo['addr1'] = $addr_street.' '.$addr_num;
        $contactInfo['addr2'] = $addr_post.' '.$addr_city;
        $contactInfo['phone'] = SettingsModel::getInvoiceProviderPhone();
        $contactInfo['email'] = SettingsModel::getInvoiceProviderEmail();
        $contactInfo['website'] = SettingsModel::getInvoiceProviderWebsite();
        $contactInfo['btw'] = SettingsModel::getInvoiceProviderBTW();
        $contactInfo['country'] = "Belgium";
        
        $this->view->cart_data = $result;
        $this->view->total_cost = $total;
        $this->view->discount_cost = $discount;
        $this->view->full_total = $full_total;
        $this->view->contact_info = $contactInfo;
        
        $this->view->logged_in = $is_logged_in;
        $layout = $this->_helper->layout();
        $layout->setLayout('layout-shop');
    }

    public function indexAction()
    {
        // temp
        $product_id = $this->_getParam('product_id');
        // *****
        $productModel = new ProductModel();
        $result = $productModel->getWebshopProducts();
        $product = null;
        $group_name = "";
        $product = new Product($product_id);
        if ( !$product->exists() && !empty($result) ) {
            $product = new Product();
            $product->load($result[0]);
            $group_name = $result[0]['group_name'];
        }
        $this->view->result = $result;
        $this->view->product = $product;
        $this->view->group_name = $group_name;
        
        $this->view->page_sub_title = "Home";
    }
    
    public function accountAction()
    {
        $this->view->page_sub_title = "Mijn account";
        if ( !$this->view->logged_in ) {
            $this->_redirect('/shop/register');
        }
    }
    
    public function registerAction()
    {
        $this->view->page_sub_title = "Registreer of Log in";
        $err = $this->_getParam('err');
        if ( !empty($err) ) {
            $this->view->message = "Verkeerde login, probeer opnieuw!";
        }
        
        $this->view->contact = $contact;
    }
    
    public function changepwdAction()
    {
        $this->view->page_sub_title = "Wachtwoord veranderen";
        
        $curPass = $this->_getParam('curpass', null);
        $newPass = $this->_getParam('newpass', null);
        
        if ( $newPass != null ) {
            $usrId = Utils::contact()->id;
            $contact = new Contact($usrId);
            if ( $contact->pwd != Employee::saltPassword($curPass) ) {
                $this->view->message = "Current password didn't match!";
            } else {
                $contact->pwd = Employee::saltPassword($newPass);
                $contact->save();
                $this->_redirect('/shop/account');
            }
        }
    }
    
    public function editaccountAction()
    {
        $this->view->from = $this->_getParam('from', '');
        if ( $this->view->logged_in ) {
            $usrId = Utils::contact()->id;
            $contact = new Contact($usrId);
            $this->view->user = $contact;
            $this->view->page_sub_title = "Mijn gegevens wijzigen";
        } else {
            $this->view->user = new Contact();
            $this->view->page_sub_title = "Nieuw account maken";
        }
    }
    
    public function saveaccountAction()
    {
        $uid = $this->_getParam('uid', null);
        $company = $this->_getParam('company');
        $firstname = $this->_getParam('firstname');
        $lastname = $this->_getParam('lastname');
        $email = $this->_getParam('email');
        $btw = $this->_getParam('btw');
        $tele1 = $this->_getParam('tele1');
        $tele2 = $this->_getParam('tele2');
        $username = $this->_getParam('username');
        $usrpwd = $this->_getParam('userpwd', null);
        $address = $this->_getParam('address');
        $postcode = $this->_getParam('postcode');
        $city = $this->_getParam('city');
        $country = $this->_getParam('country');
        
        $contact = new Contact($uid);
        $contact->company_name = $company;
        $contact->firstname = $firstname;
        $contact->lastname = $lastname;
        $contact->email_address = $email;
        $contact->address = $address;
        $contact->postcode = $postcode;
        $contact->city = $city;
        $contact->country = $country;
        $contact->vat_number = $btw;
        $contact->phone1 = $tele1;
        $contact->phone2 = $tele2;
        $contact->username = $username;
        if ( $usrpwd != null ) {
            $contact->pwd = Employee::saltPassword($usrpwd);
        }
        $contact->save();
        
        $user = new Zend_Session_Namespace('contact');
        $user->id = $contact->id;
        
        $from = $this->_getParam('from');
        if ( $from != "" )
            $this->_redirect('/shop/'.$from);
        $this->_redirect('/shop/account');
    }
    
    public function orderAction()
    {
        $this->view->page_sub_title = "Bekijk bestelgeschiedenis";
        
        $usrId = Utils::contact()->id;
        $contact = new Contact($usrId);
        
        $invoiceModel = new InvoiceModel();
        $orders = $invoiceModel->getOrderInvoices($usrId);
        
        $this->view->order_list = $orders;
    }
    
    public function orderdetailAction()
    {
        
    }
    
    public function invoiceAction()
    {
        
    }
    
    public function ajaxloginAction()
    {
        $username = $this->_getParam('loginuser');
        $userpass = $this->_getParam('loginpass');
        
        $contactModel = new ContactModel();
        $contact = $contactModel->getContactByLogin($username, Employee::saltPassword($userpass));

        $flag = "failed";
        if( $contact != null ){
            $user = new Zend_Session_Namespace('contact');
            $user->id = $contact->id;
            $flag = "success";
        } 
        $result = array("result" => $flag, "contact" => $contact->data());
        $this->_helper->json($result);
    }
    
    public function signinAction()
    {
        $username = $this->_getParam('loginuser');
        $userpass = $this->_getParam('loginpass');
        
        $contactModel = new ContactModel();
        $contact = $contactModel->getContactByLogin($username, Employee::saltPassword($userpass));

        if( $contact != null ){
            $user = new Zend_Session_Namespace('contact');
            $user->id = $contact->id;
            $this->_redirect('/shop/account');
        }
        
        $this->_redirect('/shop/register?err=1');
    }
    
    public function signoutAction()
    {
        $contact = new Zend_Session_Namespace('contact');
        $contact->id = null;
        $this->_redirect('/shop');
    }
    
    public function createAction()
    {
        die();
    }
    
    public function cartAction()
    {
        $this->view->page_sub_title = "Mijn winkelwagen";
    }
    
    public function addcartAction()
    {
        $pid = intval($this->_getParam('product_id'));
        $qty = intval($this->_getParam('qty', 1));
        
        $cart = Utils::cart();
        
        $is_exist = false;
        if ( !empty($cart)) {
            foreach ( $cart as $key => $row ) {
                if ( $row['pid'] == $pid ) {
                    $row['qty'] = $row['qty'] + $qty;
                    $cart[$key] = $row;
                    $is_exist = true;
                    break;
                }
            }
        }
        
        if ( !$is_exist ) {
            $cart[] = array('pid' => $pid, 'qty' => $qty);
        }
        
        $cartSession = new Zend_Session_Namespace('cart');
        $cartSession->data = $cart;
        
        $this->_redirect('/shop/index');
    }
    
    public function checkoutAction()
    {
        $this->view->page_sub_title = "Afrekenen";
        
        $usrId = Utils::contact()->id;
        $contact = new Contact($usrId);
        $this->view->contact = $contact;
    }
    
    public function loginAction()
    {
        
    }
    
    public function saveorderAction()
    {
        $bill_company   = $this->_getParam('company');
        $bill_firstname = $this->_getParam('firstname');
        $bill_lastname  = $this->_getParam('lastname');
        $bill_address   = $this->_getParam('address');
        $bill_postcode  = $this->_getParam('postcode');
        $bill_city      = $this->_getParam('city');
        $bill_country   = $this->_getParam('country');
        
        $delivery_company   = $this->_getParam('delivery_company');
        $delivery_firstname = $this->_getParam('delivery_firstname');
        $delivery_lastname  = $this->_getParam('delivery_lastname');
        $delivery_address   = $this->_getParam('delivery_address');
        $delivery_postcode  = $this->_getParam('delivery_postcode');
        $delivery_city      = $this->_getParam('delivery_city');
        $delivery_country   = $this->_getParam('delivery_country');
        
        $include_condition = $this->_getParam('include_condition');
        
        $uid = Utils::contact()->id;
        $contact = new Contact($uid);
        $contact->company_name = $bill_company;
        $contact->firstname = $bill_firstname;
        $contact->lastname = $bill_lastname;
        $contact->address = $bill_address;
        $contact->postcode = $bill_postcode;
        $contact->city = $bill_city;
        $contact->country = $bill_country;
        $contact->delivery_firstname = $delivery_firstname;
        $contact->delivery_lastname = $delivery_lastname;
        $contact->delivery_address = $delivery_address;
        $contact->delivery_postcode = $delivery_postcode;
        $contact->delivery_city = $delivery_city;
        $contact->delivery_country = $delivery_country;
        $contact->save();
        $delivery_method = $this->_getParam('delivery_method');
        $payment_method = $this->_getParam('payment_method');
        $order_note = $this->_getParam('order_note');
        
        $order = new Order();
        $order->bill_company    = $bill_company;
        $order->bill_firstname  = $bill_firstname;
        $order->bill_lastname   = $bill_lastname;
        $order->bill_address    = $bill_address;
        $order->bill_postcode   = $bill_postcode;
        $order->bill_city       = $bill_city;
        $order->bill_country    = $bill_country;
        
        $order->delivery_company    = $delivery_company;
        $order->delivery_firstname  = $delivery_firstname;
        $order->delivery_lastname   = $delivery_lastname;
        $order->delivery_address    = $delivery_address;
        $order->delivery_postcode   = $delivery_postcode;
        $order->delivery_city       = $delivery_city;
        $order->delivery_country    = $delivery_country;
        
        $order->delivery_method     = $delivery_method;
        $order->payment_method      = $payment_method;
        $order->order_note          = $order_note;
        
        $order->contact_id  = Utils::contact()->id;
        $order->subtotal    = $this->view->full_total;
        $order->vat         = $this->view->full_total * 0.21;
        $order->total       = $this->view->full_total * 1.21;
        
        $order->save();
        
        $invoice = new Invoice();
        $invoice->contact_id = $order->contact_id;
        $invoice->proforma = 0;
        $invoice->credit = 0;
        $invoice->status = InvoiceModel::STATUS_FINAL;
        $invoice->from_webshop = InvoiceModel::WEBSHOP_INVOICE;
        
        if ( $this->view->is_intro == 1 ) {
            $invoice->total_sum = $this->view->total_cost;
            $invoice->vat_sum = 0;
            $invoice->total_excl_vat = $this->view->total_cost;
        } else {
            $invoice->total_sum = $this->view->total_cost * 1.21;
            $invoice->vat_sum = $this->view->total_cost * 0.21;
            $invoice->total_excl_vat = $this->view->total_cost;
        }
        $invoice->create();
        
        $cart = Utils::cart();
        if ( !empty($cart) ) {
            foreach ( $cart as $row ) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $row['pid'];
                $orderItem->quantity = $row['qty'];
                $orderItem->save();
                
                $product = new Product($row['pid']);
                $invoiceProduct = new InvoiceProduct();
                $invoiceProduct->qty = $row['qty'];
                $invoiceProduct->invoice_id = $invoice->id;
                $invoiceProduct->product_id = $row['pid'];
                $invoiceProduct->tag_id = $product->income_tag_id;
                $invoiceProduct->description = $product->name;
                if ( $product->has_new_price ) {
                    $invoiceProduct->price = $product->new_price*(100-$product->new_discount)/100;
                } else {
                    $invoiceProduct->price = $product->price*(100-$product->discount)/100;
                }
                $invoiceProduct->total_sum = $invoiceProduct->price * $invoiceProduct->qty;
                $invoiceProduct->save();
            }
        }   
        
        $cart = new Zend_Session_Namespace('cart');
        unset($cart->data);

        $subject = "Invoice from avaxo!";
        $body = "This is invoice from avaxo";
        
        $this->email($invoice->id, $contact->email_address, $subject, $body, $this->pdf($invoice->id), $include_condition);
     
        $this->_redirect('/shop/index');
    }
    
    protected function pdf($id){
        $name = $this->pdfName($id);
        $this->generatePDF($id, $name, 'F');
        return $name;
    }
    
    protected function pdfName($id){
           $file = tempnam(sys_get_temp_dir(), 'factuur-');
        $name = dirname($file) . '/factuur-' . $id . '-' . time() . '.pdf';
        if( !rename($file, $name) ){
            $name = $file;
        }
        
        return $name;
    }
    
    public function pdfAction(){
        $id = $this->_getParam('id');
        $invoice = new Invoice($id);
        $this->generatePDF($id, 'factuur-' . $id . '.pdf', 'D');
        die();
    }
    
    protected function email($id, $email, $subject, $body, $pdf, $include_condition){
        $invoice = new Invoice($id);
        
        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        Utils::activity('email', $invoice->proforma ? 'offer' : 'invoice', $invoice->id);

        $validator = new Zend_Validate_EmailAddress();

        if( !$validator->isValid($email) ){
            throw new Exception(_t('Invalid email address!'));
        }

        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        $log->data = $email;
        $log->event = LogModel::EVENT_INVOICE_SENT_EMAIL;
        $log->save();
        
        $subject = str_replace('<br>', '', $subject);
        
        // email 
        $mail = Mail::factory();
        $mail->setSubject($subject);
        $mail->addTo($email);
        $mail->setBodyHtml($body);
        $mail->createAttachment(    file_get_contents($pdf), 'application/pdf',
                                    Zend_Mime::DISPOSITION_ATTACHMENT,
                                    Zend_Mime::ENCODING_BASE64,
                                    basename($pdf));

        foreach( $invoice->annexes as $annex ){
            $mail->createAttachment( file_get_contents($annex->toPath()), mime_content_type($annex->toPath()),
                                     Zend_Mime::DISPOSITION_ATTACHMENT,
                                     Zend_Mime::ENCODING_BASE64,
                                     basename($annex->toPath()));
        }
        
        if ( !empty($include_condition) ) {
            $condPDF = SettingsModel::getWebshopConditionPDF();
            $mail->createAttachment( file_get_contents($condPDF, mime_content_type($condPDF),
                                     Zend_Mime::DISPOSITION_ATTACHMENT,
                                     Zend_Mime::ENCODING_BASE64,
                                     basename($condPDF)));
        }

        $mail->send();
    }
    
    protected function generatePDF($id, $name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $invoice = new Invoice($id);    
        if( !$invoice->exists() ){
            throw new Exception(_t("Invoice not found!"));
        }

        if( $invoice->parent_invoice_id && !$invoice->parent ){
            throw new Exception(_t("Cannot find invoice parent invoice!"));
        }
        
        $this->view->invoice = $invoice;

        //$header = $this->view->render('shop/pdf/header.phtml');
        //$footer = $this->view->render('shop/pdf/footer.phtml');
        $content = $this->view->render('shop/pdf/pdf.phtml');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        //$mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);
        
        $mpdf->Output($name, $destination);
    }
    
    public function removeAction()
    {
        $productId = (int) $this->_getParam('id', 0);
        $cart = Utils::cart();
        
        $tmp_price = 0;
        $is_exist = false;
        if ( !empty($cart)) {
            foreach ( $cart as $key => $row ) {
                if ( $row['pid'] == $productId ) {
                    $product = new Product($row['pid']);
                    if ( $product->has_new_price ) {
                        $tmp_price = $product->new_price * (100 - $product->new_discount) / 100 * $row['qty'];
                    } else {
                        $tmp_price = $product->price * (100 - $product->discount) / 100 * $row['qty'];
                    }
                    unset($cart[$key]);
                    break;
                }
            }
        }
        
        $cartSession = new Zend_Session_Namespace('cart');
        $cartSession->data = $cart;
        
        $this->view->total_cost -= $tmp_price;
        $result = array('total_price' => $this->view->total_cost);
        $this->_helper->json($result);
    }
    
    public function updatecountAction()
    {
        $productId = (int) $this->_getParam('id', 0);
        $count = intval($this->_getParam('count'));
        $cart = Utils::cart();
        
        $tmp_price = 0;
        $unit_price = 0;
        $sub_total = 0;
        if ( !empty($cart)) {
            foreach ( $cart as $key => $row ) {
                if ( $row['pid'] == $productId ) {
                    $product = new Product($row['pid']);
                    if ( $product->has_new_price ) {
                        $unit_price = $product->new_price * (100 - $product->new_discount) / 100;
                    } else {
                        $unit_price = $product->price * (100 - $product->discount) / 100;
                    }
                    $tmp_price = $unit_price * ($row['qty'] - $count);
                    $sub_total = $unit_price * $count;
                    $row['qty'] = $count;
                    $cart[$key] = $row;
                    break;
                }
            }
        }
        
        $cartSession = new Zend_Session_Namespace('cart');
        $cartSession->data = $cart;
        
        $this->view->total_cost -= $tmp_price;
        $result = array('total_price' => $this->view->total_cost, 'unit_price' => $unit_price, 'sub_total' => $sub_total);
        $this->_helper->json($result);
    }
    
    public function retrieveAction()
    {
        $productId = (int) $this->_getParam('id', 0);
        
        $product = new Product($productId);
        $product = $product->exists() ? $product : null ;
        $group_name = $product->group->name;
        
        $result = array();
        $result['content'] = $this->view->partial('shop/product-view.phtml', array('product' => $product, 'group_name' => $group_name));
        
        $this->_helper->json($result);
    }
    
    public function homeAction()
    {
        $productModel = new ProductModel();
        $result = $productModel->getWebshopProducts();
        
        $product = new Product();
        $product->load($result[0]);
        $group_name = $result[0]['group_name'];
        
        $sliderImage1 = SettingsModel::getWebshopHomeDefaultImage1();
        $sliderImage2 = SettingsModel::getWebshopHomeDefaultImage2();
        $sliderImage3 = SettingsModel::getWebshopHomeDefaultImage3();
        
        $sliderArray = array();
        $sliderArray[] = $sliderImage1;
        $sliderArray[] = $sliderImage2;
        $sliderArray[] = $sliderImage3;
        
        $this->view->result = $result;
        $this->view->product = $product;
        $this->view->group_name = $group_name;
        $this->view->default_slider = $sliderArray;
        
        $this->view->page_sub_title = "";
    }
    
    public function contactAction()
    {
        $this->view->page_sub_title = "Contacteer ons";
    }
    
    public function sendcontactAction(){
        $name = $this->_getParam('name');
        $email = $this->_getParam('email');
        $message = $this->_getParam('message'); 
        $subject = $name . " contacted from webshop!";
        $body = "Contact Name : " . $name . "<br>" . "Email Address : " . $email . "<br>" . $message;
        
        $contactEmail = SettingsModel::getInvoiceProviderEmail();
        
        try {
            $mail = Mail::factory();
            $mail->setSubject($subject);
            $mail->addTo($contactEmail);
            $mail->setBodyHtml($body);

            $mail->send();
        } catch ( Exception $e ) {
            
        }
    }

}

