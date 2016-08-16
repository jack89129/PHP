<?php
/**
 * Helper for retrieving base URL
 * 
 * @uses      Zend_View_Helper_Abstract
 * @package   Paste
 * @author    Matthew Weier O'Phinney <matthew@weierophinney.net> 
 * @copyright Copyright (C) 2008 - Present, Matthew Weier O'Phinney
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version   $Id: $
 */
class Jaycms_View_Helper_Paginate extends Zend_View_Helper_Abstract
{
	protected $_page = null;
	protected $_total = null;
	protected $_per_page = null;
	
    public function paginate($page, $total, $per_page){
         $this->_page = $page;
         $this->_total = $total;
         $this->_per_page = $per_page;
         return $this;
    }
    
    public function prevPage(){
    	return $this->_page - 1 > -1 ? $this->_page - 1 : null;
    }
    
    public function nextPage(){
    	return $this->_page + 1 > $this->totalPages() - 1 ? null : $this->_page + 1;
    }
    
    public function currentPage(){
    	return $this->_page;
    }
    
    public function totalPages(){
    	return ceil($this->_total/$this->_per_page);
    }
    
    public function pages($limit=5){
    	$limit = floor($limit/2);
    	$start = max(0, $this->_page - $limit);
    	$stop  = min($this->totalPages()-1, $this->_page + $limit);
    	return range($start, $stop);
    }
    
    public function html($link=null,$renderer=null){
		$link = $link ? $link : $_SERVER['REQUEST_URI'] . '?page={page}';
    	$renderer = $renderer ? $renderer : '_helpers/paginate.phtml';
    	return $this->view->partial($renderer, array('paginator' => $this, 'link' => $link));
    }
}
