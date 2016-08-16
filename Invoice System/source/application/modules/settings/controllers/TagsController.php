<?php

    class Settings_TagsController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Categorieen");
            $this->view->page_sub_title = _t("Overzicht, categorieen en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            $this->_redirect('/settings/contact/index');             
            $categories = new TagCategory();
            $categories = $categories->findAll(array(), array('natsort(id, "natural") ASC'));

            if( !Utils::user()->can('settings_tags_view') ){
                throw new Exception(_t('Access denied!'));
            }

            Utils::activity('index', 'settings-tags');

            $this->view->categories = $categories;
            //$this->view->categories = array();
        }

        public function updateAction(){
            $categories = new TagCategory();
            $categories = $categories->findAll(array(), array('natsort(id, "natural") ASC'));

            if( !Utils::user()->can('settings_tags_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            $result = array();
            $result['categories_list'] = $this->view->partial('tags/_partials/categories-list.phtml', array('categories' => $categories));
            $result['categories'] = $this->view->partial('tags/_partials/categories.phtml', array('categories' => $categories));
            $result['tag_dialog'] = $this->view->partial('tags/_partials/tag-dialog.phtml', array('categories' => $categories));

            $this->_helper->json($result);
        }

        public function addCategoryAction(){
            $categoryParam = $this->_getParam('tag_category');

            if( !Utils::user()->can('settings_tags_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            if( !$categoryParam['name'] ){
                throw new Exception(_t("Enter category name!"));
            }

            $category = new TagCategory($categoryParam['id']);
            $category->id = $categoryParam['id'];
            $category->name = $categoryParam['name'];
            $category->vat = $categoryParam['vat'];
            $category->type = $categoryParam['type'];
            $category->save();

            if( $categoryParam['id'] ){
                Utils::activity('edit-category', 'settings-tags');
            }else{
                Utils::activity('add-category', 'settings-tags');
            }

            $this->_forward('update');
        }

        public function removeCategoryAction(){
            $id = $this->_getParam('id');

            if( !Utils::user()->can('settings_tags_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            $category = new TagCategory($id);

            if( !$category->exists() ){
                throw new Exception(_t("Category not found!"));
            }

            if( $category->tags ){
                throw new Exception(_t("Category have tags. Please remove them to be able to delete this category!"));
            }

            $category->delete();

            Utils::activity('delete-category', 'settings-tags');

            $this->_forward('update');
        }

        public function addTagAction(){
            $tagParam = $this->_getParam('tag');

            if( !Utils::user()->can('settings_tags_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            if( !$tagParam['name'] ){
                throw new Exception(_t('Enter tag name!'));
            }

            if( !$tagParam['tag_category_id'] ){
                throw new Exception(_t("Select category!"));
            }

            $tag = new Tag($tagParam['id']);
            $category = new TagCategory($tagParam['tag_category_id']);

            if( !$category->exists() ){
                throw new Exception(_t("Category not found!"));
            }

            $tag->id = $tagParam['id'] ? $tagParam['id'] : null;
            $tag->name = $tagParam['name'];
            $tag->tag_category_id = $tagParam['tag_category_id'];
            $tag->vat = $tagParam['vat'];
            $tag->number = $tagParam['number'];
            $tag->vat_category_id = $tagParam['vat_category_id'] ? $tagParam['vat_category_id'] : null ;
            $tag->save();

            if( $tagParam['id'] ){
                Utils::activity('edit-tag', 'settings-tags', $tag->id);
            }else{
                Utils::activity('add-tag', 'settings-tags', $tag->id);
            }

            $this->_forward('update');
        }

        public function removeTagAction(){
            $id = $this->_getParam('id');

            if( !Utils::user()->can('settings_tags_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            $tag = new Tag($id);

            if( !$tag->exists() ){
                throw new Exception(_t("Tag not found!"));
            }

            $tag->delete();
            Utils::activity('delete-tag', 'settings-tags', $tag->id);

            $this->_forward('update');
        }

        public function initTagAction(){
            $id = $this->_getParam('id');
            $category_id = $this->_getParam('category_id');

            $category = new TagCategory($category_id);
            if( !$category->exists() ){
                throw new Exception(_t("Category not found!"));
            }

            $vatCategories = array();
            if( in_array($category->type, array(TagCategoryModel::TYPE_INVOICE, TagCategoryModel::TYPE_PURCHASE)) ){
                $vatCategory = new VatCategory();
                $vatCategories = $vatCategory->findAll(array(array('type = ?', $category->type)), array('ord ASC'));
            }

            $tag = new Tag($id);
            $tag->tag_category_id = $category_id;
            
            $vatArray = array();
            foreach ( $vatCategories as $vatCat ) {
                $vatArray[] = (object)$vatCat->data();
            }
            
            $vats = array(0, 6, 12, 21);

            $result = array('tag' => (object)$tag->data(), 'vat_categories' => $vatArray, 'vats' => $vats);
            $this->_helper->json($result);
        }
        
		public function preDispatch() {
			$results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
			
			if ($results) {
				$products = array();
				$minstock_products = new Zend_Session_Namespace('min_stock_products');
				if (!$minstock_products->id_list) $minstock_products->id_list = array();
				
				foreach ($results as $product) {
					if (in_array($product->id, $minstock_products->id_list)) continue;
					$products[] = $product;
				}
				
				if ($products) $this->view->show_box = true;
				
				$this->view->minstock_products = $results;
			}
		}
    }