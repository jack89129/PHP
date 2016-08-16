-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2013 at 05:44 PM
-- Server version: 5.1.62
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `avaxo2_avaxo2`
--

--
-- Dumping data for table `agenda_location`
--

INSERT INTO `agenda_location` (`id`, `name`) VALUES
(1, 'Brasserie verhoog'),
(2, 'Brasserie toog'),
(3, 'Rest 1'),
(4, 'Rest 2'),
(5, 'Feestzaal inkom'),
(6, 'Feestzaal links'),
(7, 'Feestzaal rechts'),
(8, 'Feestzaal toilet'),
(9, 'Vergaderzaal'),
(10, 'Vergaderzaal 2de stuk');

--
-- Dumping data for table `emp_settings`
--

INSERT INTO `emp_settings` (`id`, `emp_id`, `setting_name`, `value`) VALUES
(5, 2, 'INVOICE_DETAULT_INTRO', ''),
(6, 2, 'INVOICE_DEFAULT_EMAIL_SUBJECT', ''),
(7, 2, 'INVOICE_DEFAULT_EMAIL_BODY', ''),
(8, 2, 'INVOICE_DEFAULT_NOTICE', ''),
(9, 2, 'PROFORMA_DEFAULT_INTRO', ''),
(10, 2, 'PROFORMA_DEFAULT_EMAIL_SUBJECT', ''),
(11, 2, 'PROFORMA_DEFAULT_EMAIL_BODY', ''),
(26, 2, 'INVOICE_LATE_EMAIL_BODY', ''),
(27, 2, 'INVOICE_LATE_EMAIL_SUBJECT', ''),
(31, 2, 'INVOICE_JUDGE_EMAIL_SUBJECT', ''),
(32, 2, 'PROFORMA_LATE_EMAIL_BODY', ''),
(30, 2, 'INVOICE_JUDGE_EMAIL_BODY', ''),
(15, 2, 'INVOICE_PROVIDER_ADDRESS', ''),
(16, 2, 'INVOICE_PROVIDER_PHONE', ''),
(17, 2, 'INVOICE_PROVIDER_EMAIL', ''),
(1, 2, 'INVOICE_PROVIDER_LAND', ''),
(2, 2, 'INVOICE_PROVIDER_BANKNAME', ''),
(18, 2, 'INVOICE_PROVIDER_WEBSITE', ''),
(19, 2, 'INVOICE_PROVIDER_BANK_NUMBER', ''),
(20, 2, 'INVOICE_PROVIDER_KVK', ''),
(21, 2, 'INVOICE_PROVIDER_BIC', ''),
(22, 2, 'INVOICE_PROVIDER_BTW', ''),
(23, 2, 'CONTACT_DEFAULT_TABLE_COLOR', '#65be3b'),
(24, 2, 'CONTACT_DEFAULT_TEXT_COLOR', ''),
(25, 2, 'CONTACT_DEFAULT_LOGO_PATH', ''),
(28, 2, 'INVOICE_URGENT_EMAIL_BODY', ''),
(29, 2, 'INVOICE_URGENT_EMAIL_SUBJECT', ''),
(33, 2, 'PROFORMA_LATE_EMAIL_SUBJECT', ''),
(34, 2, 'PROFORMA_URGENT_EMAIL_BODY', ''),
(35, 2, 'PROFORMA_URGENT_EMAIL_SUBJECT', ''),
(36, 2, 'PROFORMA_JUDGE_EMAIL_BODY', ''),
(37, 2, 'PROFORMA_JUDGE_EMAIL_SUBJECT', ''),
(38, 2, 'INVOICE_PROVIDER_ADDRESS_STREET', NULL),
(39, 2, 'INVOICE_PROVIDER_ADDRESS_NUM', NULL),
(40, 2, 'INVOICE_PROVIDER_ADDRESS_POST', NULL),
(41, 2, 'INVOICE_PROVIDER_ADDRESS_CITY', NULL);

--
-- Dumping data for table `government_item`
--

INSERT INTO `government_item` (`code`, `type`, `name`) VALUES
('00', 1, '(00) Onderworpen aan 0%'),
('01', 1, '(01) Onderworpen aan 6%'),
('02', 1, '(02) Onderworpen aan 12%'),
('03', 1, '(03) Onderworpen aan 21%'),
('46', 1, '(46) Intracommunautaire leveringen'),
('48', 1, '(48) Creditnota intracommunautaire leveringen'),
('49', 1, '(49) Creditnota binnen België'),
('54', 3, '(54) BTW bedrag op Belgische verwervingen'),
('81', 2, '(81) Handelsgoederen, grond- en hulpstoffen'),
('82', 2, '(82) Diensten en diverse goederen'),
('83', 2, '(83) Investeringen'),
('84', 2, '(84) Creditnota''s intracommunautaire verwervingen'),
('85', 2, '(85) Creditnota''s binnen belgië'),
('86', 2, '(86) Intracommunautaire verwervingen'),
('87', 2, '(87) Werken in onroerende staat'),
('XX', 2, '(XX) BTW bedrag aankopen'),
('55', 3, '(55) BTW bedrag op Intercommunautaire verwervingen'),
('56', 3, '(56) BTW bedrag op Mede-contractant'),
('57', 3, '(57) BTW bedrag op verwervingen buiten de EU'),
('61', 3, '(61) Rechtzettingen betalen'),
('63', 3, '(63) BTW bedrag van credit-nota''s BE + EU'),
('59', 4, '(59) BTW bedrag op aankopen'),
('62', 4, '(62) Ingevolge herzieningen'),
('64', 4, '(64) BTW bedrag op creditnota''s België'),
('YY', 4, '(YY) Totaal'),
('71', 5, '(71) Saldo verschuldigde BTW'),
('72', 5, '(72) Saldo te ontvangen BTW');

INSERT INTO `tag_category` (`id`, `name`, `vat`, `type`) VALUES 
('1', 'Omzet/Verkopen', NULL, 'invoice'), 
('2', 'Aankopen/Onkosten', NULL, 'purchase');

INSERT INTO `vat_category` (`id`, `code`, `name`, `type`, `ord`) VALUES 
('1', '00', 'Onderworpen aan 0%', 'invoice', '1'), 
('2', '01', 'Onderworpen aan 6%', 'invoice', '2'), 
('3', '02', 'Onderworpen aan12%', 'invoice', '3'), 
('4', '03', 'Onderworpen aan 21%', 'invoice', '4');

--
-- Dumping data for table `right`
--

INSERT INTO `right` (`id`, `key`, `group`, `action`, `info`) VALUES
(1, 'invoice_view', 'Facturen', 'view', ''),
(2, 'invoice_edit', 'Facturen', 'edit', ''),
(3, 'invoice_delete', 'Facturen', 'delete', ''),
(4, 'offer_view', 'Offertes', 'view', ''),
(5, 'offer_edit', 'Offertes', 'edit', ''),
(6, 'wholesaler_view', 'Wholesaler', 'view', ''),
(7, 'wholesaler_edit', 'Wholesaler', 'edit', ''),
(8, 'offer_delete', 'Offertes', 'delete', ''),
(9, 'purchase_view', 'Inkopen', 'view', ''),
(10, 'purchase_edit', 'Inkopen', 'edit', ''),
(11, 'purchase_delete', 'Inkopen', 'delete', ''),
(12, 'contact_view', 'Contacten', 'view', ''),
(13, 'contact_edit', 'Contacten', 'edit', ''),
(14, 'contact_delete', 'Contacten', 'delete', ''),
(15, 'settings_tags_view', 'Instellingen - Categoriën', 'view', ''),
(16, 'settings_tags_edit', 'Instellingen - Categoriën', 'edit', ''),
(17, 'log_view', 'Activiteit log', 'view', ''),
(18, 'log_edit', 'Activiteit log', 'edit', ''),
(19, 'report_view', 'Rapporten', 'view', ''),
(20, 'stock_view', 'Stock', 'view', ''),
(21, 'stock_edit', 'Stock', 'edit', ''),
(22, 'stock_delete', 'Stock', 'delete', ''),
(23, 'stock_receipt_view', 'Stock - Receipts', 'view', ''),
(24, 'stock_receipt_edit', 'Stock - Receipts', 'edit', ''),
(25, 'stock_receipt_delete', 'Stock - Receipts', 'delete', ''),
(26, 'stock_manage_view', 'Stock - Manage', 'view', ''),   
(27, 'stock_manage_edit', 'Stock - Manage', 'edit', '');

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('ACTIVITY_LOG_SECRET', 'apple'),
('CONTACT_DEFAULT_LOGO_PATH', ''),
('CONTACT_DEFAULT_TABLE_COLOR', '#65be3b'),
('CONTACT_DEFAULT_TEXT_COLOR', '#60676d'),
('INVOICE_ANNEX_FOLDER', 'resources/invoice/annex/'),
('INVOICE_ANNEX_TYPES', 'jpg\r\npng\r\njpeg\r\ngif\r\ntiff\r\npdf\r\ndoc\r\nxdoc\r\ntxt\r\nrtf\r\nbmp\r\nzip\r\nrar'),
('INVOICE_DEFAULT_EMAIL_BODY', 'Geachte {client_firstname} {client_lastname},<br><br>Bij deze ontvangt u de factuur met factuurnummer {invoice_number} voor onze geleverde diensten. Gelieve deze factuur te betalen voor {invoice_expiration_date}.<br><br>U kunt het totaalbedrag van {invoice_total_price} overmaken op ons rekening nummer {bank_number} met vermelding van uw factuurnummer ({invoice_number}).<br><br>Voor internationale betalingen, kunt u onderstaand onze IBAN en BIC/SWIFT gegevens aantreffen.<br><br>IBAN : {bank_iban}<br>BIC/SWIFT : {bank_bic}<br><br>Alvast bedankt,<br><br>Met vriendelijke groeten,<br>{company_name}'),
('INVOICE_DEFAULT_EMAIL_SUBJECT', 'Nieuwe factuur ontvangen - {invoice_number}'),
('INVOICE_DEFAULT_NOTICE', 'We verzoeken u vriendelijk het bovenstaande bedrag van {invoice_total_price} voor {invoice_expiration_date} te voldoen op onze bankrekening onder vermelding van het factuurnummer {invoice_number}.<br><br>Bank: {bank_number} • IBAN: {bank_iban} • BIC: {bank_bic}'),
('INVOICE_DETAULT_INTRO', 'Geachte {client_firstname} {client_lastname},<br>Onderstaand kunt u de factuur aantreffen aangaande onze geleverde diensten. Indien u niet akkoord bent met onderstaande factuur, gelieve ons dan te contacteren via de gegevens in de rechterbovenhoek van deze factuur!'),
('INVOICE_JUDGE_EMAIL_BODY', 'Geachte {client_firstname} {client_lastname},<br><br>Op {invoice_creation_date} hebben wij u onze factuur toegestuurd met factuurnummer {invoice_number}. Deze factuur is vervallen op {invoice_expiration_date}, echter hebben wij uw betaling nog niet mogen ontvangen.<br><br>Dit is de laatste herinnering welke wij u geven, hierna zullen wij onze factuur aan het incassobureau doorgeven en is het volledig uit onze handen.<br><br>Gelieve deze factuur binnen 3 werkdagen over te maken op ons rekeningnummer {bank_number} met vermelding van uw factuurnummer ({invoice_number}).<br><br>Voor internationale overschrijvingen<br>IBAN : {bank_iban}<br>BIC/SWIFT : {bank_bic}<br><br>Indien wij de betaling niet binnen 3 werkdagen op onze rekening hebben staan, zullen de factuurvordering uit handen geven.<br><br>Mocht dit bericht uw betaling kruisen, onze excuses alvast, dan mag u dit bericht negeren.<br><br>Met vriendelijke groeten,<br>{company_name}'),
('INVOICE_JUDGE_EMAIL_SUBJECT', 'Laatste herinnering voor factuur {invoice_number}'),
('INVOICE_LATE_EMAIL_BODY', 'Geachte {client_firstname} {client_lastname},<br><br>Op {invoice_creation_date} hebben wij u onze factuur toegestuurd met factuurnummer {invoice_number}. Deze factuur is vervallen op {invoice_expiration_date}, echter hebben wij uw betaling nog niet mogen ontvangen.<br><br>Gelieve deze factuur binnen 7 werkdagen over te maken op ons rekeningnummer {bank_number} met vermelding van uw factuurnummer ({invoice_number}).<br><br>Voor internationale overschrijvingen<br>IBAN : {bank_iban}<br>BIC/SWIFT : {bank_bic}<br><br>Mocht dit bericht uw betaling kruisen, onze excuses alvast, dan mag u dit bericht negeren.<br><br>Met vriendelijke groeten,<br>{company_name}'),
('INVOICE_LATE_EMAIL_SUBJECT', 'Herinnering voor factuur {invoice_number}'),       
('INVOICE_INTEREST_EMAIL_BODY', ''),
('INVOICE_INTEREST_EMAIL_SUBJECT', ''),       
('INVOICE_THANKS_EMAIL_BODY', 'Geachte {client_firstname} {client_lastname},<br><br>Wij hebben uw betaling van de factuur met factuurnummer {invoice_total_price} succesvol ontvangen op onze rekening. Wij danken u dan ook hiervoor.<br><br>Indien u in de toekomst opnieuw wenst gebruik te maken van onze diensten, aarzel dan niet om ons te contacteren.<br><br>Met vriendelijke groeten,<br>{company_name}'),
('INVOICE_THANKS_EMAIL_SUBJECT', 'Betaling ontvangen voor factuur {invoice_number}!'),
('INVOICE_NEXT_NUM', '1'),
('INVOICE_PRODUCT_DEFAULT_VAT', ''),
('INVOICE_PROVIDER_COMPANY', ''),
('INVOICE_PROVIDER_ADDRESS', ''),
('INVOICE_PROVIDER_ADDRESS_CITY', ''),
('INVOICE_PROVIDER_ADDRESS_NUM', ''),
('INVOICE_PROVIDER_ADDRESS_POST', ''),
('INVOICE_PROVIDER_ADDRESS_STREET', ''),
('INVOICE_PROVIDER_LAND', ''),
('INVOICE_PROVIDER_BANKNAME', ''),
('INVOICE_PROVIDER_BANK_LOCATION', ''),
('INVOICE_PROVIDER_BANK_NUMBER', ''),
('INVOICE_PROVIDER_BIC', ''),
('PURCHASE_NUMBER_FORMAT', 'INK-[Jaar]-'),
('INVOICE_PROVIDER_BTW', ''),
('INVOICE_PROVIDER_EMAIL', ''),
('INVOICE_PROVIDER_KVK', ''),
('INVOICE_PROVIDER_PHONE', ''),
('INVOICE_PROVIDER_WEBSITE', ''),
('INVOICE_B2B_PAYMENT_TERM', '30'),
('INVOICE_B2B_FIRST_TERM', '10'),
('INVOICE_B2B_SECOND_TERM', '5'),
('INVOICE_B2B_LAST_TERM', '5'),
('INVOICE_B2B_HAS_INTEREST', '0'),
('INVOICE_B2B_INTEREST_RATE', '6.24'),
('INVOICE_B2B_INTEREST_TERM', 'off'),
('INVOICE_B2B_AUTOSENDEMAIL', '0'),
('INVOICE_B2C_PAYMENT_TERM', '30'),
('INVOICE_B2C_FIRST_TERM', '10'),
('INVOICE_B2C_SECOND_TERM', '5'),
('INVOICE_B2C_LAST_TERM', '5'),
('INVOICE_B2C_HAS_INTEREST', '0'),
('INVOICE_B2C_INTEREST_RATE', '6.24'),
('INVOICE_B2C_INTEREST_TERM', 'off'),
('INVOICE_B2C_AUTOSENDEMAIL', '0'),
('INVOICE_SIGNATURE_FOLDER', 'resources/invoice/signature/'),
('INVOICE_SIGNATURE_TYPES', 'jpg\r\npng\r\njpeg\r\ngif'),
('INVOICE_URGENT_EMAIL_BODY', 'Geachte {client_firstname} {client_lastname},<br><br>Op {invoice_creation_date} hebben wij u onze factuur toegestuurd met factuurnummer {invoice_number}. Deze factuur is vervallen op {invoice_expiration_date}, echter hebben wij uw betaling nog niet mogen ontvangen.<br><br>Dit is reeds de 2de en tevens laatste herinnering welke wij u sturen.<br><br>Gelieve deze factuur binnen 5 werkdagen over te maken op ons rekeningnummer {bank_number} met vermelding van uw factuurnummer ({invoice_number}).<br><br>Voor internationale overschrijvingen<br>IBAN : {bank_iban}<br>BIC/SWIFT : {bank_bic}<br><br>Indien wij de betaling niet binnen 5 werkdagen op onze rekening hebben staan, zullen de factuurvordering uit handen geven.<br><br>Mocht dit bericht uw betaling kruisen, onze excuses alvast, dan mag u dit bericht negeren.<br><br>Met vriendelijke groeten,<br>{company_name}'),
('INVOICE_URGENT_EMAIL_SUBJECT', '2de herinnering voor factuur {invoice_number}'),
('INVOICE_NUMBER_FORMAT', 'FAC-[Jaar]-'), 
('CONTACT_NUMBER_FORMAT', 'DB-'), 
('WHOLESALER_NUMBER_FORMAT', 'CR-'), 
('CREDIT_NUMBER_FORMAT', 'CRF-[Jaar]-'), 
('MAIL_FROM_ADDRESS', ''),
('MAIL_FROM_NAME', ''),
('PACK_NEXT_NUM', '1'),
('PROFORMA_DEFAULT_EMAIL_BODY', ''),
('PROFORMA_DEFAULT_EMAIL_SUBJECT', ''),
('PROFORMA_DEFAULT_INTRO', ''),
('PROFORMA_DEFAULT_NOTICE', ''),
('PROFORMA_JUDGE_EMAIL_BODY', ''),
('PROFORMA_JUDGE_EMAIL_SUBJECT', ''),
('PROFORMA_LATE_EMAIL_BODY', ''),
('PROFORMA_LATE_EMAIL_SUBJECT', ''),
('PROFORMA_THANKS_EMAIL_BODY', ''),
('PROFORMA_THANKS_EMAIL_SUBJECT', ''),
('PROFORMA_NEXT_NUM', '1'),
('PROFORMA_URGENT_EMAIL_BODY', ''),
('PROFORMA_URGENT_EMAIL_SUBJECT', ''),
('PROFORMA_NUMBER_FORMAT', 'OFF-[Jaar]-'),
('PURCHASE_ATTACHMENT_FOLDER', 'resources/purchase/attachment/'),
('PURCHASE_ATTACHMENT_TYPES', 'jpg\r\npng\r\njpeg\r\ngif\r\ntiff\r\npdf\r\ndoc\r\nxdoc\r\ntxt\r\nrtf\r\nbmp\r\nzip\r\nrar'),
('PURCHASE_NEXT_NUM', '1'),
('RECEIPT_NEXT_NUM', '1'),
('SMTP_ACTIVATE', '1'),
('SMTP_AUTH', 'login'),
('SMTP_AUTH_PASSWORD', 'chaos359'),
('SMTP_AUTH_USERNAME', 'entropy359@gmail.com'),
('SMTP_HOST', 'smtp.gmail.com'),
('SMTP_PORT', '587'),
('SMTP_SSL', 'tls'),
('SYSTEM_YEAR', YEAR(SYSDATE())),  
('SYSTEM_CREATED_DATE', SYSDATE()),
('STANDARD_PACK_NEXT_NUM', '1'),
('PERMISSION_PURCHASE', '0'),
('WEBSHOP_ABOUT_IMAGE', ''),
('WEBSHOP_ABOUT_TEXT', ''),
('WEBSHOP_ACTIVATION', 'off'),
('WEBSHOP_CONDITION_PDF', ''),
('WEBSHOP_FACEBOOK', 'www.facebook.com'),
('WEBSHOP_GOOGLE', ''),
('WEBSHOP_HOME_DEFAULT_IMAGE1', ''),
('WEBSHOP_HOME_DEFAULT_IMAGE2', ''),
('WEBSHOP_HOME_DEFAULT_IMAGE3', ''),
('WEBSHOP_LINKEDIN', ''),
('WEBSHOP_LOGO', ''),
('WEBSHOP_MAIN_COLOR', '#e61e24'),
('WEBSHOP_TITLE', ''),
('WEBSHOP_TWITTER', 'www.twitter.com'),
('WEBSHOP_VIMEO', 'www.vimeo.com');


INSERT INTO `format_type` (`id`, `value`) VALUES
(1, 'Jaar'),
(2, 'Maand'),
(3, 'Dag');

INSERT INTO `tag` (`id`, `tag_category_id`, `name`, `vat`, `vat_category_id`, `number`) VALUES 
('1', '1', 'Onderworpen aan 0%', '0', '1', '1'), 
('2', '1', 'Onderworpen aan 6%', '6', '2', '2'), 
('3', '1', 'Onderworpen aan 12%', '12', '3', '3'), 
('4', '1', 'Onderworpen aan 21%', '21', '4', '4');
