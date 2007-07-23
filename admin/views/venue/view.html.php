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
 * View class for the EventList Venueedit screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewVenue extends JView {

	function display($tpl = null)
	{
		global $mainframe;

		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$pane		= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();
		$settings	= ELAdmin::config();

		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$url 			= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		//add css and js to document
		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		// Get data from the model
		$model		= & $this->getModel();
		$row      	= & $this->get( 'Data');

		// fail if checked out not by 'me'
		if ($row->id) {
			if ($model->isCheckedOut( $user->get('id') )) {
				JError::raiseWarning( 'SOME_ERROR_CODE', $row->venue.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ));
				$mainframe->redirect( 'index.php?option=com_eventlist&view=venues' );
			}
		}

		//create the toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT VENUE' ), 'venuesedit' );

			//makes data safe
			jimport('joomla.filter.output');
			JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );

		} else {
			JToolBarHelper::title( JText::_( 'ADD VENUE' ), 'venuesedit' );

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
		JToolBarHelper::help( 'el.editvenues', true );

		//Build the image select functionality
		$js = "
		function elSelectImage(image, imagename) {
			document.getElementById('a_image').value = image;
			document.getElementById('a_imagename').value = imagename;
			document.getElementById('imagelib').src = '../images/eventlist/venues/' + image;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;layout=uploadimage&amp;task=venueimg&amp;tmpl=component';
		$link2 = 'index.php?option=com_eventlist&amp;view=imagehandler&amp;task=selectvenueimg&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$document->addScript($url.'includes/js/joomla/modal.js');
		$document->addStyleSheet($url.'includes/js/joomla/modal.css');
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"$row->locimage\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/venues/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n<input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n&nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n&nbsp;<input class=\"inputbox\" type=\"button\" onclick=\"elSelectImage('', '".JText::_('SELECTIMAGE')."' );\" value=\"".JText::_('Reset')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"locimage\" value=\"$row->locimage\" />";

		//assign data to template
		$this->assignRef('row'      	, $row);
		$this->assignRef('pane'      	, $pane);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('settings'     , $settings);
		$this->assignRef('imageselect' 	, $imageselect);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>