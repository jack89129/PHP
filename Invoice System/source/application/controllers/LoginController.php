<?php

    class LoginController extends Jaycms_Controller_Action {

        public function init(){
            $this->_helper->layout()->setLayout('unauthorized');
        }

        public function indexAction(){
            if( Utils::user() ){
                $this->_redirect('/');
            }                    
            $success = $this->_getParam('success');
            if ( !empty($success) ) {
                if ( $success == 1 ) {
                    $this->view->msg = "U bent succesvol geregistreerd, gelieve uw account te activeren via de ontvangen e-mail!";
                } else if ( $success == 2 ) {
                    $this->view->msg = "Bedankt! Uw activatie was succesvol, u kunt nu inloggen!";
                } else if ( $success == 3 ) {
                    $this->view->msg = "Uw wachtwoord is succesvol aangepast, u kunt nu inloggen!!";
                }
            } else if( !empty($_POST) ){
                $username = $this->_getParam('username');
                $password = Employee::saltPassword($this->_getParam('password'));
                
                $db = Zend_Db_Table::getDefaultAdapter();
                
                $dbName = $db->fetchOne("select DATABASE();");
            
                if ( $dbName != 'avaxo_sysman' ) {
                    Utils::locate_db('avaxo_sysman');
                }    
                
                $db = Zend_Db_Table::getDefaultAdapter();
                
                $employee = $db->query('SELECT * FROM `user` WHERE username = ? AND password = ? AND (status = 1 OR status = 2)', array($username, $password))->fetch();
                
                if( $employee ){
                    $user = new Zend_Session_Namespace('user');
                    $user->id = $employee['id'];
                    $user->database = $employee['database'];
                    
                    Utils::locate_db($employee['database']);
                    
                    if ( SettingsModel::getSystemYear() != date('Y') ){
                        $settingModel = new SettingsModel();
                        $settingModel->setSetting(SettingsModel::getSystemYearKey(), date('Y'));
                        $settingModel->setSetting(SettingsModel::getInvoiceCurrentNumKey(), "1");
                        $settingModel->setSetting(SettingsModel::getProformaCurrentNumKey(), "1");
                        $settingModel->setSetting(SettingsModel::getPurchaseCurrentNumKey(), "1");
                    }
                                                               
                    $this->_redirect("/");
                }

                $this->view->username = $username;
                $this->view->error = 'Gebruikersnaam of wachtwoord onjuist!';
            }
        }

        public function logoutAction(){
            $user = new Zend_Session_Namespace('user');
            $user->id = null;    
            $minstock_products = new Zend_Session_Namespace('min_stock_products');
            $minstock_products->id_list = null;
            $this->_redirect('/');
        }
    }