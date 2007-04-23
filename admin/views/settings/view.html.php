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
 * View class for the EventList Settings screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewSettings extends JView {

	function display($tpl = null) {

		global $mainframe;

		// Load tooltips behavior
		jimport('joomla.html.tooltips');

		//initialise variables
		$elsettings = ELAdmin::config();
		$document 	= & JFactory::getDocument();
		$acl		= & JFactory::getACL();
		$uri 		= & JFactory::getURI();
		$user 		= & JFactory::getUser();

		//only admins have access to this view
		if ($user->get('gid') < 24) {
			JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'ALERTNOTAUTH'));
			$mainframe->redirect( 'index.php?option=com_eventlist&view=eventlist' );
			return false;
		}

		//get vars
		$live_site 	= $mainframe->getCfg('live_site');

		//Build submenu
		$contents = '';
		ob_start();
			require_once(dirname(__FILE__).DS.'tmpl'.DS.'navigation.php');
		$contents = ob_get_contents();
		ob_end_clean();

		//add css, js and submenu to document
		$document->setBuffer($contents, 'module', 'submenu');
		$document->addScript( JURI::base().'components/com_eventlist/assets/js/settings.js' );
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'SETTINGS' ), 'settings' );
		JToolBarHelper::save('savesettings');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.settings', true );

		$accessLists = array();

 	  	//Create custom group levels to include into the public group selectList
 	  	$access   = array();
 	  	$access[] = JHTMLSelect::option( -2, '- disabled -' );
 	  	//$access[] = JHTMLSelect::option( 0 , '- Everybody -' );
 	  	$access[] = JHTMLSelect::option( -1, '- All Registered Users -' );
 	  	//$pub_groups = array_merge( $pub_groups, $acl->get_group_children_tree( null, 'Registered', true ) );
		$access = array_merge( $access, $acl->get_group_children_tree( null, 'USERS', false ) );

		//Create the access control list
		$accessLists['evdel_access']	= JHTMLSelect::genericList( $access, 'delivereventsyes', 'class="inputbox" size="4"', 'value', 'text', $elsettings->delivereventsyes );
		$accessLists['locdel_access']	= JHTMLSelect::genericList( $access, 'deliverlocsyes', 'class="inputbox" size="4"', 'value', 'text', $elsettings->deliverlocsyes );
		$accessLists['evpub_access']	= JHTMLSelect::genericList( $access, 'autopubl', 'class="inputbox" size="4"', 'value', 'text', $elsettings->autopubl );
		$accessLists['locpub_access']	= JHTMLSelect::genericList( $access, 'autopublocate', 'class="inputbox" size="4"', 'value', 'text', $elsettings->autopublocate );
		$accessLists['ev_edit']			= JHTMLSelect::genericList( $access, 'eventedit', 'class="inputbox" size="4"', 'value', 'text', $elsettings->eventedit );
		$accessLists['venue_edit']		= JHTMLSelect::genericList( $access, 'venueedit', 'class="inputbox" size="4"', 'value', 'text', $elsettings->venueedit );

		//assign data to template
		$this->assignRef('live_site'	, $live_site);
		$this->assignRef('accessLists'	, $accessLists);
		$this->assignRef('elsettings'	, $elsettings);
		$this->assignRef('WarningIcon'	, $this->WarningIcon());
		$this->assignRef('request_url'	, $uri->toString());

		parent::display();

	}

	function WarningIcon()
	{
		global $mainframe;

		$url = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		$tip = '<img src="'.$url.'includes/js/ThemeOffice/warning.png" border="0"  alt="" />';

		return $tip;
	}
}