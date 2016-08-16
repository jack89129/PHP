<?php

    class Log_LogController extends Jaycms_Controller_Action {

        public function logAction(){
            $source_type = $this->_getParam('source_type');
            $source_id = $this->_getParam('source_id');

            if( !Utils::user()->can('log_view') ){
                $result = array();
                $result['log'] = '';
                $this->_helper->json($result);
                return;
            }

            $log = new Log();
            $logs = $log->findAll(array(array('source_type = ?', $source_type), array('source_id = ?', $source_id)), array('id DESC'));

            $result = array();
            $result['log'] = $this->view->partial('log/log.phtml', array('logs' => $logs));
            $this->_helper->json($result);
        }

        public function addAction(){
            $source_type = $this->_getParam('source_type');
            $source_id = $this->_getParam('source_id');
            $logParam = $this->_getParam('log');

            if( !Utils::user()->can('log_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            if( empty($logParam['data']) ){
                throw new Exception(_t("Enter log information!"));
            }

            $log = new Log(!empty($logParam['id']) ? $logParam['id'] : null);
            if( $log->exists() && $log->employee_id != Utils::user()->id ){
                throw new Exception(_t("Log not yours!"));
            }
            $log->source_type = $source_type;
            $log->source_id = $source_id;
            $log->data = $logParam['data'];
            $log->event = LogModel::EVENT_MANUAL;
            $log->save();

            $this->_helper->json(array('success' => 1));
        }

        public function editAction(){
            $id = (int) $this->_getParam('id', 0);

            $log = new Log($id);

            if( !$log->exists() ){
                throw new Exception(_t('Log not found!'));
            }

            if( $log->employee_id != Utils::user()->id ){
                throw new Exception(_t("Log not yours!"));
            }

            $result = array();
            $result['log_add'] = $this->view->partial('log/add.phtml', array('id' => $log->id, 'data' => $log->data));
            $this->_helper->json($result);
        }

        public function deleteAction(){
            $id = (int) $this->_getParam('id', 0);

            $log = new Log($id);

            if( !$log->exists() ){
                throw new Exception(_t('Log not found!'));
            }

            if( $log->employee_id != Utils::user()->id ){
                throw new Exception(_t("Log not yours!"));
            }

            $log->delete();
            $this->_helper->json(array());
        }
    }