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
		global $mainframe, $option;
		
		// Load tooltips and pane behavior
		jimport('joomla.html.pane');
		jimport('joomla.html.tooltips');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$db		 	= & JFactory::getDBO();
		$uri 		= & JFactory::getURI();
		$pane		= & JPane::getInstance('sliders');

		//get vars
		$request_url 	= $uri->toString();
		$live_site		= $mainframe->getCfg('live_site');
		$cid 			= JRequest::getVar( 'cid' );
		$url 			= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		
		//add css and js to document
		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//image
		//JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		
		// Get data from the model
		$row      	= & $this->get( 'Data');
		$image 		= & JTable::getInstance('eventlist_venues', '');

		//create the toolbar
		if ( $cid ) {
			JMenuBar::title( JText::_( 'EDIT VENUE' ), 'venuesedit' );
			jimport('joomla.filter.output');
			JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );

			$image->load($row->id);

		} else {
			JMenuBar::title( JText::_( 'ADD VENUE' ), 'venuesedit' );

			//set the submenu
			$submenu = ELAdmin::submenu();
			$document->setBuffer($submenu, 'module', 'submenu');

		}
		JMenuBar::apply('apply');
		JMenuBar::spacer();
		JMenuBar::save('savevenue');
		JMenuBar::spacer();
		JMenuBar::cancel('cancel');
		JMenuBar::spacer();
		JMenuBar::help( 'el.editvenues', true );

		//Build the image select functionality
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
		$imageselect = "\n<input style=\"background: #ffffff;\" type=\"text\" id=\"a_imagename\" value=\"$image->locimage\" disabled=\"disabled\" onchange=\"javascript:if (document.forms[0].a_imagename.value!='') {document.imagelib.src='../images/eventlist/events/' + document.forms[0].a_imagename.value} else {document.imagelib.src='../images/blank.png'}\"; /><br />";
		$imageselect .= "\n <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 400, null);\" value=\"".JText::_('Upload')."\" />";
		$imageselect .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link2', 650, 400, null);\" value=\"".JText::_('SELECTIMAGE')."\" />";
		$imageselect .= "\n<input type=\"hidden\" id=\"a_image\" name=\"locimage\" value=\"$image->locimage\" />";

		//assign data to template
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('row'      	, $row);
		$this->assignRef('image'      	, $image);
		$this->assignRef('pane'      	, $pane);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('imageselect' 	, $imageselect);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>