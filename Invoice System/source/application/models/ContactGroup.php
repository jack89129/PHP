<?php

    /**
     * @property int $id
     * @property string $name
     *
     * @property Contact[] $contacts
     */
    class ContactGroup extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ContactGroupModel(), $id);
        }

        public function relations(){
            return array(
                'contacts' => array('Contact', array('ContactGroupMap' => array('group_id', 'contact_id')), self::HAS_MANY_TO_MANY, array(), array('firstname ASC'))
            );
        }

        public static function all($where=array(), $sort=array('natsort(name, "natural") ASC')){
            $contactGroup = new ContactGroup();
            return $contactGroup->findAll($where, $sort);
        }

    }