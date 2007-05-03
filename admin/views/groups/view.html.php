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
 * View class for the EventList groups screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewGroups extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$db			= & JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$submenu 	= ELAdmin::submenu();

		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.groups.filter_order", 		'filter_order', 	'name' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.groups.filter_order_Dir",	'filter_order_Dir',	'' );
		$search 			= $mainframe->getUserStateFromRequest( "$option.groups.search", 			'search', 			'' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		$request_url 		= $uri->toString();

		//add css and submenu to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		$document->setBuffer($submenu, 'module', 'submenu');

		JHTML::_('behavior.tooltip');

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		//create the toolbar
		JToolBarHelper::title( JText::_( 'GROUPS' ), 'groups' );
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.listgroups', true );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);

		parent::display($tpl);
	}
}
?>