<?php

    class TagModel extends Jaycms_Db_Model {

        protected $_name = 'tag';

        public function autocomplete($term, $limit=5, $exclude=''){
            $limit = $limit < 1 ? 5 : $limit;

            $query = "
                        SELECT
                                t.*,
                                c.name as category
                        FROM
                                tag t
                                LEFT JOIN tag_category c ON c.id = t.tag_category_id
                        WHERE
                                (
                                    " . $this->getAdapter()->quoteInto('t.name LIKE ?', $term . '%') . "
                                    OR
                                    " . $this->getAdapter()->quoteInto('c.name LIKE ?', $term . '%') . "
                                ) AND (
                                    " . $this->getAdapter()->quoteInto('c.type != ?', $exclude) . "
                                )

                        ORDER BY natsort(c.id, 'natural') ASC, natsort(t.id, 'natural') ASC
                        LIMIT " . (int) $limit;

            $tags = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
            return $tags;
        }
    }