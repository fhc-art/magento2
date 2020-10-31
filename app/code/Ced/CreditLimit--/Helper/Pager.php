<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CreditLimit
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CreditLimit\Helper;

class Pager
{
	
	/**
	 * Properties array
	 * @var array
	 * @access private
	 */
	
	private $_properties = array();
	
	/**
	 * Default configurations
	 * @var array
	 * @access public
	*/
	
	public $_defaults = array(
			'page' => 1,
			'perPage' => 5
	);
	
	public $pages;
	
	public function __construct(array $array=[], $currentPage = null, $per_page = null)
    {
      $this->array   = $array;
      $this->curPage = ($currentPage == null ? $this->defaults['page']    : $currentPage);
      $this->perPage = ($per_page == null ? $this->defaults['perPage'] : $per_page);
    }
    
   /**
    * 
    * @param unknown $name
    * @param unknown $value
    */
    
    public function __set($name, $value)
    {
    	$this->_properties[$name] = $value;
    }
    
   /**
    * 
    * @param string $name
    * @return multitype:|boolean
    */
    public function __get($name)
    {
    	if (array_key_exists($name, $this->_properties)) {
    		return $this->_properties[$name];
    	}
    	return false;
    }
    
    /**
     * 
     * @param unknown $firstAndLast
     */
    
    public function setShowFirstAndLast($firstAndLast)
    {
    	$this->_showFirstAndLast = $firstAndLast;
    }
    
   /**
    * 
    * @param unknown $mSeperator
    */
    
    public function setMainSeperator($mSeperator)
    {
    	$this->mainSeperator = $mSeperator;
    }
    
    /**
     * 
     * @return multitype:
     */
    
    public function getResults()
    {
    	
    	if (empty($this->curPage) !== false) {
    		$this->page = $this->curPage; 
    	} else {
    		$this->page = 1; 
    	}
    	$this->length = count($this->array);
    	$this->pages = ceil($this->length / $this->perPage);
    	$this->start = ceil(($this->page - 1) * $this->perPage);
    	return array_slice($this->array, $this->start, $this->perPage,true);
    }
    
   /**
    * 
    * @param array $params
    * @return void|string
    */
    
    public function getLinks($params = array())
    {
    	$pagelinks = array();
    	$links = array();
    	$slinks = array();
    
    	$queryUrl = '';
    	if (!empty($params) === true) {
    		unset($params['page']);
    		$queryUrl = '&amp;'.http_build_query($params);
    	}
    	if (($this->pages) > 1) {  		
    		if ($this->page != 1) {
    			if ($this->_showFirstAndLast) {
    				$pagelinks[] = ' <li class="item"><a class="page" href="?page=1'.$queryUrl.'">&laquo;&laquo; First </a> </li>';
    			}
    			$pagelinks[] = ' <li class="item"><a class="action  previous" href="?page='.($this->page - 1).$queryUrl.'"><span class="label">Page</span><span>&laquo; Prev</span></a></li>';
    		}
    		for ($j = 1; $j < ($this->pages + 1); $j++) {
    			if ($this->page == $j) {
    				$links[] = ' <li class="item current"><strong class="page"><span class="label">You are currently reading page</span><span>'.$j.'</span></strong></li> '; 
    			} else {
    				$links[] = ' <li class="item"><a href="?page='.$j.$queryUrl.'">'.$j.'</a></li> '; 
    			}
    		}
    		if ($this->page < $this->pages) {
    			$slinks[] = ' <li class="item"><a class="action next" href="?page='.($this->page + 1).$queryUrl.'"><span class="label">Page</span><span> Next &raquo;</span> </a></li> ';
    			if ($this->_showFirstAndLast) {
    				$slinks[] = ' <li class="item"><a href="?page='.($this->pages).$queryUrl.'"> Last &raquo;&raquo; </a></li> ';
    			}
    		}
    		return implode(' ', $pagelinks).implode($this->mainSeperator, $links).implode(' ', $slinks);
    	}
    	return;
    }
    
    /**
     * 
     * @return number
     */
    public function getCount(){
    	return count($this->array);
    }
}