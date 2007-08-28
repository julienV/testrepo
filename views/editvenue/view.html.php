<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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

		// Get requests
		$id				= JRequest::getInt('id');
		$returnview		= JRequest::getWord('returnview');

		//Get Data from the model
		$row 		= $this->Get('Venue');

		jimport('joomla.filter.output');
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );

		JHTML::_('behavior.formvalidation');
		JHTML::_('behavior.tooltip');

		//add css file
		$doc->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$doc->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		// Get the menu object of the active menu item
		$menu		= & JMenu::getInstance();
		$item    	= $menu->getActive();
		$params 	= & $mainframe->getPageParameters('com_eventlist');

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
		$limage = ELImage::flyercreator($row->locimage, $elsettings);

		//Set the info image
		$infoimage = JHTML::_('image.site', 'icon-16-hint.png', 'components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );

		$this->assignRef('row' , 					$row);
		$this->assignRef('editor' , 				$editor);
		$this->assignRef('editoruser' , 			$editoruser);
		$this->assignRef('limage' , 				$limage);
		$this->assignRef('infoimage' , 				$infoimage);
		$this->assignRef('returnview' ,				$returnview);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('item' , 					$item);

		parent::display($tpl);

	}
}
?>