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
		$live_site 			= $mainframe->getCfg('live_site');
		$template			= $mainframe->getTemplate();
		$request_url 		= $uri->toString();

		//add css and submenu to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		$document->setBuffer($submenu, 'module', 'submenu');

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		//create the toolbar
		JMenuBar::title( JText::_( 'GROUPS' ), 'groups' );
		JMenuBar::addNewX('newgroup');
		JMenuBar::spacer();
		JMenuBar::editListX( 'editgroup', 'Edit' );
		JMenuBar::spacer();
		JMenuBar::deleteList( '', 'removegroup', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::help( 'el.listgroups', true );

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
		$this->assignRef('user'			, $user);
		$this->assignRef('search'		, $search);
		$this->assignRef('template'		, $template);

		parent::display($tpl);
	}
}
?>