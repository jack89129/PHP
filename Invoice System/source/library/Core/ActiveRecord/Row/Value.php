<?php

    class Core_ActiveRecord_Row_Value {

        protected $_row;
        protected $_value;

        public function __construct(Core_ActiveRecord_Row $row, $value){
            $this->_row = $row;
            $this->_value = $value;
        }

        public function __call($method, $arguments){
            $classMethod = 'modifier' . ucfirst($method);
            array_unshift($arguments, $this->_value);

            if( method_exists($this->_row, $classMethod) ){
                return new self($this->_row, call_user_func_array(array($this->_row, $classMethod), $arguments));
            }

            if( function_exists($method) ){
                return new self($this->_row, call_user_func_array($method, $arguments));
            }

            if( method_exists($this, $classMethod) ){
                return new self($this->_row, call_user_func_array(array($this, $classMethod), $arguments));
            }

            if( $method == 'raw' ){
                return $this->_value;
            }

            throw new Exception("Unknown modifier " . $method . "!");
        }

        public function modifierToInt($value){
            return (int) $value;
        }

        public function modifierToFloat($value){
            return (float) $value;
        }

        public function modifierToArray($value){
            return (array) $value;
        }

        public function modifierToObject($value){
            return (object) $value;
        }

        public function modifierToString($value){
            return (string) $value;
        }

        public function __toString(){
            return (string) $this->_value;
        }
    }