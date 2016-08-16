<?php

    /**
     * @property int $id
     * @property int $ipid
     * @property int $score
     * @property int $oldscore
     * @property int $dateid
     * @property Ip $ip                 
     */
    class Score extends Core_ActiveRecord_Row {

        public function relations(){
            return array(
                'ip' => array('Ip', 'ipid', self::HAS_ONE),                       
            );
        }

        public function __construct($id=null){
            parent::__construct(new ScoreModel(), $id);
        }
        
    }