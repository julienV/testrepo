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
 * View class for the EventList imageupload screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewImageupload extends JView {
	
	function display($tpl = null)
	{
		global $mainframe;

		//initialise variables
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$elsettings = ELAdmin::config();
		
		//get vars
		$live_site 	= $mainframe->getCfg('live_site');
		$template	= $mainframe->getTemplate();	
		$task 		= JRequest::getVar( 'task' );
	
		//add css
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		//assign data to template
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('task'      	, $task);
		$this->assignRef('elsettings'  	, $elsettings);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>