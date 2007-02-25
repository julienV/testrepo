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
 * View class for the EventList categories screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategories extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$user 		= & JFactory::getUser();
		$db  		= & JFactory::getDBO();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$submenu 	= ELAdmin::submenu();

		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.categories.filter_order", 		'filter_order', 	'c.ordering' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.categories.filter_order_Dir",	'filter_order_Dir',	'' );
		$filter_state 		= $mainframe->getUserStateFromRequest( "$option.categories.filter_state", 		'filter_state', 	'*' );
		$search 			= $mainframe->getUserStateFromRequest( "$option.categories.search", 			'search', 			'' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$live_site 			= $mainframe->getCfg('live_site');
		
		//add css and submenu to document
		$document->setBuffer($submenu, 'module', 'submenu');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		//create the toolbar
		JMenuBar::title( JText::_( 'CATEGORIES' ), 'elcategories' );
		JMenuBar::publishList('publish');
		JMenuBar::spacer();
		JMenuBar::unpublishList('unpublish');
		JMenuBar::spacer();
		JMenuBar::addNewX('categorynew');
		JMenuBar::spacer();
		JMenuBar::editListX('categoryedit');
		JMenuBar::spacer();
		JMenuBar::deleteList( ' ', 'delete', 'Remove' );
		JMenuBar::spacer();
		JMenuBar::help( 'el.listcategories', true );

		//Get data from the model
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

		$ordering = ($lists['order'] == 'c.ordering');

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('search'		, $search);

		parent::display($tpl);
	}
}
?>