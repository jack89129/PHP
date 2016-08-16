<?php
class FormContactForm extends Zend_Form
{
	public function init(){
	
		$firstName = $this->createElement('text', 'firstname');
		$firstName->setLabel('Enter your FirstName:');
		$firstName->setRequired(TRUE);
		$firstName->setAttrib('size', 30);
		$this->addElement($firstName);

		
		$lastName = $this->createElement('text', 'lastname');
		$lastName->setLabel('Enter your LastName:');
		$lastName->setRequired(TRUE);
		$lastName->setAttrib('size', 30);
		$this->addElement($lastName);

		
		$address = $this->createElement('text', 'address');
		$address->setLabel('Enter your address:');
		$address->setRequired(TRUE);
		$address->setAttrib('size', 30);
		$this->addElement($address);

		
		$postcode = $this->createElement('text', 'postcode');
		$postcode->setLabel('Enter your postcode:');
		$postcode->setRequired(TRUE);
		$postcode->setAttrib('size', 30);
		$this->addElement($postcode);
		
		$city = $this->createElement('text', 'city');
		$city->setLabel('Enter your city:');
		$city->setRequired(TRUE);
		$city->setAttrib('size', 30);
		$this->addElement($city);
		
		$country = $this->createElement('text', 'country');
		$country->setLabel('Enter your country:');
		$country->setRequired(TRUE);
		$country->setAttrib('size', 30);
		$this->addElement($country);
		
		$email_address = $this->createElement('text', 'email_address');
		$email_address->setLabel('Enter your email_address:');
		$email_address->setRequired(TRUE);
		$email_address->addValidator(new Zend_Validate_EmailAddress());
		$email_address->addFilters(array(
			new Zend_Filter_StringTrim(),
			new Zend_Filter_StringToLower()
			));
		$email_address->setAttrib('size', 40);
		$this->addElement($email_address);
		
		$phone = $this->createElement('text', 'phone');
		$phone->setLabel('Enter your phone:');
		$phone->setRequired(TRUE);
		$phone->addValidator(new Zend_Validate_Digits());		
		$phone->setAttrib('size', 30);
		$this->addElement($phone);
		
		$role = $this->createElement('text', 'role');
		$role->setLabel('Enter your role:');
		$role->setRequired(TRUE);
		$role->setAttrib('size', 30);
		$this->addElement($role);

		$id = $this->createElement('hidden', 'id');
		$this->addElement($id);
		
		$this->addElement('submit', 'submit', array('label' => 'Submit'));
		
	}
}