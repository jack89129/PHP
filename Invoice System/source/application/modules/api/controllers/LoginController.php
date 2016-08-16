<?php

    class Api_LoginController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();
        }

        public function loginAction(){
            $username = $this->_getParam('username', '');
            $password = Employee::saltPassword($this->_getParam('password', ''));

            $employee = new Employee();
            $employee = reset($employee->findAll(array(array('username = ?', $username), array('password = ?', $password))));

            if( !$employee ){
                $this->_helper->json(false);
            }

            $user = new Zend_Session_Namespace('user');
            $user->id = $employee->id;

            $result = $employee->data();
            unset($result['username']);
            unset($result['password']);

            $this->_helper->json(stringify($result));
        }

        public function logoutAction(){
            $user = new Zend_Session_Namespace('user');
            $user->id = null;
            $this->_helper->json(array());
        }
    }