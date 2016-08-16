<?php

    /**
     * @property int $id
     * @property int $invoice_id
     * @property string $filename
     * @property string $name
     *
     * @property Invoice $invoice
     *
     */
    class InvoiceAnnex extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new InvoiceAnnexModel(), $id);
        }

        public function relations(){
            return array(
                'invoice' => array('Invoice', 'invoice_id', self::HAS_ONE)
            );
        }

        public function createFilename($filename){
            if( !$this->id ){
                throw new Exception(_t("Id is required for proper creation of filename!"));
            }

            return preg_replace('@[^a-zA-Z\-0-9_]@iu', '_', pathinfo($filename, PATHINFO_FILENAME))
                     . '-' . $this->id . '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        }

        public function toPath(){
            if( !$this->invoice_id ){
                throw new Exception(_t("Field invoice_id is required for path creation!"));
            }

            return dirname(APPLICATION_PATH) . "/public/" . SettingsModel::getInvoiceAnnexFolder() . "/" . $this->invoice_id . "/" . $this->filename;
        }

        public function toUrl(){
            return "/" . SettingsModel::getInvoiceAnnexFolder() . "/" . $this->invoice_id . "/" . $this->filename;
        }

        public static function isAllowed($filename){
            return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), SettingsModel::getInvoiceAnnexTypes());
        }
    }