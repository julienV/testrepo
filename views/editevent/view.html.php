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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the EditeventView
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEditevent extends JView
{
	/**
	 * Creates the output for event submissions
	 *
	 * @since 0.4
	 *
	 */
	function display( $tpl=null )
	{
		global $mainframe;

		if($this->getLayout() == 'selectvenue') {
			$this->_displayselectvenue($tpl);
			return;
		}


		// Initialize variables
		$editor 	= & JFactory::getEditor();
		$doc 		= & JFactory::getDocument();
		$elsettings = ELHelper::config();

		//Get Data from the model
		$row 		= $this->Get('Event');
		$categories	= $this->Get('Categories');

		//Get requests
		$id					= JRequest::getInt('id');
		$returnview			= JRequest::getWord('returnview');

		//Clean output
		jimport('joomla.filter.output');
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'datdescription' );

		JHTML::_('behavior.formvalidation');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		//add css file
		$doc->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$doc->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		//Set page title
		$id ? $title = JText::_( 'EDIT EVENT' ) : $title = JText::_( 'ADD EVENT' );

		$doc->setTitle($title);

		// Get the menu object of the active menu item
		$menu		= & JMenu::getInstance();
		$item    	= $menu->getActive();
		$params 	= & $mainframe->getPageParameters('com_eventlist');

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);
		$pathway->addItem($title, '');

		//Has the user access to the editor and the add venue screen
		$editoruser = ELUser::editoruser();
		$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );

		//Get image information
		$dimage = ELImage::flyercreator($row->datimage, $elsettings, 'event');

		//Set the info image
		$infoimage = JHTML::_('image.site', 'icon-16-hint.png', 'components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );

		//Create the stuff required for the venueselect functionality
		$url	= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		$js = "
		function elSelectVenue(id, venue) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = venue;
			document.getElementById('sbox-window').close();
		}";

		$link = JRoute::_('index.php?view=editevent&layout=selectvenue&tmpl=component');
		$doc->addScriptDeclaration($js);

		JHTML::_('behavior.modal', 'a.modal');
		// include the recurrence script
		$doc->addScript($url.'components/com_eventlist/assets/js/recurrence.js');

		$venueselect = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$row->venue\" disabled=\"disabled\" /></div>";
		$venueselect .= "<div class=\"button2-left\"><div class=\"blank\"><a class=\"modal\" title=\"".JText::_('SELECT')."\" href=\"$link\" rel=\"{handler: 'iframe', size: {x: 650, y: 375}}\">".JText::_('SELECT')."</a></div></div>\n";
		$venueselect .= "\n<input class=\"inputbox required validate-venue\" type=\"hidden\" id=\"a_id\" name=\"locid\" value=\"$row->locid\" />";


		$this->assignRef('row' , 					$row);
		$this->assignRef('categories' , 			$categories);
		$this->assignRef('editor' , 				$editor);
		$this->assignRef('dimage' , 				$dimage);
		$this->assignRef('infoimage' , 				$infoimage);
		$this->assignRef('delloclink' , 			$delloclink);
		$this->assignRef('editoruser' , 			$editoruser);
		$this->assignRef('venueselect' , 			$venueselect);
		$this->assignRef('returnview' , 			$returnview);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('item' , 					$item);

		parent::display($tpl);

	}

	/**
	 * Creates the output for the venue select listing
	 *
	 * @since 0.9
	 *
	 */
	function _displayselectvenue($tpl)
	{
		global $mainframe;

		$document	= & JFactory::getDocument();

		$limit				= JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$limitstart			= JRequest::getInt('limitstart');
		$filter_order		= JRequest::getCmd('filter_order', 'l.venue');
		$filter_order_Dir	= JRequest::getWord('filter_order_Dir', 'ASC');;
		$filter				= JRequest::getString('filter');
		$filter_type		= JRequest::getInt('filter_type');

		// Get/Create the model
		$rows 	= $this->get('Venues');
		$total 	= $this->get('Countitems');

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// table ordering
		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;

		$document->setTitle(JText::_( 'SELECTVENUE' ));
		$document->addScript(JPATH_SITE.'includes/js/joomla/modal.js');
		$document->addStyleSheet(JPATH_SITE.'includes/js/joomla/modal.css');

		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');

		$filters = array();
		$filters[] = JHTML::_('select.option', '1', JText::_( 'VENUE' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'CITY' ) );
		$searchfilter = JHTML::_('select.genericlist', $filters, 'filter_type', 'size="1" class="inputbox"', 'value', 'text', $filter_type );

		$this->assignRef('rows' , 				$rows);
		$this->assignRef('searchfilter' , 		$searchfilter);
		$this->assignRef('pageNav' , 			$pageNav);
		$this->assignRef('lists' , 				$lists);
		$this->assignRef('filter' , 			$filter);


		parent::display($tpl);
	}
}
?>