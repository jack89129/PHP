<?php

	
		
class ContactForm extends Zend_Form
{

    public function __construct($option=null)

   {
       parent::__construct($option);
	 
		$firstname=$this->CreateElement('text','firstname')
                       ->setLabel('Voornaam:');
		$firstname->class = "forminputfield";		   
		$firstname->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$firstname->setRequired(TRUE);
		$firstname->setAttrib('size', 30);
		
		$lastname=$this->CreateElement('text','lastname')
                       ->setLabel('Achternaam:');
		$lastname->class = "forminputfield";		   
		$lastname->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$lastname->setRequired(TRUE);
		$lastname->setAttrib('size', 30);
		
		$address=$this->CreateElement('text','address')
                       ->setLabel('Adres:');
		$address->class = "forminputfield";		   
		$address->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$address->setRequired(TRUE);
		$address->setAttrib('size', 30);
		
		$postcode=$this->CreateElement('text','postcode')
                       ->setLabel('Postcode:');
		$postcode->class = "forminputfield";		   
		$postcode->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$postcode->setRequired(TRUE);
		$postcode->setAttrib('size', 30);
		
		$city=$this->CreateElement('text','city')
                       ->setLabel('plaats:');
		$city->class = "forminputfield";		   
		$city->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$city->setRequired(TRUE);
		$city->setAttrib('size', 30);
		   
		$country=$this->CreateElement('text','country')
                       ->setLabel('Land:');
		$country->class = "forminputfield";		   
		$country->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$country->setRequired(TRUE);
		$country->setAttrib('size', 30);
		   
		$email_address=$this->CreateElement('text','email_address')
                       ->setLabel('E-mailadres:');
		$email_address->class = "forminputfield";		   
		$email_address->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$email_address->setRequired(TRUE);
		$email_address->addValidator(new Zend_Validate_EmailAddress());
		$email_address->addFilters(array(
			new Zend_Filter_StringTrim(),
			new Zend_Filter_StringToLower()
			));
		$email_address->setAttrib('size', 40);
		   
		$phone=$this->CreateElement('text','phone')
                       ->setLabel('Telefoonnummers:');
		$phone->class = "forminputfield";		   
		$phone->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$phone->setRequired(TRUE);
		$phone->setAttrib('size', 30);
		   
		$role=$this->CreateElement('text','role')
                       ->setLabel('Functie:');
		$role->class = "forminputfield";			   
		$role->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'),array('tag'=>'tr'))
           ));
		$role->setAttrib('size', 30);
		
		$id = $this->createElement('hidden', 'id');
		$this->addElement($id);
		
		   
	   $submit=$this->CreateElement('submit','submit_new')

                       ->setLabel('voorleggen');

       $submit->setDecorators(array(

               'ViewHelper',

               'Description',

               'Errors', array(array('data'=>'HtmlTag'), array('tag' => 'td',

               'colspan'=>'2','align'=>'center')),

               array(array('row'=>'HtmlTag'),array('tag'=>'tr'))

       ));
	   
	   $this->addElements(array(
	   
               $firstname,
			   $lastname,
			   $address,
			   $postcode,
			   $city,
			   $country,
				$email_address,
               $phone,
			   $role,
               $submit
       ));

	   $this->setDecorators(array(

               'FormElements',

               array(array('data'=>'HtmlTag'),array('tag'=>'table')),

               'Form'

       ));
	  
    }
	
}
