<?php

    /**
     * @property int $id
     * @property string $module
     * @property string $controller
     * @property string $action
     * @property string $description
     *
     * @property ActivityLog[] $logs
     */
    class ActivityLogEvent extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ActivityLogEventModel(), $id);
        }

        public function relations(){
            return array(
                'logs' => array('ActivityLog', 'activity_log_event_id', self::HAS_MANY)
            );
        }
    }