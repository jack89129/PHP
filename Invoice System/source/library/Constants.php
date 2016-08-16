<?php
class Constants {
	
	
	//SITE CONFIGURATION
	const AUDIT_LOG_ENABLED		= 1;
	const MYSQL_DATE_FORMAT 	= "Y-m-d H:i:s";
	const MYSQL_DAY_FORMAT 		= "Y-m-d";		
	const MYSQL_HOUR_FORMAT		= "Y-m-d H";
	const MYSQL_MINUTE_FORMAT   = "Y-m-d H:i";
	const SMTP_HOST				= "localhost";
	
	//APPLICATION CONFIGURATION
	const DATE_FORMAT  				= "d-m-Y";
    const DATE_HOUR_MINUTE_FORMAT   = "d-m-Y h:i";

    const INVOICE_NUMBER_PREFIX		= "FAC";
    const INVOICE_TMPNUM_PREFIX     = "CON";
	const INVOICE_NUMBER_PADDING 	= 4;
	const INVOICE_PAY_DAYS			= 14;
	const INVOICE_URGENT_DAYS		=  5;
    const INVOICE_JUDGE_DAYS        =  3;

    const PROFORMA_NUMBER_PREFIX    = "OFR";
    const PROFORMA_NUMBER_PADDING 	= 4;

    const PURCHASE_NUMBER_PREFIX    = "INFAC";
    const PURCHASE_NUMBER_PADDING 	= 4;

    const RECEIPT_NUMBER_PREFIX     = "BBN";
    const RECEIPT_NUMBER_PADDING 	= 4;

    const PACK_NUMBER_PREFIX        = "PKL";
    const PACK_NUMBER_PADDING 	    = 4;
    
    const STANDARD_PACK_NUMBER_PREFIX  = "SPKL";
    const STANDARD_PACK_NUMBER_PADDING = 5;

	const CONTACT_NUMBER_PREFIX		= "DB";
	const CONTACT_NUM_PADDING		= 4;

    const WHOLESALER_NUMBER_PREFIX  = "WS";
    const WHOLESALER_NUM_PADDING    = 4;

	const DECIMAL_SEPARATOR 		= ",";
	const THOUSANDS_SEPARATOR 		= ".";

    static $VATS = array(0, 6, 12, 21);
}
?>
