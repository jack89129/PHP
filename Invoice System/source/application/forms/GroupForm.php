<?php
class GroupForm extends Zend_Form
{
	public function init(){
	

		$name=$this->CreateElement('text','name')
                       ->setLabel('Voornaam:');
		$name->class = "forminputfield";		   
		$name->setDecorators(array(
		
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array('Label', array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$name->setRequired(TRUE);
		$name->setAttrib('size', 30);

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
	   
               $name,
               $submit
       ));

	   $this->setDecorators(array(

               'FormElements',

               array(array('data'=>'HtmlTag'),array('tag'=>'table')),

               'Form'

       ));
		
	}
}