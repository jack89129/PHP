<?php

    class PackModel extends Jaycms_Db_Model {

        const STATUS_NEW = 'new';
        const STATUS_FINAL = 'final';

        protected $_name = 'pack';

        public function findPacks($type, $employeeId=0, &$total=0, $limit=null, $page=null){
            $select = 	$this->select();

            if( $employeeId ){
                $select->where('employee_id = ?', $employeeId);
            }

            $method = 'where' . ucfirst($type);
            if( method_exists($this, $method) ){
                call_user_func_array(array($this, $method), array($select));
            }

            $statSelect = clone $select;
            $select->order('id DESC');

            if( $limit !== null && $page !== null ){
                $select->limit($limit, $page*$limit);
            }

            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            $stat	= $statSelect->from( $this->_name, array(new Zend_Db_Expr('COUNT(*) as `count`')))
                ->query(Zend_Db::FETCH_OBJ)->fetch();

            $total 	= $stat->count;

            foreach( $result as $key => $receipt ){
                $result[$key] = new Pack();
                $result[$key]->load($receipt);
            }

            return $result;
        }
        
        public function findPacksForPDF($employeeId=0, $date){        
            $db = Zend_Registry::get('db1');
            
            /*select s.id, s.name, sum(s.total)
from
(SELECT pp.product_id id, pp.description name, sum(pp.qty) total FROM `pack_product` pp
LEFT JOIN `pack` p ON pp.pack_id = p.id
WHERE 1=1
GROUP BY pp.product_id
UNION
SELECT spp.product_id id, spp.description name, sum(spp.qty) total FROM `setting_pack_product` spp
LEFT JOIN `setting_pack` sp ON spp.pack_id = sp.id
WHERE 1=1
GROUP BY spp.product_id) s
group by s.id
order by s.id*/
            
            /*$sql = "
                SELECT pp.product_id id, pp.description name, sum(pp.qty) total FROM `pack_product` pp
                LEFT JOIN `pack` p ON pp.pack_id = p.id
                WHERE DATE(p.delivery_date) = '".date(Constants::MYSQL_DAY_FORMAT, strtotime($date))."' and p.employee_id = '".$employeeId."'
                GROUP BY pp.product_id
                ORDER BY pp.product_id";*/
                
            $sql = "
                SELECT s.id id, s.name name, sum(s.total) total
                FROM
                (SELECT pp.product_id id, pp.description name, sum(pp.qty) total FROM `pack_product` pp
                LEFT JOIN `pack` p ON pp.pack_id = p.id
                WHERE DATE(p.delivery_date) = '".date(Constants::MYSQL_DAY_FORMAT, strtotime($date))."' and p.employee_id = '".$employeeId."'
                GROUP BY pp.product_id
                UNION
                SELECT spp.product_id id, spp.description name, sum(spp.qty) total FROM `setting_pack_product` spp
                LEFT JOIN `setting_pack` sp ON spp.pack_id = sp.id
                WHERE sp.employee_id = '".$employeeId."'
                GROUP BY spp.product_id) s
                GROUP BY s.id
                ORDER BY s.id";
                
            $stmt = $db->query($sql);

            $result = $stmt->fetchAll();
            
            $packs = array();
            foreach ( $result as $row ) {
                $packs["id$row[id]"] =  $row;
            }

            return $packs;
        }


        public function whereAll($select){
            return $select;
        }
    }