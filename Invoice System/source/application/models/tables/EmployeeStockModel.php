<?php

    class EmployeeStockModel extends Jaycms_Db_Model {

        protected $_name = 'employee_stock';

        public function getProductTransitCount($productId){
            $query =    "
                            SELECT
                                    SUM(transit)
                            FROM
                                    " . $this->_name . "
                            WHERE
                                    product_id = ?
                        ";

            return (int) reset($this->getAdapter()->query($query, $productId)->fetch(Zend_Db::FETCH_ASSOC));
        }

        public function getProductReservationCount($productId){
            $query =    "
                            SELECT
                                    SUM(reservation)
                            FROM
                                    " . $this->_name . "
                            WHERE
                                    product_id = ?
                        ";

            return (int) reset($this->getAdapter()->query($query, $productId)->fetch(Zend_Db::FETCH_ASSOC));
        }

        public function getProductEmployeesCount($productId){
            $query =    "
                            SELECT
                                    COUNT(*)
                            FROM
                                    " . $this->_name . "
                            WHERE
                                    product_id = ?
                                    AND
                                    ( transit > 0 OR reservation > 0 )
                        ";

            return (int) reset($this->getAdapter()->query($query, $productId)->fetch(Zend_Db::FETCH_ASSOC));
        }
    }