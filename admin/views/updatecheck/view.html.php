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
		
		$template	= $mainframe->getTemplate();
		$document	= & JFactory::getDocument();
		
		$document->addStyleSheet("templates/$template/css/general.css");
		
		$updatedata      = & $this->get( 'Updatedata');
		
		$this->assignRef('template'		, $template);
		$this->assignRef('updatedata'	, $updatedata);
		
		parent::display();
	}
}