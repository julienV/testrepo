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

		// Load tooltips and pane behavior
		jimport('joomla.html.pane');
		jimport('joomla.html.tooltips');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$pane		= & JPane::getInstance('sliders');

		//get vars
		$request_url 	= $uri->toString();
		$cid 			= JRequest::getVar( 'cid' );
		$url 			= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		//add css and js to document
		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		// Get data from the model
		$row      	= & $this->get( 'Data');

		//create the toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT VENUE' ), 'venuesedit' );

			//makes data safe
			jimport('joomla.filter.output');
			JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );

		} else {
			JToolBarHelper::title( JText::_( 'ADD VENUE' ), 'venuesedit' );

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
		$this->assignRef('imageselect' 	, $imageselect);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>