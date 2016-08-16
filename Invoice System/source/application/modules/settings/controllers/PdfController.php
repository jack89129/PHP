<?php

    class Settings_PdfController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Overheids rapporten");
            $this->view->page_sub_title = _t("Btw aangifte, klantenlisting en meer...");
            $this->view->current_module = "settings";
            $year = date('Y');
            $month = date('m');
            $this->view->year = $year;
            $this->view->month = $month;
        }

        public function indexAction(){
            Utils::activity('pdf', 'settings');
        }
        
        public function pdfAction()
        {   
            $year = $this->_getParam('year', date('Y'));
            $from = $year.'-01-01';
            $to = $year.'-12-31';
                                                                 
            $contactModel = new ContactModel();
            $total = 0;
            $result = $contactModel->getGoodCustomers($total);
            
            $this->view->count = count($result);
            $this->view->total = $total;
            $this->view->result = $result;
            
            $this->generatePDF('klantenlisting-' . date('Y') . '.pdf', 'D');
            die();                                  
        }
        
        private function getGovernmentReport($year, $month) {
            $governmentModel = new GovernmentItemModel();
            $data = $governmentModel->getGovernmentReport($year, $month);
            
            if ( count($data) > 0 ) {
                return $data;
            } else {
                $now = strtotime($year.'-'.$month.'-01');
                $periodDates = Utils::name2date($period_name, $now);
                $reportModel = new ReportModel();
                
                $invoices = Reports::vatInvoicesGovernment($periodDates[0], $periodDates[1]);
                
                $vat54 = 0; $vat54Array = array("00", "01", "02", "03");
                $vatXX = 0; $vatXXArray = array("81", "82", "83", "86", "87"); 
                $vat63 = 0; $vat63Array = array("84", "85");
                $vat59 = 0; $vat59Array = array("55", "56", "81", "82", "83", "86", "87");
                $vat71 = 0; $vat71Array = array("54", "55", "56", "61", "63");
                foreach( $invoices as $row ){
                    $reportModel->saveReport($year.'-'.$month, $row['code'], $row['total_excl_vat']);
                    if ( in_array($row['code'], $vat54Array) ) {
                        $vat54 += $row['vat_sum'];
                    }
                }
                $itemModel = new GovernmentItemModel();
                $row = array();
                $row['category'] = $itemModel->getItem('54');
                $row['code'] = '54';
                $row['total_excl_vat'] = $vat54;
                $row['vat_sum'] = 0;
                $result['invoices']['rows'][] = $row;
                $reportModel->saveReport($year.'-'.$month, '54', $vat54);

                $purchases = Reports::vatPurchasesGovernment($periodDates[0], $periodDates[1]);
                foreach( $purchases as $row ){
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
                $reportModel->saveReport($year.'-'.$month, '62v', 0);
                $reportModel->saveReport($year.'-'.$month, '64', 0);
                $reportModel->saveReport($year.'-'.$month, 'YY', $vat59);
                
                $others = Reports::vatOthersGovernment($periodDates[0], $periodDates[1]);
                foreach( $others as $row ){
                    $reportModel->saveReport($year.'-'.$month, $row['code'], $row['total_excl_vat']);
                    if ( in_array($row['code'], $vat71Array) ) {
                        $vat71 += $row['vat_sum'];
                    }
                }
                
                $end_result = $vat71 + $vat54 + $vat63 - $vat59;
                if ( $end_result > 0 ) {
                    $reportModel->saveReport($year.'-'.$month, '71', $end_result);
                    $reportModel->saveReport($year.'-'.$month, '72', 0);
                } else {
                    $reportModel->saveReport($year.'-'.$month, '71', 0);
                    $reportModel->saveReport($year.'-'.$month, '72', $end_result * -1);
                }
            }
            $data = $governmentModel->getGovernmentReport($year, $month);
            return $data;
        }
        
        public function btwagAction()
        {
            $year = $this->_getParam('year');
            $month = $this->_getParam('month');
            if ( strlen($month) == 1 ) $month = "0".$month;
            
            $data = $this->getGovernmentReport($year, $month);
            
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="btwag'.$month.$year.'.xml"'); 
            header('Content-Transfer-Encoding: binary');

            // ********************* Header *************************
            echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
            echo "\n" . '<ns2:VATConsignment xmlns="http://www.minfin.fgov.be/InputCommon" xmlns:ns2="http://www.minfin.fgov.be/VATConsignment" VATDeclarationsNbr="1">';
            echo "\n" . " <ns2:Representative>";
            echo "\n" . '  <RepresentativeID identificationType="TIN" issuedBy="BE">0871302795</RepresentativeID>';
            echo "\n" . "  <Name>VANDORMAEL</Name>";
            echo "\n" . "  <Street>INDUSTRIEWEG  22</Street>";
            echo "\n" . "  <PostCode>3620</PostCode>";
            echo "\n" . "  <City> L A N A K E N</City>";
            echo "\n" . "  <CountryCode>BE</CountryCode>";
            echo "\n" . "  <EmailAddress>info@jovado.be</EmailAddress>";
            echo "\n" . "  <Phone>089717302</Phone>";
            echo "\n" . ' </ns2:Representative>';
            echo "\n" . ' <ns2:VATDeclaration SequenceNumber="1">';
            echo "\n" . '  <ns2:Declarant>';
            echo "\n" . "   <VATNumber>0871302795</VATNumber>";
            echo "\n" . "   <Name>K.D.S. HORECASERVICE n.v.</Name>";
            echo "\n" . "   <Street>INDUSTRIEWEG  22</Street>";
            echo "\n" . "   <PostCode>3620</PostCode>";
            echo "\n" . "   <City> L A N A K E N</City>";
            echo "\n" . "   <CountryCode>BE</CountryCode>";
            echo "\n" . "   <EmailAddress>info@jovado.be</EmailAddress>";
            echo "\n" . "   <Phone>089717302</Phone>";
            echo "\n" . '  </ns2:Declarant>';
            echo "\n" . '  <ns2:Period>';
            echo "\n" . "   <ns2:Month>$month</ns2:Month>";
            echo "\n" . "   <ns2:Year>$year</ns2:Year>";
            echo "\n" . '  </ns2:Period>';
            // ********************* ****** *************************
            
            // ********************* Content *************************
            echo "\n" . '  <ns2:Data>';
            foreach ( $data as $row ) {
                if ( $row['code'] == '62v' ) $row['code'] = '62';
                if ( $row['total'] > 0 && $row['code'] != 'XX' && $row['code'] != 'YY' ) {
                    echo "\n" . '   <ns2:Amount GridNumber="' . $row['code'] . '">' . Utils::numberFormat($row['total']) . '</ns2:Amount>';
                }
            }
            echo "\n" . '  </ns2:Data>';
            // ********************* ****** *************************
            
            // ********************* Footer *************************
            echo "\n" . '  <ns2:ClientListingNihil>NO</ns2:ClientListingNihil>';
            echo "\n" . '  <ns2:Ask Payment="NO" Restitution="YES"/>';
            echo "\n" . '  <ns2:Comment> </ns2:Comment>';
            echo "\n" . ' </ns2:VATDeclaration>';
            echo "\n" . '</ns2:VATConsignment>';
            // ********************* ****** *************************
            die();
        }
        
        public function xmlAction()
        {
            $year = $this->_getParam('year');
            $month = $this->_getParam('month');
            if ( strlen($month) == 1 ) $month = "0".$month;
                       
            $invoiceModel = new InvoiceModel();
            $contactList = $invoiceModel->getContactsReport($year, $month);
            
            $total_amount = 0;
            foreach ( $contactList as $contact ) {
                $total_amount += $contact['total_sum'];
            }
            
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="icomag'.$month.$year.'.xml"'); 
            header('Content-Transfer-Encoding: binary');

            // ********************* Header *************************
            echo '<?xml version="1.0" encoding="UTF-8" ?>';
            echo "\n" . '<ns2:IntraConsignment xmlns="http://www.minfin.fgov.be/InputCommon" xmlns:ns2="http://www.minfin.fgov.be/IntraConsignment" IntraListingsNbr="1">';
            echo "\n" . " <ns2:Representative>";
            echo "\n" . '  <RepresentativeID identificationType="NVAT" issuedBy="BE">0871302795</RepresentativeID>';
            echo "\n" . "  <Name>VANDORMAEL</Name>";
            echo "\n" . "  <Street>INDUSTRIEWEG  22</Street>";
            echo "\n" . "  <PostCode>3620</PostCode>";
            echo "\n" . "  <City>3620  L A N A K E N</City>";
            echo "\n" . "  <CountryCode>BE</CountryCode>";
            echo "\n" . "  <EmailAddress>info@jovado.be</EmailAddress>";
            echo "\n" . "  <Phone>089717302</Phone>";
            echo "\n" . ' </ns2:Representative>';
            echo "\n" . ' <ns2:RepresentativeReference> </ns2:RepresentativeReference>';
            echo "\n" . ' <ns2:IntraListing AmountSum="'.Utils::numberFormat($total_amount).'" DeclarantReference="0871302795'.$month.substr($year, 2).'" ClientsNbr="'.count($contactList).'" SequenceNumber="1">';
            echo "\n" . '  <ns2:Declarant>';
            echo "\n" . "   <VATNumber>0871302795</VATNumber>";
            echo "\n" . "   <Name>K.D.S. HORECASERVICE n.v.</Name>";
            echo "\n" . "   <Street>INDUSTRIEWEG  22</Street>";
            echo "\n" . "   <PostCode>3620</PostCode>";
            echo "\n" . "   <City>3620  L A N A K E N</City>";
            echo "\n" . "   <CountryCode>BE</CountryCode>";
            echo "\n" . "   <EmailAddress>info@jovado.be</EmailAddress>";
            echo "\n" . "   <Phone>089717302</Phone>";
            echo "\n" . '  </ns2:Declarant>';
            echo "\n" . '  <ns2:Period>';
            echo "\n" . "   <ns2:Month>$month</ns2:Month>";
            echo "\n" . "   <ns2:Year>$year</ns2:Year>";
            echo "\n" . '  </ns2:Period>';
            // ********************* ****** *************************
            
            // ********************* Content *************************
            $i = 1;
            foreach ( $contactList as $contact ) {
                echo "\n" . '  <ns2:IntraClient SequenceNumber="'.$i.'">';
                echo "\n" . '   <ns2:CompanyVATNumber issuedBy="NL">'.$contact['vat_number'].'</ns2:CompanyVATNumber>';
                echo "\n" . "   <ns2:Code>L</ns2:Code>";
                echo "\n" . "   <ns2:Amount>".Utils::numberFormat($contact["total_sum"])."</ns2:Amount>";
                echo "\n" . "   <ns2:CorrectingPeriod>";
                echo "\n" . "    <ns2:Month>$month</ns2:Month>";
                echo "\n" . "    <ns2:Year>$year</ns2:Year>";
                echo "\n" . "   </ns2:CorrectingPeriod>";
                echo "\n" . "  </ns2:IntraClient>";
                $i++;
            }
            // ********************* ****** *************************
            
            // ********************* Footer *************************
            echo "\n" . '  <ns2:Comment> </ns2:Comment>';
            echo "\n" . ' </ns2:IntraListing>';
            echo "\n" . '</ns2:IntraConsignment>';
            // ********************* ****** *************************
            die();
        }
        
        protected function generatePDF($name, $destination){
            require_once('MPDF/mpdf.php');
            $this->_helper->layout()->disableLayout();

            $content = $this->view->render('pdf/pdf.phtml');

            $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'L');
            $mpdf->WriteHTML($content);
            
            $mpdf->Output($name, $destination);
        }
        
    }