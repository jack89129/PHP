<?php 
	
	class Mail {
		
		/**
		 * @return Zend_Mail
		 */
		public static function factory(){
			$mail = new Zend_Mail('UTF-8');            
			$mail->setFrom(SettingsModel::getMailFromAddress(), SettingsModel::getMailFromName());
			$transport = self::transportFactory();
			if( $transport ){
				$mail->setDefaultTransport($transport);
			}
			return $mail;
		}
				
		public static function transportFactory($config=null){
			$conf['smtp'] = SettingsModel::getSMTPActivate();
            $conf['host'] = SettingsModel::getSMTPHost();
            $conf['port'] = SettingsModel::getSMTPPort();
			$conf['auth'] = SettingsModel::getSMTPAuth();
			$conf['username'] = SettingsModel::getSMTPAuthUsername();
			$conf['password'] = SettingsModel::getSMTPAuthPassword();
			$conf['ssl'] = SettingsModel::getSMTPSSL();
			
			if( is_array($config) ){
				foreach( $conf as $key => $value ){
					if( array_key_exists($key, $config) ){
						$conf[$key] = $config[$key];
					}
				}
			}
			
			$transport = null;
			
			if( $conf['smtp'] ){
				$host = $conf['host'];
				unset($conf['smtp']);
				unset($conf['host']);
				$transport = new Zend_Mail_Transport_Smtp($host, $conf);
			}
			
			return $transport;
		}
	}