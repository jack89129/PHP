<?php

    /**
     * @property int $id
     * @property int contact_id
     * @property int group_id
     *
     * @property Contact $contact
     * @property ContactGroup $group
     */
    class ContactGroupMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ContactGroupMapModel(), $id);
        }

        public function relations(){
            return array(
                'contact' => array('Contact', 'contact_id', self::HAS_ONE),
                'group' => array('ContactGroup', 'group_id', self::HAS_ONE)
            );
        }

    }