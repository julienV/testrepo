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
 * View class for the EventList Updatecheck screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewUpdatecheck extends JView {
	
	function display($tpl = null) {
		
		global $mainframe;
		
		//initialise variables
		$document	= & JFactory::getDocument();
		
		//get vars
		$template	= $mainframe->getTemplate();
		
		//add css
		$document->addStyleSheet("templates/$template/css/general.css");
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		//Get data from the model
		$updatedata      = & $this->get( 'Updatedata');
		
		//assign data to template
		$this->assignRef('template'		, $template);
		$this->assignRef('updatedata'	, $updatedata);
		
		parent::display();
	}
}