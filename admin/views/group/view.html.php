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
		$user 		= & JFactory::getUser();

		//get vars
		$template		= $mainframe->getTemplate();
		$request_url 	= $uri->toString();
		$cid 			= JRequest::getInt( 'cid' );

		//add css
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//Get data from the model
		$model				= & $this->getModel();
		$row      			= & $this->get( 'Data');
		$maintainers 		= & $this->get( 'Members');
		$available_users 	= & $this->get( 'Available');

		// fail if checked out not by 'me'
		if ($row->id) {
			if ($model->isCheckedOut( $user->get('id') )) {
				JError::raiseWarning( 'SOME_ERROR_CODE', $row->name.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ));
				$mainframe->redirect( 'index.php?option=com_eventlist&view=groups' );
			}
		}

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

			//Create Submenu
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
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.editgroup', true );

		//create selectlists
		$lists = array();
		$lists['maintainers']		= JHTML::_('select.genericlist', $maintainers, 'maintainers[]', 'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm[\'maintainers[]\'], document.adminForm[\'available_users\'])" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text' );
		$lists['available_users']	= JHTML::_('select.genericlist', $available_users, 'available_users', 'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm[\'available_users\'], document.adminForm[\'maintainers[]\'])" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text' );

		//assign data to template
		$this->assignRef('row'      	, $row);
		$this->assignRef('pane'      	, $pane);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('template'		, $template);
		$this->assignRef('lists'      	, $lists);

		parent::display($tpl);
	}
}
?>