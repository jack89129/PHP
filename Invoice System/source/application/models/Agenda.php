<?php

    /**
     * @property int $id
     * @property string $street
     * @property string $addrnr
     * @property string $city
     * @property string $post
     * @property string $phone
     * @property string $cellphone
     * @property string $mail
     * @property string vat
     * @property string $specialInvoiceRequirement
     * @property int $adults
     * @property int $children
     * @property string $start_time
     * @property string $end_time
     * @property string $comment
     * @property int $created_user
     * @property int $status
     * @property int $cnt
     * @property string $location
     * @property int $reception_id
     * @property int $hapje_count
     * @property int $drink
     * @property int $hours
     * 
     * @property Contact $contact 
     * @property AgendaPartyType $partyType
     * @property Reception $reception
     */
    class Agenda extends Core_ActiveRecord_Row {
        
        private static $AGENDA_STATUS_TEXT = array(0 => 'deleted',
                                                   1 => 'confirm',
                                                   2 => 'optional');
        private static $AGENDA_DRINK_TEXT = array(0 => 'Exclusief dranken',
                                                  1 => 'Drankenformule');

        public function __construct($id=null){
            return parent::__construct(new AgendaModel(), $id);
        }
        
        public function relations(){
            return array(
                        'contact' =>array('Contact', 'contact_id', self::HAS_ONE),  
                        'partyType' =>array('AgendaPartyType', 'party_type', self::HAS_ONE), 
                        'reception' =>array('Reception', 'reception_id', self::HAS_ONE), 
                    );
        }
    }