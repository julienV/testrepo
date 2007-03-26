<?php 
/**
 * @version 0.9 $Id$
 * @package Joomla 
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Editevents View
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEditvenue extends JView 
{
	/**
	 * Creates the output for venue submissions
	 *
	 * @since 0.5
	 * @param int $tpl
	 */
	function display( $tpl=null )
	{
		global $Itemid, $mainframe, $option;			

		$editor 	= & JFactory::getEditor();
		$doc 		= & JFactory::getDocument();
		$elsettings = ELHelper::config();

		$live_site 	= $mainframe->getCfg('live_site');
		
		// Get requests
		$id				= JRequest::getVar('id', 0, '', 'int');
		$returnview		= JRequest::getVar('returnview', '', '', 'string');
		
		//Get Data from the model
		$row 		= $this->Get('Venue');

		// Load tooltips behavior
		jimport('joomla.html.tooltips');
		
		jimport('joomla.filter.output');
		JOutputFilter::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );
		
		//add css file
		$doc->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$doc->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');
		
		// Get the menu object of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);
		
		$id ? $title = JText::_( 'EDIT VENUE' ) : $title = JText::_( 'ADD VENUE' );
		
		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);
		$pathway->addItem($title, '');
		
		//Set Title
		$doc->setTitle($title);
		
		//editor user
		$editoruser = ELUser::editoruser();
				
		//Get image
		$limage = ELImage::venueimage($live_site, $row->locimage, $elsettings->imagewidth, $elsettings->imagehight, $elsettings->imageprob, $elsettings->gddisabled);
		
		//Set the info image
		$infoimage = JAdminMenus::ImageCheck( 'icon-16-hint.png', 'components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );
		
		$this->assignRef('row' , 					$row);
		$this->assignRef('editor' , 				$editor);
		$this->assignRef('live_site' , 				$live_site);
		$this->assignRef('editoruser' , 			$editoruser);
		$this->assignRef('limage' , 				$limage);
		$this->assignRef('infoimage' , 				$infoimage);
		$this->assignRef('returnview' ,				$returnview);
		$this->assignRef('elsettings' , 			$elsettings);
		
		parent::display($tpl);

	}
}
?>