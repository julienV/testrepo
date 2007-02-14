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
 * View class for the EventList Quick Venueadd screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewVenueadd extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		$live_site	= $mainframe->getCfg('live_site');
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$db		 	= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();

		$request_url =  $uri->toString();

		// Get data from the model
		$row      	= & $this->get( 'Data');

		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');

		/*
		* image
		*/
		$url 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();


		$js = "
		function elSelectImage(image, imagename) {
			document.getElementById('a_image').value = image;
			document.getElementById('a_imagename').value = imagename;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=imageupload&amp;task=venueimg&amp;tmpl=component';
		$link2 = 'index.php?option=com_eventlist&amp;view=imageselect&amp;task=selectvenueimg&amp;tmpl=component';
		$document->addScriptDeclaration($js);
		$document->addScript($url.'includes/js/joomla/modal.js');
		$document->addStyleSheet($url.'includes/js/joomla/modal.css');
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"".JText::_('SELECTIMAGE')."\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/events/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"locimage\" value=\"".JText::_('SELECTIMAGE')."\" />";

		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('row'      	, $row);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('imageselect' 	, $imageselect);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>