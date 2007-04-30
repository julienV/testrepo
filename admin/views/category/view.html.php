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
 * View class for the EventList category screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategory extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$pane 		= & JPane::getInstance('sliders');

		//get vars
		$cid 		= JRequest::getVar( 'cid' );

		//add css to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//create the toolbar
		if ( $cid ) {
			JToolBarHelper::title( JText::_( 'EDIT CATEGORY' ), 'categoriesedit' );

		} else {
			JToolBarHelper::title( JText::_( 'ADD CATEGORY' ), 'categoriesedit' );

			//set the submenu
			$submenu = ELAdmin::submenu();
			$document->setBuffer($submenu, 'module', 'submenu');

		}
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::media_manager();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'el.editcategories', true );

		//Get data from the model
		$row     	= & $this->get( 'Data' );
		$groups = & $this->get( 'Groups' );

		//clean data
		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'catdescription' );

		//build selectlists
		$Lists = array();
		$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		$Lists['imagelist'] 		= JAdministratorHelper::Images( 'image', $row->image, $javascript, '/images/stories/' );
		$Lists['access'] 			= JAdministratorHelper::Access( $row );


		//build grouplist
		$grouplist		= array();
		$grouplist[] 	= JHTMLSelect::option( '0', JText::_( 'NO GROUP' ) );
		$grouplist 		= array_merge( $grouplist, $groups );

		$Lists['groups']	= JHTMLSelect::genericList( $grouplist, 'groupid', 'size="1" class="inputbox"', 'value', 'text', $row->groupid );

		//assign data to template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('editor'		, $editor);
		$this->assignRef('pane'			, $pane);

		parent::display($tpl);
	}
}
?>