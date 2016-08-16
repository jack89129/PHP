<?php
class FormGroupForm extends Zend_Form
{
	public function init(){
	
		$name = $this->createElement('text', 'name');
		$name->setLabel('Enter Group Name:');
		$name->setRequired(TRUE);
		$name->setAttrib('size', 30);
		$this->addElement($name);

		$id = $this->createElement('hidden', 'id');
		$this->addElement($id);
		
		$this->addElement('submit', 'submit', array('label' => 'Submit'));
		
	}
}