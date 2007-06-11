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

		if($this->getLayout() == 'addvenue') {
			$this->_displayaddvenue($tpl);
			return;
		}

		//Load behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$pane 		= & JPane::getInstance('sliders');
		$uri 		= & JFactory::getURI();
		$user 		= & JFactory::getUser();
		$elsettings = ELAdmin::config();

		//get vars
		$cid		= JRequest::getVar( 'cid' );
		$task		= JRequest::getVar('task');
		$url 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		//add the custom stylesheet and the seo javascript
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		$document->addScript($url.'administrator/components/com_eventlist/assets/js/seo.js');
		$document->addScript($url.'administrator/components/com_eventlist/assets/js/recurrence.js');

		//build toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT EVENT' ), 'eventedit' );
		} else {
			JToolBarHelper::title( JText::_( 'ADD EVENT' ), 'eventedit' );

			//set the submenu
			JSubMenuHelper::addEntry( JText::_( 'EVENTLIST' ), 'index.php?option=com_eventlist');
			JSubMenuHelper::addEntry( JText::_( 'EVENTS' ), 'index.php?option=com_eventlist&view=events');
			JSubMenuHelper::addEntry( JText::_( 'VENUES' ), 'index.php?option=com_eventlist&view=venues');
			JSubMenuHelper::addEntry( JText::_( 'CATEGORIES' ), 'index.php?option=com_eventlist&view=categories');
			JSubMenuHelper::addEntry( JText::_( 'ARCHIVE' ), 'index.php?option=com_eventlist&view=archive');
			JSubMenuHelper::addEntry( JText::_( 'GROUPS' ), 'index.php?option=com_eventlist&view=groups');
			JSubMenuHelper::addEntry( JText::_( 'HELP' ), 'index.php?option=com_eventlist&view=help');
			if ($user->get('gid') > 24) {
				JSubMenuHelper::addEntry( JText::_( 'SETTINGS' ), 'index.php?option=com_eventlist&controller=settings&task=edit');
			}
		}
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.editevents', true );

		//get data from model
		$model		= & $this->getModel();
		$row     	= & $this->get( 'Data');

		// fail if checked out not by 'me'
		if ($row->id) {
			if ($model->isCheckedOut( $user->get('id') )) {
				JError::raiseWarning( 'SOME_ERROR_CODE', $row->titel.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ));
				$mainframe->redirect( 'index.php?option=com_eventlist&view=events' );
			}
		}

		//make data safe
		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'datdescription' );

		//Create category list
		$categories = & $this->get( 'Categories');

		$catlist 	= array();
		$catlist[] 	= JHTML::_('select.option', '0', JText::_( 'SELECT CATEGORY' ) );
		$catlist 	= array_merge( $catlist, $categories );

		$Lists = array();
		$Lists['category'] = JHTML::_('select.genericlist', $catlist, 'catsid', 'size="1" class="inputbox"', 'value', 'text', $row->catsid );

		//build venue select js and load the view
		$js = "
		function elSelectVenue(id, venue) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = venue;
			document.popup.hide();
		}";

		$linkvsel = 'index.php?option=com_eventlist&amp;view=venueelement&amp;tmpl=component';
		$linkvadd = 'index.php?option=com_eventlist&amp;task=addvenue&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$document->addScript($url.'includes/js/joomla/modal.js');
		$document->addStyleSheet($url.'includes/js/joomla/modal.css');
		$venueselect = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$row->venue\" disabled=\"disabled\" /></div>";
		$venueselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$linkvsel', 650, 375, null);\" value=\"".JText::_('SELECT')."\" />";
		$venueselect .= "\n<input type=\"hidden\" id=\"a_id\" name=\"locid\" value=\"$row->locid\" />";
		$venueselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"window.open('$linkvadd', 'popup', 'width=750,height=400,scrollbars=yes,toolbar=no,status=no,resizable=yes,menubar=no,location=no,directories=no,top=10,left=10')\" value=\"".JText::_('ADD')."\" />";
		$venueselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"elSelectVenue(0, '".JText::_('NO VENUE')."' );\" value=\"".JText::_('NO VENUE')."\" onblur=\"seo_switch()\" />";

		//build image select js and load the view
		$js = "
		function elSelectImage(image, imagename) {
			document.getElementById('a_image').value = image;
			document.getElementById('a_imagename').value = imagename;
			document.getElementById('imagelib').src = '../images/eventlist/events/' + image;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;layout=uploadimage&amp;task=eventimg&amp;tmpl=component';
		$link2 = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;task=selecteventimg&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"$row->datimage\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/events/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"elSelectImage('', '".JText::_('SELECTIMAGE')."' );\" value=\"".JText::_('Reset')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"datimage\" value=\"$row->datimage\" />";

		//assign vars to the template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('imageselect'	, $imageselect);
		$this->assignRef('venueselect'	, $venueselect);
		$this->assignRef('editor'		, $editor);
		$this->assignRef('pane'			, $pane);
		$this->assignRef('task'			, $task);
		$this->assignRef('elsettings'	, $elsettings);

		parent::display($tpl);
	}

	/**
	 * Creates the output for the add venue screen
	 *
	 * @since 0.9
	 *
	 */
	function _displayaddvenue($tpl)
	{
		global $mainframe;

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();

		//get vars
		$url 			= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		$live_site		= $mainframe->getCfg('live_site');

		//add css and js to document
		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');

		//Build the image select functionality
		$js = "
		function elSelectImage(image, imagename) {
			document.getElementById('a_image').value = image;
			document.getElementById('a_imagename').value = imagename;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;layout=uploadimage&amp;task=venueimg&amp;tmpl=component';
		$link2 = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;task=selectvenueimg&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$document->addScript($url.'includes/js/joomla/modal.js');
		$document->addStyleSheet($url.'includes/js/joomla/modal.css');
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"".JText::_('SELECTIMAGE')."\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/events/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n<input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"elSelectImage('', '".JText::_('SELECTIMAGE')."' );\" value=\"".JText::_('Reset')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"locimage\" value=\"".JText::_('SELECTIMAGE')."\" />";

		//set published
		$published = 1;

		//create the toolbar
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('imageselect' 	, $imageselect);
		$this->assignRef('published' 	, $published);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>