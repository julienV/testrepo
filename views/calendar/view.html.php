<?php
/**
 * @version 1.1 $Id: view.html.php 700 2008-06-23 10:25:10Z julienv $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2008 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'categories.class.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'calendar.class.php');

/**
 * HTML View class for the Categoryevents View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCalendar extends JView
{
	/**
	 * Creates the Categoryevents View
	 *
	 * @since 0.9
	 */
	function display( $tpl=null )
	{
		global $mainframe, $option;

    // Load tooltips behavior
    JHTML::_('behavior.tooltip');
        
		//initialize variables
		$document 	= & JFactory::getDocument();
		$menu		= & JSite::getMenu();
		$elsettings = & ELHelper::config();
		$item    	= $menu->getActive();
		$params 	= & $mainframe->getParams();
		$uri 		= & JFactory::getURI();
		$pathway 	= & $mainframe->getPathWay();
		
		//add css file
		$document->addStyleSheet($this->baseurl.'/components/com_eventlist/assets/css/eventlist.css');
    $document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #eventlist dd { height: 1%; }</style><![endif]-->');
    $document->addStyleSheet($this->baseurl.'/components/com_eventlist/assets/css/eventlistcalendar.css');
    // add javascript
    $document->addScript( $this->baseurl.'/components/com_eventlist/assets/js/calendar.js' );

		
		$task 			= JRequest::getWord('task');
		
    $year  = intval( JRequest::getVar('yearID', strftime( "%Y" ) ));
    $month = intval( JRequest::getVar('monthID', strftime( "%m" ) ));
    $day   = intval( JRequest::getVar('dayID', strftime( "%d" ) ));

		//get data from model
		$model = & $this->getModel();
		$model->setDate( mktime( 0, 0, 1, $month, $day, $year) );
		
		$rows 		= & $this->get('Data');
		$category 	= & $this->get('Category');
		$categories	= & $this->get('Categories');

		//are events available?
		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

		//does the category exist
		
		if (!$category) // display all
		{
			//return JError::raiseError( 404, JText::sprintf( 'Category #%d not found', $category->id ) );
			$category = new stdclass();
			$category->id = 0;
			$category->catname = JText::_('All categories');
			$category->meta_keywords = '';
			$category->meta_description = '';
			$category->slug = '0';
		}

		//Set Meta data
		$document->setTitle( $item->name.' - '.$category->catname );
          
    $document->setMetadata( 'keywords', $category->meta_keywords );
    $document->setDescription( strip_tags($category->meta_description) );
    
    //Set Page title    
    $pagetitle = $params->def( 'page_title', $item->name);
    $mainframe->setPageTitle( $pagetitle );
    $mainframe->addMetaTag( 'title' , $pagetitle );

		//create the pathway
		$cats		= new eventlist_cats($category->id);
		$parents	= $cats->getParentlist();

		foreach($parents as $parent) {
			$pathway->addItem( $this->escape($parent->catname), JRoute::_('index.php?view=calendar&id='.$parent->categoryslug));
		}

		//create select lists
		$lists	= $this->_buildFilterLists($elsettings);
		$this->assign('lists', 						$lists);
		$this->assign('action', 					$uri->toString());

		$this->assignRef('rows' , 				$rows);
		$this->assignRef('noevents' , 		$noevents);
		$this->assignRef('category' , 		$category);
		$this->assignRef('params' , 			$params);
    $this->assignRef('pagetitle' ,    $pagetitle);
		$this->assignRef('task' , 				$task);
		$this->assignRef('elsettings' , 	$elsettings);
		$this->assignRef('item' , 				$item);
		$this->assignRef('categories' , 	$categories);

    $this->assignRef('year' ,           $year);
    $this->assignRef('month' ,           $month);
    $this->assignRef('day' ,           $day);

		parent::display($tpl);
	}

	/**
	 * Manipulate Data
	 *
	 * @since 0.9
	 */
	function &getRows()
	{
		$count = count($this->rows);

		if (!$count) {
			return;
		}
		
		$k = 0;
		foreach($this->rows as $key => $row)
		{
			$row->odd   = $k;
			
			$this->rows[$key] = $row;
			$k = 1 - $k;
		}

		return $this->rows;
	}

	function _buildFilterLists($elsettings)
	{
		$filter				= JRequest::getString('filter');
		$filter_type		= JRequest::getString('filter_type');

		$lists['filter'] 		= $filter;
		$lists['filter_type'] 	= $filter_type;

		return $lists;
	}
}
?>