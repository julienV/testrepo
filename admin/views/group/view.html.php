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
 * View class for the EventList editgroup screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewGroup extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$pane 		= & JPane::getInstance('sliders');

		//get vars
		$live_site 		= $mainframe->getCfg('live_site');
		$template		= $mainframe->getTemplate();
		$request_url 	= $uri->toString();
		$cid 			= JRequest::getVar( 'cid' );

		//add css
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//Get data from the model
		$row      	= & $this->get( 'Data');
		$maintainers = & $this->get( 'Members');
		$available_users = & $this->get( 'Available');

		//make data safe
		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row );

		//build toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT GROUP' ), 'groupedit' );
			JToolBarHelper::spacer();
		} else {
			JToolBarHelper::title( JText::_( 'ADD GROUP' ), 'groupedit' );
			JToolBarHelper::spacer();

			//set the submenu
			$submenu = ELAdmin::submenu();
			$document->setBuffer($submenu, 'module', 'submenu');

		}
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.editgroup', true );

		//create selectlists
		$lists = array();
		$lists['maintainers']		= JHTMLSelect::genericList( $maintainers, 'maintainers[]', 'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm[\'maintainers[]\'], document.adminForm[\'available_users\'])" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text' );
		$lists['available_users']	= JHTMLSelect::genericList( $available_users, 'available_users', 'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm[\'available_users\'], document.adminForm[\'maintainers[]\'])" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text' );

		//assign data to template
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('row'      	, $row);
		$this->assignRef('pane'      	, $pane);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('template'		, $template);
		$this->assignRef('lists'      	, $lists);

		parent::display($tpl);
	}
}
?>