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
 * View class for the EventList Venues screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewVenues extends JView {
	
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		// Load tooltips behavior
		jimport('joomla.html.tooltips');
		
		//initialise variables
		$user 		= & JFactory::getUser();
		$db 		= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();
		$document	= & JFactory::getDocument();
		$submenu 	= ELAdmin::submenu();
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.venues.filter_order", 		'filter_order', 	'l.ordering' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.venues.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter_state 		= $mainframe->getUserStateFromRequest( "$option.venues.filter_state", 		'filter_state', 	'*' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.venues.filter", 			'filter', '' );
		$filter 			= intval( $filter );
		$search 			= $mainframe->getUserStateFromRequest( "$option.search", 			'search', '' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		$live_site 			= $mainframe->getCfg('live_site');
				
		//add css and submenu to document
		$document->setBuffer($submenu, 'module', 'submenu');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//create the toolbar
		JMenuBar::title( JText::_( 'VENUES' ), 'venues' );
		JMenuBar::publishList('publish');
		JMenuBar::spacer();
		JMenuBar::unpublishList('unpublish');
		JMenuBar::spacer();
		JMenuBar::addNewX('newvenue');
		JMenuBar::spacer();
		JMenuBar::editListX('editvenue', 'Edit');
		JMenuBar::spacer();
		JMenuBar::deleteList( ' ', 'remove', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::help( 'el.listvenues', true );
		
		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );
		
		//publish unpublished filter
		$lists['state']	= JCommonHTML::selectState( $filter_state );
		
		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'CITY' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );
		
		//table ordering
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}
	
		$lists['order'] = $filter_order;

		$ordering = ($lists['order'] == 'l.ordering');
		
		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('search'		, $search);
		$this->assignRef('template'		, $template);

		parent::display($tpl);
	}
}
?>