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
 * View class for the EventList events screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEvents extends JView {
	
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		//Load tooltips behavior
		jimport('joomla.html.tooltips');
		
		//initialise variables
		$user 		= & JFactory::getUser();
		$document	= & JFactory::getDocument();
		$db  		= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();
		$elsettings = ELAdmin::config();
		$submenu 	= ELAdmin::submenu();	
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.events.filter_order", 		'filter_order', 	'a.dates' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.events.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter_state 		= $mainframe->getUserStateFromRequest( "$option.events.filter_state", 		'filter_state', 	'*' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.events.filter", 			'filter', '' );
		$search 			= $mainframe->getUserStateFromRequest( "$option.events.search", 			'search', 			'' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$live_site 			= $mainframe->getCfg('live_site');
		$template			= $mainframe->getTemplate();
		$request_url 		= $uri->toString();
		
		//add css and submenu to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		$document->setBuffer($submenu, 'module', 'submenu');
		
		//create the toolbar
		JMenuBar::title( JText::_( 'EVENTS' ), 'events' );
		JMenuBar::archiveList();
		JMenuBar::spacer();
		JMenuBar::publishList('publish');
		JMenuBar::spacer();
		JMenuBar::unpublishList('unpublish');
		JMenuBar::spacer();
		JMenuBar::addNewX('newevent');
		JMenuBar::spacer();
		JMenuBar::editListX( 'editevent', 'Edit' );
		JMenuBar::spacer();
		JMenuBar::deleteList( '', 'remove', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JMenuBar::spacer();
		JMenuBar::help( 'el.listevents', true );
		
		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );
		
		//publish unpublished filter 
		$lists['state']	= JCommonHTML::selectState( $filter_state );
	
		//table ordering
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}
		$lists['order'] = $filter_order;
		
		//search filter 
		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'EVENT TITLE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '3', JText::_( 'CITY' ) );
		$filters[] = JHTMLSelect::option( '4', JText::_( 'CATEGORY' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('user'			, $user);
		$this->assignRef('search'		, $search);
		$this->assignRef('template'		, $template);
		$this->assignRef('elsettings'	, $elsettings);

		parent::display($tpl);
	}
}
?>