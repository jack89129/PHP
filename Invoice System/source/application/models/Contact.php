<?php

	/**
	 * @property int $id	 
	 * @property string $firstname	 
	 * @property string $lastname
     * @property string $company_name
	 * @property string $address	 
	 * @property string $postcode	 
	 * @property string $city	 
	 * @property string $country
     * @property string $vat_number
     * @property string $delivery_firstname
     * @property string $delivery_lastname
     * @property string $delivery_address
     * @property string $delivery_postcode
     * @property string $delivery_city
     * @property string $delivery_country
     * @property string $contact_person
	 * @property string $email_address	 
	 * @property string $phone1
     * @property string $phone2
     * @property string $fax
     * @property string $cellphone
     * @property string $days
     * @property string $info
	 * @property string $role
     * @property int $is_b2b
     * @property int $discount
     *
     * @property string $number
     * @property string $name
     * @property string $delivery     
     *
     * @property ContactGroup[] $groups
     * @property Employee[] $employees
     *
     * @property Invoice[] $finalInvoices
     * @property Invoice[] $finalOffers
	 * 
	 * @author tolito
	 */
	class Contact extends Core_ActiveRecord_Row {
		
		public function __construct($id=null){
			parent::__construct(new ContactModel(), $id);
		}

        public function relations(){
            return array(
                'groups' => array('ContactGroup', array('ContactGroupMap' => array('contact_id', 'group_id')), self::HAS_MANY_TO_MANY, array(), array('natsort(name, "natural") ASC')),
                'employees' => array('Employee', array('ContactEmployeeMap' => array('contact_id', 'employee_id')), self::HAS_MANY_TO_MANY, array(), array('firstname ASC')),
                'finalInvoices' => array('Invoice', 'contact_id', self::HAS_MANY, array(array('proforma = ?', 0), array('status = ?', InvoiceModel::STATUS_FINAL))),
                'finalOffers' => array('Invoice', 'contact_id', self::HAS_MANY, array(array('proforma = ?', 1), array('status = ?', InvoiceModel::STATUS_FINAL))),
            );
        }

        public function getDays(){
            $days = array_fill(0, 7, array('','','','','',''));
            $parts = $this->get('days') ? explode('|', $this->get('days')) : array();


            foreach( $days as $dk => $day ){
                foreach( $day as $tk => $time ){
                    if( !$parts ){
                        break 2;
                    }

                    $days[$dk][$tk] = array_shift($parts);
                }
            }

            return $days;
        }

        public function getDay($index){
            $result = array();
            $morning = array();
            $afternoon = array();

            if( !empty($this->days[$index][2]) || (empty($this->days[$index][0]) && empty($this->days[$index][1])) ){
                $morning = false;
            }else{
                $morning[] = !empty($this->days[$index][0]) ? $this->days[$index][0] : false ;
                $morning[] = !empty($this->days[$index][1]) ? $this->days[$index][0] : false ;
            }
            $result[] = $morning;


            if( !empty($this->days[$index][5]) || (empty($this->days[$index][3]) && empty($this->days[$index][4]))){
                $afternoon = false;
            }else{
                $afternoon[] = !empty($this->days[$index][3]) ? $this->days[$index][3] : false ;
                $afternoon[] = !empty($this->days[$index][4]) ? $this->days[$index][4] : false ;
            }

            $result[] = $afternoon;
            return $result;
        }

        public function setDays($days){
        	if (!function_exists('array_replace_recursive')) require_once(APPLICATION_PATH . '/../library/array_replace_recursive.php');
            $days = array_replace_recursive(array_fill(0, 7, array('','','','','','')), $days);
            $result = array();
            foreach($days as $day){
                foreach( $day as $index => $time ){
                    $result[] = $time;
                }
            }
            $this->set('days', implode('|', $result));
        }

        public function getHaveDelivery(){
            return  $this->get('delivery_firstname')  || $this->get('delivery_lastname') ||
                    $this->get('delivery_address')    || $this->get('delivery_postcode') ||
                    $this->get('delivery_city')       || $this->get('delivery_country' );
        }

        public function getNumber(){
            return Utils::contactNumberFormat($this->id);
        }

        public function getName(){
            $fl = trim($this->firstname . ' ' . $this->lastname);
            return $fl ? $fl : $this->company_name;
        }

        public function getDeliveryName(){
            if( !$this->getHaveDelivery() ){
                return $this->getName();
            }

            $fl = trim($this->delivery_firstname . ' ' . $this->delivery_lastname);
            return $fl ? $fl : $this->company_name;
        }

        public function getNameReversed(){
            $fl = trim($this->lastname . ', ' . $this->firstname);
            return $fl != ',' ? $fl : $this->company_name;
        }
	}