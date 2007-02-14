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
 * View class for the EventList venueselect screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewVenueelement extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		$db			= & JFactory::getDBO();
		$document	= & JFactory::getDocument();
		$template 	= $mainframe->getTemplate();

		$document->setTitle(JText::_( 'SELECTVENUE' ));
		$document->addScript(JPATH_SITE.'includes/js/joomla/modal.js');
		$document->addStyleSheet(JPATH_SITE.'includes/js/joomla/modal.css');
		$document->addStyleSheet("templates/$template/css/general.css");

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.venueelement.filter_order", 		'filter_order', 	'l.ordering' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.venueelement.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter 			= $mainframe->getUserStateFromRequest( "$option.venueelement.filter", 			'filter', '' );
		$filter_state 		= $mainframe->getUserStateFromRequest( "$option.venueelement.filter_state", 		'filter_state', 	'*' );
		$search 			= $mainframe->getUserStateFromRequest( "$option.venueelement.search", 			'search', '' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

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

		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'CITY' ) );
		$lists['filter'] = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('search'		, $search);

		parent::display($tpl);
	}
}
?>