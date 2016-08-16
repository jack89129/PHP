<?php
class ProductModel extends Jaycms_Db_Model {

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
	/**
	 * The default table name 
	 */
	protected $_name = 'product';	
	
	public function autocomplete($term, $limit=5){
		$limit = $limit < 1 ? 5 : $limit;

        $cacheId = md5('product_autocomplete_' . $term . $limit);
        $cache = Zend_Registry::get('cache');

        if( ($products = $cache->load($cacheId)) === false ){

            $query = "SELECT
                            p.*
                      FROM
                            product p
                            LEFT JOIN employee_product_map epm ON epm.product_id = p.id AND epm.employee_id = " . Utils::user()->id . "
                            LEFT JOIN right_employee_map erm ON erm.employee_id = " . Utils::user()->id . "
                            LEFT JOIN `right` r ON r.id = erm.right_id AND r.key = 'product_view_all'
                      WHERE
                            (
                                " . $this->getAdapter()->quoteInto('p.name LIKE ?', $term . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.article_code LIKE ?', $term . '%') . "
                            )
                            AND
                            p.deleted = 0
                            AND
                            (
                                epm.id IS NOT NULL
                                OR
                                r.id IS NOT NULL
                            )


                      GROUP BY p.id
                      ORDER BY natsort_save(p.name, 'natural') ASC
                      LIMIT 0, $limit
                      ";

            $products =	$this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
            foreach( $products as $key => $product ){
                $p = new Product($product->id);
                if( $p->income_tag ){
                    if( !$product->vat ){
                        $products[$key]->vat = $p->income_tag->vat !== null ? $p->income_tag->vat : $p->income_tag->category->vat;
                    }
                    $products[$key]->income_category_path = $p->income_tag->category->name . ' - ' . $p->income_tag->name;
                }else{
                    $products[$key]->income_category_path = '';
                }

                if( $p->expense_tag ){
                    if( !$product->vat ){
                        $products[$key]->vat = $p->expense_tag->vat !== null ? $p->expense_tag->vat : $p->expense_tag->category->vat;
                    }
                    $products[$key]->expense_category_path = $p->expense_tag->category->name . ' - ' . $p->expense_tag->name;
                }else{
                    $products[$key]->expense_category_path = '';
                }

                if( !isset($products[$key]->vat) ){
                    $products[$key]->vat = reset(Constants::$VATS);
                }
            }

            $cache->save($products, null, array(), 60*2);
        }

        return $products;
	}

    /**
     * @param int $contact
     * @param int $limit
     * @return Product[]
     */
    public function topProducts($contact=0, $limit=10){

        $query =    "
                        SELECT
                                p.*

                        FROM
                              invoice i
                              LEFT JOIN invoice_product ip ON ip.invoice_id = i.id
                              LEFT JOIN product p ON p.id = ip.product_id
                        WHERE
                    ";

        $wheres = array();

        if( $contact ){
            $wheres[] = $this->getAdapter()->quoteInto("i.contact_id = ?", $contact);
        }

        $wheres[] = "p.id IS NOT NULL";
        $wheres[] = "p.deleted = 0";

        $query .= implode("\nAND\n", $wheres) . "\n";
        $query .= "GROUP BY p.id        \n";
        $query .= "ORDER BY COUNT(ip.id) DESC, natsort_save(p.name, 'natural') ASC \n";
        $query .= "LIMIT " . ((int) $limit);

        $products = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $products as $key => $product ){
            $products[$key] = new Product();
            $products[$key]->load($product);
        }

        return $products;
    }

    public function search($search){
        $query = "
                    SELECT
                            p.*
                    FROM
                            product p
                            LEFT JOIN tag it ON it.id = p.income_tag_id
                            LEFT JOIN tag et ON et.id = p.expense_tag_id
                            LEFT JOIN tag_category itc ON itc.id = it.tag_category_id
                            LEFT JOIN tag_category etc ON etc.id = et.tag_category_id

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('p.name LIKE ?',       '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.order_code LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.short_description LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.long_description LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.article_code LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('it.name LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('et.name LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('itc.name LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('etc.name LIKE ?', '%' . $search . '%') . "
                            )

                    ORDER BY p.name DESC
                    LIMIT 20
                 ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $row ){
            $result[$key] = new Product();
            $result[$key]->load($row);
        }

        return $result;
    }

    public function getUsageResult(){
        $query =    "
                        SELECT
                                p.*,
                                p.stock + IFNULL(SUM(es.transit), 0) + IFNULL(SUM(es.reservation), 0) as cnt_all,
                                p.stock as cnt_stock,
                                IFNULL(SUM(es.transit), 0) as cnt_transit,
                                IFNULL(SUM(es.reservation), 0) as cnt_reservation,
                                IFNULL(CEIL(IF( p.stock < p.min_stock, 0, (100 / (p.stock / p.min_stock)))), 0) as cnt_usage
                        FROM
                                product p
                                LEFT JOIN employee_stock es ON es.product_id = p.id

                        WHERE
                                p.deleted = 0

                        GROUP BY p.id
                        ORDER BY cnt_usage ASC
                    ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        return $result;
    }
    
    public function getWebshopProducts(){
        $db = Zend_Registry::get('db1');
            
        $sql = "
            SELECT p.*, pg.name group_name
            FROM `product` p
            LEFT JOIN `product_group` pg ON p.product_group_id = pg.id
            WHERE p.has_webshop = 1
            ORDER BY pg.id, p.id";
            
        $stmt = $db->query($sql);

        $result = $stmt->fetchAll();

        return $result;
    }
	
}

