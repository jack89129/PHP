<?php

/**
 * @property int $id
 * @property int $purchase_id
 * @property string $filename
 * @property string $name
 *
 * @property Purchase $purchase
 */
class PurchaseAttachment extends Core_ActiveRecord_Row {

    public function __construct($id=null){
        parent::__construct(new PurchaseAttachmentModel(), $id);
    }

    public function relations(){
        return array(
            'purchase' => array('Purchase', 'purchase_id', self::HAS_ONE)
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
        if( !$this->purchase_id ){
            throw new Exception(_t("Field purchase_id is required for path creation!"));
        }

        return dirname(APPLICATION_PATH) . "/public/" . SettingsModel::getPurchaseAttachmentFolder() . "/" . $this->purchase_id . "/" . $this->filename;
    }

    public function toUrl(){
        return str_replace('//', '/', "/" . SettingsModel::getPurchaseAttachmentFolder() . "/" . $this->purchase_id . "/" . $this->filename);
    }

    public static function isAllowed($filename){
        return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), SettingsModel::getPurchaseAttachmentTypes());
    }
}