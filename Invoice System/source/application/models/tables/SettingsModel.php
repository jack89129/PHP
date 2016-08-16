<?php 

	class SettingsModel extends Jaycms_Db_Model {
		
		protected $_name = 'settings';
		protected static $_instance = null;
		
		const INVOICE_NEXT_NUM = 'INVOICE_NEXT_NUM';
        const PROFORMA_NEXT_NUM = 'PROFORMA_NEXT_NUM';
        const INVOICE_PROVIDER_COMPANY = 'INVOICE_PROVIDER_COMPANY';
		const INVOICE_PROVIDER_ADDRESS = 'INVOICE_PROVIDER_ADDRESS';
        const INVOICE_PROVIDER_ADDRESS_STREET = 'INVOICE_PROVIDER_ADDRESS_STREET';
        const INVOICE_PROVIDER_ADDRESS_NUM = 'INVOICE_PROVIDER_ADDRESS_NUM';
        const INVOICE_PROVIDER_ADDRESS_POST = 'INVOICE_PROVIDER_ADDRESS_POST';
        const INVOICE_PROVIDER_ADDRESS_CITY = 'INVOICE_PROVIDER_ADDRESS_CITY';
        const INVOICE_PROVIDER_LAND = 'INVOICE_PROVIDER_LAND';
		const INVOICE_PROVIDER_PHONE = 'INVOICE_PROVIDER_PHONE';
		const INVOICE_PROVIDER_EMAIL = 'INVOICE_PROVIDER_EMAIL';
		const INVOICE_PROVIDER_WEBSITE = 'INVOICE_PROVIDER_WEBSITE';
        const INVOICE_PROVIDER_BANKNAME = 'INVOICE_PROVIDER_BANKNAME';
        const INVOICE_PROVIDER_BANK_NUMBER = 'INVOICE_PROVIDER_BANK_NUMBER';
		const INVOICE_PROVIDER_BANK_LOCATION = 'INVOICE_PROVIDER_BANK_LOCATION';
		const INVOICE_PROVIDER_KVK = 'INVOICE_PROVIDER_KVK';
        const INVOICE_PROVIDER_BIC = 'INVOICE_PROVIDER_BIC';
		const INVOICE_PROVIDER_BTW = 'INVOICE_PROVIDER_BTW';

        const INVOICE_ANNEX_FOLDER = "INVOICE_ANNEX_FOLDER";
        const INVOICE_ANNEX_TYPES = "INVOICE_ANNEX_TYPES";

        const INVOICE_SIGNATURE_FOLDER = 'INVOICE_SIGNATURE_FOLDER';
        const INVOICE_SIGNATURE_TYPES = 'INVOICE_SIGNATURE_TYPES';
        
        const INVOICE_B2B_PAYMENT_TERM = 'INVOICE_B2B_PAYMENT_TERM';
        const INVOICE_B2B_FIRST_TERM = 'INVOICE_B2B_FIRST_TERM';
        const INVOICE_B2B_SECOND_TERM = 'INVOICE_B2B_SECOND_TERM';
        const INVOICE_B2B_LAST_TERM = 'INVOICE_B2B_LAST_TERM';
        const INVOICE_B2B_HAS_INTEREST = 'INVOICE_B2B_HAS_INTEREST';
        const INVOICE_B2B_INTEREST_RATE = 'INVOICE_B2B_INTEREST_RATE';
        const INVOICE_B2B_INTEREST_TERM = 'INVOICE_B2B_INTEREST_TERM';
        const INVOICE_B2B_AUTOSENDEMAIL = 'INVOICE_B2B_AUTOSENDEMAIL';
        const INVOICE_B2C_PAYMENT_TERM = 'INVOICE_B2C_PAYMENT_TERM';
        const INVOICE_B2C_FIRST_TERM = 'INVOICE_B2C_FIRST_TERM';
        const INVOICE_B2C_SECOND_TERM = 'INVOICE_B2C_SECOND_TERM';
        const INVOICE_B2C_LAST_TERM = 'INVOICE_B2C_LAST_TERM';
        const INVOICE_B2C_HAS_INTEREST = 'INVOICE_B2C_HAS_INTEREST';
        const INVOICE_B2C_INTEREST_RATE = 'INVOICE_B2C_INTEREST_RATE';
        const INVOICE_B2C_INTEREST_TERM = 'INVOICE_B2C_INTEREST_TERM';
        const INVOICE_B2C_AUTOSENDEMAIL = 'INVOICE_B2C_AUTOSENDEMAIL';
		
		const INVOICE_PRODUCT_DEFAULT_VAT = 'INVOICE_PRODUCT_DEFAULT_VAT';
		const INVOICE_DEFAULT_NOTICE = 'INVOICE_DEFAULT_NOTICE';
		const INVOICE_DEFAULT_EMAIL_SUBJECT = 'INVOICE_DEFAULT_EMAIL_SUBJECT';
		const INVOICE_DEFAULT_EMAIL_BODY = 'INVOICE_DEFAULT_EMAIL_BODY';
        
        const INVOICE_NUMBER_FORMAT = 'INVOICE_NUMBER_FORMAT';
        const PROFORMA_NUMBER_FORMAT = 'PROFORMA_NUMBER_FORMAT';
        const PURCHASE_NUMBER_FORMAT = 'PURCHASE_NUMBER_FORMAT';
        const CONTACT_NUMBER_FORMAT = 'CONTACT_NUMBER_FORMAT';
        const WHOLESALER_NUMBER_FORMAT = 'WHOLESALER_NUMBER_FORMAT';
        const CREDIT_NUMBER_FORMAT = 'CREDIT_NUMBER_FORMAT';

        const PROFORMA_DEFAULT_NOTICE = 'PROFORMA_DEFAULT_NOTICE';
        const PROFORMA_DEFAULT_EMAIL_SUBJECT = 'PROFORMA_DEFAULT_EMAIL_SUBJECT';
        const PROFORMA_DEFAULT_EMAIL_BODY = 'PROFORMA_DEFAULT_EMAIL_BODY';

        const PURCHASE_NEXT_NUM = 'PURCHASE_NEXT_NUM';
        const PURCHASE_ATTACHMENT_FOLDER = 'PURCHASE_ATTACHMENT_FOLDER';
        const PURCHASE_ATTACHMENT_TYPES = 'PURCHASE_ATTACHMENT_TYPES';

        const RECEIPT_NEXT_NUM = 'RECEIPT_NEXT_NUM';
        const PACK_NEXT_NUM = 'PACK_NEXT_NUM';
        const STANDARD_PACK_NEXT_NUM = 'STANDARD_PACK_NEXT_NUM';
		
		const SMTP_ACTIVATE = 'SMTP_ACTIVATE';
		const SMTP_HOST = 'SMTP_HOST';
		const SMTP_PORT = 'SMTP_PORT';
		const SMTP_AUTH = 'SMTP_AUTH';
		const SMTP_AUTH_USERNAME = 'SMTP_AUTH_USERNAME';
		const SMTP_AUTH_PASSWORD = 'SMTP_AUTH_PASSWORD';
		const SMTP_SSL	= 'SMTP_SSL';
		
		const MAIL_FROM_NAME = 'MAIL_FROM_NAME';
		const MAIL_FROM_ADDRESS = 'MAIL_FROM_ADDRESS';

        const ACTIVITY_LOG_SECRET = 'ACTIVITY_LOG_SECRET';
        
        const INVOICE_DETAULT_INTRO  = 'INVOICE_DETAULT_INTRO';
        
        const PROFORMA_DEFAULT_INTRO  = 'PROFORMA_DEFAULT_INTRO';
        
        const CONTACT_DEFAULT_TABLE_COLOR = 'CONTACT_DEFAULT_TABLE_COLOR';
        const CONTACT_DEFAULT_TEXT_COLOR  = 'CONTACT_DEFAULT_TEXT_COLOR';
        const CONTACT_DEFAULT_LOGO_PATH   = 'CONTACT_DEFAULT_LOGO_PATH';
        
        const INVOICE_LATE_EMAIL_SUBJECT   = 'INVOICE_LATE_EMAIL_SUBJECT';
        const INVOICE_LATE_EMAIL_BODY      = 'INVOICE_LATE_EMAIL_BODY';
        const INVOICE_URGENT_EMAIL_SUBJECT = 'INVOICE_URGENT_EMAIL_SUBJECT';
        const INVOICE_URGENT_EMAIL_BODY    = 'INVOICE_URGENT_EMAIL_BODY';
        const INVOICE_JUDGE_EMAIL_SUBJECT  = 'INVOICE_JUDGE_EMAIL_SUBJECT';
        const INVOICE_JUDGE_EMAIL_BODY     = 'INVOICE_JUDGE_EMAIL_BODY';
        const INVOICE_INTEREST_EMAIL_SUBJECT  = 'INVOICE_INTEREST_EMAIL_SUBJECT';
        const INVOICE_INTEREST_EMAIL_BODY     = 'INVOICE_INTEREST_EMAIL_BODY';
        const INVOICE_THANKS_EMAIL_SUBJECT  = 'INVOICE_THANKS_EMAIL_SUBJECT';
        const INVOICE_THANKS_EMAIL_BODY     = 'INVOICE_THANKS_EMAIL_BODY';
        
        const PROFORMA_LATE_EMAIL_SUBJECT   = 'PROFORMA_LATE_EMAIL_SUBJECT';
        const PROFORMA_LATE_EMAIL_BODY      = 'PROFORMA_LATE_EMAIL_BODY';
        const PROFORMA_URGENT_EMAIL_SUBJECT = 'PROFORMA_URGENT_EMAIL_SUBJECT';
        const PROFORMA_URGENT_EMAIL_BODY    = 'PROFORMA_URGENT_EMAIL_BODY';
        const PROFORMA_JUDGE_EMAIL_SUBJECT  = 'PROFORMA_JUDGE_EMAIL_SUBJECT';
        const PROFORMA_JUDGE_EMAIL_BODY     = 'PROFORMA_JUDGE_EMAIL_BODY';
        const PROFORMA_THANKS_EMAIL_SUBJECT = 'PROFORMA_THANKS_EMAIL_SUBJECT';
        const PROFORMA_THANKS_EMAIL_BODY    = 'PROFORMA_THANKS_EMAIL_BODY';
        
        const WEBSHOP_ACTIVATION = 'WEBSHOP_ACTIVATION';
        const WEBSHOP_LOGO = 'WEBSHOP_LOGO';
        const WEBSHOP_TWITTER = 'WEBSHOP_TWITTER';
        const WEBSHOP_FACEBOOK = 'WEBSHOP_FACEBOOK';
        const WEBSHOP_GOOGLE = 'WEBSHOP_GOOGLE';
        const WEBSHOP_VIMEO = 'WEBSHOP_VIMEO';
        const WEBSHOP_LINKEDIN = 'WEBSHOP_LINKEDIN';
        const WEBSHOP_TITLE = 'WEBSHOP_TITLE';
        const WEBSHOP_MAIN_COLOR = 'WEBSHOP_MAIN_COLOR';
		const WEBSHOP_ABOUT_TEXT = 'WEBSHOP_ABOUT_TEXT';
        const WEBSHOP_ABOUT_IMAGE = 'WEBSHOP_ABOUT_IMAGE';
        const WEBSHOP_HOME_DEFAULT_IMAGE1 = 'WEBSHOP_HOME_DEFAULT_IMAGE1';
        const WEBSHOP_HOME_DEFAULT_IMAGE2 = 'WEBSHOP_HOME_DEFAULT_IMAGE2';
        const WEBSHOP_HOME_DEFAULT_IMAGE3 = 'WEBSHOP_HOME_DEFAULT_IMAGE3';
        const WEBSHOP_CONDITION_PDF = 'WEBSHOP_CONDITION_PDF';
        
        const SYSTEM_YEAR = 'SYSTEM_YEAR';
        const SYSTEM_CREATED_DATE = 'SYSTEM_CREATED_DATE';
        
        const PERMISSION_PURCHASE = 'PERMISSION_PURCHASE';
		
		/**
		 * @return SettingsModel
		 */
		public static function getInstance(){
			if( !self::$_instance ){
				self::$_instance = new self();
			}
			
			return self::$_instance;
		}
		
		public static function get($name){
			$result = self::getInstance()->findByName($name);
			
			if( !$result ){
				throw new Exception(_t("Setting with name '%s' not found!", $name));
			}
			
			return $result->value;
		}
		
		public static function getInvoiceNextNum(){
			$nextNum = self::get(self::INVOICE_NEXT_NUM);
			
			self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
										self::getInstance()->getAdapter()->quoteInto('name = ?', self::INVOICE_NEXT_NUM));
			return $nextNum;
		}

        public static function getProformaNextNum(){
            $nextNum = self::get(self::PROFORMA_NEXT_NUM);

            self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
                self::getInstance()->getAdapter()->quoteInto('name = ?', self::PROFORMA_NEXT_NUM));
            return $nextNum;
        }

        public static function getPurchaseNextNum(){
            $nextNum = self::get(self::PURCHASE_NEXT_NUM);

            self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
                self::getInstance()->getAdapter()->quoteInto('name = ?', self::PURCHASE_NEXT_NUM));
            return $nextNum;
        }

        public static function getReceiptNextNum(){
            $nextNum = self::get(self::RECEIPT_NEXT_NUM);

            self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
                self::getInstance()->getAdapter()->quoteInto('name = ?', self::RECEIPT_NEXT_NUM));
            return $nextNum;
        }

        public static function getPackNextNum(){
            $nextNum = self::get(self::PACK_NEXT_NUM);

            self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
                self::getInstance()->getAdapter()->quoteInto('name = ?', self::PACK_NEXT_NUM));
            return $nextNum;
        }
		
        public static function getStandardPackNextNum(){
            $nextNum = self::get(self::STANDARD_PACK_NEXT_NUM);

            self::getInstance()->update(array('value' => new Zend_Db_Expr('value + 1')),
                self::getInstance()->getAdapter()->quoteInto('name = ?', self::STANDARD_PACK_NEXT_NUM));
            return $nextNum;
        }
        
		public static function getInvoiceProviderAddress(){
			return self::get(self::INVOICE_PROVIDER_ADDRESS);
		}
        
        public static function getInvoiceProviderCompanyKey(){
            return self::INVOICE_PROVIDER_COMPANY;
        }
        
        public static function getInvoiceProviderCompany(){
            return self::get(self::INVOICE_PROVIDER_COMPANY);
        }
        
        public static function getInvoiceProviderAddressStreet(){
            return self::get(self::INVOICE_PROVIDER_ADDRESS_STREET);
        }
        
        public static function getInvoiceProviderAddressNum(){
            return self::get(self::INVOICE_PROVIDER_ADDRESS_NUM);
        }
        
        public static function getInvoiceProviderAddressPost(){
            return self::get(self::INVOICE_PROVIDER_ADDRESS_POST);
        }
        
        public static function getWebshopActivation(){
            return self::get(self::WEBSHOP_ACTIVATION);
        }
        
        public static function getWebshopLogo(){
            return self::get(self::WEBSHOP_LOGO);
        }
        
        public static function getWebshopTwitter(){
            return self::get(self::WEBSHOP_TWITTER);
        }
        
        public static function getWebshopFacebook(){
            return self::get(self::WEBSHOP_FACEBOOK);
        }
        
        public static function getWebshopGoogle(){
            return self::get(self::WEBSHOP_GOOGLE);
        }
        
        public static function getWebshopVimeo(){
            return self::get(self::WEBSHOP_VIMEO);
        }
        
        public static function getWebshopMainColor(){
            return self::get(self::WEBSHOP_MAIN_COLOR);
        }
        
        public static function getWebshopAboutText(){
            return self::get(self::WEBSHOP_ABOUT_TEXT);
        }
        
        public static function getInvoiceProviderLand(){
            return self::get(self::INVOICE_PROVIDER_LAND);
        }
        
        public static function getInvoiceProviderLandKey(){
            return self::INVOICE_PROVIDER_LAND;
        }
        
        public static function getWebshopAboutImage(){
            return self::get(self::WEBSHOP_ABOUT_IMAGE);
        }
        
        public static function getWebshopHomeDefaultImage1(){
            return self::get(self::WEBSHOP_HOME_DEFAULT_IMAGE1);
        }
        
        public static function getWebshopHomeDefaultImage2(){
            return self::get(self::WEBSHOP_HOME_DEFAULT_IMAGE2);
        }
        
        public static function getWebshopHomeDefaultImage3(){
            return self::get(self::WEBSHOP_HOME_DEFAULT_IMAGE3);
        }
        
        public static function getWebshopConditionPDF() {
            return self::get(self::WEBSHOP_CONDITION_PDF);
        }
        
        public static function getWebshopLinkedin(){
            return self::get(self::WEBSHOP_LINKEDIN);
        }
        
        public static function getWebshopTitle(){
            return self::get(self::WEBSHOP_TITLE);
        }
        
        public static function getInvoiceProviderAddressCity(){
            return self::get(self::INVOICE_PROVIDER_ADDRESS_CITY);
        }
        
        public static function getContactTableColor(){
            return self::get(self::CONTACT_DEFAULT_TABLE_COLOR);
        }
        
        public static function getInvoiceLateEmailSubject(){
            return self::get(self::INVOICE_LATE_EMAIL_SUBJECT);
        }
        
        public static function getInvoiceNumberFormat(){
            return self::get(self::INVOICE_NUMBER_FORMAT);
        }
        
        public static function getInvoiceNumberFormatKey(){
            return self::INVOICE_NUMBER_FORMAT;
        }
        
        public static function getProformaNumberFormat(){
            return self::get(self::PROFORMA_NUMBER_FORMAT);
        }
        
        public static function getProformaNumberFormatKey(){
            return self::PROFORMA_NUMBER_FORMAT;
        }
        
        public static function getPurchaseNumberFormat(){
            return self::get(self::PURCHASE_NUMBER_FORMAT);
        }
        
        public static function getPurchaseNumberFormatKey(){
            return self::PURCHASE_NUMBER_FORMAT;
        }
        
        public static function getContactNumberFormat(){
            return self::get(self::CONTACT_NUMBER_FORMAT);
        }
        
        public static function getInvoiceB2BPaymentTerm(){
            return self::get(self::INVOICE_B2B_PAYMENT_TERM);
        }
        
        public static function getInvoiceB2BFirstReminderTerm(){
            return self::get(self::INVOICE_B2B_FIRST_TERM);
        }
        
        public static function getInvoiceB2BSecondReminderTerm(){
            return self::get(self::INVOICE_B2B_SECOND_TERM);
        }
        
        public static function getInvoiceB2BLastReminderTerm(){
            return self::get(self::INVOICE_B2B_LAST_TERM);
        }
        
        public static function getInvoiceB2BHasInterest(){
            return self::get(self::INVOICE_B2B_HAS_INTEREST);
        }
        
        public static function getInvoiceB2BInterestRate(){
            return self::get(self::INVOICE_B2B_INTEREST_RATE);
        }
        
        public static function getInvoiceB2BInterestTerm(){
            return self::get(self::INVOICE_B2B_INTEREST_TERM);
        }
        
        public static function getInvoiceB2BAutoSendEmail(){
            return self::get(self::INVOICE_B2B_AUTOSENDEMAIL);
        }
        
        public static function getInvoiceB2CPaymentTerm(){
            return self::get(self::INVOICE_B2C_PAYMENT_TERM);
        }
         
        public static function getInvoiceB2CFirstReminderTerm(){
            return self::get(self::INVOICE_B2C_FIRST_TERM);
        }
        
        public static function getInvoiceB2CSecondReminderTerm(){
            return self::get(self::INVOICE_B2C_SECOND_TERM);
        }
        
        public static function getInvoiceB2CLastReminderTerm(){
            return self::get(self::INVOICE_B2C_LAST_TERM);
        }
        
        public static function getInvoiceB2CHasInterest(){
            return self::get(self::INVOICE_B2C_HAS_INTEREST);
        }
        
        public static function getInvoiceB2CInterestRate(){
            return self::get(self::INVOICE_B2C_INTEREST_RATE);
        }
        
        public static function getInvoiceB2CInterestTerm(){
            return self::get(self::INVOICE_B2C_INTEREST_TERM);
        }
        
        public static function getInvoiceB2CAutoSendEmail(){
            return self::get(self::INVOICE_B2C_AUTOSENDEMAIL);
        }
        
        public static function getInvoiceB2BPaymentTermKey(){
            return self::INVOICE_B2B_PAYMENT_TERM;
        }
        
        public static function getInvoiceB2BFirstReminderTermKey(){
            return self::INVOICE_B2B_FIRST_TERM;
        }
        
        public static function getInvoiceB2BSecondReminderTermKey(){
            return self::INVOICE_B2B_SECOND_TERM;
        }
        
        public static function getInvoiceB2BLastReminderTermKey(){
            return self::INVOICE_B2B_LAST_TERM;
        }
        
        public static function getInvoiceB2BHasInterestKey(){
            return self::INVOICE_B2B_HAS_INTEREST;
        }
        
        public static function getInvoiceB2BInterestRateKey(){
            return self::INVOICE_B2B_INTEREST_RATE;
        }
        
        public static function getInvoiceB2BInterestTermKey(){
            return self::INVOICE_B2B_INTEREST_TERM;
        }
        
        public static function getInvoiceB2BAutoSendEmailKey(){
            return self::INVOICE_B2B_AUTOSENDEMAIL;
        }
        
        public static function getInvoiceB2CPaymentTermKey(){
            return self::INVOICE_B2C_PAYMENT_TERM;
        }
        
        public static function getInvoiceB2CFirstReminderTermKey(){
            return self::INVOICE_B2C_FIRST_TERM;
        }
        
        public static function getInvoiceB2CSecondReminderTermKey(){
            return self::INVOICE_B2C_SECOND_TERM;
        }
        
        public static function getInvoiceB2CLastReminderTermKey(){
            return self::INVOICE_B2C_LAST_TERM;
        }
        
        public static function getInvoiceB2CHasInterestKey(){
            return self::INVOICE_B2C_HAS_INTEREST;
        }
        
        public static function getInvoiceB2CInterestRateKey(){
            return self::INVOICE_B2C_INTEREST_RATE;
        }
        
        public static function getInvoiceB2CInterestTermKey(){
            return self::INVOICE_B2C_INTEREST_TERM;
        }
        
        public static function getInvoiceB2CAutoSendEmailKey(){
            return self::INVOICE_B2C_AUTOSENDEMAIL;
        }
        
        public static function getContactNumberFormatKey(){
            return self::CONTACT_NUMBER_FORMAT;
        }
        
        public static function getWholesalerNumberFormat(){
            return self::get(self::WHOLESALER_NUMBER_FORMAT);
        }
        
        public static function getWholesalerNumberFormatKey(){
            return self::WHOLESALER_NUMBER_FORMAT;
        }
        
        public static function getCreditNumberFormat(){
            return self::get(self::CREDIT_NUMBER_FORMAT);
        }
        
        public static function getCreditNumberFormatKey(){
            return self::CREDIT_NUMBER_FORMAT;
        }
        
        public static function getInvoiceLateEmailBody(){
            return self::get(self::INVOICE_LATE_EMAIL_BODY);
        }
        
        public static function getInvoiceUrgentEmailSubject(){
            return self::get(self::INVOICE_URGENT_EMAIL_SUBJECT);
        }
        
        public static function getInvoiceUrgentEmailBody(){
            return self::get(self::INVOICE_URGENT_EMAIL_BODY);
        }
        
        public static function getInvoiceJudgeEmailSubject(){
            return self::get(self::INVOICE_JUDGE_EMAIL_SUBJECT);
        }
        
        public static function getInvoiceJudgeEmailBody(){
            return self::get(self::INVOICE_JUDGE_EMAIL_BODY);
        }
        
        public static function getInvoiceInterestEmailSubject(){
            return self::get(self::INVOICE_INTEREST_EMAIL_SUBJECT);
        }
        
        public static function getInvoiceInterestEmailBody(){
            return self::get(self::INVOICE_INTEREST_EMAIL_BODY);
        }
        
        public static function getSystemYear(){
            return self::get(self::SYSTEM_YEAR);
        }
        
        public static function getSystemCreatedDate(){
            return self::get(self::SYSTEM_CREATED_DATE);
        }
        
        public static function getSystemYearKey(){
            return self::SYSTEM_YEAR;
        }
        
        public static function getProformaLateEmailSubject(){
            return self::get(self::PROFORMA_LATE_EMAIL_SUBJECT);
        }
        
        public static function getProformaLateEmailBody(){
            return self::get(self::PROFORMA_LATE_EMAIL_BODY);
        }
        
        public static function getProformaUrgentEmailSubject(){
            return self::get(self::PROFORMA_URGENT_EMAIL_SUBJECT);
        }
        
        public static function getProformaUrgentEmailBody(){
            return self::get(self::PROFORMA_URGENT_EMAIL_BODY);
        }
        
        public static function getProformaJudgeEmailSubject(){
            return self::get(self::PROFORMA_JUDGE_EMAIL_SUBJECT);
        }
        
        public static function getProformaJudgeEmailBody(){
            return self::get(self::PROFORMA_JUDGE_EMAIL_BODY);
        }
        
        public static function getInvoiceThanksEmailSubject(){
            return self::get(self::INVOICE_THANKS_EMAIL_SUBJECT);
        }
        
        public static function getInvoiceThanksEmailBody(){
            return self::get(self::INVOICE_THANKS_EMAIL_BODY);
        }
        
        public static function getInvoiceThanksEmailSubjectKey(){
            return self::INVOICE_THANKS_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceThanksEmailBodyKey(){
            return self::INVOICE_THANKS_EMAIL_BODY;
        }
        
        public static function getProformaThanksEmailSubject(){
            return self::get(self::PROFORMA_THANKS_EMAIL_SUBJECT);
        }
        
        public static function getProformaThanksEmailBody(){
            return self::get(self::PROFORMA_THANKS_EMAIL_BODY);
        }
        
        public static function getProformaeThanksEmailSubjectKey(){
            return self::PROFORMA_THANKS_EMAIL_SUBJECT;
        }
        
        public static function getProformaThanksEmailBodyKey(){
            return self::PROFORMA_THANKS_EMAIL_BODY;
        }
        
        public static function getContactTextColor(){
            return self::get(self::CONTACT_DEFAULT_TEXT_COLOR);
        }
		
		public static function getInvoiceProviderPhone(){
			return self::get(self::INVOICE_PROVIDER_PHONE);
		}
		
		public static function getInvoiceProviderEmail(){
			return self::get(self::INVOICE_PROVIDER_EMAIL);
		}
		
		public static function getInvoiceProviderWebsite(){
			return self::get(self::INVOICE_PROVIDER_WEBSITE);
		}
		
		public static function getInvoiceProviderBankNumber(){
			return self::get(self::INVOICE_PROVIDER_BANK_NUMBER);
		}
        
        public static function getInvoiceProviderBankname(){
            return self::get(self::INVOICE_PROVIDER_BANKNAME);
        }
        
        public static function getInvoiceProviderBanknameKey(){
            return self::INVOICE_PROVIDER_BANKNAME;
        }
        
        public static function getInvoiceProviderBankloc(){
            return self::get(self::INVOICE_PROVIDER_BANK_LOCATION);
        }
        
        public static function getInvoiceProviderBanklocKey(){
            return self::INVOICE_PROVIDER_BANK_LOCATION;
        }
		
		public static function getInvoiceProviderKVK(){
			return self::get(self::INVOICE_PROVIDER_KVK);
		}

        public static function getInvoiceProviderBIC(){
            return self::get(self::INVOICE_PROVIDER_BIC);
        }
		
		public static function getInvoiceProviderBTW(){
			return self::get(self::INVOICE_PROVIDER_BTW);
		}
		
		public static function getSMTPActivate(){
			return self::get(self::SMTP_ACTIVATE);
		} 
		
		public static function getSMTPHost(){
			return self::get(self::SMTP_HOST);
		} 
		
		public static function getSMTPPort(){
			return self::get(self::SMTP_PORT);
		} 
		
		public static function getSMTPAuth(){
			return self::get(self::SMTP_AUTH);
		} 
		
		public static function getSMTPAuthUsername(){
			return self::get(self::SMTP_AUTH_USERNAME);
		} 
		
		public static function getSMTPAuthPassword(){
			return self::get(self::SMTP_AUTH_PASSWORD);
		} 
		
		public static function getSMTPSSL(){
			return self::get(self::SMTP_SSL);
		} 
		
		public static function getMailFromName(){
			return self::get(self::MAIL_FROM_NAME);
		}
        
        public static function getMailFromNameKey(){
            return self::MAIL_FROM_NAME;
        }
		
		public static function getMailFromAddress(){
			return self::get(self::MAIL_FROM_ADDRESS);
		}
        
        public static function getMailFromAddressKey(){
            return self::MAIL_FROM_ADDRESS;
        }
		
		public static function getInvoiceDefaultNotice(){
			return self::get(self::INVOICE_DEFAULT_NOTICE);	
		}
        
        public static function getInvoiceDefaultNoticeKey(){
            return self::INVOICE_DEFAULT_NOTICE;    
        }
        
        public static function getContactDefaultLogoPath(){
            return self::get(self::CONTACT_DEFAULT_LOGO_PATH);
        }
		
		public static function getInvoiceDefaultEmailBody(){
			return self::get(self::INVOICE_DEFAULT_EMAIL_BODY);
		}
        
        public static function getInvoiceDefaultIntro(){
            return self::get(self::INVOICE_DETAULT_INTRO);
        }
        
        public static function getInvoiceDefaultIntroKey(){
            return self::INVOICE_DETAULT_INTRO;
        }
        
		
		public static function getProformaDefaultEmailSubject(){
			return self::get(self::PROFORMA_DEFAULT_EMAIL_SUBJECT);
		}
        
        public static function getProformaDefaultIntro(){
            return self::get(self::PROFORMA_DEFAULT_INTRO);
        }

        public static function getProformaDefaultNotice(){
            return self::get(self::PROFORMA_DEFAULT_NOTICE);
        }
        
        public static function getProformaDefaultIntroKey(){
            return self::PROFORMA_DEFAULT_INTRO;
        }

        public static function getProformaDefaultNoticeKey(){
            return self::PROFORMA_DEFAULT_NOTICE;
        }

        public static function getProformaDefaultEmailBody(){
            return self::get(self::PROFORMA_DEFAULT_EMAIL_BODY);
        }

        public static function getInvoiceDefaultEmailSubject(){
            return self::get(self::INVOICE_DEFAULT_EMAIL_SUBJECT);
        }
		
		public static function getInvoiceProductDefaultVAT(){
			return self::get(self::INVOICE_PRODUCT_DEFAULT_VAT);
		}

        public static function getInvoiceAnnexFolder(){
            return self::get(self::INVOICE_ANNEX_FOLDER);
        }

        public static function getInvoiceSignatureFolder(){
            return self::get(self::INVOICE_SIGNATURE_FOLDER);
        }

        public static function getInvoiceAnnexTypes(){
            $result = array_map('trim', preg_split("@(\s|,)@", self::get(self::INVOICE_ANNEX_TYPES)));
            foreach( $result as $key => $val ){
                if( !$val ){
                    unset($result[$key]);
                }
            }
            return $result;
        }

        public static function getInvoiceSignatureTypes(){
            $result = array_map('trim', preg_split("@(\s|,)@", self::get(self::INVOICE_SIGNATURE_TYPES)));
            foreach( $result as $key => $val ){
                if( !$val ){
                    unset($result[$key]);
                }
            }
            return $result;
        }

        public static function getPurchaseAttachmentFolder(){
            return self::get(self::PURCHASE_ATTACHMENT_FOLDER);
        }

        public static function getPurchaseAttachmentTypes(){
            $result = array_map('trim', preg_split("@(\s|,)@", self::get(self::PURCHASE_ATTACHMENT_TYPES)));
            foreach( $result as $key => $val ){
                if( !$val ){
                    unset($result[$key]);
                }
            }
            return $result;
        }

        public static function getActivityLogSecret(){
            return self::get(self::ACTIVITY_LOG_SECRET);
        }
        
        public static function getInvoiceIntro(){
            return self::get(self::INVOICE_DETAULT_INTRO);
        }
        
        public static function getProformaIntro(){
            return self::get(self::PROFORMA_DETAULT_INTRO);
        }
        
        public static function getInvoiceIntroKey(){
            return self::INVOICE_DETAULT_INTRO;
        }
        
        public static function getInvoiceCurrentNum(){
            return self::get(self::INVOICE_NEXT_NUM);
        }                                          
        
        public static function getInvoiceCurrentNumKey(){
            return self::INVOICE_NEXT_NUM;
        } 
        
        public static function getProformaCurrentNumKey(){
            return self::PROFORMA_NEXT_NUM;
        }
        
        public static function getPurchaseCurrentNumKey(){
            return self::PURCHASE_NEXT_NUM;
        }                                         
        
        public static function getProformaCurrentNum(){
            return self::get(self::PROFORMA_NEXT_NUM);
        }                                          
        
        public static function getPurchaseCurrentNum(){
            return self::get(self::PURCHASE_NEXT_NUM);
        }                                          
        
        public static function getInvoiceMailSubjectKey(){
            return self::INVOICE_DEFAULT_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceMailBodyKey(){
            return self::INVOICE_DEFAULT_EMAIL_BODY;
        }
        
        public static function getInvoiceNoticeKey(){
            return self::INVOICE_DEFAULT_NOTICE;
        }
        
        public static function getProformaIntroKey(){
            return self::PROFORMA_DEFAULT_INTRO;
        }
        
        public static function getProformaMailSubjectKey(){
            return self::PROFORMA_DEFAULT_EMAIL_SUBJECT;
        }
        
        public static function getProformaMailBodyKey(){
            return self::PROFORMA_DEFAULT_EMAIL_BODY;
        }
        
        public static function getProformaNoticeKey(){
            return self::PROFORMA_DEFAULT_NOTICE;
        }
        
        public static function getInvoiceProviderAddressKey(){
            return self::INVOICE_PROVIDER_ADDRESS;
        }
        
        public static function getInvoiceProviderAddressStreetKey(){
            return self::INVOICE_PROVIDER_ADDRESS_STREET;
        }
        
        public static function getInvoiceProviderAddressNumKey(){
            return self::INVOICE_PROVIDER_ADDRESS_NUM;
        }
        
        public static function getInvoiceProviderAddressPostKey(){
            return self::INVOICE_PROVIDER_ADDRESS_POST;
        }
        
        public static function getInvoiceProviderAddressCityKey(){
            return self::INVOICE_PROVIDER_ADDRESS_CITY;
        }
        
        public static function getInvoiceProviderPhoneKey(){
            return self::INVOICE_PROVIDER_PHONE;
        }
        
        public static function getInvoiceProviderEmailKey(){
            return self::INVOICE_PROVIDER_EMAIL;
        }
        
        public static function getInvoiceProviderWebsiteKey(){
            return self::INVOICE_PROVIDER_WEBSITE;
        }
        
        public static function getInvoiceProviderBankNumberKey(){
            return self::INVOICE_PROVIDER_BANK_NUMBER;
        }
        
        public static function getInvoiceProviderKVKKey(){
            return self::INVOICE_PROVIDER_KVK;
        }
        
        public static function getInvoiceProviderBICKey(){
            return self::INVOICE_PROVIDER_BIC;
        }
        
        public static function getInvoiceProviderBTWKey(){
            return self::INVOICE_PROVIDER_BTW;
        }
        
        public static function getContactTableColorKey(){
            return self::CONTACT_DEFAULT_TABLE_COLOR;
        }
        
        public static function getContactTextColorKey(){
            return self::CONTACT_DEFAULT_TEXT_COLOR;
        }
        
        public static function getContactLogoPathKey(){
            return self::CONTACT_DEFAULT_LOGO_PATH;
        }
        
        public static function getInvoiceLateEmailSubjectKey(){
            return self::INVOICE_LATE_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceLateEmailBodyKey(){
            return self::INVOICE_LATE_EMAIL_BODY;
        }
        
        public static function getInvoiceUrgentEmailSubjectKey(){
            return self::INVOICE_URGENT_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceUrgentEmailBodyKey(){
            return self::INVOICE_URGENT_EMAIL_BODY;
        }
        
        public static function getInvoiceJudgeEmailSubjectKey(){
            return self::INVOICE_JUDGE_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceJudgeEmailBodyKey(){
            return self::INVOICE_JUDGE_EMAIL_BODY;
        }
        
        public static function getInvoiceInterestEmailSubjectKey(){
            return self::INVOICE_INTEREST_EMAIL_SUBJECT;
        }
        
        public static function getInvoiceInterestEmailBodyKey(){
            return self::INVOICE_INTEREST_EMAIL_BODY;
        }
        
        public static function getProformaLateEmailSubjectKey(){
            return self::PROFORMA_LATE_EMAIL_SUBJECT;
        }
        
        public static function getProformaLateEmailBodyKey(){
            return self::PROFORMA_LATE_EMAIL_BODY;
        }
        
        public static function getProformaUrgentEmailSubjectKey(){
            return self::PROFORMA_URGENT_EMAIL_SUBJECT;
        }
        
        public static function getProformaUrgentEmailBodyKey(){
            return self::PROFORMA_URGENT_EMAIL_BODY;
        }
        
        public static function getProformaJudgeEmailSubjectKey(){
            return self::PROFORMA_JUDGE_EMAIL_SUBJECT;
        }
        
        public static function getProformaJudgeEmailBodyKey(){
            return self::PROFORMA_JUDGE_EMAIL_BODY;
        }
        
        public static function getWebshopActivationKey(){
            return self::WEBSHOP_ACTIVATION;
        }
        
        public static function getWebshopLogoKey(){
            return self::WEBSHOP_LOGO;
        }
        
        public static function getWebshopTwitterKey(){
            return self::WEBSHOP_TWITTER;
        }
        
        public static function getWebshopFacebookKey(){
            return self::WEBSHOP_FACEBOOK;
        }
        
        public static function getWebshopGoogleKey(){
            return self::WEBSHOP_GOOGLE;
        }
        
        public static function getWebshopVimeoKey(){
            return self::WEBSHOP_VIMEO;
        }
        
        public static function getWebshopLinkedinKey(){
            return self::WEBSHOP_LINKEDIN;
        }
        
        public static function getWebshopTitleKey(){
            return self::WEBSHOP_TITLE;
        }
        
        public static function getWebshopMainColorKey(){
            return self::WEBSHOP_MAIN_COLOR;
        }
        
        public static function getWebshopAboutTextKey(){
            return self::WEBSHOP_ABOUT_TEXT;
        }
        
        public static function getWebshopAboutImageKey(){
            return self::WEBSHOP_ABOUT_IMAGE;
        }
        
        public static function getWebshopHomeDefaultImage1Key(){
            return self::WEBSHOP_HOME_DEFAULT_IMAGE1;
        }
        
        public static function getWebshopHomeDefaultImage2Key(){
            return self::WEBSHOP_HOME_DEFAULT_IMAGE2;
        }
        
        public static function getWebshopHomeDefaultImage3Key(){
            return self::WEBSHOP_HOME_DEFAULT_IMAGE3;
        }
        
        public static function getWebshopConditionPDFKey(){
            return self::WEBSHOP_CONDITION_PDF;
        }
        
        public static function getPermissionPurchaseKey(){
            return self::PERMISSION_PURCHASE;
        }
        
        public static function getPermissionPurchase(){
            return self::get(self::PERMISSION_PURCHASE);
        }
        
        public function getSetting($settingKey) {
            $result = $this->select()->where('name = ?', $settingKey)->query(Zend_Db::FETCH_OBJ)->fetchAll();
            if ( empty($result) ) {
                return null;
            }
            return $result[0];
        }
        
        public function setSetting($settingKey, $value) {
            $setting = self::getSetting($settingKey);
            if ( empty($setting) ) {
                $data = array(
                    'name'  => $settingKey,
                    'value' => $value
                );
                $this->insert($data);
            } else {
                $data = array(
                    'value' => $value
                );

                $n = $this->update($data, "name = '$settingKey'");
            }
        }
	}
