<?php

    class ActivityLogEventModel extends Jaycms_Db_Model {

        protected $_name = 'activity_log_event';

        public function getEventByMCA($module, $controller, $action){
            $event = $this->select()->where('module = ?'    , $module)
                                    ->where('controller = ?', $controller)
                                    ->where('action = ?'    , $action)
                                    ->query(Zend_Db::FETCH_OBJ)->fetch();

            if( !$event ){
                return null;
            }

            return new ActivityLogEvent($event->id);
        }
    }