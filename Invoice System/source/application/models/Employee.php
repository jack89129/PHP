<?php

    /**
     * @property int $id
     * @property string $firstname
     * @property string $lastname
     * @property string $address
     * @property string $postcode
     * @property string $city
     * @property string $country
     * @property string $email_address
     * @property string $phone
     * @property string $role
     * @property string $username
     * @property string $password
     *
     * @property EmployeeGroup[] $groups
     * @property Contact[] $contacts
     * @property Right[] $rights
     * @property EmployeeStock[] $stocks
     * @property Product[] $products
     */
    class Employee extends Core_ActiveRecord_Row {

        const PRODUCT_TRANSIT = 'transit';
        const PRODUCT_RESERVATION = 'reservation';

        public function __construct($id=null){
            parent::__construct(new EmployeeModel(), $id);
        }

        public function relations(){
            return array(
                'groups' => array('EmployeeGroup', array('EmployeeGroupMap' => array('employee_id', 'employee_group_id')), self::HAS_MANY_TO_MANY, array(), array('natsort(name, "natural") ASC')),
                'contacts' => array('Contact', array('ContactEmployeeMap' => array('employee_id', 'contact_id')), self::HAS_MANY_TO_MANY, array(), array('firstname ASC')),
                'rights' => array('Right', array('RightEmployeeMap' => array('employee_id', 'right_id')), self::HAS_MANY_TO_MANY),
                'stocks' => array('EmployeeStock', 'employee_id', self::HAS_MANY),
                'p_products' => array('Product', array('EmployeeProductMap' => array('employee_id', 'product_id')), self::HAS_MANY_TO_MANY)
            );
        }

        public function getName(){
            return trim($this->firstname . ' ' . $this->lastname);
        }

        public static function saltPassword($password){
            $config = Zend_Registry::get('config');
            return sha1($password . $config['PASSWD_SALT']);
        }

        public function can($rightKey){
            foreach( $this->rights as $right ){
                if( $right->key == $rightKey || $right->key == 'super' ){
                    return true;
                }
            }

            return false;
        }

        public function addStock($id, $count, $state){
            $existing = null;
            foreach( $this->stocks as $product ){
                if( $product->product_id == $id ){
                    $existing = $product->id;
                }
            }

            $employeeStock = new EmployeeStock($existing);
            $employeeStock->employee_id = $this->id;
            $employeeStock->product_id = $id;

            if( $state == Employee::PRODUCT_RESERVATION ){
                if( $count >= 0 ){
                    $employeeStock->reservation += $count;
                    $employeeStock->product->stock -= $count;
                    $employeeStock->product->save();
                }elseif( $employeeStock->reservation >= abs($count) ){
                    $employeeStock->reservation -= abs($count);
                    $employeeStock->product->stock += abs($count);
                    $employeeStock->product->save();
                }
            }

            if( $state == Employee::PRODUCT_TRANSIT ){
                if( $count >= 0 ){
                    $removeStock = 0;
                    if( $employeeStock->reservation - $count >= 0 ){
                        $employeeStock->reservation -= $count;
                        $employeeStock->transit += $count;
                    }else{
                        $removeStock = abs($employeeStock->reservation - $count);
                        $employeeStock->reservation = 0;
                    }

                    if( $removeStock ){
                        if( $employeeStock->product->stock < $removeStock ){
                            throw new Exception(_t("Not enough products in stock!"));
                        }
                        $employeeStock->product->stock -= $removeStock;
                        $employeeStock->product->save();

                        $employeeStock->transit += $removeStock;
                    }

                    $employeeStock->save();
                }else{
                    $count = abs($count);
                    if( $employeeStock->transit < $count ){
                        throw new Exception(_t("Not enough products in stock!"));
                    }

                    $employeeStock->product->stock += $count;
                    $employeeStock->product->save();

                    $employeeStock->transit -= $count;
                    $employeeStock->save();
                }
            }

            $employeeStock->save();
        }

        public function removeStock($id, $count){
            $existing = null;
            $p = new Product($id);

            if( !$p->exists() ){
                throw new Exception(_t("Product not found!"));
            }

            foreach( $this->stocks as $product ){
                if( $product->product_id == $id ){
                    $existing = $product->id;
                }
            }

            $employeeStock = new EmployeeStock($existing);
            $employeeStock->employee_id = $this->id;
            $employeeStock->product_id = $id;

            if( $employeeStock->transit < $count ){
                throw new Exception(_t("Not enough products in stock for %s need %s have %s !", array($p->name, $count, $employeeStock->transit)));
            }

            $employeeStock->transit -= $count;
            $employeeStock->save();
        }

        public function getProducts(){
            if( Utils::user()->can('product_view_all') ){
                return Product::all();
            }

            return $this->p_products;
        }
    }