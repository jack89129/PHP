<?php

    class ScoreModel extends Jaycms_Db_Model {

        protected $_name = 'score';
        
        public function getLastScoreList($dateid) {
            $result = $this->select()->where('dateid = ?', $dateid)->order('ipid')->query(Zend_Db::FETCH_OBJ)->fetchAll();
            
            $scorelist = array();
            foreach( $result as $key => $score ){
                $scorelist[$score->ipid] = new Score();
                $scorelist[$score->ipid]->load($score);         
            }
            return $scorelist;
        }

    }