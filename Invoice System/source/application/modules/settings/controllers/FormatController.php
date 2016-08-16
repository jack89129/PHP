<?php

    class Settings_FormatController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Automatische nummering");
            $this->view->page_sub_title = _t("");//Instellen van factuurnummers, offertenummers, en ...
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            Utils::activity('format', 'settings');
            
            $tagModel = new FormatTypeModel();                                                        
            $tags = $tagModel->fetchAll();     
            $invoice = SettingsModel::getInvoiceNumberFormat();
            $offer = SettingsModel::getProformaNumberFormat();
            $purchase = SettingsModel::getPurchaseNumberFormat();
            $inum = SettingsModel::getInvoiceCurrentNum();
            $onum = SettingsModel::getProformaCurrentNum();
            $pnum = SettingsModel::getPurchaseCurrentNum();
            $contact = SettingsModel::getContactNumberFormat();
            $wholesaler = SettingsModel::getWholesalerNumberFormat();
            $credit = SettingsModel::getCreditNumberFormat();
            $cfnum = $inum;                                                              
            
            $cModel = new ContactModel();
            $wModel = new WholesalerModel();
            
            $cnum = $cModel->getNextNumber();
            $wnum = $wModel->getNextNumber();
                            
            $this->view->invoice = $invoice;
            $this->view->offer = $offer;
            $this->view->purchase = $purchase;
            $this->view->contact = $contact;
            $this->view->wholesaler = $wholesaler;
            $this->view->credit = $credit;
            $this->view->inum = self::formatNumber($inum);
            $this->view->onum = self::formatNumber($onum);
            $this->view->pnum = self::formatNumber($pnum);
            $this->view->cnum = self::formatNumber($cnum);
            $this->view->wnum = self::formatNumber($wnum);
            $this->view->cfnum = self::formatNumber($cfnum);
            $this->view->tags = $tags;           
        }
        
        private function formatNumber($num){        
            return str_repeat("0", 4-strlen($num)) . $num;
        }              
        
        public function saveAction(){
            $settings = $this->_getParam('setting_format');
            
            $settingModel = new SettingsModel();
            
            $settingModel->setSetting($settingModel->getInvoiceNumberFormatKey(), $settings['invoice']);
            $settingModel->setSetting($settingModel->getProformaNumberFormatKey(), $settings['offer']);
            $settingModel->setSetting($settingModel->getPurchaseNumberFormatKey(), $settings['purchase']);
            $settingModel->setSetting($settingModel->getContactNumberFormatKey(), $settings['contact']);
            $settingModel->setSetting($settingModel->getWholesalerNumberFormatKey(), $settings['wholesaler']);
            $settingModel->setSetting($settingModel->getCreditNumberFormatKey(), $settings['credit']);
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
