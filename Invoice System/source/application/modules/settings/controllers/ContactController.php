<?php

    class Settings_ContactController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Algemene instellingen");
            $this->view->page_sub_title = _t("Overzicht, settings en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            Utils::activity('contact', 'settings');
            
            $settingModel = new SettingsModel();
            
            $user = new Zend_Session_Namespace('user');
            $emp_id = $user->id;
            $firstname = $user->firstname;
            $lastname = $user->lastname;
            
            $is_welcome = $this->_getParam('first');
            
            if ( !empty($is_welcome) ) {
                $settingModel = new SettingsModel();                                                             
                $settingModel->setSetting(SettingsModel::getInvoiceProviderBankNumberKey(), '&nbsp;');
            }
            
            $company = SettingsModel::getInvoiceProviderCompany();
            $addr_street = SettingsModel::getInvoiceProviderAddressStreet();
            $addr_num = SettingsModel::getInvoiceProviderAddressNum();
            $addr_post = SettingsModel::getInvoiceProviderAddressPost();
            $addr_city = SettingsModel::getInvoiceProviderAddressCity();
            $phone = SettingsModel::getInvoiceProviderPhone();
            $email = SettingsModel::getInvoiceProviderEmail();
            $website = SettingsModel::getInvoiceProviderWebsite();   
            $bankname = SettingsModel::getInvoiceProviderBankname();
            $bankloc = SettingsModel::getInvoiceProviderBankloc();
            $banknum = SettingsModel::getInvoiceProviderBankNumber();
            $kvk = SettingsModel::getInvoiceProviderKVK();
            $bic = SettingsModel::getInvoiceProviderBIC();
            $btw = SettingsModel::getInvoiceProviderBTW();
            $tableColor = SettingsModel::getContactTableColor();
            $textColor = SettingsModel::getContactTextColor();
            $logoPath = SettingsModel::getContactDefaultLogoPath();
            $mail_from_name = SettingsModel::getMailFromName();
            $mail_from_addr = SettingsModel::getMailFromAddress();
            $country = SettingsModel::getInvoiceProviderLand();
            
            $this->view->company = $company;
            $this->view->phone   = $phone;
            $this->view->email   = $email;
            $this->view->website = $website;
            $this->view->bankname = $bankname;
            $this->view->banknum = $banknum;
            $this->view->banklocation = $bankloc;
            $this->view->kvk     = $kvk;
            $this->view->bic     = $bic;
            $this->view->btw     = $btw; 
            $this->view->table_color = $tableColor;
            $this->view->text_color  = $textColor;
            $this->view->logo_path = $logoPath;
            $this->view->addr_street = $addr_street;
            $this->view->addr_num = $addr_num;
            $this->view->addr_postal = $addr_post;
            $this->view->addr_city = $addr_city;
            $this->view->mail_from_name = $mail_from_name;
            $this->view->mail_from_addr = $mail_from_addr;
            $this->view->firstname = $firstname;
            $this->view->lastname = $lastname;
            $this->view->is_welcome = $is_welcome;
            $this->view->country = $country;
        }
        
        public function uploadAction(){
            $settings = $this->_getParam('setting_contact');
            
            $settingModel = new SettingsModel();
            
            $settingModel->setSetting($settingModel->getInvoiceProviderCompanyKey(), $settings['company']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressStreetKey(), $settings['addr_street']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressNumKey(), $settings['addr_num']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressPostKey(), $settings['addr_post']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressCityKey(), $settings['addr_city']);
            $settingModel->setSetting($settingModel->getInvoiceProviderLandKey(), $settings['land']);
            $settingModel->setSetting($settingModel->getInvoiceProviderPhoneKey(), $settings['phone']);
            $settingModel->setSetting($settingModel->getInvoiceProviderEmailKey(), $settings['email']);
            $settingModel->setSetting($settingModel->getInvoiceProviderWebsiteKey(), $settings['website']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBanknameKey(), $settings['bankname']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBanklocKey(), $settings['banklocation']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBankNumberKey(), $settings['banknum']);
            $settingModel->setSetting($settingModel->getInvoiceProviderKVKKey(), $settings['kvk']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBICKey(), $settings['bic']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBTWKey(), $settings['btw']);
            $settingModel->setSetting($settingModel->getContactTableColorKey(), $settings['table_color']);
            $settingModel->setSetting($settingModel->getContactTextColorKey(), $settings['text_color']);
            $settingModel->setSetting($settingModel->getMailFromNameKey(), $settings['mail_from_name']);
            $settingModel->setSetting($settingModel->getMailFromAddressKey(), $settings['mail_from_addr']);
            if (!empty($_FILES) && $_FILES['setting_contact_logo']['name'] != '') {
                $tempFile = $_FILES['setting_contact_logo']['tmp_name'];

                $basePath = realpath(APPLICATION_PATH . '/../public/');
                $logoPath = $basePath;
                $logoPath .= DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'logo';
                $savePath = DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'logo';

                $logoPath = str_replace('//', '/', $logoPath);
                $logoPath = str_replace('\\\\', '\\', $logoPath);
                $savePath = str_replace('//', '/', $savePath);
                $savePath = str_replace('\\\\', '\\', $savePath);

                if(!is_dir($logoPath)) mkdir($logoPath);

                date_default_timezone_set('Asia/Hong_Kong');

                $targetPath = $logoPath . DIRECTORY_SEPARATOR . date('Y-m-d');
                $savePath = $savePath . DIRECTORY_SEPARATOR . date('Y-m-d');

                if(!is_dir($targetPath)) mkdir($targetPath);

                $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $_FILES['setting_contact_logo']['name'];
                $savePath = $savePath . DIRECTORY_SEPARATOR . $_FILES['setting_contact_logo']['name'];

                if(file_exists($targetFile)){
                    $path_parts = pathinfo($targetFile);
                    $pos = strrpos($path_parts['basename'], ".");
                    if ($pos === false) {
                        $fname = $path_parts['basename'] . '_bak';
                    } else {
                        $fname = substr($path_parts['basename'], 0, $pos) . '_bak.' . $path_parts['extension'];
                    }
                    $bakFile = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $fname;
                    rename($targetFile, $bakFile);
                }
                move_uploaded_file($tempFile, $targetFile);
                
                $settingModel = new SettingsModel();
                $settingModel->setSetting($settingModel->getContactLogoPathKey(), $savePath);

                //$this->_helper->json(array('path'=>$targetFile));    
            }
            $this->_redirect('/settings/contact/index');
        }
        
        public function saveAction(){
            $settings = $this->_getParam('setting_contact');
            
            $settingModel = new SettingsModel();
            
            $settingModel->setSetting($settingModel->getInvoiceProviderCompanyKey(), $settings['company']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressStreetKey(), $settings['addr_street']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressNumKey(), $settings['addr_num']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressPostKey(), $settings['addr_post']);
            $settingModel->setSetting($settingModel->getInvoiceProviderAddressCityKey(), $settings['addr_city']);
            $settingModel->setSetting($settingModel->getInvoiceProviderLandKey(), $settings['land']);
            $settingModel->setSetting($settingModel->getInvoiceProviderPhoneKey(), $settings['phone']);
            $settingModel->setSetting($settingModel->getInvoiceProviderEmailKey(), $settings['email']);
            $settingModel->setSetting($settingModel->getInvoiceProviderWebsiteKey(), $settings['website']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBanknameKey(), $settings['bankname']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBanklocKey(), $settings['banklocation']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBankNumberKey(), $settings['banknum']);
            $settingModel->setSetting($settingModel->getInvoiceProviderKVKKey(), $settings['kvk']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBICKey(), $settings['bic']);
            $settingModel->setSetting($settingModel->getInvoiceProviderBTWKey(), $settings['btw']);
            $settingModel->setSetting($settingModel->getContactTableColorKey(), $settings['table_color']);
            $settingModel->setSetting($settingModel->getContactTextColorKey(), $settings['text_color']);
            $settingModel->setSetting($settingModel->getMailFromNameKey(), $settings['mail_from_name']);
            $settingModel->setSetting($settingModel->getMailFromAddressKey(), $settings['mail_from_addr']);  
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }