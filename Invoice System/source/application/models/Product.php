<?php 

	/**
	 * @property int $id	 
	 * @property string $name	 
	 * @property int $product_group_id
     * @property int $wholesaler_id
     * @property string $order_code
	 * @property string $short_description	 
	 * @property string $long_description	 
	 * @property string $article_code	 
	 * @property int $discount
	 * @property float $price	 
	 * @property float $min_price	 
	 * @property float $cost_price
     * @property int $vat
	 * @property float $weight
	 * @property int $stock
     * @property int $min_stock
     * @property int $last_stock
     * @property string $status
     * @property int $income_tag_id
     * @property int $expense_tag_id
     * @property int $deleted
     * @property int $has_new_price
     * @property float $new_price
     * @property int $new_discount
     * @property int $new_vat
     *
     * @property int $transit_count
     * @property int $reservation_count
     * @property int $employees_count
     * @property int $stock_all_count
     * @property int $calculated_vat
     *
	 * @property ProductGroup[] $group
     * @property Tag $income_tag
     * @property Tag $expense_tag
     * @property Wholesaler $wholesaler
     * @property Employees[] $employees
	 */
	class Product extends Core_ActiveRecord_Row {
		
		public function __construct($id=null){
            $this->id = null;
            $this->name = null;
            $this->short_description = null;
            $this->long_description = null;
            $this->article_code = null;
            $this->discount = 0;
            $this->price = 0.0;
            $this->min_price = 0.0;
            $this->cost_price = 0.0;
            $this->vat = 0;
            $this->weight = 0.0;
            $this->stock = 0;
            $this->has_webshop = 0;
            $this->status = ProductModel::STATUS_ACTIVE;
            $this->deleted = 0;
            $this->main_img = "";
            $this->sub_img1 = "";
            $this->sub_img2 = "";
            $this->sub_img3 = "";
			parent::__construct(new ProductModel(), $id);
		}

        public function relations(){
            return array(
                'group' => array('ProductGroup', 'product_group_id', self::HAS_ONE),
                'income_tag' => array('Tag', 'income_tag_id', self::HAS_ONE),
                'expense_tag' => array('Tag', 'expense_tag_id', self::HAS_ONE),
                'wholesaler' => array('Wholesaler', 'wholesaler_id', self::HAS_ONE),
                'employees' => array('Employee', array('EmployeeProductMap' => array('product_id', 'employee_id')), self::HAS_MANY_TO_MANY)
            );
        }

        public function isActive(){
            return $this->status == ProductModel::STATUS_ACTIVE;
        }

        public function isInactive(){
            return $this->status == ProductModel::STATUS_INACTIVE;
        }

        public function isDeleted(){
            return (bool) $this->deleted;
        }

        public function getTransitCount(){
            $employeeStockModel = new EmployeeStockModel();
            return $employeeStockModel->getProductTransitCount($this->id);
        }

        public function getReservationCount(){
            $employeeStockModel = new EmployeeStockModel();
            return $employeeStockModel->getProductReservationCount($this->id);
        }

        public function getEmployeesCount(){
            $employeeStockModel = new EmployeeStockModel();
            return $employeeStockModel->getProductEmployeesCount($this->id);
        }

        public function getStockAllCount(){
            return $this->stock + $this->transit_count + $this->reservation_count;
        }

        public function getCalculatedVat(){
            $vat = $this->vat;
            $vat = !$vat && $this->incomet_tag ? $this->income_tag->vat : $vat ;
            $vat = !$vat && $this->income_tag && $this->income_tag->category && $this->income_tag->category->vat ? $this->income_tag->category->vat : $vat;
            $vat = !$vat ? 21 : $vat;
            return $vat;
        }

        public static function all($where=array(array('deleted = ?', 0)), $sort=array('natsort(name, "natural") ASC')){
            $product = new Product();
            return $product->findAll($where, $sort);
        }
	}