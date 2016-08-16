<?php

    class Api_EmployeeController extends Jaycms_Controller_Action {


        public function init(){
            parent::init();
        }

        public function setLocationAction(){
            $latitude = $this->_getParam('latitude', 0);
            $longitude = $this->_getParam('longitude', 0);

            if( $latitude < -90 || $latitude > 90 || $longitude < 0 || $longitude > 360 ){
                throw new Exception(_t("Invalid location!"));
            }


            $location = new EmployeeLocation();
            $location->employee_id = Utils::user()->id;
            $location->latitude = $latitude;
            $location->longitude = $longitude;
            $location->time = time();
            $location->save();

            $this->_helper->json(array());
        }
    }