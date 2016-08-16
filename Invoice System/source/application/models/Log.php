<?php

/**
 * @property int $id
 * @property string $source_type
 * @property int $source_id
 * @property int $employee_id
 * @property string $data
 * @property string $event
 * @property int $created_time
 *
 * @property Employee $employee
 *
 */
class Log extends Core_ActiveRecord_Row {

    public function __construct($id=null){
        $this->created_time = time();
        $this->employee_id = Utils::user()->id;
        parent::__construct(new LogModel(), $id);
    }

    public function relations(){
        return array(
            'employee' => array('Employee', 'employee_id', self::HAS_ONE)
        );
    }

    public function getCreatedTime(){
        return $this->get('created_time') != 0 ? strtotime($this->get('created_time')) : 0 ;
    }

    public function setCreatedTime($value){
        $this->set('created_time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : 0 );
    }

    public function getText(){
        $name = '';
        if( $this->source_type == LogModel::SOURCE_TYPE_INVOICE ){
            $invoice = new Invoice($this->source_id);
            $name = $invoice->exists() && $invoice->proforma ? 'Offerte' : 'Factuur';
            $name = $invoice->credit ? 'Credit-' . $name : $name;
        }
        switch($this->event){
            case LogModel::EVENT_INVOICE_CREATED:
                return _t('%s aangemaakt', $name);
                break;

            case LogModel::EVENT_INVOICE_SENT_EMAIL:
                return _t('%s verzonden aan <a href="mailto:%s">%s</a>', array($name, $this->data, $this->data));
                break;

            case LogModel::EVENT_INVOICE_SENT_PERSONAL:
                return _t('%s zelf verzonden', $name);
                break;

            case LogModel::EVENT_INVOICE_PAYMENT:
                $invoice = new Invoice($this->source_id);
                $data = explode(";", $this->data);
                $tmp = "gedeeltelijk";
                if ( !empty($data[2]) && $data[2] == "yes") $tmp = "";
                return _t('%s '.$tmp.' %s voor een bedrag van &euro; %s hierdoor is het nieuwe te ontvangen bedrag &euro; %s', array($name, _t('method_'.$data[1]), Utils::numberFormat($data[0]), Utils::numberFormat($invoice->unpaid_sum+$data[0])));
                break;

            case LogModel::EVENT_INVOICE_PAID:
                return _t('%s voldaan', $name);
                break;
                
            case LogModel::EVENT_INVOICE_UNPAID:
                return _t('Betaling ongedaan gemaakt');
                break;

            case LogModel::EVENT_INVOICE_ANNEX:
                $annex = new InvoiceAnnex($this->data);
                return _t('%s bijlage toegevoegd %s', array($name, ($annex->exists() ? '<a href="%baseUrl%' . $annex->toUrl() . '">' . $annex->filename . '</a>' : '')));
                break;

            case LogModel::EVENT_INVOICE_ANNEX_EDIT:
                return _t('Bijlage hernoemt naar <b>%s</b>', $this->data);
                break;
                
            case LogModel::EVENT_INVOICE_ANNEX_DELETE:
                return _t('Factuur bijlage <b>%s</b> verwijderd!', $this->data);
                break;

            case LogModel::EVENT_INVOICE_CREDIT:
                $credit = new Invoice($this->data);
                return _t('%s credit-factuur %s', array($name, ($credit->exists() ? '<a href="%baseUrl%/invoices/index/view/id/' . $credit->id . '">' . $credit->number . '</a>' : '')));
                break;
                
            case LogModel::EVENT_INVOICE_DUPLICATE:
                $src = new Invoice($this->data);
                $tmp = $src->proforma ? 'offerte' : 'factuur';
                return _t('Concept werd gedupliceerd van bestaande '.$tmp.' %s', '<a href="%baseUrl%/invoices/index/view/id/' . $src->id . '">' . $src->number . '</a>');
                break;
                
            case LogModel::EVENT_INVOICE_LATE:
                return _t("1ste Herinnering verzonden");
                break;
            
            case LogModel::EVENT_INVOICE_URGENT:
                return _t("2de Herinnering verzonden");
                break;
            
            case LogModel::EVENT_INVOICE_JUDGE:
                return _t("Aanmaning verzonden");
                break;

            case LogModel::EVENT_INVOICE_PROFORMA_STATUS:
                return _t('%s status veranderd naar %s', array($name, Invoice::getProformaStatusName($this->data)));
                break;

            case LogModel::EVENT_PROFORMA_TO_INVOICE:
                $invoice = new Invoice($this->data);
                $proforma = new Invoice($this->source_id);
                return _t('Factuur %s gemaakt op basis van offerte %s', array(($invoice->exists() ? '<a href="%baseUrl%/invoices/index/view/id/' . $invoice->id . '">' . $invoice->number . '</a>' : ''), ($proforma->exists() ? '<a href="%baseUrl%/offers/index/view/id/' . $proforma->id . '">' . $proforma->number . '</a>' : '')));
                break;

            case LogModel::EVENT_INVOICE_FROM_PROFORMA:
                $invoice = new Invoice($this->source_id);
                $proforma = new Invoice($this->data);
                return _t('Factuur %s gemaakt op basis van offerte %s', array(($invoice->exists() ? '<a href="%baseUrl%/invoices/index/view/id/' . $invoice->id . '">' . $invoice->number . '</a>' : ''), ($proforma->exists() ? '<a href="%baseUrl%/offers/index/view/id/' . $proforma->id . '">' . $proforma->number . '</a>' : '')));
                break;

            case LogModel::EVENT_PURCHASE_CREATED:
                return _t('Inkoopfactuur aangemaakt');
                break;

            case LogModel::EVENT_PURCHASE_PAYMENT:
                return _t('Inkoopfactuur betaald bedrag &euro; %s', Utils::numberFormat($this->data));
                break;

            case LogModel::EVENT_PURCHASE_PAID:
                return _t('Inkoopfactuur voldaan');
                break;

            case LogModel::EVENT_PURCHASE_ATTACHMENT:
                $attachment = new PurchaseAttachment($this->data);
                return _t('Bijlage toegevoegd %s', ($attachment->exists() ? '<a href="%baseUrl%' . $attachment->toUrl() . '">' . $attachment->filename . '</a>' : ''));
                break;

            case LogModel::EVENT_PURCHASE_ATTACHMENT_EDIT:
                return _t('Bijlage hernoemt naar <b>%s</b>', $this->data);
                break;

            case LogModel::EVENT_RECEIPT_CREATED:
                return _t('Bestelbon aangemaakt');
                break;

            case LogModel::EVENT_PACK_CREATED:
                return _t('Paklijst aangemaakt');
                break;

            case LogModel::EVENT_PACK_UNFINAL:
                return _t('Paklijst gewijzigd');
                break;
                
            case LogModel::EVENT_PACK_FINAL:
                return _t('Paklijst final');
                break;

            case LogModel::EVENT_PACK_PRODUCT_ADDED:
                $product = new Product($this->data);
                $productLink = $product->exists() ? '<a href="%baseUrl%/stock/?product_id=' . $product->id . '">' . $product->name . '</a>' : '';
                return _t('Paklijst product toegevoegd %s', $productLink);
                break;

            case LogModel::EVENT_PACK_PRODUCT_REMOVED:
                $product = new Product($this->data);
                $productLink = $product->exists() ? '<a href="%baseUrl%/stock/?product_id=' . $product->id . '">' . $product->name . '</a>' : '';
                return _t('Paklijst product verwijderd %s', $productLink);
                break;

            case LogModel::EVENT_MANUAL:
                return Utils::strip_bad_tags($this->data);
                break;

            default:
                throw new Exception(_t('Niet ondersteunde geschiedenis gebeurtenis %s!', $this->event));
                break;
        }
    }
}