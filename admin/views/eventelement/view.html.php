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
 * View class for the EventList eventelement screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEventelement extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		$db			= & JFactory::getDBO();
		$elsettings = & ELAdmin::config();

		$document	= & JFactory::getDocument();
		$document->setTitle(JText::_( 'SELECTEVENT' ));
		$document->addScript(JPATH_SITE.'includes/js/joomla/modal.js');
		$document->addStyleSheet(JPATH_SITE.'includes/js/joomla/modal.css');

		$template = $mainframe->getTemplate();
		$document->addStyleSheet("templates/$template/css/general.css");

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.eventelement.filter_order", 		'filter_order', 	'a.dates' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.eventelement.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.eventelement.filter", 'filter', '' );
		$filter_state 		= $mainframe->getUserStateFromRequest( "$option.eventelement.filter_state", 		'filter_state', 	'*' );
		$search 			= $mainframe->getUserStateFromRequest( "$option.eventelement.search", 			'search', '' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		/*
		* publish unpublished filter
		*/
		$lists['state']	= JCommonHTML::selectState( $filter_state );

		/*
		* table ordering
		*/
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}
		$lists['order'] = $filter_order;

		//Create the filter selectlist
		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'EVENT TITLE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '3', JText::_( 'CITY' ) );
		$filters[] = JHTMLSelect::option( '4', JText::_( 'CATEGORY' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('search'		, $search);
		$this->assignRef('elsettings'	, $elsettings);

		parent::display($tpl);
	}

}//end class
?>