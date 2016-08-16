<?php 
	
	class Email {
		
		/**
		 * @return Zend_Mail
		 */
		public static function factory(){
			$mail = new Zend_Mail('UTF-8');            
			$mail->setFrom('activation@simpelfacturatie.nl"', 'Simpel Facturatie'); 
			$transport = self::transportFactory();
			if( $transport ){
				$mail->setDefaultTransport($transport);
			}
			return $mail;
		}
				
		public static function transportFactory($config=null){
            $conf['port'] = '587';
            $conf['auth'] = 'login';
            $conf['username'] = 'entropy359@gmail.com';
            $conf['password'] = 'chaos359';
            $conf['ssl'] = 'tls';
            
            if( is_array($config) ){
                foreach( $conf as $key => $value ){
                    if( array_key_exists($key, $config) ){
                        $conf[$key] = $config[$key];
                    }
                }
            }
            
            $transport = null;      
            $host = 'smtp.gmail.com';   
            $transport = new Zend_Mail_Transport_Smtp($host, $conf);   
            
            return $transport;
		}
	}