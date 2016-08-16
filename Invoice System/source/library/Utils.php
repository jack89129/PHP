<?php 

	class Utils {

        /**
         * @return Employee|null
         */
        public static function user(){
            $user = new Zend_Session_Namespace('user');          
            
            if ( $user->id == NULL || empty($user->database) ) return null;
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $dbName = $db->fetchOne("select DATABASE();");
            
            if ( $dbName != $user->database ) {
                self::locate_db($user->database);
            }
            
            $employee = new Employee($user->id);       
            return $employee->exists() ? $employee : null ;
        }
        
        public static function locate_db($dbname){
			$params = array(
				'host'     => 'localhost',
				'username' => 'root',
				'password' => 'C0v4M7t0',
				'dbname'   => $dbname
			);

            $db = Zend_Db::factory("PDO_MYSQL", $params);
            Zend_Db_Table::setDefaultAdapter($db);
        }

		public static function numberFormat($number){
			return number_format($number, 2, Constants::DECIMAL_SEPARATOR, Constants::THOUSANDS_SEPARATOR);
		}
		
		public static function addVAT($number, $vat){
			return $number * (1 + ($vat/100));
		}
		
		public static function removeVAT($number, $vat){
			return $number / (1 + ($vat/100)); 
		}
		
		public static function totalVAT($number, $vat=Constants::VAT){
			return $number - self::removeVAT($number, $vat);
		}
		
		public static function contactNumberFormat($id){
			return self::generalFormat($id, Constants::CONTACT_NUMBER_PREFIX, '', Constants::CONTACT_NUM_PADDING, '0');
		}

        public static function wholesalerNumberFormat($id){
            return self::generalFormat($id, Constants::WHOLESALER_NUMBER_PREFIX, '', Constants::WHOLESALER_NUM_PADDING, '0');
        }
		
		public static function generalFormat($thing, $prefix='', $suffix='', $length=null, $padding='', $paddingSide='left', $format='%s'){
			$result[0] = $prefix;
			$result[2] = sprintf($format, $thing);
			$result[4] = $suffix;
			
			if( $length && ($left = $length - iconv_strlen($thing, 'UTF-8')) > 0 ){
				$pad = str_repeat($padding, $left);
				$index = $paddingSide == "left" ? 1 : 3;
				$result[$index] = $pad;
			}
			
			ksort($result);
			return join('', $result);
		}

        public static function strip_bad_tags($html, $appendTags=""){
            $html = preg_replace('@<mark>(.*)</mark>@', '<span style="background-color:#ffff00;">$1</span>', $html);
            $html = preg_replace('@<lt>(.*)</lt>@', '<strike>$1</strike>', $html);

            if( !function_exists('replace_link') ){
                function replace_link($match){
                    $href = $match = trim(end($match));
                    $href = parse_url($match, PHP_URL_SCHEME) ? $href : 'http://' . $href;
                    return '<a href="' . $href . '">' . $match . '</a>';
                }
            }

            $html = preg_replace_callback('@<link>(.*)</link>@', 'replace_link', $html);


            return strip_tags($html, "<mark><b><strong><strike><i><u><font><p><h1><h2><h3><h4><h5><h6><a><em><small><quote><div><span><br><hr><br/><hr/><form><input><input/><button><table><tr><th><td><img><img/><textarea><li><ul><ol><del><cite>" . $appendTags);
        }

        public static function name2date($type, $now=null){
            $now = $now !== null ? $now : time();

            switch( $type ){
                case 'last_month':
                    $df = mktime(0,0,0, date('m', $now)-1, date('d', $now), date('y', $now));
                    $dt = mktime(0, 0, -1, date('m', $now), 1, date('y', $now));
                    break;

                case 'this_month':
                    $df = mktime(0,0,0, date('m', $now), 1, date('y', $now));
                    $dt = mktime(0, 0, -1, date('m', $now) + 1, 1, date('y', $now));
                    break;

                case 'last_quarter':
                    $df = mktime(0,0,0, ceil((date('m', $now)-3)/3) * 3 - 2, 1, date('y', $now));
                    $dt = mktime(0,0,-1, ceil((date('m', $now)-3)/3) * 3 + 1, 1, date('y', $now));
                    break;

                case 'this_quarter':
                    $df = mktime(0,0,0, ceil(date('m', $now)/3) * 3 - 2, 1, date('y', $now));
                    $dt = mktime(0,0,-1, ceil(date('m', $now)/3) * 3 + 1, 1, date('y', $now));
                    break;

                case 'last_year':
                    $df = mktime(0,0,0, 1, 1, date('y')-1);
                    $dt = mktime(0,0,-1, 1, 1, date('y'));
                    break;

                case 'this_year':
                    $df = mktime(0,0,0, 1, 1, date('y'));
                    $dt = mktime(0,0,-1, 1, 1, date('y')+1);
                    break;

                default:
                    throw new Exception('Non supported date format!');
                    break;

            }

            return array($df, $dt);
        }
        
        public static function getRealPrefix($format){
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $format = str_replace('[Jaar]', $year, $format);
            $format = str_replace('[Maand]', $month, $format);
            $format = str_replace('[Dag]', $day, $format);
            return $format;
        }
        
        /**
         * @return Contact|null
         */
        public static function contact(){
            $contact = new Zend_Session_Namespace('contact');
            $customer = new Contact($contact->id);
            return $customer->exists() ? $customer : null ;
        }
        
        public static function cart(){
            $cart = new Zend_Session_Namespace('cart');
            $mydata = $cart->data;
            return $mydata;
        }

        public static function activity($action, $target, $target_id=null){
            $log = new ActivityLog();
            $log->action = $action;
            $log->target = $target;
            $log->target_id = $target_id;       
            $log->employee_id = Utils::user()->id;
            $log->created_time = time();
            $log->params = serialize(Zend_Controller_Front::getInstance()->getRequest()->getParams());
            $log->save();
        }
        
        public static function convertVATNumber($vat) {
            $vat = str_replace(".", "", $vat);
            $vat = substr($vat, 2);
            return $vat;
        }
        
        public static function validationVATNumber($vat) {
            if ( $vat == "" ) return false;
            $type = substr($vat, 0, 2);
            $number = self::convertVATNumber($vat);
            $url = 'http://ec.europa.eu/taxation_customs/vies/vieshome.do?selectedLanguage=EN';
            //$country = 'BE';
            //$msCode = 'BE';            
            $country = $type;
            $msCode = $type;            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
            //curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
            curl_setopt($ch, CURLOPT_HEADER, 0);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);        
            $formFields = array();
            $formFields['action'] = 'check';
            $formFields['memberStateCode']  = $type;                           
            $formFields['check']  = 'Verify';                           
            $formFields['number']  = $number;           
            $post_string = '';
            foreach($formFields as $key => $value) {
                $post_string .= $key . '=' . urlencode($value) . '&';
            }

            $post_string = substr($post_string, 0, -1);
            $desUrl = "http://ec.europa.eu/taxation_customs/vies/vatResponse.html";                   
            curl_setopt($ch, CURLOPT_URL, $desUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);    

            $result = curl_exec($ch);                              
            if (strpos($result, 'invalid VAT number') === false) {
                return true;
            } else {
                return false;
            }
            curl_close($ch);
        }   
        
        public static function getScore($ip) {
            if ( empty($ip) ) return 0; 
            try {
                $url = 'https://senderscore.org/lookup.php?lookup=' . $ip;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
                curl_setopt($ch, CURLOPT_HEADER, 0);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
                curl_setopt($ch, CURLOPT_TIMEOUT, 120);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);            
                
                $score = '';
                $output = preg_match_all('/(<div id="senderScore" class="ssLowRisk">.*?<\/div>)/is', $result, $matches);
                $score = isset($matches[1][0])?$matches[1][0]:'';
                $test = explode(">", $score);     
                $test = explode("<", $test[1]);
                $score = intval(trim($test[0]));
                curl_close($ch);
                return $score;
            } catch ( Exception $ex ) {
                return 0;
            }
        }   

	}
