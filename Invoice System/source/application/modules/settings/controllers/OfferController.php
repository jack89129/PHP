<?php

    class Settings_OfferController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Offerte instellingen");
            $this->view->page_sub_title = _t("Overzicht, settings en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            Utils::activity('offer', 'settings');
            
            $intro = SettingsModel::getProformaDefaultIntro();
            $subject = SettingsModel::getProformaDefaultEmailSubject();
            $message = SettingsModel::getProformaDefaultEmailBody();
            $late_subject = SettingsModel::getProformaLateEmailSubject();
            $late_message = SettingsModel::getProformaLateEmailBody();
            $urgent_subject = SettingsModel::getProformaUrgentEmailSubject();
            $urgent_message = SettingsModel::getProformaUrgentEmailBody();
            $judge_subject = SettingsModel::getProformaJudgeEmailSubject();
            $judge_message = SettingsModel::getProformaJudgeEmailBody();
            $footer = SettingsModel::getProformaDefaultNotice();
            
            $this->view->intro   = $intro;
            $this->view->subject = $subject;
            $this->view->message = $message;
            $this->view->late_subject = $late_subject;
            $this->view->late_message = $late_message;
            $this->view->urgent_subject = $urgent_subject;
            $this->view->urgent_message = $urgent_message;
            $this->view->judge_subject = $judge_subject;
            $this->view->judge_message = $judge_message;
            $this->view->footer  = $footer;
        }
        
        public function saveAction(){
            $settings = $this->_getParam('setting_offer');
            
            $settingModel = new SettingsModel();
            
            $settingModel->setSetting($settingModel->getProformaIntroKey(), $settings['intro']);
            $settingModel->setSetting($settingModel->getProformaMailSubjectKey(), $settings['subject']);
            $settingModel->setSetting($settingModel->getProformaMailBodyKey(), $settings['message']);
            $settingModel->setSetting($settingModel->getProformaLateEmailBodyKey(), $settings['late_message']);
            $settingModel->setSetting($settingModel->getProformaLateEmailSubjectKey(), $settings['late_subject']);
            $settingModel->setSetting($settingModel->getProformaUrgentEmailBodyKey(), $settings['urgent_message']);
            $settingModel->setSetting($settingModel->getProformaUrgentEmailSubjectKey(), $settings['urgent_subject']);
            $settingModel->setSetting($settingModel->getProformaJudgeEmailBodyKey(), $settings['judge_message']);
            $settingModel->setSetting($settingModel->getProformaJudgeEmailSubjectKey(), $settings['judge_subject']);
            $settingModel->setSetting($settingModel->getProformaDefaultNoticeKey(), $settings['footer']);
            
            $this->_helper->viewRenderer->setNoRender(true);
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
                        $result['subject'] = SettingsModel::getProformaDefaultEmailSubject();
                        $result['body'] = SettingsModel::getProformaDefaultEmailBody();
                        $result['title'] = "E-mailbericht nieuwe offerte";
                        break;
                    case 'late':
                        $result['subject'] = SettingsModel::getProformaLateEmailSubject();
                        $result['body'] = SettingsModel::getProformaLateEmailBody();
                        $result['title'] = "E-mailbericht offerteherinnering";
                        break;
                    case 'urgent':
                        $result['subject'] = SettingsModel::getProformaUrgentEmailSubject();
                        $result['body'] = SettingsModel::getProformaUrgentEmailBody();
                        $result['title'] = "E-mailbericht 2de offerteherinnering";
                        break;
                    case 'judge':
                        $result['subject'] = SettingsModel::getProformaJudgeEmailSubject();
                        $result['body'] = SettingsModel::getProformaJudgeEmailBody();
                        $result['title'] = "E-mailbericht laatste offerteherinnering";
                        break;
                    case 'thanks':
                        $result['subject'] = SettingsModel::getProformaThanksEmailSubject();
                        $result['body'] = SettingsModel::getProformaThanksEmailBody();
                        $result['title'] = "E-mailbericht ontvangen betaling";
                        break;
                }
                if ( $result['subject'] == "" ) $result['subject'] = "&nbsp;";
            } else {
                $result['intro'] = SettingsModel::getProformaDefaultIntro();
                $result['footer'] = SettingsModel::getProformaDefaultNotice();
                $result['title'] = "Offerte instellingen";
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
            
            $settingModel = new SettingsModel();
            
            switch ( $type ) {
                case 'default':
                    $settingModel->setSetting($settingModel->getProformaMailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaMailBodyKey(), $body);
                    break;
                case 'late':
                    $settingModel->setSetting($settingModel->getProformaLateEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaLateEmailBodyKey(), $body);
                    break;
                case 'urgent':
                    $settingModel->setSetting($settingModel->getProformaUrgentEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaUrgentEmailBodyKey(), $body);
                    break;
                case 'judge':
                    $settingModel->setSetting($settingModel->getProformaJudgeEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaJudgeEmailBodyKey(), $body);
                    break;
                case 'thanks':
                    $settingModel->setSetting($settingModel->getProformaThanksEmailSubjectKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaThanksEmailBodyKey(), $body);
                    break;
                case 'notice':
                    $settingModel->setSetting($settingModel->getProformaDefaultIntroKey(), $subject);
                    $settingModel->setSetting($settingModel->getProformaDefaultNoticeKey(), $body);
                    break;
            }
            
            $this->_helper->viewRenderer->setNoRender(true); 
        }
    }