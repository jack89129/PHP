<?php
$url = 'http://ec.europa.eu/taxation_customs/vies/vieshome.do?selectedLanguage=EN';
$country = 'BE';
$msCode = 'BE';
$number = '0895653755';
//$number = '343423453';
$ch = curl_init();
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_URL, $url);
$data = curl_exec($ch);        
$formFields = array();//getFormFields($data);
$formFields['action'] = 'check';
$formFields['memberStateCode']  = 'BE';                           
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
    die("Success!");  
} else {
    die("Failed!");   
}
curl_close($ch);

function getFormFields($data)
{                         
    if (preg_match('/(<form.*?id=.?vowRequest.*?<\/form>)/is', $data, $matches)) {
        $inputs = getInputs($matches[1]);         
        return $inputs;
    } else {
        die('didnt find login form');
    }
}

function getInputs($form)
{
    $inputs = array();

    $elements = preg_match_all('/(<input[^>]+>)/is', $form, $matches);
    if ($elements > 0) {
        for($i = 0; $i < $elements; $i++) {
            $el = preg_replace('/\s{2,}/', ' ', $matches[1][$i]);

            if (preg_match('/name=(?:["\'])?([^"\'\s]*)/i', $el, $name)) {
                $name  = $name[1];
                $value = '';

                if (preg_match('/value=(?:["\'])?([^"\'\s]*)/i', $el, $value)) {
                    $value = $value[1];
                }

                $inputs[$name] = $value;
            }
        }
    }         

    return $inputs;
}
?>