<?php                                               

    class RegisterController extends Jaycms_Controller_Action {

        public function init(){
            $this->_helper->layout()->setLayout('unauthorized');
        }

        public function indexAction(){
            if( !empty($_POST) ){
                $user = $this->_getParam('user');
                Utils::locate_db('avaxo_sysman');
                $db = Zend_Db_Table::getDefaultAdapter();
                $customer = $db->query('SELECT * FROM `user` WHERE username = ? ', array($user['username']))->fetch();
                $chk = $db->query('SELECT * FROM `user` WHERE email = ?', array($user['email']))->fetch();
                if ( $customer ) {
                    $this->view->error = "Gebruikersnaam reeds in gebruik!";
                    $this->view->name = $user['name'];
                    $this->view->firstname = $user['firstname'];
                    $this->view->company = $user['company'];
                    $this->view->email = $user['email'];
                    return;
                }
                if ( $chk ) {
                    $this->view->error = "E-mailadres reeds in gebruik!";
                    $this->view->name = $user['name'];
                    $this->view->firstname = $user['firstname'];
                    $this->view->company = $user['company'];
                    $this->view->email = $user['email'];
                    return;
                }
                $pwd = self::saltPassword($user['password']);
                $database = 'avaxo_' . $user['username'];
                $db->query('INSERT INTO `user`(company, name, firstname, username, email, password, `database`) VALUES(?, ?, ?, ?, ?, ?, ?)', array($user['company'], $user['name'], $user['firstname'], $user['username'], $user['email'], $pwd, $database));
                $customer = $db->query('SELECT * FROM `user` WHERE username = ?', array($user['username']))->fetch();
                $cid = $customer['id'];
                
                $db->query('CREATE DATABASE ' . $database);                
                $db->beginTransaction();                
                $sql = 'USE ' . $database . '; ' . file_get_contents(APPLICATION_PATH . '/dump/structure.sql');  
                $db->query($sql);
                 
                $sql = 'USE ' . $database . '; ' . file_get_contents(APPLICATION_PATH . '/dump/data.sql');  
                $db->query($sql);
                $db->query("USE " . $database . "; INSERT INTO `employee` (`id`, `firstname`, `lastname`, `address`, `postcode`, `city`, `country`, `email_address`, `phone`, `role`, `username`, `password`) VALUES
                    (".$cid.", '".$user['name']."', '".$user['firstname']."', '', '', '', 'BE', '', '', '', '".$user['username']."', '".$pwd."');");
                    
                $db->query("USE " . $database . "; INSERT INTO `right_employee_map` (`right_id`, `employee_id`) VALUES              
                    (1, $cid),
                    (2, $cid),
                    (3, $cid),
                    (4, $cid),
                    (5, $cid),
                    (6, $cid),
                    (7, $cid),
                    (8, $cid),
                    (9, $cid),
                    (10, $cid),
                    (11, $cid),
                    (12, $cid),
                    (13, $cid),
                    (14, $cid),
                    (15, $cid),
                    (16, $cid),
                    (17, $cid),
                    (18, $cid),
                    (19, $cid);");           
                
                $db->commit();
                
                Utils::locate_db($database);
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                $sql = 'USE ' . $database . '; ' . file_get_contents(APPLICATION_PATH . '/dump/procedure.sql');  
                $db->query($sql);
                $sql = "USE " . $database . "; UPDATE settings SET value = '".$user['email']."' WHERE name = '" . SettingsModel::INVOICE_PROVIDER_EMAIL . "';
                    UPDATE settings SET value = '".$user['email']."' WHERE name = '" . SettingsModel::MAIL_FROM_ADDRESS . "';
                    UPDATE settings SET value = '".$user['name']."' WHERE name = '" . SettingsModel::MAIL_FROM_NAME . "';
                    UPDATE settings SET value = '".$user['email']."' WHERE name = '" . SettingsModel::MAIL_FROM_ADDRESS . "';
                    UPDATE settings SET value = '".$user['company']."' WHERE name = '" . SettingsModel::INVOICE_PROVIDER_COMPANY . "';
                    UPDATE settings SET value = '".$user['company']."' WHERE name = '" . SettingsModel::MAIL_FROM_NAME . "';";
                $db->query($sql);
                $db->commit();
                
                $validator = new Zend_Validate_EmailAddress();

                if( !$validator->isValid($user['email']) ){
                    throw new Exception(_t('Invalid email address!'));
                }                              
                                                      
                self::sendMail($user['name'], $user['firstname'], $user['email'], $user['username']);

                /*$log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
                $log->source_id = $invoice->id;
                $log->data = $email;
                $log->event = LogModel::EVENT_INVOICE_SENT_EMAIL;
                $log->save();*/  
                $this->_redirect("/login?success=1");         
                
            }
        }            
        
        public function checkExistAction(){
            $username = $this->_getParam('username');
            Utils::locate_db('avaxo_sysman');
            $db = Zend_Db_Table::getDefaultAdapter();
            $customer = $db->query('SELECT * FROM `user` WHERE username = ? ', array($username))->fetch();
            $result = false;
            if ( $customer ) {
                $result = true;
            }
            $this->_helper->json(array('is_exist' => $result));
        }                                            
        
        public function confirmAction(){
            $username = base64_decode($this->_getParam('user'));   
            Utils::locate_db('avaxo_sysman');
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->query('UPDATE `user` SET status = 1 WHERE username = ?', array($username));
            $this->_redirect("/login?success=2");  
        }
        
        public function saltPassword($pass){                  
            $config = Zend_Registry::get('config');
            return sha1($pass . $config['PASSWD_SALT']);
        }
        
        private function sendMail($name, $firstname, $email, $username){       
            $subject = "Simpel Facturatie - Activatie";
            /*$body = "Welkom " . $firstname . ' ' . $name . ", <br><br>";
            $body .= "Wij heten u van harte welkom op <a href='www.simpelfacturatie.nl'>www.simpelfacturatie.nl</a>! Het makkelijkste facturatie systeem op het internet.<br><br>";
            $body .= "Wij willen u alvast bedanken voor je registratie op onze website, echter is het noodzakelijk dat u uw e-mail bij ons systeem bevestigd. Dit kan op een simpele manier door gewoon op onderstaande link te klikken :<br>";
            $body .= '<a href="http://www.avaxo.be/register/confirm?user='. base64_encode($username) . '">http://www.avaxo.be/register/confirm?user='. base64_encode($username) . '</a><br><br>';
            $body .= "Indien het niet lukt via bovenstaande link, gelieve dan de link te kopiëren en plakken in uw browser.<br><br>";
            $body .= "Na de bevestiging van uw e-mail adres kunt u meteen van start met uw facturatie op www.simpelfacturatie.nl<br><br>";
            $body .= "Met vriendelijke groeten,<br>";
            $body .= "Het Simpel Facturatie team!";*/
            
            $body = '<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<!-- Define Charset -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- Responsive Meta Tag -->
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />

    <title>Simpel Facturatie - Activatie</title><!-- Responsive Styles and Valid Styles -->

    <style type="text/css">
    
        
        body{
            width: 100%; 
            background-color: #ecf1f5; 
            margin:0; 
            padding:0; 
            -webkit-font-smoothing: antialiased;
        }
        p,h1,h2,h3,h4{
            margin-top:0;
            margin-bottom:0;
            padding-top:0;
            padding-bottom:0;
        }

        span.preheader{display: none; font-size: 1px;}
        
        html{
            width: 100%; 
        }
        
        table{
            font-size: 14px;
            border: 0;
        }
        
        
        /* ----------- responsivity ----------- */
        @media only screen and (max-width: 640px){
            
            /*----- main image -------*/
            .main-image{width: 440px !important; height: auto !important;}
            
            /*-------- container --------*/            
            .container{width: 440px !important;}
            .mainContent{width: 420px !important;}
            
            /*-------- secions ----------*/
            
            .section-item{width: 420px !important;}
            .section-img{width: 420px !important; height: auto !important;}
            
            /*------- footer ------*/
            .footer-bg{width: 440px !important; height: auto !important;}
        }
        
        @media only screen and (max-width: 479px){
            
            
            /*------- header ----------*/
            .logo{width: 280px !important;}
            .nav{width: 280px !important;}
            
            /*----- main image -------*/
            .main-image{width: 280px !important; height: auto !important;}
            
            /*-------- container --------*/            
            .container{width: 280px !important;}
            .mainContent{width: 260px !important;}
            
            /*-------- secions ----------*/
            
            .section-item{width: 260px !important;}
            .section-img{width: 260px !important; height: auto !important;}
            
            .cta{width: 260px !important;}
            /*------- footer ------*/
            .footer-bg{width: 280px !important; height: auto !important;}
            
            .unsubscribe{width: 240px !important;}
            
        }
        
</style>
    
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table style="width: 100%;" bgcolor="ecf1f5" border="0" cellpadding="0" cellspacing="0"><!-------------- header ------------->
<tbody>
<tr>
<td align="center" bgcolor="ffffff">
<table style="width: 580px;" class="container" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td height="30"></td>
</tr>
<tr>
<td>
<table style="width: 182px; height: 84px;" class="logo" align="left" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td align="center"><img src="http://www.avaxo.be/images/mail/logo.jpg" height="105" width="220" /></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td height="25"></td>
</tr>
</tbody>
</table>
</td>
</tr>
<!-------------- end header ---------------><!-------------- main image ------------>
<tr>
<td align="center">
<table style="width: 580px;" class="container" align="center" bgcolor="ffffff" border="0" cellpadding="0" cellspacing="0">
<tbody><!------ end main section ----------><!---------- cta --------------->
<tr mc:repeatable="">
<td align="center">
<table style="width: 580px;" class="container" align="center" bgcolor="dae3ec" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td height="45"></td>
</tr>
<tr>
<td align="center">
<table style="width: 400px;" class="cta" align="center" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td mc:edit="title6" style="color: #596064; font-size: 18px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; line-height: 24px;" align="center">Welkom '.$firstname.' '.$name.'</td>
</tr>
<tr>
<td height="25"></td>
</tr>
<tr>
<td mc:edit="subtitle6" style="color: #596064; font-size: 13px; font-family: Helvetica, Arial, sans-serif; line-height: 24px;" align="center">
<p><multiline>Wij heten u van harte welkom op <a>www.simpelfacturatie.nl</a>! Het makkelijkste facturatie systeem op het internet.<br /><br />Wij willen u alvast bedanken voor je registratie op onze website, echter is het noodzakelijk dat u uw e-mail bij ons systeem bevestigd. Dit kan op een simpele manier door gewoon op onderstaande knop te klikken<br /></multiline></p>
<p></p>
<p><multiline>Indien het niet lukt via onderstaande knop, gelieve dan de link te kopieren en plakken in uw browser.<br /><br />Na de bevestiging van uw e-mail adres kunt u meteen van start met uw facturatie op www.simpelfacturatie.nl</multiline></p>
<p><multiline><a href="http://www.avaxo.be/register/confirm?user=cHNZY2gw" target="_blank"></a><br />Met vriendelijke groeten,<br />Het Simpel Facturatie team!</multiline></p>
</td>
</tr>
<tr>
<td height="30"></td>
</tr>
<tr>
<td align="center">
<p><a href="http://www.avaxo.be/register/confirm?user='. base64_encode($username) . '"><img src="http://www.avaxo.be/images/mail/activeer_account.png" height="40" width="160" /></a></p>
<p></p>
<p style="color: #596064; font-size: 13px; font-family: Helvetica, Arial, sans-serif;">Alternatieve link :<br /><a href="http://www.avaxo.be/register/confirm?user='. base64_encode($username) . '" target="_blank">http://www.avaxo.be/register/confirm?user='. base64_encode($username) . '</a></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td height="45"></td>
</tr>
</tbody>
</table>
</td>
</tr>
<!---------- end cta ---------------> <!---------- footer --------------->
<tr>
<td align="center">
<table style="width: 580px;" class="container" align="center" bgcolor="ffffff" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td height="5"></td>
</tr>
<tr>
<td align="center">
<table style="width: 540px;" class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td mc:edit="copy" style="color: #87909c; font-size: 12px; font-family: Helvetica, Arial, sans-serif;" align="center"><multiline>&nbsp; Simpel Facturatie <span style="color: #cad4de;">&copy; Copyright 2013</span> </multiline></td>
</tr>
<tr>
<td align="center">&nbsp;<a target="_blank" href="http://www.facebook.com/simpelfacturatie"><img src="http://www.avaxo.be/images/mail/facebook.png" height="36" width="36" /></a>&nbsp;&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</body>
</html>';                                                                                                           
            
            $mail = Email::factory();
            $mail->setSubject($subject);
            $mail->addTo($email);
            $mail->setBodyHtml($body);
            $mail->send();
        }
        
    }