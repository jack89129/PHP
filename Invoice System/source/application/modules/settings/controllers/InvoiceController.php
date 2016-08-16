<?php

    class Settings_InvoiceController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Factuur instellingen");
            $this->view->page_sub_title = _t("Overzicht, settings en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            Utils::activity('invoice', 'settings');
            
            $intro = SettingsModel::getInvoiceDefaultIntro();
            $subject = SettingsModel::getInvoiceDefaultEmailSubject();
            $message = SettingsModel::getInvoiceDefaultEmailBody();
            $late_subject = SettingsModel::getInvoiceLateEmailSubject();
            $late_message = SettingsModel::getInvoiceLateEmailBody();
            $urgent_subject = SettingsModel::getInvoiceUrgentEmailSubject();
            $urgent_message = SettingsModel::getInvoiceUrgentEmailBody();
            $judge_subject = SettingsModel::getInvoiceJudgeEmailSubject();
            $judge_message = SettingsModel::getInvoiceJudgeEmailBody();
            $interest_subject = SettingsModel::getInvoiceInterestEmailSubject();
            $interest_message = SettingsModel::getInvoiceInterestEmailBody();
            $footer = SettingsModel::getInvoiceDefaultNotice();
            $b2b_payment_term = SettingsModel::getInvoiceB2BPaymentTerm();
            $b2b_first_term = SettingsModel::getInvoiceB2BFirstReminderTerm();
            $b2b_second_term = SettingsModel::getInvoiceB2BSecondReminderTerm();
            $b2b_last_term = SettingsModel::getInvoiceB2BLastReminderTerm();
            $b2b_has_interest = SettingsModel::getInvoiceB2BHasInterest();
            $b2b_interest_rate = SettingsModel::getInvoiceB2BInterestRate();
            $b2b_interest_term = SettingsModel::getInvoiceB2BInterestTerm();
            $b2b_auto_sendmail = SettingsModel::getInvoiceB2BAutoSendEmail();
            $b2c_payment_term = SettingsModel::getInvoiceB2CPaymentTerm();
            $b2c_first_term = SettingsModel::getInvoiceB2CFirstReminderTerm();
            $b2c_second_term = SettingsModel::getInvoiceB2CSecondReminderTerm();
            $b2c_last_term = SettingsModel::getInvoiceB2CLastReminderTerm();
            $b2c_has_interest = SettingsModel::getInvoiceB2CHasInterest();
            $b2c_interest_rate = SettingsModel::getInvoiceB2CInterestRate();
            $b2c_interest_term = SettingsModel::getInvoiceB2CInterestTerm();
            $b2c_auto_sendmail = SettingsModel::getInvoiceB2CAutoSendEmail();
            
            $this->view->intro   = $intro;
            $this->view->subject = $subject;
            $this->view->message = $message;
            $this->view->late_subject = $late_subject;
            $this->view->late_message = $late_message;
            $this->view->urgent_subject = $urgent_subject;
            $this->view->urgent_message = $urgent_message;
            $this->view->judge_subject = $judge_subject;
            $this->view->judge_message = $judge_message;
            $this->view->interest_subject = $interest_subject;
            $this->view->interest_message = $interest_message;
            $this->view->footer  = $footer;
            $this->view->b2b_payment_term   = $b2b_payment_term;
            $this->view->b2b_first_term   = $b2b_first_term;
            $this->view->b2b_second_term   = $b2b_second_term;
            $this->view->b2b_last_term   = $b2b_last_term;
            $this->view->b2b_has_interest   = $b2b_has_interest;
            $this->view->b2b_interest_rate   = $b2b_interest_rate;
            $this->view->b2b_interest_term   = $b2b_interest_term;
            $this->view->b2b_auto_sendmail   = $b2b_auto_sendmail;
            $this->view->b2c_payment_term   = $b2c_payment_term;
            $this->view->b2c_first_term   = $b2c_first_term;
            $this->view->b2c_second_term   = $b2c_second_term;
            $this->view->b2c_last_term   = $b2c_last_term;
            $this->view->b2c_has_interest   = $b2c_has_interest;
            $this->view->b2c_interest_rate   = $b2c_interest_rate;
            $this->view->b2c_interest_term   = $b2c_interest_term;
            $this->view->b2c_auto_sendmail   = $b2c_auto_sendmail;
        }
        
        public function fillPopupAction(){
            $result = array();
            $result['type'] = $this->_getParam('type');
            
            if ( $result['type'] != 'notice' ) {
                $result['subject'] = "";
                $result['body'] = "";
                $result['title'] = "";
                switch ( $result['type'] ) {
                    case 'default':
                        $result['subject'] = SettingsModel::getInvoiceDefaultEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceDefaultEmailBody();
                        $result['title'] = "E-mailbericht nieuwe factuur";
                        break;
                    case 'late':
                        $result['subject'] = SettingsModel::getInvoiceLateEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceLateEmailBody();
                        $result['title'] = "E-mailbericht factuurherinnering";
                        break;
                    case 'urgent':
                        $result['subject'] = SettingsModel::getInvoiceUrgentEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceUrgentEmailBody();
                        $result['title'] = "E-mailbericht 2de factuurherinnering";
                        break;
                    case 'judge':
                        $result['subject'] = SettingsModel::getInvoiceJudgeEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceJudgeEmailBody();
                        $result['title'] = "E-mailbericht laatste factuurherinnering";
                        break;
                    case 'interest':
                        $result['subject'] = SettingsModel::getInvoiceInterestEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceInterestEmailBody();
                        $result['title'] = "E-mailbericht interest factuurherinnering";
                        break;
                    case 'thanks':
                        $result['subject'] = SettingsModel::getInvoiceThanksEmailSubject();
                        $result['body'] = SettingsModel::getInvoiceThanksEmailBody();
                        $result['title'] = "E-mailbericht ontvangen betaling";
                        break;
                }
                if ( $result['subject'] == "" ) $result['subject'] = "&nbsp;";
            } else {
                $result['intro'] = SettingsModel::getInvoiceDefaultIntro();
                $result['footer'] = SettingsModel::getInvoiceDefaultNotice();
                $result['title'] = "Factuur instellingen";
            }
            
            $this->_helper->json($result);   
        }
        
        public function saveTemplateAction(){
            $type = $this->_getParam('type');
            if ( $type != 'notice' ) {
                $subject = $this->_getParam('subject');
                $body = $this->_getParam('body');
            } else {
                $subject = $this->_getParam('intro');
                $body = $this->_getParam('footer');
            }
            
            $subject = str_replace('&nbsp;', '', $subject);
            $body = str_replace('<br />', '<br>', $body);
            
            $settingModel = new SettingsModel();      
            
            switch ( $type ) {
                case 'default':
                    $settingModel->setSetting($settingModel->getInvoiceMailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceMailBodyKey(), $body);
                    break;
                case 'late':
                    $settingModel->setSetting($settingModel->getInvoiceLateEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceLateEmailBodyKey(), $body);
                    break;
                case 'urgent':
                    $settingModel->setSetting($settingModel->getInvoiceUrgentEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceUrgentEmailBodyKey(), $body);
                    break;
                case 'judge':
                    $settingModel->setSetting($settingModel->getInvoiceJudgeEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceJudgeEmailBodyKey(), $body);
                    break;
                case 'interest':
                    $settingModel->setSetting($settingModel->getInvoiceInterestEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceInterestEmailBodyKey(), $body);
                    break;
                case 'thanks':
                    $settingModel->setSetting($settingModel->getInvoiceThanksEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceThanksEmailBodyKey(), $body);
                    break;
                case 'notice':
                    $settingModel->setSetting($settingModel->getInvoiceDefaultIntroKey(), $subject);
                    $settingModel->setSetting($settingModel->getInvoiceDefaultNoticeKey(), $body);
                    break;
            }
            
            $this->_helper->viewRenderer->setNoRender(true); 
        }
        
        public function saveAction(){
            $settings = $this->_getParam('setting_invoice');
            
            $settingModel = new SettingsModel();
            
            /*$settingModel->setSetting($settingModel->getInvoiceIntroKey(), $settings['intro']);
            $settingModel->setSetting($settingModel->getInvoiceMailSubjectKey(), $settings['subject']);
            $settingModel->setSetting($settingModel->getInvoiceMailBodyKey(), $settings['message']);
            $settingModel->setSetting($settingModel->getInvoiceLateEmailBodyKey(), $settings['late_message']);
            $settingModel->setSetting($settingModel->getInvoiceLateEmailSubjectKey(), $settings['late_subject']);
            $settingModel->setSetting($settingModel->getInvoiceUrgentEmailBodyKey(), $settings['urgent_message']);
            $settingModel->setSetting($settingModel->getInvoiceUrgentEmailSubjectKey(), $settings['urgent_subject']);
            $settingModel->setSetting($settingModel->getInvoiceJudgeEmailBodyKey(), $settings['judge_message']);
            $settingModel->setSetting($settingModel->getInvoiceJudgeEmailSubjectKey(), $settings['judge_subject']);
            $settingModel->setSetting($settingModel->getInvoiceNoticeKey(), $settings['footer']);*/
            
            $settingModel->setSetting($settingModel->getInvoiceB2BPaymentTermKey(), $settings['b2b_payment_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2BFirstReminderTermKey(), $settings['b2b_first_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2BSecondReminderTermKey(), $settings['b2b_second_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2BHasInterestKey(), $settings['b2b_has_interest']);
            $settingModel->setSetting($settingModel->getInvoiceB2BInterestRateKey(), $settings['b2b_interest_rate']);
            $settingModel->setSetting($settingModel->getInvoiceB2BInterestTermKey(), $settings['b2b_interest_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2BAutoSendEmailKey(), $settings['b2b_autosendemail']);
            $settingModel->setSetting($settingModel->getInvoiceB2CPaymentTermKey(), $settings['b2c_payment_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2CFirstReminderTermKey(), $settings['b2c_first_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2CSecondReminderTermKey(), $settings['b2c_second_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2CHasInterestKey(), $settings['b2c_has_interest']);
            $settingModel->setSetting($settingModel->getInvoiceB2CInterestRateKey(), $settings['b2c_interest_rate']);
            $settingModel->setSetting($settingModel->getInvoiceB2CInterestTermKey(), $settings['b2c_interest_term']);
            $settingModel->setSetting($settingModel->getInvoiceB2CAutoSendEmailKey(), $settings['b2c_autosendemail']);
            
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
