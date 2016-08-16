<?php

    abstract class Core_ActiveRecord_Row {

    	const HAS_ONE = 1;
    	const HAS_MANY = 2;
        const HAS_MANY_TO_MANY = 4;

        protected static $_globalStrict;

        /**
         * @var Zend_Db_Table_Abstract
         */
        private $_pk;
        protected $_model = null;
        protected $_data = array();
        protected $_relationsCache = array();
        protected $_strict = false;

        public function __construct(Zend_Db_Table_Abstract $model, $id=null){
            $this->_model = $model;

            if( $id ){
            	$this->findByPk($id);
            }

            $this->_strict = self::getGlobalStrict();
        }

        /**
         * Gets field value first tries to call getFieldName()
         *
         * @param string $property
         * @return mixed|null
         * @throws Exception
         */
        public function __get($property){
        	
        	# method call
        	$method = 'get' . implode('', array_map('ucfirst', preg_split('@[-_]@', $property)));
        	if( method_exists($this, $method) ){
        		return call_user_func_array(array($this, $method), array());
        	}

            # relations cached
            if( array_key_exists($property, $this->_relationsCache) ){
                return $this->_relationsCache[$property];
            }

        	# relations call
        	if( array_key_exists($property, $this->relations()) ){
        		$relations = $this->relations();
        		@list($class, $field, $type, $wheres, $orders) = $relations[$property];
                $wheres = $wheres ? $wheres : array();
                $orders = $orders ? $orders : array();

        		$object = new $class;
        		
        		switch( $type ){
        			case self::HAS_ONE:
                        if( $this->{$field} === null ){
                            return null;
                        }

        				$relation = $object->findByPk($this->{$field}, $wheres, $orders);
                        return $this->_relationsCache[$property] = $relation;
        				break;
        				
        			case self::HAS_MANY:
                        if( $this->getPk() === null ){
                            return array();
                        }

        				$relation = $object->findByColumn($field, $this->getPk(), $wheres, $orders);
                        return $this->_relationsCache[$property] = $relation;
        				break;

                    case self::HAS_MANY_TO_MANY :
                        if( $this->getPk() === null ){
                            return array();
                        }

                        $relClass = reset(array_keys($field));
                        $relClass = new $relClass;
                        $fields = reset(array_values($field));

                        $maps = $relClass->findByColumn($fields[0], $this->getPk());
                        $ids = array();

                        foreach($maps as $map ){
                            $ids[] = $map->{$fields[1]};
                        }

                        $relation = array();

                        if( $ids ){
                            $relation = $object->findAll(array(array($object->pk() . ' IN (?)', $ids)), $orders);
                        }

                        return $this->_relationsCache[$property] = $relation;
                        break;
        				
        			default:
        				throw new Exception("Unknown relation type!");
        				break;
        		}

        		return null;
        	}
        	
        	# property
            return $this->get($property);
        }

        /**
         * Sets field value first tries to call setFieldName($value) method
         *
         * @param string $property
         * @param mixed $value
         * @return void
         */
        public function __set($property, $value){
            # method call
            $method = 'set' . implode('', array_map('ucfirst', preg_split('@[-_]@', $property)));
            if( method_exists($this, $method) ){
                return call_user_func_array(array($this, $method), array($value));
            }

            $this->set($property, $value);
        }

        /**
         * Used to get value field value instance which can be manipulated
         *
         * @param string $method
         * @param array $arguments
         * @return mixed|null
         * @throws Exception
         */
        public function __call($method, $arguments){
            if( array_key_exists($method, $this->_data) ){
                return new Core_ActiveRecord_Row_Value($this, $this->_data[$method]);
            }

            throw new Exception("Trying to call undefined method " . get_class($this) . "::" . $method . " !");
        }

        /**
         * Get property value without calling get methods
         *
         * @param string $property
         * @return mixed
         * @throws Exception
         */
        protected function get($property){
            if( $this->_strict && !array_key_exists($property, $this->_data) ){
                throw new Exception("Trying to access undefined property!");
            }

            return array_key_exists($property, $this->_data) ? $this->_data[$property] : null ;
        }

        /**
         * Sets field value
         *
         * @param string $property
         * @param mixed $value
         * @return mixed
         * @throws Exception
         */
        protected function set($property, $value){
            if( $this->_strict && !array_key_exists($property, $this->_data) ){
                throw new Exception("Trying to set undefined property!");
            }

            return ($this->_data[$property] = $value);
        }

        /**
         * Adds relation to runtime relations it's useful when you need instance
         * without having to store it to database
         *
         * @param string $relation - Relation name
         * @param string $instance - Instance of the relation record
         * @throws Exception
         */
        protected function add($relation, $instance){
            if( !array_key_exists($relation, $this->relations()) ){
                throw new Exception('Trying to add to unexistent relation!');
            }

            if( !array_key_exists($relation, $this->_relationsCache) ){
                $this->_relationsCache[$relation] = array();
            }

            $this->_relationsCache[$relation][] = $instance;
        }

        protected function applyWheres(Zend_Db_Select $select, $wheres){
            foreach( $wheres as $where ){
                @list( $condition, $value, $type) = $where;

                if( $type == 'or' ){
                    $select->orWhere($condition, $value);
                }else{
                    $select->where($condition, $value);
                }
            }
            return $select;
        }

        protected function applyOrders(Zend_Db_Select $select, $orders){
            foreach( $orders as $order ){
                $select->order( $order );
            }
            return $select;
        }

        /**
         * Get name of the primary key column
         *
         * @return string Name of the primary key column
         */
        public function pk(){
            if( $this->_pk === null ){
                $info = $this->_model->info(Zend_Db_Table_Abstract::PRIMARY);
                $this->_pk = reset($info);
            }

            return $this->_pk;
        }

        /**
         * Get all data directly
         *
         * @return array
         */
        public function data(){
            return $this->_data;
        }

        /**
         * Get value of primary key
         *
         * @return mixed|null Value of the primary key
         */
        public function getPk(){
        	return $this->{$this->pk()};
        }

        /**
         * Find row by primary key value
         *
         * @param int|string $id
         * @return self
         */
        public function findByPk($id, $wheres=array(), $orders=array()){
            $select = $this->_model->select()->where($this->_model->getAdapter()->quoteIdentifier($this->pk()) . '=?', $id);
            $this->applyWheres($select, $wheres);
            $this->applyOrders($select, $orders);
            $row = $select->limit(1)->query(Zend_Db::FETCH_ASSOC)->fetch();
            
            if( !$row ){
                $this->_data = array();
            	return null;
            }

            $this->load($row);
            return $this;
        }

        /**
         * @param null|string|array $relations
         */
        public function clearRelations($relations=null){
            if( $relations === null ){
                $this->_relationsCache = array();
            }

            if( is_scalar($relations) ){
                unset($this->_relationsCache[$relations]);
            }

            if( is_array($relations) ){
                foreach( $relations as $relation ){
                    unset($this->_relationsCache[$relation]);
                }
            }
        }

        /**
         * Returns array of matched rows as instances
         *
         * @param string $column
         * @param mixed $value
         * @return self[]
         */
        public function findByColumn($column, $value, $wheres=array(), $orders=array()){
        	$select = $this->_model->select()->where($this->_model->getAdapter()->quoteIdentifier($column) . '=?', $value);
            $this->applyWheres($select, $wheres);
            $this->applyOrders($select, $orders);
            $result = $select->query(Zend_Db::FETCH_ASSOC)->fetchAll();
        	
        	foreach( $result as $key => $row ){
        		$object = new $this;
        		$object->load($row);
        		$result[$key] = $object;	
        	}
        	
        	return $result;
        }

        /**
         * Returns array of all rows as instances
         *
         * @return self[]
         */
        public function findAll($wheres=array(), $orders=array()){
            $select = $this->_model->select();
            $this->applyWheres($select, $wheres);
            $this->applyOrders($select, $orders);
            $result = $select->query(Zend_Db::FETCH_ASSOC)->fetchAll();

            foreach( $result as $key => $row ){
                $object = new $this;
                $object->load($row);
                $result[$key] = $object;
            }

            return $result;
        }

        /**
         * Updates or inserts all fields depending on primary key
         * The primary key field is updated with new id in case of insert
         *
         * @param string|array $only - Save only this field(s)
         * @return void
         */
        public function save($only=array()){
        	$data = $this->_data;

            if( is_string($only) ){
                $only = array($only);
            }

        	foreach( $only as $key => $val ){
        		if( $key != $this->pk() && array_key_exists($key, $data) ){
        			unset($data[$key]);
        		}
        	}
        	
        	if( !array_key_exists($this->pk(), $data) || !$data[$this->pk()] ){
        		$this->{$this->pk()} = $this->_model->insert($data);
        	}else{
        		$this->_model->update($data, $this->_model->getAdapter()->quoteInto( $this->pk() . '=?', $data[$this->pk()]));
        	}

            $this->_relationsCache = array();
        }

        /**
         * Deletes a record by primary key
         */
        public function delete(){
            $this->_model->delete($this->_model->getAdapter()->quoteInto($this->pk() . '=?', $this->{$this->pk()}));
        }

        /**
         * Checks that loaded record exists
         *
         * @return bool
         */
        public function exists(){
        	return isset($this->_data[$this->pk()]);
        }

        /**
         * Replaces the all fields with provided data
         *
         * @param array $data
         */
        public function load($data){
        	$this->_data = (array) $data;
        }

        /**
         * @return Zend_Db_Table_Abstract
         */
        public function model(){
            return $this->_model;
        }

        /**
         * Used to get relations from child class
         * This method should be overrided in child class if there are relations
         *
         * @return array
         */
        public function relations(){
        	return array();
        }

        /**
         * Get/Set strict mode
         *
         * Enable/Disable strict mode for fields
         * In strict mode fields can be set only before parent::__construct is called
         * after that no new fields can be set
         *
         * @param bool $strict
         * @return bool
         */
        public function strict($strict=null){
            if( $strict !== null ){
                $this->_strict = (bool) $strict;
            }
            return $this->_strict;
        }

        /**
         * Gets global strict mode
         *
         * @see $this->strict()
         * @return bool
         */
        public static function getGlobalStrict(){
            return self::$_globalStrict;
        }

        /**
         * Sets global strict mode
         *
         * @see $this->strict()
         * @return bool
         */
        public static function setGlobalStrict($strict){
            self::$_globalStrict = (bool) $strict;
        }

        /*
         * Clones the object with primary key set to null
         *
         * Example:
         *
         * $post = new Post(1);
         * $clone = clone $post;
         * $clone->title = 'My cloned post'
         * $clone->save();
         *
         */
        public function __clone(){
           $this->{$this->pk()} = null;
        }

        public function toTree($skip=array()){
            $result = (array) $this->data();
            foreach( $this->relations() as $property => $relation ){
                if( in_array($property, $skip) ){
                    continue;
                }

//                if( is_object($this->{$property}) ){
//                    $result[$property] = $this->{$property}->toTree();
//                }else{
//                    $result[ $property ] = $this->{$property};
//                }
            }
            return $result;
        }
    }