<?php
/**
 * @version 0.9 $Id$
 * @package Joomla 
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * View class for the EventList attendees screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewAttendees extends JView {
	
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		//initialise variables
		$db			= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();
		$elsettings = & ELAdmin::config();
		$submenu 	= ELAdmin::submenu();
		$document	= & JFactory::getDocument();
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.attendees.filter_order", 		'filter_order', 	'r.urname' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.attendees.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.attendees.filter", 'filter', '' );
		$filter 			= intval( $filter );
		$search 			= $mainframe->getUserStateFromRequest( "$option.attendees.search", 'search', '' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$live_site 			= $mainframe->getCfg('live_site');
		
		//add css and submenu to document
		$document->setBuffer($submenu, 'module', 'submenu');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		//add toolbar
		JMenuBar::title( JText::_( 'REGISTERED USERS' ), 'users' );
		JMenuBar::deleteList( ' ', 'remove', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::back();
		JMenuBar::spacer();
		JMenuBar::help( 'el.registereduser', true );
		
		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );
		$event 		= & $this->get( 'Event' );
		
		//build filter selectlist
		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'NAME' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'USERNAME' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );
		
		//table ordering
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}
	
		$lists['order'] = $filter_order;

		//assign to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('event'		, $event);
		$this->assignRef('search'		, $search);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('elsettings'	, $elsettings);

		parent::display($tpl);
	}
}
?>