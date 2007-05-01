<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

/**
 * EventList venues Model class
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_venues extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id 				= null;
	/** @var string */
	var $venue 				= null;
	/** @var string */
	var $alias	 			= null;
	/** @var string */
	var $url 				= null;
	/** @var string */
	var $street 			= null;
	/** @var string */
	var $plz 				= null;
	/** @var string */
	var $city 				= null;
	/** @var string */
	var $state				= null;
	/** @var string */
	var $country			= null;
	/** @var string */
	var $locdescription 	= null;
	/** @var string */
	var $meta_description 	= null;
	/** @var string */
	var $meta_keywords		= null;
	/** @var string */
	var $locimage 			= null;
	/** @var int */
	var $created_by			= null;
	/** @var string */
	var $author_ip	 		= null;
	/** @var date */
	var $created		 	= null;
	/** @var date */
	var $modified 			= null;
	/** @var int */
	var $modified_by 		= null;
	/** @var int */
	var $published	 		= null;
	/** @var int */
	var $checked_out 		= null;
	/** @var date */
	var $checked_out_time 	= null;
	/** @var int */
	var $ordering 			= null;

	function eventlist_venues(& $db) {
		parent::__construct('#__eventlist_venues', 'id', $db);
	}

	// overloaded check function
	function check($elsettings)
	{
		// not typed in a venue name
		if(!trim($this->venue)) {
	      	$this->_error = JText::_( 'ADD VENUE');
	       	return false;
		}

		jimport('joomla.filter.output');
		$alias = JOutputFilter::stringURLSafe($this->venue);

		if(empty($this->alias) || $this->alias === $alias ) {
			$this->alias = $alias;
		}

		if ( $elsettings->showcity == 1 ) {
			if(!trim($this->city)) {
        		$this->_error = JText::_( 'ADD CITY');
        		return false;
			}
		}

		if (($elsettings->showmapserv == 1 ) && ($elsettings->showdetailsadress == 1 )){
			if ((!trim($this->street)) || (!trim($this->plz)) || (!trim($this->city)) || (!trim($this->country))) {
				$this->_error = JText::_( 'ADD ADDRESS');
				return false;
			}
		}

		if (trim($this->url)) {
			$this->url = strip_tags($this->url);
			$urllength = strlen($this->url);

			if ($urllength > 150) {
      			$this->_error = JText::_( 'ERROR URL LONG' );
      			return false;
			}
			if (!preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'
       		.'((:[0-9]{1,5})?\/.*)?$/i' , $this->url)) {
				$this->_error = JText::_( 'ERROR URL WRONG FORMAT' );
				return false;
			}
		}

		$this->street = strip_tags($this->street);
		$streetlength = JString::strlen($this->street);
		if ($streetlength > 50) {
     	 	$this->_error = JText::_( 'ERROR STREET LONG' );
     	 	return false;
		}

		$this->plz = strip_tags($this->plz);
		$plzlength = JString::strlen($this->plz);
		if ($plzlength > 10) {
      		$this->_error = JText::_( 'ERROR ZIP LONG' );
      		return false;
		}

		$this->city = strip_tags($this->city);
		$citylength = JString::strlen($this->city);
		if ($citylength > 50) {
    	  	$this->_error = JText::_( 'ERROR CITY LONG' );
    	  	return false;
		}

		$this->state = strip_tags($this->state);
		$statelength = JString::strlen($this->state);
		if ($statelength > 50) {
    	  	$this->_error = JText::_( 'ERROR STATE LONG' );
    	  	return false;
		}

		$this->country = strip_tags($this->country);
		$countrylength = JString::strlen($this->country);
		if ($countrylength > 2) {
     	 	$this->_error = JText::_( 'ERROR COUNTRY LONG' );
     	 	return false;
		}

		return true;
	}
}
?>