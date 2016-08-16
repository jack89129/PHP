<?php
class EmpSettingModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'emp_settings'; 
    
    public function getSetting($empNo, $settingKey) {
        $result = $this->select()->where('emp_id = ?', $empNo)->where('setting_name = ?', $settingKey)->query(Zend_Db::FETCH_OBJ)->fetchAll();
        if ( empty($result) ) {
            return null;
        }
        return $result[0];
    }
    
    public function getValue($empNo, $settingKey) {
        $result = $this->getSetting($empNo, $settingKey);
        if ( empty($result) ) {
            return null;
        }
        return $result->value;
    }
    
    public function setSetting($empNo, $settingKey, $value) {
        $setting = $this->getSetting($empNo, $settingKey);
        if ( empty($setting) ) {
            $data = array(
                'emp_id'       => $empNo,
                'setting_name' => $settingKey,
                'value'        => $value
            );
            $this->insert($data);
        } else {
            $data = array(
                'value' => $value
            );

            $n = $this->update($data, "emp_id = '$empNo' and setting_name = '$settingKey'");
        }
    }
}

