<?php

class ScoreController extends Jaycms_Controller_Action
{

    public function init()
    {                                             
        $layout = $this->_helper->layout();
        $layout->setLayout('layout-score');
    }

    public function indexAction()
    {
         // Utils::getScore('92.48.201.160');
         Utils::locate_db('avaxo_sysman');
         $ipModel = new IpModel();
         $checkModel = new CheckdateModel();
         $scoreModel = new ScoreModel();
         
         $lastcheck = $checkModel->getLastCheckResult();
         $scorelist = $scoreModel->getLastScoreList($lastcheck->id);
         
         $this->view->iplist = $ipModel->fetchAll();
         $this->view->lastcheck = $lastcheck;
         $this->view->scorelist = $scorelist;
    }
    
    public function checkAction()
    {
        $is_first = $this->_getParam('first', null);
        $is_last = $this->_getParam('last', null);
        $ip = $this->_getParam('ip');
        $ipid = $this->_getParam('ipid');
        $old = $this->_getParam('old');  
        
        $scoreModel = new ScoreModel();
        $checkModel = new CheckdateModel();
        
        $lastcheck = $checkModel->getLastCheckResult();
        
        $score = new Score();
        $score->oldscore = $old;
        $score->score = Utils::getScore($ip);
        $score->ipid = $ipid;
        $score->dateid = $lastcheck->id;
        if ( !empty($is_first) ) {
            $check = new Checkdate();
            $check->check_date = date('Y-m-d H:i:s');
            $result['start_time'] = date('d/m/Y-H:i:s', strtotime($check->check_date));
            $check->save();
            $score->dateid = $check->id;
            $score->save();
        } else if ( !empty($is_last) ) {
            $score->save(); 
            $lastcheck->end_date = date('Y-m-d H:i:s');
            $result['end_time'] = date('d/m/Y-H:i:s', strtotime($lastcheck->end_date));
            $lastcheck->inc_count = $checkModel->getIncCount();
            $lastcheck->eq_count = $checkModel->getEqCount();
            $lastcheck->dec_count = $checkModel->getDecCount();
            $lastcheck->save();
        } else {
            $score->save(); 
        }
        $result['score'] = $score->score;                 
        $result['old'] = $score->oldscore;
        $result['dif'] = $score->score - $score->oldscore;        
        $this->_helper->json($result);  
    }                    

}

