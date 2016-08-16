<?php

    class Api_ContactController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();
        }

        public function getContactAction(){
            $contact = new Contact((int)$this->_getParam('id'));

            if( !$contact->exists() ){
                throw new Exception(_t('Contact not found!'));
            }

            $employee = new Employee(Utils::user()->id);

            if( !$employee->exists() ){
                throw new Exception(_t("Employee not found!"));
            }

            $view = $employee->can('contact_view_all');
            if( !$view ){
                foreach( $employee->contacts as $employeeContact ){
                    if( $contact->id == $employeeContact->id ){
                        $view = true;
                        break;
                    }
                }
            }

            if( !$view ){
                throw new Exception(_t("Employee does not have access to this contact!"));
            }
            
            $total = "0.00";
            $sum = 0;
            foreach( $contact->finalInvoices as $invoice ) {
                if ( $invoice->getPaidTime() == 0 )
                    $sum += $invoice->total_sum;
            }
            $total = $sum;
            /*if ( $total == 0 ) {
                $total = '0,00';
            } else {
                $total = str_replace('.', ',', $total);
            }*/

            return $this->_helper->json(stringify(array_merge($contact->data(), array('saldo' => $total))));
        }

        public function getContactsAction(){
            $contact = new Contact();
            $contacts = $contact->findAll(array(), array('firstname ASC'));

            $employee = new Employee(Utils::user()->id);

            if( !$employee->exists() ){
                throw new Exception(_t("Employee not found!"));
            }

            $result = array();

            foreach( $contacts as $key => $contact ){
                $view_all = $employee->can('contact_view_all');
                $view = $view_all;
                if( !$view_all ){
                    foreach( $employee->contacts as $employeeContact ){
                        if( $contact->id == $employeeContact->id ){
                            $view = true;
                        }
                    }
                }

                if( !$view ){
                    continue;
                }
                
                $total = "0.00";
                $sum = 0;
                foreach( $contact->finalInvoices as $invoice ) {
                    if ( $invoice->getPaidTime() == 0 )
                        $sum += $invoice->total_sum;
                }
                $total = $sum;

                $result[] = array_merge($contact->data(), array('saldo' => $total));
            }

            $result = stringify($result);
            return $this->_helper->json($result);
        }

        public function saveContactAction(){
            $contactParam = (array) $this->_getParam('contact', array());
            $daysParam = (array) $this->_getParam('days', array());
            $contactId = !empty($contactParam['id']) ? $contactParam['id'] : null;

            // removed for now
            unset($contactParam['days']);

            if( empty($contactParam['firstname']) && empty($contactParam['lastname']) && empty($contactParam['company_name']) ){
                throw new Exception(_t("Enter firstname, lastname or company name!"));
            }

            $contact = new Contact($contactId);

            if( $contactId && !$contact->exists() ){
                throw new Exception(_t("Contact not found!"));
            }

            $contact->load($contactParam);
            $contact->save();

            $this->_helper->json(array('id' => stringify($contact->id)));
        }

        public function saveContactDaysAction(){
            $contactId = (int) $this->_getParam('id', 0);
            $days = (array) $this->_getParam('days', array());

            $contact = new Contact($contactId);

            if( !$contact->exists() ){
                throw new Exception(_t("Contact not found!"));
            }
        }
    }