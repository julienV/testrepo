<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
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
		$user		= & JFactory::getUser();
		$elsettings = ELAdmin::config();

		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.archive.filter_order', 'filter_order', 'a.dates', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.archive.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.archive.filter', 'filter', '', 'int' );
		$filter 			= intval( $filter );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.archive.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();

		//add css and submenu to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//Create Submenu
		JSubMenuHelper::addEntry( JText::_( 'EVENTLIST' ), 'index.php?option=com_eventlist');
		JSubMenuHelper::addEntry( JText::_( 'EVENTS' ), 'index.php?option=com_eventlist&view=events');
		JSubMenuHelper::addEntry( JText::_( 'VENUES' ), 'index.php?option=com_eventlist&view=venues');
		JSubMenuHelper::addEntry( JText::_( 'CATEGORIES' ), 'index.php?option=com_eventlist&view=categories');
		JSubMenuHelper::addEntry( JText::_( 'ARCHIVE' ), 'index.php?option=com_eventlist&view=archive', true);
		JSubMenuHelper::addEntry( JText::_( 'GROUPS' ), 'index.php?option=com_eventlist&view=groups');
		JSubMenuHelper::addEntry( JText::_( 'HELP' ), 'index.php?option=com_eventlist&view=help');
		if ($user->get('gid') > 24) {
			JSubMenuHelper::addEntry( JText::_( 'SETTINGS' ), 'index.php?option=com_eventlist&controller=settings&task=edit');
		}

		JHTML::_('behavior.tooltip');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'ARCHIVE' ), 'archive' );
		JToolBarHelper::unarchiveList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.archive', true );

		// Get data from the model
		$rows      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );

		//search filter
		$filters = array();
		$filters[] = JHTML::_('select.option', '1', JText::_( 'EVENT TITLE' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'VENUE' ) );
		$filters[] = JHTML::_('select.option', '3', JText::_( 'CITY' ) );
		$filters[] = JHTML::_('select.option', '4', JText::_( 'CATEGORY' ) );
		$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

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
		$this->assignRef('elsettings'	, $elsettings);
		$this->assignRef('template'		, $template);

		parent::display($tpl);
	}
}
?>