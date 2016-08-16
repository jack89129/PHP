<?php

/**
 * @property int $id
 * @property string $action
 * @property string $target
 * @property int $target_id
 * @property int $employee_id
 * @property int $params;
 * @property string $created_time
 *
 * @property Employee $employee
 */
class ActivityLog extends Core_ActiveRecord_Row {

    public function __construct($id=null){
        parent::__construct(new ActivityLogModel(), $id);
    }

    public function relations(){
        return array(
            'employee' => array('Employee', 'employee_id', self::HAS_ONE)
        );
    }

    public function getParams(){
        return unserialize($this->get('params'));
    }

    public function setParams($params){
        $this->set('params', serialize($params));
    }

    public function getCreatedTime(){
        return $this->get('created_time') != 0 ? strtotime($this->get('created_time')) : 0 ;
    }

    public function setCreatedTime($value){
        $this->set('created_time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : 0 );
    }

    public function getText(){
        switch( $this->target ){

            case 'invoice':
                $invoice = new Invoice($this->target_id);
                $invoiceLink = $invoice->exists() ? '<a href="{%baseUrl%}/invoices/index/view/id/' . $invoice->id . '">' . $invoice->number . '</a>' : '';

                switch( $this->action ){
                    case 'index'    : return _t('Facturen Overzicht'); break;
                    case 'new'      : return _t('Factuur aangemaakt %s', $invoiceLink); break;
                    case 'view'     : return _t('Bekijkt factuur %s', $invoiceLink); break;
                    case 'edit'     : return _t('Factuur gewijzigd %s', $invoiceLink); break;
                    case 'pdf'      : return _t('PDF van factuur gemaakt %s', $invoiceLink); break;
                    case 'email'    : return _t('Factuur per e-mail verstuurd %s', $invoiceLink); break;
                    case 'credit'   : return _t('Credit-Factuur gemaakt %s', $invoiceLink); break;
                    case 'duplicate': return _t('Factuur gedupliceerd %s', $invoiceLink); break;
                    case 'delete'   : return _t('Factuur verwijderd'); break;
                    case 'bulk-pdf' : return _t('Bulk pdf gedownload'); break;
                    case 'bulk-email': return _t('Bulk email verzonden'); break;
                    case 'annex'    : return _t('Bijlage toegevoegd aan factuur %s', $invoiceLink); break;
                    case 'proforma-status': return _t('Factuur status aangepast'); break;
                    case 'payment'  : return _t('Factuur op betaald gezet %s', $invoiceLink); break;
                }

                break;

            case 'offer':
                $invoice = new Invoice($this->target_id);
                $invoiceLink = $invoice->exists() ? '<a href="{%baseUrl%}/offers/index/view/id/' . $invoice->id . '">' . $invoice->number . '</a>' : '';

                switch( $this->action ){
                    case 'index'    : return _t('Offerte Overzicht'); break;
                    case 'new'      : return _t('Nieuwe offerte gemaakt %s', $invoiceLink); break;
                    case 'view'     : return _t('Offerte bekeken %s', $invoiceLink); break;
                    case 'edit'     : return _t('Offerte aangepast %s', $invoiceLink); break;
                    case 'pdf'      : return _t('PDF gemaakt van offerte %s', $invoiceLink); break;
                    case 'email'    : return _t('Offerte verzonden %s', $invoiceLink); break;
                    case 'credit'   : return _t('Credit-Offerte gemaakt %s', $invoiceLink); break;
                    case 'duplicate': return _t('Offerte gedupliceerd %s', $invoiceLink); break;
                    case 'delete'   : return _t('Offerte verwijderd'); break;
                    case 'bulk-pdf' : return _t('Bulk pdf gedownload'); break;
                    case 'bulk-email': return _t('Bulk emails verzonden'); break;
                    case 'annex'    : return _t('Bijlage toegevoegd aan offerte %s', $invoiceLink); break;
                    case 'proforma-status': return _t('Offerte status aangepast'); break;
                    case 'payment'  : return _t('Offerte op betaald gezet %s', $invoiceLink); break;
                }

                break;

            case 'purchase':
                $purchase = new Purchase($this->target_id);
                $purchaseLink = $purchase->exists() ? '<a href="{%baseUrl%}/purchases/index/view/id/' . $purchase->id . '">' . $purchase->number . '</a>' : '';

                switch( $this->action ){
                    case 'index'    : return _t('Inkoop Overzicht'); break;
                    case 'new'      : return _t('Nieuwe inkoop gemaakt %s', $purchaseLink); break;
                    case 'view'     : return _t('Bekijkt inkoopfactuur %s', $purchaseLink); break;
                    case 'edit'     : return _t('Inkoop gewijzigd %s', $purchaseLink); break;
                    case 'pdf'      : return _t('PDF van inkoop gemaakt %s', $purchaseLink); break;
                    case 'bulk-pdf' : return _t('Bulk pdf gedownload'); break;
                    case 'attachment': return _t('Bijlage toegevoegd aan inkoop %s', $purchaseLink); break;
                    case 'delete'   : return _t('Inkoop verwijderd');
                    case 'delete-attachment': return _t('Bijlage verwijderd bij inkoopfactuur %s', $purchaseLink); break;
                    case 'add-attachment': return _t('Bijlage toegevoegd aan inkoopfactuur %s', $purchaseLink); break;
                    case 'payment'  : return _t('Inkoopfactuur op betaald gezet %s', $purchaseLink); break;
                }

                break;


            case 'stock':
                $product = new Product($this->target_id);
                $productLink = $product->exists() ? $product->name : '';

                switch( $this->action ){
                    case 'index'    : return _t('Voorraadbeheer overzicht'); break;
                    case 'view'     : return _t('Bekijkt product %s', $productLink); break;
                    case 'add-group': return _t('Voegt groep toe'); break;
                    case 'add-product': return _t('Voegt product toe %s', $productLink); break;
                    case 'edit-product': return _t('Product gewijzigd %s', $productLink); break;
                    case 'delete-product': return _t('Product verwijderd %s', $productLink); break;
                    case 'delete-group' : return _t('Groep verwijderd'); break;
                }

                break;

            case 'stock-receipt':
                $receipt = new Receipt($this->target_id);
                $receiptLink = $receipt->exists() ? '<a href="{%baseUrl%}/stock/receipt/view/id/' . $receipt->id . '">' . $receipt->number . '</a>' : '';

                switch( $this->action ){

                    case 'index'    : return _t('Bestelbon overzicht'); break;
                    case 'view'     : return _t('Bekijkt bestelbon %s', $receiptLink); break;
                    case 'edit'     : return _t('Bestelbon gewijzigd %s', $receiptLink); break;
                    case 'new'      : return _t('Nieuwe bestelbon aangemaakt %s', $receiptLink); break;
                    case 'delete'   : return _t('Bestelbon verwijderd'); break;
                    case 'pdf'      : return _t('PDF aangemaakt van bestelbon %s', $receiptLink); break;
                    case 'bulk-pdf' : return _t('Bulk pdf gedownload'); break;
                }

                break;

            case 'employee-pack':
                $pack = new Pack($this->target_id);
                $packLink = $pack->exists() ? '<a href="{%baseUrl%}/employees/pack/view/id/' . $pack->id . '">' . $pack->number . '</a>' : '';

                switch( $this->action ){

                    case 'index'    : return _t('Paklijst overzicht'); break;
                    case 'view'     : return _t('Bekijkt paklijst %s', $packLink); break;
                    case 'edit'     : return _t('Paklijst gewijzigd %s', $packLink); break;
                    case 'new'      : return _t('Nieuwe paklijst %s', $packLink); break;
                    case 'delete'   : return _t('Paklijst verwijderd'); break;
                    case 'pdf'      : return _t('PDF aangemaakt van paklijst %s', $packLink); break;
                    case 'bulk-pdf' : return _t('Bulk pdf gedownload'); break;
                }

                break;

            case 'stock-manage':
                $product = new Product($this->target_id);
                //$productLink = $product->exists() ? $product->name : '';

                switch( $this->action ){

                    case 'index': return _t('Voorraadbeheer Beheren'); break;
                    case 'add-to-product': return _t('Voorraad toegevoegd'); break;
                    case 'remove-from-product': return _t('Voorraad verwijderd'); break;
                    case 'edit': return _t('Voorraad gewijzigd'); break;
                }

                break;

            case 'contact':
                $contact = new Contact($this->target_id);
                $contactLink = $contact->exists() ? '<a href="{%baseUrl%}/contacts/?contact_id=' . $contact->id . '">' . $contact->name . '</a>' : '';

                switch( $this->action ){

                    case 'index': return _t('Contacten overzicht'); break;
                    case 'view': return _t('Bekijkt contact %s', $contactLink); break;
                    case 'new': return _t('Contact aangemaakt %s', $contactLink); break;
                    case 'edit': return _t('Contact gewijzigd %s', $contactLink); break;
                    case 'delete': return _t('Contact verwijderd %s', $contactLink); break;
                    case 'remove-contact-from-group': return _t('Contact %s verwijderd van groep', $contactLink); break;
                    case 'add-contact-to-group': return _t('Contact %s toegevoegd aan groep', $contactLink); break;
                    case 'remove-employee-from-contact': return _t('Werknemer verwijderd van contact %s', $contactLink); break;
                    case 'add-employee-to-contact': return _t('Werknemer toegevoegd aan contact %s', $contactLink); break;
                    case 'add-group': return _t('Groep aangemaakt'); break;
                    case 'import-contacts': return _t('Contacten geimporteerd'); break;
                    case 'export-contacts': return _t('Contacten geexporteerd'); break;

                }

                break;

            case 'employee':
                $employee = new Employee($this->target_id);
                $employeeLink = $employee->exists() ? '<a href="{%baseUrl%}/employees/?employee_id=' . $employee->id . '">' . $employee->name . '</a>' : '';

                switch( $this->action ){

                    case 'index': return _t('Werknemers overzicht'); break;
                    case 'view': return _t('Bekijkt werknemer %s', $employeeLink); break;
                    case 'new': return _t('Werknemer aangemaakt %s', $employeeLink); break;
                    case 'edit': return _t('Werknemer gewijzigt %s', $employeeLink); break;
                    case 'delete': return _t('Werknemer verwijderd %s', $employeeLink); break;
                    case 'remove-employee-from-group': return _t('Werknemer %s verwijderd van groep', $employeeLink); break;
                    case 'add-employee-to-group': return _t('Werknemer %s toegevoegd aan groep', $employeeLink); break;
                    case 'add-group': return _t('Groep aangemaakt'); break;

                }

                break;

            case 'wholesaler':
                $wholesaler = new Wholesaler($this->target_id);
                $wholesalerLink = $wholesaler->exists() ? '<a href="{%baseUrl%}/wholesalers/?wholesaler_id=' . $wholesaler->id . '">' . $wholesaler->name . '</a>' : '';

                switch( $this->action ){

                    case 'index': return _t('Leveranciers overzicht'); break;
                    case 'view': return _t('Bekijkt leverancier %s', $wholesalerLink); break;
                    case 'new': return _t('Leverancier toegevoegd %s', $wholesalerLink); break;
                    case 'edit': return _t('Leverancier gewijzigd %s', $wholesalerLink); break;
                    case 'delete': return _t('Leverancier verwijderd %s', $wholesalerLink); break;
                    case 'remove-wholesaler-from-group': return _t('Leverancier %s verwijderd van groep', $wholesalerLink); break;
                    case 'add-wholesaler-to-group': return _t('Leverancier %s toegevoegd aan groep', $wholesalerLink); break;
                    case 'add-group': return _t('Groep toegevoegd'); break;
                    case 'import-wholesalers': return _t('Leveranciers geimporteerd'); break;
                    case 'export-wholesalers': return _t('Leveranciers geexporteerd'); break;

                }

                break;

            case 'settings':

                switch( $this->action ){
                    case 'index': return _t('Instellingen overzicht'); break;
                }

                break;

            case 'settings-tags':
                $tag = new Tag($this->target_id);
                $tagLink = $tag->exists() ? $tag->name : '';

                switch( $this->action ){
                    case 'index': return _t('Categorien overzicht'); break;
                    case 'add-category': return _t('Categorie toegevoegd'); break;
                    case 'edit-category': return _t('Categorie gewijzigd'); break;
                    case 'delete-category': return _t('Categorie verwijderd'); break;
                    case 'add-tag': return _t('Categorie onderdeel toegevoegd %s', $tagLink); break;
                    case 'edit-tag': return _t('Categorie onderdeel gewijzigd %s', $tagLink); break;
                    case 'delete-tag': return _t('Categorie onderdeel verwijderd'); break;
                }

                break;
        }

        return $this->action;
    }

}