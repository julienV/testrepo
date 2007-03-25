<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
		global $mainframe, $option;

		if($this->getLayout() == 'selectvenue') {
			$this->_displayselectvenue($tpl);
			return;
		}

		// Initialize variables
		$editor 	= & JFactory::getEditor();
		$doc 		= & JFactory::getDocument();
		$db			= & JFactory::getDBO();
		$elsettings = ELHelper::config();

		//Get Data from the model
		$row 		= $this->Get('Event');
		$categories	= $this->Get('Categories');

		// Get requests
		$id					= JRequest::getVar('id', 0, '', 'int');
		$returnview			= JRequest::getVar('returnview', '', '', 'string');
		$live_site 			= $mainframe->getCfg('live_site');

		// Add the Calendar includes to the document <head> section
		JCommonHTML::loadCalendar();
		// Load tooltips behavior
		jimport('joomla.html.tooltips');

		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'datdescription' );

		/*
		* Set page title
		*/
		$id ? $title = JText::_( 'EDIT EVENT' ) : $title = JText::_( 'ADD EVENT' );

		$doc->setTitle($title);

		// Get the menu object of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);
		$pathway->addItem($title, '');

		//Has the user access to the editor and the add venue screen
		$editoruser = ELUser::editoruser();
		$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );

		//Get image information
		$dimage = ELImage::eventimage($live_site, $row->datimage, $elsettings->imagewidth, $elsettings->imagehight, $elsettings->imageprob, $elsettings->gddisabled);

		//Set the info image
		$infoimage = JAdminMenus::ImageCheck( 'icon-16-hint.png', 'components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );

		//Create the stuff required for the venueselect functionality
		$url	= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		/*
		* TODO Move to model
		*/
		$venue =& JTable::getInstance('eventlist_venues', '');

		if ($row->id) {
			$venue->load($row->locid);
		} else {
			$venue->club = JText::_('SELECTVENUE');
		}

		$js = "
		function elSelectVenue(id, venue) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = venue;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;task=selectvenue&amp;tmpl=component';
		$doc->addScriptDeclaration($js);
		$doc->addScript($url.'includes/js/joomla/modal.js');
		$doc->addStyleSheet($url.'includes/js/joomla/modal.css');
		$venueselect = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$venue->club\" disabled=\"disabled\" /></div>";
		$venueselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 375, null);\" value=\"".JText::_('Select')."\" />";
		$venueselect .= "\n<input type=\"hidden\" id=\"a_id\" name=\"locid\" value=\"$row->locid\" />";


		$this->assignRef('row' , 					$row);
		$this->assignRef('categories' , 			$categories);
		$this->assignRef('editor' , 				$editor);
		$this->assignRef('dimage' , 				$dimage);
		$this->assignRef('infoimage' , 				$infoimage);
		$this->assignRef('delloclink' , 			$delloclink);
		$this->assignRef('editoruser' , 			$editoruser);
		$this->assignRef('live_site' , 				$live_site);
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
		global $mainframe, $option;

		$document	= & JFactory::getDocument();

		$limit				= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart			= JRequest::getVar('limitstart', 0, '', 'int');
		$filter 			= JRequest::getVar('filter', '', 'request');
		$filter 			= intval( $filter );
		$filter_order		= JRequest::getVar('filter_order');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir');
		$search 			= JRequest::getVar('search');

		$live_site = $mainframe->getCfg('live_site');

		// Get/Create the model
		$rows 	= $this->get('Venues');
		$total 	= $this->get('Countitems');

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// table ordering
		if ( $filter_order_Dir == 'DESC' ) {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}

		$lists['order'] = $filter_order;

		$document->setTitle(JText::_( 'SELECTVENUE' ));
		$document->addScript(JPATH_SITE.'includes/js/joomla/modal.js');
		$document->addStyleSheet(JPATH_SITE.'includes/js/joomla/modal.css');

		// TODO change to own css sheet
		$document->addStyleSheet("administrator/templates/khepri/css/general.css");

		$link = JRoute::_('index.php?option=com_eventlist&amp;task=selectvenue&amp;tmpl=component');

		$filters = array();
		$filters[] = JHTMLSelect::option( '1', JText::_( 'VENUE' ) );
		$filters[] = JHTMLSelect::option( '2', JText::_( 'CITY' ) );
		$searchfilter = JHTMLSelect::genericList( $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		$this->assignRef('rows' , 				$rows);
		$this->assignRef('searchfilter' , 		$searchfilter);
		$this->assignRef('pageNav' , 			$pageNav);
		$this->assignRef('link' , 				$link);
		$this->assignRef('lists' , 				$lists);
		$this->assignRef('search' , 			$search);
		

		parent::display($tpl);
	}
}
?>