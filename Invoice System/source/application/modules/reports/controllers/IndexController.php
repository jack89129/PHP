<?php

    class Reports_IndexController extends Jaycms_Controller_Action {

        public static $PERIODS = array(
            'this_month'   => array('label' => 'Deze maand'     , 'compare' => 'last_month'     ),
            'last_month'   => array('label' => 'Vorige maand'   , 'compare' => 'this_month'     ),
            'this_quarter' => array('label' => 'Dit kwartaal'   , 'compare' => 'last_quarter'   ),
            'last_quarter' => array('label' => 'Vorig kwartaal' , 'compare' => 'this_quarter'   ),
            'this_year'    => array('label' => 'Dit jaar'       , 'compare' => 'last_year'      ),
            'last_year'    => array('label' => 'Vorig jaar'     , 'compare' => 'this_year'      )
        );

        public static $SHOW_TYPES = array(
            'month'     => array('label' => 'Per maand'     ),
            'quarter'   => array('label' => 'Per kwartaal'   ),
            'year'      => array('label' => 'Per jaar'      )
        );

        public function init(){
            parent::init();

            $this->view->page_title = _t("Rapporten");
            $this->view->page_sub_title = _t("Overzicht, rapporten en meer...");
            $this->view->current_module = "reports";
        }

        public function indexAction(){
            $period_name = $this->_getParam('period');
            $show_type_name = $this->_getParam('show_type');    

            if( !array_key_exists($period_name, self::$PERIODS) ){
                $period_name = key(self::$PERIODS);
            }

            if( !array_key_exists($show_type_name, self::$SHOW_TYPES) ){
                $show_type_name = key(self::$SHOW_TYPES);
            }

            $period     = self::$PERIODS[ $period_name ];
            $show_type  = self::$SHOW_TYPES[ $show_type_name ];
            $dates      = array();

            $resultDate     = Utils::name2date($period_name);

            $date = $resultDate[0];
            do{
                $offset = '';
                $offset = $show_type_name == 'month'    ? '+ 1 month'   : $offset;
                $offset = $show_type_name == 'quarter'  ? '+ 3 month'   : $offset;
                $offset = $show_type_name == 'year'     ? '+ 1 year'    : $offset;
                $dates[] = $date;
                $date = strtotime($offset, $date);
            }while( $date < $resultDate[1] );

            $types = array( TagCategoryModel::TYPE_INVOICE      ,
                            TagCategoryModel::TYPE_PURCHASE     );
            $result = array();
            $categoryModel = new TagCategoryModel();

            foreach( $types as $type ){
                $result[$type] = array();
                $categories = $categoryModel->getCategoriesByType($type);

                foreach( $categories as $category ){
                    $categoryResult = array();
                    $categoryResult['category'  ] = $category;
                    $categoryResult['sums'      ] = array();
                    $categoryResult['total'     ] = array();

                    foreach( $category->tags as $tag ){
                        $tagResult = array();
                        $tagResult['tag'] = $tag;
                        $tagResult['totals'] = array();
                        if( $type == TagCategoryModel::TYPE_INVOICE ){
                            $totals = Reports::getEarnings($resultDate[0], $resultDate[1], $tag, $show_type_name);
                        }

                        if( $type == TagCategoryModel::TYPE_PURCHASE ){
                            $totals = Reports::getExpenses($resultDate[0], $resultDate[1], $tag, $show_type_name);
                        }

                        foreach( $dates as $date ){
                            if( !isset($categoryResult['total'][$date]) ){
                                $categoryResult['total'][$date] = 0;
                            }

                            if( $totals ){
                                foreach( $totals as $key => $total ){
                                    $same = false;
                                    $same = $show_type_name == 'month'  && date('mY', $total['time']) == date('mY', $date)   ? true : $same;
                                    $same = $show_type_name == 'year'   && date('Y', $total['time']) == date('Y', $date)     ? true : $same;
                                    $same = $show_type_name == 'quarter'&& ceil(date('m', $total['time'])/3) == ceil(date('m', $date)/3) ? true : $same;


                                    if( $same ){
                                        $tagResult['totals'][] = $total['sum'];
                                        $categoryResult['total'][$date] += $total['sum'];
                                    }else{
                                        $tagResult['totals'][] = 0;
                                        $categoryResult['total'][$date] += 0;
                                    }
                                }
                            }else{
                                $tagResult['totals'] = array_fill(0, count($dates), 0);
                            }
                        }

                        $categoryResult['sums'][] = $tagResult;
                    }

                    $categoryResult['total'] = array_values($categoryResult['total']);

                    $result[$type][] = $categoryResult;
                }
            }

            if( $this->_getParam('export') ){
                $this->indexExport($result, $dates, $show_type_name);
            }

            $this->view->result = $result;
            $this->view->periods = self::$PERIODS;
            $this->view->period = $period_name;
            $this->view->show_types = self::$SHOW_TYPES;
            $this->view->show_type = $show_type_name;
            $this->view->dates = $dates;
        }

        private function indexExport($result, $dates, $show_type){
            $data = array();
            foreach( $result as $type => $sections ){
                foreach( $sections as $section ){
                    $row = array();
                    $row[] = $section['category']->name;
                    $style = array();
                    $style[] = '-btb';

                    foreach( $dates as $date ){
                        if( $show_type == 'quarter' ){
                            $row[] = 'Q' . ceil(date('m', $date)/3);
                        }

                        if( $show_type == 'year' ){
                            $row[] = date('Y', $date);
                        }

                        if( $show_type == 'month' ){
                            $row[] = date('M y', $date);
                        }
                        $style[] = '-bar';
                    }

                    $data[] = implode('|', $style);
                    $data[] = $row;

                    foreach($section['sums'] as $sums ){
                        $row = array();
                        $style = array();
                        $row[] = $sums['tag']->name;
                        $style[] = '';
                        foreach( $sums['totals'] as $total ){
                            $row[] = sprintf('%.2f', $total); $style[] = 'ff';
                        }
                        $data[] = implode("|", $style);
                        $data[] = $row;
                    }


                    $style = array();
                    $style2 = array();
                    $row = array();
                    $row[] = 'Totaal ' . strtolower($section['category']->name);
                    $style[] = 'tb';
                    $style2[] = '-b';
                    foreach($section['total'] as $total ){
                        $row[] = sprintf('%.2f', $total);
                        $style[] = 'tbff';
                        $style2[] = '-b';
                    }

                    $data[] = implode('|', $style2);
                    $data[] = array();
                    $data[] = implode('|', $style);
                    $data[] = $row;


                    $data[] = array();
                    $data[] = array();
                }
            }

            $excel = $this->excelCreate($data);  
            $this->excelOutput($excel, 'resultaten', $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
        }

        public function tagAction(){
            $period_name = $this->_getParam('period');
            $tagId = (int) $this->_getParam('tag', 0);

            $tag = new Tag($tagId);
            if( !$tag->exists() ){
                throw new Exception(_t("Tag not found!"));
            }

            if( !array_key_exists($period_name, self::$PERIODS) ){
                $period_name = key(self::$PERIODS);
            }

            $period = self::$PERIODS[ $period_name ];

            $result = array();
            $tags = $tag->findAll(array(), array('tag_category_id ASC'));
            foreach( $tags as $key => $val ){
                if( $val->category->type != TagCategoryModel::TYPE_INVOICE &&
                    $val->category->type != TagCategoryModel::TYPE_PURCHASE  ){
                    unset($tags[$key]);
                }
            }


            $periodDates = Utils::name2date($period_name);
            $result = array();
            $totals = array('debit' => 0, 'credit' => 0);
            $result = Reports::getTagTotals($periodDates[0], $periodDates[1], $tag);

            foreach( $result as $val ){
                $totals['debit'] += $val['debit'];
                $totals['credit'] += $val['credit'];
            }

            if( $this->_getParam('export') ){
                $this->tagExport($result, $totals, $tag->category->type == TagCategoryModel::TYPE_INVOICE, $tag->name);
            }


            $this->view->result = $result;
            $this->view->totals = $totals;
            $this->view->tags = $tags;
            $this->view->periods = self::$PERIODS;
            $this->view->period = $period_name;
            $this->view->tag = $tag;
            $this->view->invoice = $tag->category->type == TagCategoryModel::TYPE_INVOICE;
        }

        public function tagExport($result, $totals, $invoice, $tagname){
            $data = array();
            $row = array();                     $style = array();
            $row[] = _t('Datum');                   $style[] = '-btb';
            $row[] = _t('Referentie');              $style[] = '-btb';
            $row[] = _t('Contact & Geheugensteun');  $style[] = '-btb';
            $row[] = _t('Credit');                   $style[] = '-btbar';
            $row[] = _t('Debet');                  $style[] = '-btbar';
            $data[] = implode('|', $style);
            $data[] = $row;
            $data[] = array();

            foreach( $result as $res ){
                $row = array();
                $style = array();
                $row[] = date(Constants::DATE_FORMAT, $res['time']); $style[] = '';
                $row[] = ($invoice ? _t('Factuur') : _t('Inkoop')) . ' ' . $res['number']; $style[] = '';
                $row[] = $res['contact_name'] . ( $res['info'] ? ' - ' . $this->clearHTML($res['info']) : '' ); $style[] = '';
                $row[] = sprintf('%.2f', $res['debit']);  $style[] = 'ff';
                $row[] = sprintf('%.2f', $res['credit']); $style[] = 'ff';
                $data[] = implode('|', $style);
                $data[] = $row;
            }

            if( $result ){
                $style = array();
                $style[] = '-b';
                $style[] = '-b';
                $style[] = '-b';
                $style[] = '-b';
                $style[] = '-b';
                $data[] = implode('|', $style);
                $data[] = array();

                $style = array();
                $style[] = 'tb';
                $style[] = 'tb';
                $style[] = 'tb';
                $style[] = 'tbff';
                $style[] = 'tbff';
                $data[] = implode('|', $style);

                $row = array();
                $row[] = _t('Totaal');
                $row[] = '';
                $row[] = '';
                $row[] = sprintf('%.2f', $totals['debit']);
                $row[] = sprintf('%.2f', $totals['credit']);
                $data[] = $row;

//                $style = array();
//                $style[] = '';
//                $style[] = '';
//                $style[] = '';
//                $style[] = '-b';
//                $style[] = '';
//                $data[] = implode('|', $style);
//
//                $style = array();
//                $style[] = '';
//                $style[] = '';
//                $style[] = '';
//                $style[] = 'tbffac';
//                $style[] = '';
//                $data[] = implode('|', $style);
//
//                $row = array();
//                $row[] = '';
//                $row[] = '';
//                $row[] = '';
//                $row[] = sprintf('%.2f', $totals['debit'] - $totals['credit']);
//                $row[] = '';
//                $data[] = $row;
            }

            $excel = $this->excelCreate($data);
            //$this->excelOutput($excel, 'resultaten-categorie', $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
            $this->excelOutput($excel, 'Facturen-'.strtolower($tagname), $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
        }

        private function clearHTML($text){
            return trim(strip_tags(html_entity_decode($text)), ' ' . chr(160));
        }

        public function unpaidInvoicesTotalAction(){
            $period_name = $this->_getParam('period', 'this_month');

            if( $period_name != 'all' ){
                $periodDates = Utils::name2date($period_name);
            }else{
                $periodDates = array(0, strtotime('2030-12-21'));
            }

            $dateFrom = $periodDates[0];
            $dateTo = $periodDates[1];

            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

            $dates      = array();
            $date       = $dateFrom;

            $result = Reports::getContactsUnpaidInvoicesTotal($dateFrom, $dateTo);



            // clear zero periods
            if( $period_name == 'all' ){
                foreach( $result['total']['totals'] as $index => $total ){
                    if( !$total  && $index != '-'){
                        unset($result['total']['totals'][$index]);
                        foreach( $result['contacts'] as $key => $contact ){
                            unset($result['contacts'][$key]['totals'][$index]);
                        }
                    }
                }
            }

            do {

                // clear dates for zero periods
                if( $period_name == 'all' && !array_key_exists( $date, $result['total']['totals']) ){
                    // do nothing
                }else{
                    $dates[] = $date;
                }

                $date = strtotime('+ 1 MONTH', $date);
            }while($date < $dateTo);

            if( $this->_getParam('export') ){
                $this->unpaidTotalExport($result, $dates, 'debiteuren');
            }


            $this->view->dates = $dates;
            $this->view->period = $period_name;

            $this->view->result = $result;
            $this->view->result_dates = $dates;
        }

        public function unpaidTotalExport($result, $dates, $name){
            $data = array();

            $row = array();
            $style = array();
            $row[] = _t('Debiteur'); $style[] = '-btb';
            foreach( $dates as $date ){
                $row[] = date('M Y', $date); $style[] = '-btbar';
            }

            $row[] = _t('Ouder'); $style[] = '-btbar';
            $row[] = _t('Totaal'); $style[] = '-btbar';
            $data[] = implode('|', $style);
            $data[] = $row;
            $data[] = array();

            foreach( $result['contacts'] as $contact ){
                $row = array();
                $style = array();
                $row[] = $contact['contact']['name']; $style[] = '';

                foreach( $contact['totals'] as $total ){
                    $row[] = sprintf('%.2f', $total['sum']); $style[] = 'ff';
                }
                $row[] = sprintf('%.2f', $contact['total']);  $style[] = 'ff';
                $data[] = implode('|', $style);
                $data[] = $row;
            }

            if( $result['total'] ){
                $row = array();
                $style = array();
                $style2= array();
                $row[] = _t('Totaal'); $style[] = 'tb'; $style2[] = '-b';
                foreach( $result['total']['totals'] as $total ){
                    $row[] = sprintf('%.2f', $total); $style[] = 'tbff';  $style2[] = '-b';
                }
                $row[] = sprintf('%.2f', $result['total']['total']); $style[] = 'tbff'; $style2[] = '-b';
                $data[] = implode('|', $style2);
                $data[] = array();
                $data[] = implode('|', $style);
                $data[] = $row;
            }

            $excel = $this->excelCreate($data);
            $this->excelOutput($excel, $name, $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
        }

        public function unpaidListExport($result, $name){
            $data = array();
            $style = array();
            $row = array();
            $row[] = _t("Factuur");         $style[] = '-btb';
            $row[] = _t("Factuurdatum");    $style[] = '-btb';
            $row[] = _t("Crediteur");       $style[] = '-btb';
            $row[] = _t("Bedrag");          $style[] = '-btbar';
            $row[] = _t("Status");          $style[] = '-btbac';
            $data[] = implode('|', $style);
            $data[] = $row;
            $data[] = array();

            $total = 0;
            foreach( $result as $res ){
                $row = array();
                $style = array();
                $row[] = $res->number;                                      $style[] = '';
                $row[] = date(Constants::DATE_FORMAT, $res->invoice_time);  $style[] = '';
                $row[] = $res->contact->name;                               $style[] = '';
                $row[] = $res->unpaid_sum;                                  $style[] = 'arff';
                $row[] = $res->status_text;                                 $style[] = 'ac';
                $data[] = implode("|", $style);
                $data[] = $row;
                $total += $res->unpaid_sum;
            }

            $style = array_fill(0, 5, '-b');
            $data[] = implode('|', $style);
            $data[] = array();

            $row = array();
            $style = array();
            $row[] = 'Totaal';  $style[] = 'tb';
            $row[] = '';        $style[] = 'tb';
            $row[] = '';        $style[] = 'tb';
            $row[] = $total;    $style[] = 'tbarff';
            $row[] = '';        $style[] = 'tb';

            $data[] = implode('|', $style);
            $data[] = $row;

            $excel = $this->excelCreate($data);
            $this->excelOutput($excel, $name, $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
        }

        public function unpaidInvoicesListAction(){
            $period_name = $this->_getParam('period', 'this_month');
            $contactId = (int) $this->_getParam('contact_id', 0);
            $date = (int) $this->_getParam('date', 0);

            if( !is_numeric($period_name) ){
                if( $period_name != 'all' ){
                    $periodDates = Utils::name2date($period_name);
                }else{
                    $periodDates = array(0, strtotime('2030-12-21'));
                }
            }else{
                $periodDates = array(   mktime(0,0,0, date('m', $period_name), 1, date('Y', $period_name)),
                                        mktime(0,0,-1, date('m', $period_name) + 1, 1, date('Y', $period_name)));
                $date = $period_name;
            }

            $dateFrom = $periodDates[0];
            $dateTo = $periodDates[1];

            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

            $result = Reports::getUnpaidInvoices($dateFrom, $dateTo, $contactId);

            if( $this->_getParam('export') ){
                $this->unpaidListExport($result, 'debiteuren-list');
            }

            $contact = new Contact();
            $contacts = $contact->findAll(array(), array('firstname ASC'));

            $this->view->date = $date;
            $this->view->period = $period_name;
            $this->view->contacts = $contacts;
            $this->view->contact_id = $contactId;
            $this->view->result = $result;
        }

        public function unpaidPurchasesTotalAction(){
            $period_name = $this->_getParam('period', 'this_month');

            if( $period_name != 'all' ){
                $periodDates = Utils::name2date($period_name);
            }else{
                $periodDates = array(0, strtotime('2030-12-21'));
            }

            $dateFrom = $periodDates[0];
            $dateTo = $periodDates[1];

            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));


            $result = Reports::getContactsUnpaidPurchasesTotal($dateFrom, $dateTo);

            // clear zero periods
            if( $period_name == 'all' ){
                foreach( $result['total']['totals'] as $index => $total ){
                    if( !$total && $index != '-'){
                        unset($result['total']['totals'][$index]);
                        foreach( $result['contacts'] as $key => $contact ){
                            unset($result['contacts'][$key]['totals'][$index]);
                        }
                    }
                }
            }

            $dates      = array();
            $date       = $dateFrom;

            do {
                // clear dates for zero periods
                if( $period_name == 'all' && !array_key_exists( $date, $result['total']['totals']) ){
                    // do nothing
                }else{
                    $dates[] = $date;
                }

                $date = strtotime('+ 1 MONTH', $date);
            }while($date < $dateTo);


            if( $this->_getParam('export') ){
                $this->unpaidTotalExport($result, $dates, 'crediteuren');
            }


            $this->view->dates = $dates;
            $this->view->period = $period_name;

            $this->view->result = $result;
            $this->view->result_dates = $dates;
        }

        public function unpaidPurchasesListAction(){
            $period_name = $this->_getParam('period', 'this_month');
            $contactId = (int) $this->_getParam('contact_id', 0);
            $date = (int) $this->_getParam('date', 0);

            if( !is_numeric($period_name) ){
                if( $period_name != 'all' ){
                    $periodDates = Utils::name2date($period_name);
                }else{
                    $periodDates = array(0, strtotime('2030-12-21'));
                }
            }else{
                $periodDates = array(   mktime(0,0,0, date('m', $period_name), 1, date('Y', $period_name)),
                    mktime(0,0,-1, date('m', $period_name) + 1, 1, date('Y', $period_name)));
                $date = $period_name;
            }

            $dateFrom = $periodDates[0];
            $dateTo = $periodDates[1];

            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

            $result = Reports::getUnpaidPurchases($dateFrom, $dateTo, $contactId);

            if( $this->_getParam('export') ){
                $this->unpaidListExport($result, 'crediteuren-list');
            }

            $contact = new Wholesaler();
            $contacts = $contact->findAll(array(), array('company_name ASC'));

            $this->view->date = $date;
            $this->view->period = $period_name;
            $this->view->contacts = $contacts;
            $this->view->contact_id = $contactId;
            $this->view->result = $result;
        }

        public function vatAction(){
            $excel = $this->_getParam('excel');
            $period_name = $this->_getParam('period', 'this_quarter');
            $period_name = !array_key_exists($period_name, self::$PERIODS) ? 'this_year' : $period_name ;

            $types = array('overview' => _t('Overzicht'), 'government' => _t('Overheid'));
            $type_name = $this->_getParam('type');
            $type_name = !array_key_exists($type_name, $types) ? 'overview' : $type_name ;

            $periodDates = Utils::name2date($period_name);

            $result = array();

            if( $type_name == 'overview' ){
                $result['invoices'] = array('label' => _t('Verkoopfacturen'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                $result['invoices']['rows'] = Reports::vatInvoicesOverview($periodDates[0], $periodDates[1]);
                foreach( $result['invoices']['rows'] as $row ){
                    $result['invoices']['totals']['total_excl_vat'] += $row['total_excl_vat'];
                    $result['invoices']['totals']['vat_sum'] += $row['vat_sum'];
                }

                $result['purchases'] = array('label' => _t('Inkoopfacturen'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                $result['purchases']['rows'] = Reports::vatPurchasesOverview($periodDates[0], $periodDates[1]);
                foreach( $result['purchases']['rows'] as $row ){
                    $result['purchases']['totals']['total_excl_vat'] += $row['total_excl_vat'];
                    $result['purchases']['totals']['vat_sum'] += $row['vat_sum'];
                }

                if( $this->_getParam('export') ){
                    $this->vatOverviewExport($result);
                }
            }

            if( $type_name == 'government' ){
                $year = $this->_getParam('year', date('Y'));
                $month = $this->_getParam('month', date('m'));
                $now = strtotime($year.'-'.$month.'-01');
                $periodDates = Utils::name2date($period_name, $now);
                
                $governmentModel = new GovernmentItemModel();
                $data = array(); //$governmentModel->getGovernmentReport($year, $month);
                if ( count($data) > 0 ) {
                    $result['invoices'] = array('label' => _t('1. VERKOPEN'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['invoices']['rows'] = $governmentModel->getGovernmentReportByType($year, $month, 1);
                    
                    $result['purchases'] = array('label' => _t('2. AANKOPEN'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['purchases']['rows'] = $governmentModel->getGovernmentReportByType($year, $month, 2);
                    
                    $result['others'] = array('label' => _t('3. VERSCHULDIGDE BTW'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['others']['rows'] = $governmentModel->getGovernmentReportByType($year, $month, 3);
                    
                    $result['taxes'] = array('label' => _t('4. AFTREKBARE BTW'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['taxes']['rows'] = $governmentModel->getGovernmentReportByType($year, $month, 4);
                    
                    $result['result'] = array('label' => _t('5. EIND AFREKENING'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['result']['rows'] = $governmentModel->getGovernmentReportByType($year, $month, 5);
                } else {
                    $reportModel = new ReportModel();
                    $result['invoices'] = array('label' => _t('1. VERKOPEN'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['invoices']['rows'] = Reports::vatInvoicesGovernment($periodDates[0], $periodDates[1]);
                    
                    $vat55 = 0;
                    $vat56 = 0;
                    $vat54 = 0; $vat54Array = array("00", "01", "02", "03");
                    $vatXX = 0; $vatXXArray = array("81", "82", "83", "86", "87"); 
                    $vat63 = 0; $vat63Array = array("84", "85");
                    $vat64 = 0;
                    $vat59 = 0; $vat59Array = array("81", "82", "83", "84", "85");
                    foreach( $result['invoices']['rows'] as $row ){
                        $result['invoices']['totals']['total_excl_vat'] += $row['total_excl_vat'];
                        $result['invoices']['totals']['vat_sum'] += $row['vat_sum'];
                        $reportModel->saveReport($year.'-'.$month, $row['code'], $row['total_excl_vat']);
                        if ( in_array($row['code'], $vat54Array) ) {
                            $vat54 += $row['vat_sum'];
                        }
                        if ( $row['code'] == 49 ) {
                            $vat64 = $row['vat_sum'];
                        }
                    }
                    $itemModel = new GovernmentItemModel();

                    $result['purchases'] = array('label' => _t('2. AANKOPEN'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['purchases']['rows'] = Reports::vatPurchasesGovernment($periodDates[0], $periodDates[1]);
                    foreach( $result['purchases']['rows'] as $row ){
                        $result['purchases']['totals']['total_excl_vat'] += $row['total_excl_vat'];
                        $result['purchases']['totals']['vat_sum'] += $row['vat_sum'];
                        if ( $row['code'] == 86 ) {
                            $vat55 = $row['vat_sum'];
                        }
                        if ( $row['code'] == 87 ) {
                            $vat56 = $row['vat_sum'];
                        }
                        $reportModel->saveReport($year.'-'.$month, $row['code'], $row['total_excl_vat']);
                        if ( in_array($row['code'], $vatXXArray) ) {
                            $vatXX += $row['vat_sum'];
                        }
                        if ( in_array($row['code'], $vat63Array) ) {
                            $vat63 += $row['vat_sum'];
                        }
                        if ( in_array($row['code'], $vat59Array) ) {
                            $vat59 += $row['vat_sum'];
                        }
                    }
                    
                    $row = array();
                    $row['category'] = $itemModel->getItem('XX');
                    $row['code'] = 'XX';
                    $row['total_excl_vat'] = $vatXX;
                    $row['vat_sum'] = 0;
                    $result['purchases']['rows'][] = $row;
                    
                    $reportModel->saveReport($year.'-'.$month, 'XX', $vatXX);
                    $reportModel->saveReport($year.'-'.$month, '63', $vat63);
                    $reportModel->saveReport($year.'-'.$month, '59', $vat59);
                    $reportModel->saveReport($year.'-'.$month, '61', 0);
                    $reportModel->saveReport($year.'-'.$month, '62', 0);
                    $reportModel->saveReport($year.'-'.$month, '64', 0);
                    $reportModel->saveReport($year.'-'.$month, 'YY', $vat59);
                    
                    $result['others'] = array('label' => _t('3. VERSCHULDIGDE BTW'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $row = array();
                    $row['category'] = $itemModel->getItem('54');
                    $row['code'] = '54';
                    $row['total_excl_vat'] = $vat54;
                    $row['vat_sum'] = 0;
                    $result['others']['rows'] = array();
                    $result['others']['rows'][] = $row;
                    
                    $tmp = Reports::vatOthersGovernment($periodDates[0], $periodDates[1]);
                    $result['others']['rows'] = array_merge($result['others']['rows'], $tmp);
                    $reportModel->saveReport($year.'-'.$month, '54', $vat54);
                    
                    foreach( $result['others']['rows'] as $key => $row ){
                        $result['others']['totals']['total_excl_vat'] += $row['total_excl_vat'];
                        $result['others']['totals']['vat_sum'] += $row['vat_sum'];
                        if ( $row['code'] == 55 ) {
                            $row['total_excl_vat'] = $vat55;
                            $result['others']['rows'][$key] = $row;
                        }
                        if ( $row['code'] == 56 ) {
                            $row['total_excl_vat'] = $vat56;
                            $result['others']['rows'][$key] = $row;
                        }
                        $reportModel->saveReport($year.'-'.$month, $row['code'], $row['total_excl_vat']);
                        if ( in_array($row['code'], $vat59Array) ) {
                            $vat59 += $row['vat_sum'];
                        }
                    }
                    $row = array();
                    $row['category'] = $itemModel->getItem('61');
                    $row['code'] = '61';
                    $row['total_excl_vat'] = 0;
                    $row['vat_sum'] = 0;
                    $result['others']['rows'][] = $row;
                    $row = array();
                    $row['category'] = $itemModel->getItem('63');
                    $row['code'] = '63';
                    $row['total_excl_vat'] = $vat63;
                    $row['vat_sum'] = 0;
                    $result['others']['rows'][] = $row;
                    
                    $end_result = $vat54 + $vat55 + $vat56 + $vat63 - $vat59 - $vat64;
                    if ( $end_result > 0 ) {
                        $reportModel->saveReport($year.'-'.$month, '71', $end_result);
                        $reportModel->saveReport($year.'-'.$month, '72', 0);
                    } else {
                        $reportModel->saveReport($year.'-'.$month, '71', 0);
                        $reportModel->saveReport($year.'-'.$month, '72', $end_result * -1);
                    }
                    
                    $result['taxes'] = array('label' => _t('4. AFTREKBARE BTW'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['taxes']['rows'] = array();
                    $row = array();
                    $row['category'] = $itemModel->getItem('59');
                    $row['code'] = '59';
                    $row['total_excl_vat'] = $vat59;
                    $row['vat_sum'] = 0;
                    $result['taxes']['rows'][] = $row;
                    $row = array();
                    $row['category'] = $itemModel->getItem('62');
                    $row['code'] = '62';
                    $row['total_excl_vat'] = 0;
                    $row['vat_sum'] = 0;
                    $result['taxes']['rows'][] = $row;
                    $row = array();
                    $row['category'] = $itemModel->getItem('64');
                    $row['code'] = '64';
                    $row['total_excl_vat'] = $vat64;
                    $row['vat_sum'] = 0;
                    $result['taxes']['rows'][] = $row;
                    /*$row['category'] = $itemModel->getItem('66');
                    $row['code'] = '66';
                    $row['total_excl_vat'] = 0;
                    $row['vat_sum'] = 0;
                    $result['taxes']['rows'][] = $row;*/
                    $row = array();
                    $row['category'] = $itemModel->getItem('YY');
                    $row['code'] = 'YY';
                    $row['total_excl_vat'] = $vat59 + $vat64;
                    $row['vat_sum'] = 0;
                    $result['taxes']['rows'][] = $row;
                    
                    $result['result'] = array('label' => _t('5. EIND AFREKENING'), 'rows' => array(), 'totals' => array('total_excl_vat' => 0, 'vat_sum' => 0));
                    $result['result']['rows'] = array();
                    if ( $end_result > 0 ) {
                        $row = array();
                        $row['category'] = $itemModel->getItem('71');
                        $row['code'] = '71';
                        $row['total_excl_vat'] = $end_result;
                        $row['vat_sum'] = 0;
                        $result['result']['rows'][] = $row;
                        $row = array();
                        $row['category'] = $itemModel->getItem('72');
                        $row['code'] = '72';
                        $row['total_excl_vat'] = 0;
                        $row['vat_sum'] = 0;
                        $result['result']['rows'][] = $row;
                    } else {
                        $row = array();
                        $row['category'] = $itemModel->getItem('71');
                        $row['code'] = '71';
                        $row['total_excl_vat'] = 0;
                        $row['vat_sum'] = 0;
                        $result['result']['rows'][] = $row;
                        $row = array();
                        $row['category'] = $itemModel->getItem('72');
                        $row['code'] = '72';
                        $row['total_excl_vat'] = $end_result * -1;
                        $row['vat_sum'] = 0;
                        $result['result']['rows'][] = $row;
                    }
                }
                if( in_array($this->_getParam('export'), array('pdf', 'excel', 'check')) ){
                    if ( $this->_getParam('export')=='check' ) {
                        $this->vatGovernmentCheckExport($result, $year, $month);
                    } else {
                        $this->vatGovernmentExport($result, $year, $month);
                    }
                }

                if( $this->_getParam('export') == 'government' ){
                    $this->vatGovernmentToExcel($periodDates[0], $periodDates[1], $result);
                }
                $this->view->year = $year;
                $this->view->month = $month;
            }

            $this->view->periods = self::$PERIODS;
            $this->view->period = $period_name;
            $this->view->types = $types;
            $this->view->type = $type_name;
            $this->view->result = $result;
        }
        
        public function saveGovernmentAction(){
            $year = $this->_getParam('year');
            $month = $this->_getParam('month');
            $code = $this->_getParam('code');
            $value = $this->_getParam('value');
            
            $month = strlen($month)==1 ? "0".$month : $month;
            $report = new Report();
            $report->yearmonth = $year . '-' . $month;
            $report->code = $code;
            $report->total = $value;
            $diff = $report->create();
            
            //$this->processGovernmentReport($report->yearmonth, $code, $diff);
            
            $governmentModel = new GovernmentItemModel();
            $result = $governmentModel->getGovernmentReport($year, $month);
            //die();
            $this->_helper->json($result);
        }
        
        public function processGovernmentReport($year_month, $code, $diff){
            $reportModel = new ReportModel();
            switch ( $code ) {
                case '00': 
                    break;
                case '01': 
                    $diff *= 0.06;
                    $reportModel->updateReport($year_month, '54', $diff);
                    break;
                case '02': 
                    $diff *= 0.12;
                    $reportModel->updateReport($year_month, '54', $diff);
                    break;
                case '03':
                    $diff *= 0.21;
                    $reportModel->updateReport($year_month, '54', $diff);
                    break;
                case '84':
                case '85':
                    $diff *= 0.21;
                    $reportModel->updateReport($year_month, '63', $diff);
                    $reportModel->updateReport($year_month, 'XX', $diff);
                    break;
                case '81':
                case '82':
                case '83':
                case '86':
                case '87':
                    $diff *= 0.21;
                    $reportModel->updateReport($year_month, '59', $diff);
                    $reportModel->updateReport($year_month, 'XX', $diff);
                    $reportModel->updateReport($year_month, 'YY', $diff);
                    break;
                case '55':
                case '56':
                    break;
                case '61':
                    break;
                case '62':
                    $diff *= 0.21;
                    //$reportModel->updateReport($year_month, '62v', $diff);
                    $reportModel->updateReport($year_month, 'YY', $diff);
                    break;
                case '59':
                case '62v':
                case '64':
                    $reportModel->updateReport($year_month, 'YY', $diff);
                    break;
            }
            
            $obj71 = $reportModel->getReport($year_month, '71');
            $obj72 = $reportModel->getReport($year_month, '72');
            $lastResult = $obj71->total - $obj72->total;
            $positiveArray = array('00', '01', '02', '03', '54', '55', '56', '61', '63', '84', '85');
            $negativeArray = array('55', '56', '81', '82', '83', '86', '87', '59', '62v', '64', 'YY');
            if ( in_array($code, $positiveArray) ) {
                $lastResult += $diff;
            }
            if ( in_array($code, $negativeArray) ) {
                $lastResult -= $diff;
            }
            if ( $lastResult > 0 ) {
                $reportModel->saveReport($year_month, '71', $lastResult);
                $reportModel->saveReport($year_month, '72', 0);
            } else {
                $reportModel->saveReport($year_month, '71', 0);
                $reportModel->saveReport($year_month, '72', $lastResult * -1);
            }
        }

        public function vatOverviewExport($result){
            $data = array();

            foreach( $result as $section ){
                $data[] = array();
                $row = array();
                $row[] = $section['label'];
                $data[] = implode('|', array('tbh1'));
                $data[] = $row;
                $data[] = array();

                $row = array();
                $style = array();
                $row[] = _t('BTW tarief');      $style[] = '-b';
                $row[] = _t('Percentage');      $style[] = '-bar';
                $row[] = _t('Totaal bedrag');   $style[] = '-bar';
                $row[] = _t('Totaal BTW');      $style[] = '-bar';
                $data[] = implode('|', $style);
                $data[] = $row;
                $data[] = array();

                foreach( $section['rows'] as $res ){
                    $row = array();
                    $style = array();
                    $row[] = $res['tag'];               $style[] = '';
                    $row[] = $res['vat'] . '%';         $style[] = 'ar';
                    $row[] = $res['total_excl_vat'];    $style[] = 'arff';
                    $row[] = $res['vat_sum'];           $style[] = 'arff';
                    $data[] = implode('|', $style);
                    $data[] = $row;
                }

                $data[] = implode('|', array_fill(0, 4, '-b'));
                $data[] = array();

                $row = array();
                $style = array();
                $row[] = _t('Totaal');                          $style[] = 'tb';
                $row[] = '';                                    $style[] = '';
                $row[] = $section['totals']['total_excl_vat'];  $style[] = 'tbarff';
                $row[] = $section['totals']['vat_sum'];         $style[] = 'tbarff';
                $data[] = implode('|', $style);
                $data[] = $row;
                $data[] = array();
            }

            $excel = $this->excelCreate($data);
            $this->excelOutput($excel, 'BTW-Overzicht', $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');
        }
        
        public function vatGovernmentCheckExport($result, $year, $month){
            require_once('MPDF/mpdf.php');
            $this->_helper->layout()->disableLayout();
            
            setlocale(LC_ALL, 'nl_NL');
            $this->view->title = 'btwag'.$month.$year;
            $this->view->report_date = strftime('%B %Y', strtotime($year.'-'.$month.'-01'));
            
            $values = array();
            
            foreach( $result as $type ) {
                foreach( $type['rows'] as $idx => $row ) {
                    $values[$row['code']] = $row;
                }
            }
            
            $this->view->result = $values;
            
            $content = $this->view->render('index/check.phtml');
            
            $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
            $mpdf->WriteHTML($content);        
            $mpdf->Output('check'.$month.$year, 'D');
            
            die();
        }

        public function vatGovernmentExport($result, $year, $month){
            require_once('MPDF/mpdf.php');
            $this->_helper->layout()->disableLayout();
            
            setlocale(LC_ALL, 'nl_NL');
            $this->view->title = 'btwag'.$month.$year;
            $this->view->report_date = strftime('%B %Y', strtotime($year.'-'.$month.'-01'));
            $this->view->result = $result;
            
            $content = $this->view->render('index/pdf.phtml');
                        
            $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
            $mpdf->WriteHTML($content);    
            $mpdf->WriteHTML('<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>');
            $month = strlen($month)==1 ? "0".$month : $month;    
            
            $values = array();
            
            foreach( $result as $type ) {
                foreach( $type['rows'] as $idx => $row ) {
                    $values[$row['code']] = $row;
                }
            }
            
            $this->view->result = $values;
            
            $content = $this->view->render('index/check.phtml');
            
            //$mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
            $mpdf->WriteHTML($content);        
            //$mpdf->Output('check'.$month.$year, 'D');
            
            $mpdf->Output('btwag'.$month.$year, 'D');
            
            die();
            /*$data = array();

            foreach( $result as $section ){
                if ( strpos($section['label'], 'RESULT') === false) {
                    $data[] = array();
                    $row = array();
                    $row[] = $section['label'];
                    $data[] = implode('|', array('tbh1'));
                    $data[] = $row;
                    $data[] = array();

                    $row = array();
                    $style = array();
                    $row[] = '';      $style[] = '-b';
                    $row[] = '';      $style[] = '-bar';
                    $row[] = _t('Totaal bedrag');   $style[] = '-bar';
                    //$row[] = _t('Totaal BTW');      $style[] = '-bar';
                    $data[] = implode('|', $style);
                    $data[] = $row;
                    $data[] = array();

                    foreach( $section['rows'] as $res ){
                        $row = array();
                        $style = array();
                        $row[] = $res['category'];                          $style[] = '';
                        $row[] = '';                          $style[] = 'arff';
                        $row[] = sprintf('%.2f', $res['total_excl_vat']);   $style[] = 'arff';
                        $data[] = implode('|', $style);
                        $data[] = $row;
                    }
                } else {
                    $data[] = array();
                    $row = array();
                    $row[] = $section['label'];
                    $data[] = implode('|', array('tbh1'));
                    $data[] = $row;
                    $data[] = array();

                    $row = array();
                    $style = array();
                    $row[] = 'ENDRESULT';      $style[] = '-bt';
                    $row[] = "TO GOVENMENT\nPAYABLE\nAMOUNT";      $style[] = '-btbar';
                    $row[] = "AMOUNT\nOWED BY\nGOVERNMENT";      $style[] = '-btbar';
                    //$row[] = _t('Totaal bedrag');   $style[] = '-bar';
                    //$row[] = _t('Totaal BTW');      $style[] = '-bar';
                    $data[] = implode('|', $style);
                    $data[] = $row;
                    $data[] = array();

                    foreach( $section['rows'] as $res ){
                        $row = array();
                        $style = array();
                        $row[] = $res['category'];                          $style[] = '';
                        if ( $res['code'] == '71' ) {
                            if ( $res['total_excl_vat'] > 0 ) {
                                $row[] = sprintf('%.2f', $res['total_excl_vat']);       $style[] = 'arff';
                                $row[] = '';   $style[] = 'arff';
                                $data[] = implode('|', $style);
                                $data[] = $row;
                            } else {
                                $row[] = '';       $style[] = 'arff';
                                $row[] = '';   $style[] = 'arff';
                                $data[] = implode('|', $style);
                                $data[] = $row;
                            }
                        } else {
                            if ( $res['total_excl_vat'] > 0 ) {
                                $row[] = '';       $style[] = 'arff';
                                $row[] = sprintf('%.2f', $res['total_excl_vat']);   $style[] = 'arff';
                                $data[] = implode('|', $style);
                                $data[] = $row;
                            } else {
                                $row[] = '';       $style[] = 'arff';
                                $row[] = '';   $style[] = 'arff';
                                $data[] = implode('|', $style);
                                $data[] = $row;
                            }
                        }
                    }
                }
                //$data[] = implode('|', array_fill(0, 3, '-b'));
                $data[] = array();
            }

            $excel = $this->excelCreate($data);
            $this->excelOutput($excel, 'btwag'.$month.$year, $this->_getParam('export') == 'pdf' ? 'pdf' : 'xlsx');*/
        }

        /**
         * @param array $data
         * @return PHPExcel
         */
        private function excelCreate($data){
            $excel = new PHPExcel();
            $sheet = $excel->getActiveSheet();
            $row = 1;
            $maxColumn = 0;
            $maxRow = 1;

            foreach( $data as $result ){
                $column = 0;
                if( is_string($result)){

                }else{
                    foreach($result as $value ){
                        $column++;
                        $maxColumn = max($column, $maxColumn);
                    }
                }

                $row++;
                $maxRow = max($row, $maxRow);
            }

            $row = 1;
            foreach( $data as $result ){
                $column = 0;
                if( is_string($result) ){
                    continue;
                }else{
                    foreach($result as $value ){
                        $sheet->setCellValueByColumnAndRow($column, $row, $value);
                        $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                        $column++;
                    }
                }

                $row++;
            }

            $row = 1;
            foreach( $data as $result ){

                for( $column = 0; $column < $maxColumn; $column++ ){
                    if( $this->_getParam('export') == 'pdf' ){
                        $styleArray = array(
                            'borders' => array(
                                'outline' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('argb' => 'FFFFFFFF'),
                                ),
                            ),
                        );
                        $sheet->getStyleByColumnAndRow($column, $row)->applyFromArray($styleArray);
                    }
                }

                $row++;
            }


            $row = 1;
            foreach( $data as $result ){
                if( is_string($result) ){
                    $columns = explode('|', $result);
                    foreach( $columns as $column => $attribute ){
                        $actions = str_split($attribute,2);
                        foreach( $actions as $action ){
                            switch($action){
                                case '-b':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getBottom()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
                                    break;

                                case '-t':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getTop()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
                                    break;

                                case 'ar':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    break;

                                case 'ac':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    break;

                                case 'tb':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
                                    break;

                                case 'ff':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode('0.00');
                                    break;

                                case 'mc':
                                    $sheet->mergeCellsByColumnAndRow($column, $row, $column+1, $row);
                                    break;

                                case 'h1':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setSize(16);
                                    break;
                            }
                        }
                    }
                }else{
                    $row++;
                }
            }

            $sheet->calculateColumnWidths();

            for( $column = 0; $column < $maxColumn; $column++ ){
                $width = max((120/$maxColumn), $sheet->getColumnDimensionByColumn($column)->getWidth()) * 1.15;
                $sheet->getColumnDimensionByColumn($column)->setAutoSize(false);
                $sheet->getColumnDimensionByColumn($column)->setWidth($width);
            }

            for( $row = 0; $row < $maxRow; $row++ ){
                //$sheet->getRowDimension($row)->setRowHeight($sheet->getRowDimension($row)->getRowHeight() * 1.05);
                $sheet->getRowDimension($row)->setzeroHeight(9);
            }

            return $excel;
        }

        private function excelOutput(PHPExcel $excel, $name, $extension){
            $contentType = '';
            $filename = $name . '.' . $extension;

            if( $extension == 'pdf' ){
                $contentType = 'application/pdf';
                PHPExcel_Settings::setPdfRenderer(PHPExcel_Settings::PDF_RENDERER_MPDF, dirname(APPLICATION_PATH) . "/library/MPDF");
            }else{
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            }

            $excel->getSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $writer = PHPExcel_IOFactory::createWriter($excel, $extension == 'pdf' ? 'PDF' : 'Excel2007');

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');

            die();
        }

        private function vatGovernmentToExcel($dateFrom, $dateTo, $result){
            $fetch = array();
            $fetch['{quarter}'] = ceil(date('m', $dateTo)/3);
            $fetch['{year}'] = date('Y', $dateTo);
            foreach( $result as $type ){
                foreach( $type['rows'] as $row ){
                    if( isset($row['total_excl_vat']) && isset($row['code']) && isset($row['vat_sum']) ){
                        $fetch['{' . $row['code'] . '.total}'] = sprintf('%.2f', $row['total_excl_vat']);
                        $fetch['{' . $row['code'] . '.vat}'  ] = sprintf('%.2f', $row['vat_sum']);
                    }
                }
            }

            //PHPExcel_Settings::setPdfRenderer(PHPExcel_Settings::PDF_RENDERER_MPDF, dirname(APPLICATION_PATH) . "/library/MPDF");
            $objPHPExcel = PHPExcel_IOFactory::load(APPLICATION_PATH . "/data/reports/btw.xlsx");

            for( $column = 0; $column < 10; $column++ ){
                for( $row = 0; $row < 100; $row++ ){
                    $cell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($column, $row);
                    if( preg_match('@{.+}@', $cell->getValue()) ){
                        foreach( $fetch as $name => $value ){
                            $cell->setValue(str_replace($name, $value, $cell->getValue()));
                        }

                        $cell->setValue(preg_replace('@{.+?(total|vat)}@', '0.00', $cell->getValue()));
                        $cell->setValue(preg_replace('@{.+?}@', '', $cell->getValue()));
                    }
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="btw.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            die();
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
    }
