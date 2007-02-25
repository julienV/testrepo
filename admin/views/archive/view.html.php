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
 * View class for the EventList archive screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewArchive extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$document	= & JFactory::getDocument();
		$db			= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();
		$elsettings = ELAdmin::config();
		$submenu 	= ELAdmin::submenu();

		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.archive.filter_order", 		'filter_order', 	'a.dates' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.archive.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.archive.filter", 'filter', '' );
		$filter 			= intval( $filter );
		$search 			= $mainframe->getUserStateFromRequest( "$option.archive.search", 'search', '' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );	
		$live_site 			= $mainframe->getCfg('live_site');
		$TimeOffset			= $mainframe->getCfg('offset');
		
		//add css and submenu to document
		$document->setBuffer($submenu, 'module', 'submenu');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//create the toolbar
		JMenuBar::title( JText::_( 'ARCHIVE' ), 'archive' );
		JMenuBar::unarchiveList('unarchive');
		JMenuBar::spacer();
		JMenuBar::deleteList( ' ', 'remove', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::help( 'el.archive', true );

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		//search filter
		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'EVENT TITLE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '3', JText::_( 'CITY' ) );
		$filters[] = JHTMLSelect::option( '4', JText::_( 'CATEGORY' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		//table ordering
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}

		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('search'		, $search);
		$this->assignRef('elsettings'	, $elsettings);
		$this->assignRef('TimeOffset'	, $TimeOffset);

		parent::display($tpl);
	}
}
?>