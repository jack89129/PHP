<?php

class Agenda_IndexController extends Jaycms_Controller_Action
{                                            

 	public function init()
    {
        parent::init();                      
        
        $this->view->page_title = _t('Agenda');
        $this->view->page_sub_title = _t("Overzicht, voorraden en meer...");
        $this->view->current_module = "agenda";
        
        /*$agenda = new Zend_Session_Namespace('agenda');          
        if ( empty($agenda->calc_year) ) {
            $agenda->calc_year = date('Y');
            $agenda->calc_month = date('m');
        }*/
        $calc_year = $this->_getParam('calc_year', date('Y'));
        $calc_month = $this->_getParam('calc_month', date('m'));       
        $this->view->calc_year = $calc_year;
        $this->view->calc_month = $calc_month;
        
        $agendaModel = new AgendaModel();
        $data = $agendaModel->getAgendaByMonth($this->view->calc_year, $this->view->calc_month);
        $redDays = array();
        $greenDays = array();
        $orangeDays = array();
        foreach ( $data as $agenda ) {
            $day = date('j', strtotime($agenda['reserved_date']));
            if ( $agenda['green'] + $agenda['orange'] == 10 ) {
                $redDays[] = $day;
            } else if ( $agenda['orange'] > 0 ) {
                $orangeDays[] = $day;
            } else {
                $greenDays[] = $day;
            }
        }                  
        $this->view->redDays = $redDays;
        $this->view->orangeDays = $orangeDays;
        $this->view->greenDays = $greenDays;
    }                   
    
    public function indexAction(){         
        Utils::activity('index', 'agenda');         
    }                  
    
    public function dayAction(){         
        Utils::activity('day', 'agenda');        
    }                  
    
    public function weekAction(){         
        Utils::activity('week', 'agenda');        
    }                  
    
	public function preDispatch() {
        $results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
        
        if ($results) {
            $products = array();
            $minstock_products = new Zend_Session_Namespace('min_stock_products');
            if (!$minstock_products->id_list) $minstock_products->id_list = array();
            
            foreach ($results as $product) {
                if (in_array($product->id, $minstock_products->id_list)) continue;
                $products[] = $product;
            }
            
            if ($products) $this->view->show_box = true;
            
            $this->view->minstock_products = $results;
        }
    }
    
    public function loadLocationsAction() {
        $selectedDate = $this->_getParam('selected_date');
        
        $locationModel = new AgendaLocationModel();
        $agendaModel = new AgendaModel();
        
        $agendaList = $agendaModel->getAgendaByDate($selectedDate);
        $locations = $locationModel->fetchAll();
        
        $locArray = array();
        foreach ( $locations as $loc ) {
            $locArray[$loc->id] = $loc->name;
        }            
        
        $result = array();
        $key = array();
        foreach ( $agendaList as $agenda ) {
            $data = array();
            $data['id'] = $agenda->id;
            $data['name'] = $agenda->contact->company_name;
            $data['status'] = $agenda->status;
            $data['num'] = $agenda->adults + $agenda->children;
            $ids = explode(",", substr($agenda->location, 0, -1));
            $data['location'] = array();
            foreach ( $ids as $id ) {
                $data['location'][] = $locArray[$id];
            }
            $key = array_merge($key, $ids);
            $result[] = $data;
        }
        foreach ( $locations as $loc ) {
            if ( !in_array($loc->id, $key) ) {
                $data = array();
                $data['id'] = -1;
                $data['name'] = "";
                $data['status'] = 0;
                $data['num'] = 0;
                $data['location'] = array($loc->name);
                $result[] = $data;
            }
        }                    
        $this->_helper->json($result);
    }
    
    public function detailAction() {
        $agenda_date = $this->_getParam('agenda_date');
        $id = $this->_getParam('agenda_id');
        
        if ( !empty($id) ) {
            Utils::activity('view', 'agenda', $id);
        } else {
            Utils::activity('new', 'agenda');
        }
        
        $agendaModel = new AgendaModel();
        $locationModel = new AgendaLocationModel();
        $partyTypeModel = new AgendaPartyTypeModel();
        $receptionModel = new ReceptionModel();
        $hapjeModel = new AgendaHapjeModel();       
        $typeModel = new MenuTypeModel();
        $menuModel = new MenuProductModel();
        
        $agendaList = $agendaModel->getAgendaByDate($agenda_date);
        $locations = $locationModel->fetchAll();                           
        $receptions = $receptionModel->fetchAll();
        
        $key = array();
        foreach ( $agendaList as $agenda ) {
            $ids = explode(",", substr($agenda->location, 0, -1));
            $key = array_merge($key, $ids);
        }
        $agenda = new Agenda($id);
        $selectedLocations = array();
        $start_hour = 0;
        $start_minute = 0;
        $end_hour = 0;
        $end_minute = 0;
        $hapjes = array();
        $agendaMenus = array();
        if ( !empty($id) ) {
            $selectedLocations = explode(",", substr($agenda->location, 0, -1));
            $start_hour = date('H', strtotime($agenda->start_time));
            $start_minute = date('i', strtotime($agenda->start_time));
            $end_hour = date('H', strtotime($agenda->end_time));
            $end_minute = date('i', strtotime($agenda->end_time));
            $hapjes = $hapjeModel->getHapjes($id);
            $agendaMenus = $menuModel->getAgendaMenus($id);
        } else {
            $hapjes[] = array('id'=>0, 'hapje'=>"");                                                                                       
            $agendaMenus = $typeModel->fetchAll();
        }
        $locArray = array();
        $selectedLocArray = array();
        foreach ( $locations as $k => $loc ) {
            if ( !in_array($loc->id, $key) ) {
                $locArray[$k] = $loc;
            }
            if ( in_array($loc->id, $selectedLocations) ) {
                $selectedLocArray[$k] = $loc;
            }
        }                                          
         
        $this->view->reserve = $agenda;
        $this->view->receptions = $receptions;
        $this->view->hapjes = $hapjes;
        $this->view->start_hour = $start_hour;
        $this->view->start_minute = $start_minute;
        $this->view->end_hour = $end_hour;
        $this->view->end_minute = $end_minute;
        $this->view->selected_date = $agenda_date;
        $this->view->locations = $locArray;
        $this->view->selected_locations = $selectedLocArray;
        $this->view->party_type_list = $partyTypeModel->fetchAll();
        $this->view->agenda_menus = $agendaMenus;
    }
    
    public function saveAction() {
        $id = $this->_getParam('idx');
        $reserve = $this->_getParam('reserve');
        $locations = $this->_getParam('location');
        $contactParam = $this->_getParam('contact');
        $hapjeId = $this->_getParam('hapje_id');  
        $hapje = $this->_getParam('hapje');
        $menu_opt = $this->_getParam('menu_opt');
        $menu_select = $this->_getParam('menu_select');
        $menu_pid = $this->_getParam('menu_pid');
        $menu_amount = $this->_getParam('menu_amount');
        $menu_pname = $this->_getParam('menu_pname');
        
        $hapjeModel = new AgendaHapjeModel();       
        $agendaMenuModel = new AgendaMenuModel();
        $menuModel = new MenuProductModel();
         
        $hapjeModel->removeHapjes($id);
        $agendaMenuModel->removeMenus($id);
        
        $location = "";
        foreach ( $locations as $key => $loc ) {
            $location .= $key . ',';
        }                         
        $agenda = new Agenda($id);
        $agenda->load($reserve);      
        if( $reserve['contact_id'] == 0 ){
            $contact = new Contact();
            $contact->load($contactParam);                                          
            $contact->save();
            $agenda->contact_id = $contact->id;
        }    
        $agenda->id = $id;
        $agenda->location = $location;
        $agenda->cnt = count($locations);
        $agenda->save();
        Utils::activity('save', 'agenda', $agenda->id);
        for ( $i=0; $i<$agenda->hapje_count; $i++ ) {
            if ( $hapjeId[$i+1] == 0 ) {
                $h = new Hapje();
                $h->value = $hapje[$i+1];
                $h->save();
                $hapjeId[$i+1] = $h->id;
            }
            $ah = new AgendaHapje();
            $ah->hapje_id = $hapjeId[$i+1];
            $ah->agenda_id = $agenda->id;
            $ah->save();
        }     
        foreach ( $menu_opt as $tid ) {
            foreach ( $menu_pid[$tid] as $idx => $pid ) {
                if ( $pid == 0 ) {
                    $prod = new MenuProduct();
                    $prod->amount = $menu_amount[$tid][$idx];
                    $prod->name = $menu_pname[$tid][$idx];
                    $prod->save();
                    $pid = $prod->id;
                }
                $menu = new AgendaMenu();
                $menu->agenda_id = $agenda->id;
                $menu->type_id = $tid;
                $menu->menu_id = $pid;
                $menu->buffet = $menu_select[$tid][$idx];
                $menu->save();
            }
        }
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_AGENDA;
        $log->data = $agenda->created_user;
        $log->source_id = $agenda->id;
        if ( $agenda->status == 1 ) {
            $log->event = LogModel::EVENT_AGENDA_CONFIRM;
        } else if ( $agenda->status == 2 ) {
            $log->event = LogModel::EVENT_AGENDA_OPTIONAL;
        } else {
            $log->event = LogModel::EVENT_AGENDA_DELETED;
        }
        $log->save();
        
/* *********** Mail Sending part *****************
        Dear (customer_name),

Thank you for your interest in our company, we made an optional reservation for you on (date) at (hour) for a (party_type).

We expect u to be here at (starting_time), with (nr_adults) & (nr_kids)

If something is not right, please contact us!

Regards
Jos
*****************/
        $this->_redirect("/agenda");   
    }
    
    public function menuAutocompleteAction(){
        $menuModel = new MenuProductModel();
        $menus = $menuModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
        $this->_helper->json($menus);
    }
    
    public function hapjeAutocompleteAction(){
        $hapjeModel = new HapjeModel();
        $hapjes = $hapjeModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
        $this->_helper->json($hapjes);
    }
    
    public function sendEmailFillAction(){
        $email = "";
        $title = "Reservation";
        $body = "Dear (customer_name),<br>
                Thank you for your interest in our company, we made an optional reservation for you on (date) at (hour) for a (party_type).<br>
                We expect u to be here at (starting_time), with (nr_adults) & (nr_kids)

If something is not right, please contact us!

Regards
Jos";
    }
    
    public function emailAction() {
        $email = $this->_getParam('agenda_email');
        $subject = $this->_getParam('agenda_subject');
        $body = $this->_getParam('agenda_body');
        
        $validator = new Zend_Validate_EmailAddress();

        if( !$validator->isValid($email) ){
            throw new Exception(_t('Invalid email address!'));
        }
        /*
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_INVOICE;
        $log->source_id = $invoice->id;
        $log->data = $email;
        $log->event = LogModel::EVENT_INVOICE_SENT_EMAIL;
        $log->save(); */
        
        $subject = str_replace('<br>', '', $subject);
        
        // email 
        $mail = Mail::factory();
        $mail->setSubject($subject);
        $mail->addTo($email);
        $mail->setBodyHtml($body);                    

        $mail->send();
        
        $this->_helper->json(array('success' => '1'));
    }
    
    public function printAction(){
        $id = $this->_getParam('idx');
        $reserve = $this->_getParam('reserve');
        $locations = $this->_getParam('location');
        $contactParam = $this->_getParam('contact');
        $hapjeId = $this->_getParam('hapje_id');  
        $hapje = $this->_getParam('hapje');
        $menu_opt = $this->_getParam('menu_opt');
        $menu_select = $this->_getParam('menu_select');
        $menu_pid = $this->_getParam('menu_pid');
        $menu_amount = $this->_getParam('menu_amount');
        $menu_pname = $this->_getParam('menu_pname');
        
        $hapjeModel = new AgendaHapjeModel();       
        $agendaMenuModel = new AgendaMenuModel();
        $menuModel = new MenuProductModel();
         
        $hapjeModel->removeHapjes($id);
        $agendaMenuModel->removeMenus($id);
        
        $location = "";
        $locationText = "";
        foreach ( $locations as $key => $loc ) {
            $location .= $key . ',';
            $locationText .= $loc . ',';
        }                         
        $agenda = new Agenda($id);
        $agenda->load($reserve);      
        if( $reserve['contact_id'] == 0 ){
            $contact = new Contact();
            $contact->load($contactParam);                                          
            $contact->save();
            $agenda->contact_id = $contact->id;
        }    
        $agenda->id = $id;
        $agenda->location = $location;
        $agenda->cnt = count($locations);
        $agenda->save();
        Utils::activity('save', 'agenda', $agenda->id);
        for ( $i=0; $i<$agenda->hapje_count; $i++ ) {
            if ( $hapjeId[$i+1] == 0 ) {
                $h = new Hapje();
                $h->value = $hapje[$i+1];
                $h->save();
                $hapjeId[$i+1] = $h->id;
            }
            $ah = new AgendaHapje();
            $ah->hapje_id = $hapjeId[$i+1];
            $ah->agenda_id = $agenda->id;
            $ah->save();
        }     
        foreach ( $menu_opt as $tid ) {
            foreach ( $menu_pid[$tid] as $idx => $pid ) {
                if ( $pid == 0 ) {
                    $prod = new MenuProduct();
                    $prod->amount = $menu_amount[$tid][$idx];
                    $prod->name = $menu_pname[$tid][$idx];
                    $prod->save();
                    $pid = $prod->id;
                }
                $menu = new AgendaMenu();
                $menu->agenda_id = $agenda->id;
                $menu->type_id = $tid;
                $menu->menu_id = $pid;
                $menu->buffet = $menu_select[$tid][$idx];
                $menu->save();
            }
        }
        $log = new Log();
        $log->source_type = LogModel::SOURCE_TYPE_AGENDA;
        $log->source_id = $agenda->id;
        $log->data = $agenda->created_user;            
        $log->event = LogModel::EVENT_AGENDA_PRINT;  
        $log->save();
        $this->view->agenda = $agenda;
        $this->view->hapje = $hapje;
        $this->view->menu_opt = $menu_opt;
        $this->view->menu_pid = $menu_pid;
        $this->view->menu_select = $menu_select;
        $this->view->menu_pname = $menu_pname;
        $this->view->locationText = substr($locationText, 0, -1);
        $this->generatePDF('dagoverzicht-' . $agenda->id . '.pdf', 'D');
        die();                                                                                      
    }
    
    protected function generatePDF($name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();
                                 
        setlocale(LC_ALL, 'nl_NL');                                        
        $this->view->curday = strftime('%a %d %B', strtotime($this->view->agenda->reserved_date));
        $start_hour = date('H', strtotime($this->view->agenda->start_time));
        $start_minute = date('i', strtotime($this->view->agenda->start_time));
        $this->view->start_hour = $start_hour;
        $this->view->start_minute = $start_minute;
        
        $receptionModel = new ReceptionModel();
        $typeModel = new MenuTypeModel();
        $receptionList = $receptionModel->fetchAll();
        $typeList = $typeModel->fetchAll();
        
        $receptions = array();
        foreach ( $receptionList as $recep ) {
            $receptions[$recep->id] = $recep->name;
        }
        $this->view->receptions = $receptions;
        
        $types = array();
        foreach ( $typeList as $type ) {
            $types[$type->id] = $type->type;
        }
        $this->view->types = $types;

        $content = $this->view->render('index/pdf.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        //$mpdf->SetHTMLHeader($header);
        //$mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);                                              
        
        $mpdf->Output($name, $destination);
    }
}
