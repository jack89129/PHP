<?php

    class Settings_ShopController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Algemene instellingen");
            $this->view->page_sub_title = _t("Overzicht, settings en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            $this->_redirect('/settings/contact/index');             
            Utils::activity('contact', 'settings');
            
            $settingModel = new SettingsModel();
            
            $logo = SettingsModel::getWebshopLogo();
            
            $this->view->logo_path   = $logo;
            $this->view->tlink = SettingsModel::getWebshopTwitter();
            $this->view->flink = SettingsModel::getWebshopFacebook();
            $this->view->glink = SettingsModel::getWebshopGoogle();
            $this->view->vlink = SettingsModel::getWebshopVimeo();
            $this->view->llink = SettingsModel::getWebshopLinkedin();
            $this->view->title = SettingsModel::getWebshopTitle();
            $this->view->shop_color = SettingsModel::getWebshopMainColor();
            $this->view->has_webshop = SettingsModel::getWebshopActivation();
            $this->view->about_us = SettingsModel::getWebshopAboutText();
            $this->view->about_img = SettingsModel::getWebshopAboutImage();
            $this->view->home_slider_img1 = SettingsModel::getWebshopHomeDefaultImage1();
            $this->view->home_slider_img2 = SettingsModel::getWebshopHomeDefaultImage2();
            $this->view->home_slider_img3 = SettingsModel::getWebshopHomeDefaultImage3();
            $cond = SettingsModel::getWebshopConditionPDF();
            
            $this->view->condFile = $cond=="" ? "": basename($cond);
        }
        
        public function uploadAction(){
            if (!empty($_FILES)) {
                $tempFile = $_FILES['setting_webshop_logo']['tmp_name'];

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

                $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $_FILES['setting_webshop_logo']['name'];
                $savePath = $savePath . DIRECTORY_SEPARATOR . $_FILES['setting_webshop_logo']['name'];

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
                $settingModel->setSetting($settingModel->getWebshopLogoKey(), $savePath);

                //$this->_helper->json(array('path'=>$targetFile));
            }
            $this->_redirect('/settings/shop/index');
        }
        
        public function uploadsliderAction(){
            if (!empty($_FILES)) {
                $idx = $this->_getParam('idx');
                $tempFile = $_FILES['setting_slider_image'.$idx]['tmp_name'];

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

                $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $_FILES['setting_slider_image'.$idx]['name'];
                $savePath = $savePath . DIRECTORY_SEPARATOR . $_FILES['setting_slider_image'.$idx]['name'];

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
                if ( $idx == 1 ) {
                    $settingModel->setSetting($settingModel->getWebshopHomeDefaultImage1Key(), $savePath);
                } else if ( $idx == 2 ) {
                    $settingModel->setSetting($settingModel->getWebshopHomeDefaultImage2Key(), $savePath);
                } else {
                    $settingModel->setSetting($settingModel->getWebshopHomeDefaultImage3Key(), $savePath);
                }

                //$this->_helper->json(array('path'=>$targetFile));
            }
            $this->_redirect('/settings/shop/index');
        }
        
        public function uploadconditionAction(){
            if (!empty($_FILES)) {
                $tempFile = $_FILES['setting_condition_pdf']['tmp_name'];

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

                $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $_FILES['setting_condition_pdf']['name'];
                $savePath = $savePath . DIRECTORY_SEPARATOR . $_FILES['setting_condition_pdf']['name'];

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
                $settingModel->setSetting($settingModel->getWebshopConditionPDFKey(), $savePath);
                
            }
            $this->_redirect('/settings/shop/index');
        }
        
        public function uploadaboutAction(){
            if (!empty($_FILES)) {
                $tempFile = $_FILES['setting_about_logo']['tmp_name'];

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

                $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $_FILES['setting_about_logo']['name'];
                $savePath = $savePath . DIRECTORY_SEPARATOR . $_FILES['setting_about_logo']['name'];

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
                $settingModel->setSetting($settingModel->getWebshopAboutImageKey(), $savePath);

                //$this->_helper->json(array('path'=>$targetFile));
            }
            $this->_redirect('/settings/shop/index');
        }
        
        public function saveAction(){
            $settings = $this->_getParam('setting_shop');
            
            $settingModel = new SettingsModel();
            
            $settingModel->setSetting($settingModel->getWebshopTwitterKey(), $settings['tlink']);
            $settingModel->setSetting($settingModel->getWebshopFacebookKey(), $settings['flink']);
            $settingModel->setSetting($settingModel->getWebshopGoogleKey(), $settings['glink']);
            $settingModel->setSetting($settingModel->getWebshopVimeoKey(), $settings['vlink']);
            $settingModel->setSetting($settingModel->getWebshopLinkedinKey(), $settings['llink']);
            $settingModel->setSetting($settingModel->getWebshopTitleKey(), $settings['title']);
            $settingModel->setSetting($settingModel->getWebshopActivationKey(), $settings['has_webshop']);
            $settingModel->setSetting($settingModel->getWebshopMainColorKey(), $settings['shop_color']);
            $settingModel->setSetting($settingModel->getWebshopAboutTextKey(), $settings['about_us']);
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }