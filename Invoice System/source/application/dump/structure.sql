CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action` char(30) NOT NULL,
  `target` char(30) NOT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `params` text NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `street` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `addrnr` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `post` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `cellphone` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `mail` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `vat` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `special_invoice_requirement` text CHARACTER SET utf8,
  `party_type` int(11) NOT NULL DEFAULT '1',
  `adults` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `created_user` int(11) NOT NULL DEFAULT '2',
  `location` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `comment` text CHARACTER SET utf8,
  `reserved_date` date NOT NULL DEFAULT '2013-07-02',
  `status` int(11) NOT NULL DEFAULT '0',
  `cnt` int(11) DEFAULT '1',
  `reception_id` int(11) NOT NULL,
  `hapje_count` int(11) NOT NULL,
  `drink` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agenda_hapje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agenda_id` int(11) NOT NULL,
  `hapje_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agenda_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agenda_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agenda_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `buffet` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: Aan tafel, 1: Buffet',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agenda_party_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `bankboek` (
  `kas_date` varchar(10) CHARACTER SET utf8 NOT NULL,
  `amount` float NOT NULL,
  `afsch` varchar(100) CHARACTER SET utf8 NOT NULL,
  `before_amount` float NOT NULL,
  PRIMARY KEY (`kas_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `vat_number` varchar(255) DEFAULT NULL,
  `kvk_number` varchar(255) DEFAULT NULL,
  `delivery_firstname` varchar(255) DEFAULT NULL,
  `delivery_lastname` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_postcode` varchar(255) DEFAULT NULL,
  `delivery_city` varchar(255) DEFAULT NULL,
  `delivery_country` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `phone2` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `cellphone` varchar(255) DEFAULT NULL,
  `days` varchar(255) DEFAULT NULL,
  `info` text,
  `role` varchar(255) DEFAULT NULL,
  `discount` int(11) NOT NULL DEFAULT '0',
  `is_intro` tinyint(4) NOT NULL DEFAULT '0',
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `pwd` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_b2b` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contact_employee_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contact_group_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee_group_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `employee_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `employee_group_id` (`employee_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `latitude` decimal(8,6) NOT NULL,
  `longitude` decimal(8,6) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee_product_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employee_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `transit` int(11) NOT NULL,
  `reservation` int(11) NOT NULL,
  `reservation_pending` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `emp_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `setting_name` varchar(60) CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `government_item` (
  `code` varchar(3) CHARACTER SET utf8 NOT NULL,
  `type` tinyint(4) NOT NULL,
  `name` varchar(60) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `hapje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(200) DEFAULT NULL,
  `parent_invoice_id` int(11) unsigned DEFAULT NULL,
  `contact_id` int(11) unsigned DEFAULT NULL,
  `total_sum` decimal(10,2) DEFAULT NULL,
  `total_excl_vat` decimal(10,2) DEFAULT NULL,
  `vat_sum` decimal(10,2) DEFAULT NULL,
  `discount_sum` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) NOT NULL,
  `invoice_time` datetime DEFAULT NULL,
  `paid_time` datetime NOT NULL,
  `expire_time` datetime NOT NULL,
  `reminder_time` datetime NOT NULL,
  `notice` text,
  `notice_pattern` text,
  `info` text,
  `intro` text,
  `intro_pattern` text,
  `credit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `proforma` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `proforma_status` enum('new','open','accepted','denied','invoice','archive') NOT NULL DEFAULT 'open',
  `status` enum('new','final') DEFAULT 'new',
  `created_by` int(11) NOT NULL,
  `step` int(11) NOT NULL DEFAULT '0',
  `from_webshop` enum('no','yes') CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`number`),
  KEY `parent_invoice_id` (`parent_invoice_id`),
  KEY `status` (`status`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invoice_annex` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `filename` char(120) NOT NULL,
  `name` char(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
       
CREATE TABLE IF NOT EXISTS `invoice_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `paid_time` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('over','cont','paypal','credit','ideal','mister') NOT NULL DEFAULT 'over',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
       
CREATE TABLE IF NOT EXISTS `invoice_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `description` char(120) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `vat` tinyint(3) unsigned NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_sum` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
       
CREATE TABLE IF NOT EXISTS `kasboek` (
  `kas_date` varchar(10) CHARACTER SET utf8 NOT NULL,
  `amount` float NOT NULL,
  `afsch` varchar(100) CHARACTER SET utf8 NOT NULL,
  `before_amount` float NOT NULL,
  PRIMARY KEY (`kas_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
       
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_type` enum('agenda','invoice','purchase','receipt','pack') NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  `event` enum('agenda_confirmed','agenda_optional','agenda_deleted','agenda_print','invoice_created','invoice_duplicate','invoice_sent_email','invoice_sent_personal','invoice_late','invoice_urgent','invoice_judge','invoice_payment','invoice_paid','invoice_unpaid','invoice_annex','invoice_annex_edit','invoice_annex_delete','invoice_credit','invoice_proforma_status','proforma_to_invoice','invoice_from_proforma','purchase_created','purchase_payment','purchase_paid','purchase_attachment','purchase_attachment_edit','receipt_created','pack_created','pack_final','pack_unfinal','pack_product_added','pack_product_removed','manual') NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
       
CREATE TABLE IF NOT EXISTS `menu_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
       
CREATE TABLE IF NOT EXISTS `menu_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `format_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
              
CREATE TABLE IF NOT EXISTS `natsort_lookup` (
  `algorithm` varchar(20) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`algorithm`,`source`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
                            
CREATE TABLE IF NOT EXISTS `natsort_lookup_pending` (
  `algorithm` varchar(20) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
                                                        
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `bill_company` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_lastname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_postcode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bill_country` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_company` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_lastname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_postcode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `delivery_country` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `subtotal` float(2,0) NOT NULL,
  `vat` float(2,0) NOT NULL,
  `total` float(2,0) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_method` varchar(50) CHARACTER SET utf8 NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8 NOT NULL,
  `order_note` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(200) DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `info` text NOT NULL,
  `status` enum('new','final') NOT NULL DEFAULT 'new',
  `created_time` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pack_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pack_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `description` text,
  `qty` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`pack_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(120) DEFAULT NULL,
  `product_group_id` int(11) DEFAULT NULL,
  `wholesaler_id` int(10) unsigned DEFAULT NULL,
  `order_code` char(30) DEFAULT NULL,
  `short_description` text,
  `long_description` text,
  `article_code` char(30) DEFAULT NULL,
  `discount` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `min_price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `vat` tinyint(3) unsigned NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `stock` smallint(5) NOT NULL,
  `min_stock` smallint(5) NOT NULL,
  `last_stock` smallint(5) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `income_tag_id` int(11) unsigned NOT NULL,
  `expense_tag_id` int(11) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `has_webshop` tinyint(4) NOT NULL DEFAULT '0',
  `main_img` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sub_img1` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sub_img2` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sub_img3` varchar(255) CHARACTER SET utf8 NOT NULL,
  `has_new_price` tinyint(4) NOT NULL DEFAULT '0',
  `new_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_discount` tinyint(4) NOT NULL DEFAULT '0',
  `new_vat` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_group_id` (`product_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `product_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `purchase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(200) DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `total_sum` decimal(10,2) unsigned DEFAULT NULL,
  `total_excl_vat` decimal(10,2) unsigned DEFAULT NULL,
  `vat_sum` decimal(10,2) unsigned DEFAULT NULL,
  `discount_sum` decimal(10,2) unsigned DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `invoice_time` date DEFAULT NULL,
  `expire_time` datetime DEFAULT NULL,
  `info` text,
  `intro` text,
  `paid_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `purchase_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `filename` char(120) NOT NULL,
  `name` char(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `purchase_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `paid_time` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('over','cont','paypal','credit','ideal','mister') NOT NULL DEFAULT 'over',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `purchase_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `description` text,
  `qty` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) unsigned DEFAULT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `total_sum` decimal(10,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(200) DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `pack_id` int(10) unsigned DEFAULT NULL,
  `info` text NOT NULL,
  `status` enum('new','final') NOT NULL DEFAULT 'new',
  `created_time` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `delivery_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`number`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `receipt_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `description` text,
  `qty` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`receipt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yearmonth` varchar(7) CHARACTER SET utf8 NOT NULL,
  `code` varchar(3) CHARACTER SET utf8 NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `right` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` char(30) NOT NULL,
  `group` char(30) NOT NULL,
  `action` char(30) NOT NULL,
  `info` char(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `right_employee_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `right_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `settings` (
  `name` char(60) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `setting_pack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(200) DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `info` text NOT NULL,
  `status` enum('new','final') NOT NULL DEFAULT 'new',
  `created_time` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `setting_pack_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pack_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `description` text,
  `qty` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`pack_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_category_id` int(11) NOT NULL,
  `name` char(120) NOT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  `vat_category_id` int(11) DEFAULT NULL,
  `number` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_category_id` (`tag_category_id`),
  KEY `vat_category_id` (`vat_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tag_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(120) NOT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  `type` enum('invoice','purchase','other','temporary') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `vat_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(2) NOT NULL,
  `name` char(60) NOT NULL,
  `type` enum('invoice','purchase','other') NOT NULL,
  `ord` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wholesaler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname2` varchar(200) DEFAULT NULL,
  `lastname2` varchar(200) DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `vat_number` varchar(255) DEFAULT NULL,
  `kvk_number` varchar(255) DEFAULT NULL,
  `client_number` varchar(255) DEFAULT NULL,
  `delivery_firstname` varchar(255) DEFAULT NULL,
  `delivery_lastname` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_postcode` varchar(255) DEFAULT NULL,
  `delivery_city` varchar(255) DEFAULT NULL,
  `delivery_country` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `phone2` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `cellphone` varchar(255) DEFAULT NULL,
  `days` varchar(255) DEFAULT NULL,
  `info` text,
  `role` varchar(255) DEFAULT NULL,
  `is_intro` tinyint(4) NOT NULL DEFAULT '0',
  `expense_tag_id` int(11) NOT NULL,
  `is_b2b` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wholesaler_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wholesaler_group_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wholesaler_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `contact_id` (`wholesaler_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wholesaler_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wholesaler_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL,
  `vat` tinyint(3) unsigned NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_sum` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `contact_group_map`
  ADD CONSTRAINT `contact_group_map_ibfk_5` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_group_map_ibfk_6` FOREIGN KEY (`group_id`) REFERENCES `contact_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `employee_group_map`
  ADD CONSTRAINT `employee_group_map_ibfk_6` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_group_map_ibfk_7` FOREIGN KEY (`employee_group_id`) REFERENCES `employee_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `employee_product_map`
  ADD CONSTRAINT `employee_product_map_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_product_map_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoice_product`
  ADD CONSTRAINT `invoice_product_ibfk_3` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`product_group_id`) REFERENCES `product_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `purchase_attachment`
  ADD CONSTRAINT `purchase_attachment_ibfk_3` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `purchase_product`
  ADD CONSTRAINT `purchase_product_ibfk_2` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tag`
  ADD CONSTRAINT `tag_ibfk_2` FOREIGN KEY (`tag_category_id`) REFERENCES `tag_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tag_ibfk_4` FOREIGN KEY (`vat_category_id`) REFERENCES `vat_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;