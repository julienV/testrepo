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

		jimport('joomla.html.pane');

		$live_site	= $mainframe->getCfg('live_site');
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$pane 		= & JPane::getInstance('sliders');

		// Get data from the model
		$row     	= & $this->get( 'Data');

		$cid = JRequest::getVar( 'cid' );

		if ( $cid ) {
			JMenuBar::title( JText::_( 'EDIT CATEGORY' ), 'categoriesedit' );

		} else {
			JMenuBar::title( JText::_( 'ADD CATEGORY' ), 'categoriesedit' );

			//set the submenu
			$submenu = ELAdmin::submenu();
			$document->setBuffer($submenu, 'module', 'submenu');

		}
		JMenuBar::apply();
		JMenuBar::spacer();
		JMenuBar::save('savecategory');
		JMenuBar::spacer();
		JMenuBar::media_manager();
		JMenuBar::spacer();
		JMenuBar::cancel('cancel');
		JMenuBar::spacer();
		JMenuBar::help( 'el.editcategories', true );

		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'catdescription' );

		$Lists = array();
		$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		$Lists['imagelist'] 		= JAdminMenus::Images( 'image', $row->image, $javascript, '/images/stories/' );
		$Lists['access'] 			= JAdminMenus::Access( $row );

		$groups = & $this->get( 'Groups');

		$grouplist		= array();
		$grouplist[] 	= JHTMLSelect::option( '0', JText::_( 'NO GROUP' ) );
		$grouplist 		= array_merge( $grouplist, $groups );

		$Lists['groups']	= JHTMLSelect::genericList( $grouplist, 'groupid', 'size="1" class="inputbox"', 'value', 'text', $row->groupid );

		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('row'      	, $row);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('editor'		, $editor);
		$this->assignRef('pane'			, $pane);

		parent::display($tpl);
	}
}
?>