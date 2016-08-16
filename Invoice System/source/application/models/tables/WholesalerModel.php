<?php
class WholesalerModel extends Jaycms_Db_Model {
	/**
	 * The default table name 
	 */
	protected $_name = 'wholesaler';

    public function autocomplete($term, $limit=5){
        $limit = $limit < 1 ? 5 : $limit;
        $query = "SELECT
                        *
                  FROM
                        " . $this->_name . "
                  WHERE
                        " . $this->getAdapter()->quoteInto('company_name LIKE ?', $term . '%') . "

                  ";

        $contacts =	$this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
        return $contacts;
    }

    public function search($search){
        $query = "
                    SELECT
                            w.*
                    FROM
                            wholesaler w

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('w.company_name LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.address LIKE ?',      '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.postcode LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.city LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.country LIKE ?',      '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.vat_number LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_firstname LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_lastname LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_address LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_postcode	LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_city LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.delivery_country LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.contact_person LIKE ?',       '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.email_address LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.phone1 LIKE ?',               '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.phone2 LIKE ?',               '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.fax LIKE ?',                  '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.cellphone LIKE ?',            '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.info LIKE ?',                 '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('w.role LIKE ?',                 '%' . $search . '%') . "
                            )

                    ORDER BY w.company_name DESC
                    LIMIT 20
                 ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $row ){
            $result[$key] = new Wholesaler();
            $result[$key]->load($row);
        }

        return $result;
    }
}

