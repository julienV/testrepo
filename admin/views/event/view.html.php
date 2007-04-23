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
 * View class for the EventList event screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEvent extends JView {

	function display($tpl = null)
	{
		global $mainframe;

		//Load tooltips and pane behavior
		jimport('joomla.html.pane');
		jimport('joomla.html.tooltips');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$db		 	= & JFactory::getDBO();
		$pane 		= & JPane::getInstance('sliders');
		$uri 		= & JFactory::getURI();
		$elsettings = ELAdmin::config();

		//load calendar library
		JCommonHTML::loadCalendar();

		//get vars
		$cid		= JRequest::getVar( 'cid' );
		$task		= JRequest::getVar('task');
		$live_site 	= $mainframe->getCfg('live_site');
		$url 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		//add the custom stylesheet and the seo javascript
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		$document->addScript($url.'administrator/components/com_eventlist/assets/js/seo.js');

		//build toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT EVENT' ), 'eventedit' );
			JToolBarHelper::spacer();
		} else {
			JToolBarHelper::title( JText::_( 'ADD EVENT' ), 'eventedit' );
			JToolBarHelper::spacer();

			//set the submenu
			$submenu = ELAdmin::submenu();
			$document->setBuffer($submenu, 'module', 'submenu');
		}
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.editevents', true );

		//get data from model
		$row     	= & $this->get( 'Data');

		//make data safe
		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'datdescription' );

		//Create category list
		$categories = & $this->get( 'Categories');

		$catlist 	= array();
		$catlist[] 	= JHTMLSelect::option( '0', JText::_( 'SELECT CATEGORY' ) );
		$catlist 	= array_merge( $catlist, $categories );

		$Lists = array();
		$Lists['category'] = JHTMLSelect::genericList( $catlist, 'catsid', 'size="1" class="inputbox"', 'value', 'text', $row->catsid );

		//TODO: move to model
		$venue =& JTable::getInstance('eventlist_venues', '');
		if ($row->id) {
			$venue->load($row->locid);
		} else {
			$venue->venue = JText::_('SELECTVENUE');
		}

		//build venue select js and load the view
		$js = "
		function elSelectVenue(id, venue) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = venue;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=venueelement&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$document->addScript($url.'includes/js/joomla/modal.js');
		$document->addStyleSheet($url.'includes/js/joomla/modal.css');
		$venueselect = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$venue->venue\" disabled=\"disabled\" /></div>";
		$venueselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 375, null);\" value=\"".JText::_('SELECT')."\" />";
		$venueselect .= "\n<input type=\"hidden\" id=\"a_id\" name=\"locid\" value=\"$row->locid\" />";

		//venueadd start
		$link = 'index.php?option=com_eventlist&amp;view=venueadd&amp;tmpl=component';
		$venueadd = "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"window.open('$link', 'popup', 'width=750,height=400,scrollbars=yes,toolbar=no,status=no,resizable=yes,menubar=no,location=no,directories=no,top=10,left=10')\" value=\"".JText::_('ADD')."\" />";
		//venueadd end

		/*
		* image
		* TODO: move to model
		*/
		$image =& JTable::getInstance('eventlist_events', '');
		if ($row->id) {
			$image->load($row->id);
		}

		//build image select js and load the view
		$js = "
		function elSelectImage(image, imagename) {
			document.getElementById('a_image').value = image;
			document.getElementById('a_imagename').value = imagename;
			document.getElementById('imagelib').src = '../images/eventlist/events/' + image;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=imageupload&amp;task=eventimg&amp;tmpl=component';
		$link2 = 'index.php?option=com_eventlist&amp;view=imageselect&amp;task=selecteventimg&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"$image->datimage\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/events/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"datimage\" value=\"$image->datimage\" />";

		//assign vars to the template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('row'      	, $row);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('imageselect'	, $imageselect);
		$this->assignRef('venueselect'	, $venueselect);
		$this->assignRef('editor'		, $editor);
		$this->assignRef('pane'			, $pane);
		$this->assignRef('venue'		, $venue);
		$this->assignRef('task'			, $task);
		$this->assignRef('venueadd'		, $venueadd);
		$this->assignRef('elsettings'	, $elsettings);

		parent::display($tpl);
	}
}
?>