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
 * HTML View class for the Categoriesview View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategoriesview extends JView
{
	function display( $tpl=null )
	{
		global $mainframe;

		$document 	= & JFactory::getDocument();
		$elsettings = ELHelper::config();

		$rows 		= & $this->get('Data');
		$total 		= & $this->get('Total');

		//cleanup events
		ELHelper::cleanevents( $elsettings->lastupdate );

		//add css file
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		//get menu information
		$menu		= & JMenu::getInstance();
		$item    	= $menu->getActive();
		$params 	= & $mainframe->getPageParameters('com_eventlist');

		// Request variables
		$limitstart		= JRequest::getInt('limitstart');
		$limit			= JRequest::getInt('limit', $params->get('cat_num'));
		$task			= JRequest::getWord('task', '', '', 'string');

		$params->def( 'page_title', $item->name);

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);

		if ( $task == 'archive' ) {
			$pathway->addItem(JText::_( 'ARCHIVE' ), JRoute::_('index.php?view=categoriesview&task=archive') );
		}

		//Set Page title
		$mainframe->setPageTitle( $params->get('page_title') );
   		$mainframe->addMetaTag( 'title' , $params->get('page_title') );

		//get icon settings
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		//add alternate feed link
		$link    = 'index.php?option=com_eventlist&view=eventlist&format=feed';
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		// Create the pagination object
		jimport('joomla.html.pagination');

		$page = $total - $limit;

		$pageNav = new JPagination($total, $limitstart, $limit);

		if ( $task == 'archive' ) {
			$link = JRoute::_('index.php?view=categoriesview&task=archive');
		} else {
			$link = JRoute::_('index.php?view=categoriesview');
		}

		$this->assignRef('rows' , 					$rows);
		$this->assignRef('task' , 					$task);
		$this->assignRef('params' , 				$params);
		$this->assignRef('dellink' , 				$dellink);
		$this->assignRef('page' , 					$page);
		$this->assignRef('pageNav' , 				$pageNav);
		$this->assignRef('link' , 					$link);
		$this->assignRef('item' , 					$item);
		$this->assignRef('elsettings' , 			$elsettings);


		parent::display($tpl);
	}
}
?>