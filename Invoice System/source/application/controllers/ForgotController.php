<?php

    class ForgotController extends Jaycms_Controller_Action {

        public function init(){
            $this->_helper->layout()->setLayout('unauthorized');
        }

        public function indexAction(){                    
            if( !empty($_POST) ){
                $email = $this->_getParam('email');
                
                $db = Zend_Db_Table::getDefaultAdapter();
                
                $dbName = $db->fetchOne("select DATABASE();");
            
                if ( $dbName != 'avaxo_sysman' ) {
                    Utils::locate_db('avaxo_sysman');
                }    
                
                $db = Zend_Db_Table::getDefaultAdapter();
                
                $employee = $db->query('SELECT * FROM `user` WHERE email = ?', array($email))->fetch();
                
                if( $employee ){
                    $db->query('UPDATE `user` SET status = 2 WHERE email = ?', array($email));
                    
                    $subject = "Wachtwoord vergeten!";
                    $body = "Dear " . $employee['firstname'] . ' ' . $employee['name'] . ", <br><br>";
                    $body .= '<a href="http://www.avaxo.be/forgot/reset?user='. base64_encode($employee['username']) . '">http://www.avaxo.be/forgot/reset?user='. base64_encode($employee['username']) . '</a><br><br>';
                    
                    $mail = Email::factory();
                    $mail->setSubject($subject);
                    $mail->addTo($email);
                    $mail->setBodyHtml($body);
                    $mail->send();
                                                               
                    $this->view->msg = "Er werd een nieuw wachtwoord naar het ingegeven e-mailadres verzonden!";
                } else {
                    $this->view->error = "E-mailadres niet gevonden!";
                    $this->view->email = $email;
                }                                 
            }
        } 
        
        private function saltPassword($pass){                  
            $config = Zend_Registry::get('config');
            return sha1($pass . $config['PASSWD_SALT']);
        }
        
        public function resetAction(){
            $username = base64_decode($this->_getParam('user'));   
            Utils::locate_db('avaxo_sysman');
            $db = Zend_Db_Table::getDefaultAdapter();
            $employee = $db->query('SELECT * FROM `user` WHERE username = ? AND status = 2', array($username))->fetch();
            
            if ( empty($employee) ) {
                $this->_redirect("/login");  
                return;
            }
            if( !empty($_POST) ){
                $pwd = $this->_getParam('usrpwd');
                $pass = self::saltPassword($pwd);
                $db->query('UPDATE `user` SET password = ?, status = 1 WHERE username = ?', array($pass, $username));
                $this->_redirect("/login?success=3");
            } 
            $this->view->username = $employee['username'];
        }     
    }