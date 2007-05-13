<?php
/**
 * @version 0.9 $Id: view.html.php 115 2007-05-03 15:03:31Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Category View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategory extends JView
{
	/**
	 * Creates the Category View
	 *
	 * @since 0.9
	 */
	function display( $tpl=null )
	{
		global $mainframe;

		//initialize variables
		$document	= & JFactory::getDocument();
		$menu		= & JMenu::getInstance();
		$item		=& $menu->getActive();
		$params	=& $mainframe->getPageParameters();
		$layout 	= JRequest::getVar('layout', 'default');

//DEPRECATED
$elsettings	= ELHelper::config();

		//cleanup events
		ELHelper::cleanevents( $elsettings->lastupdate );

		//add css file
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		// Request variables
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$limit      = $mainframe->getUserStateFromRequest('com_eventlist.categoryevents.limit', 'limit', $params->def('display_num', 0));
		$task 	= JRequest::getVar('task', '', '', 'string');
		$cid		= JRequest::getVar('cid', 0, '', 'int');

		if($layout == 'list') {
			$rows		= & $this->get('Categories');
			$total 	= & $this->get('Total');
			$category	= '';
		} else {
			if($layout == 'details') {
				$rows		= & $this->get('Data');
				$total 	= & $this->get('Total');
				$category	= & $this->get('Categories');;
			} else {
				//get data from model
				$rows 	= & $this->get('Data');
				$category 	= & $this->get('Category');
				$total 	= & $this->get('Total');
			}
		}

		//are events available?
		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

		//does the category exist
		if ($category->id == 0 && $layout == 'default')
		{
			return JError::raiseError( 404, JText::sprintf( 'Category #%d not found', $categid ) );
		}

		//Set Meta data
		$document->setTitle( $item->name.' - '.$category->catname );
		$document->setMetadata( 'keywords', $category->meta_keywords );
		$document->setDescription( strip_tags($category->meta_description) );

		if ($params->def('page_title', 1)) {
			$params->def('header', $item->name);
		}
		if ( JRequest::getVar('pop', 0) ) {
			$params->set( 'popup', 1 );
		}

		$print_link = JRoute::_( 'index.php?view=category&layout=events&cid='. $category->id .'&pop=1&tmpl=component');

		//add alternate feed link
		$link    = 'index.php?view=category&format=feed&cid='.$cid;
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink(JRoute::_($link.'&type=atom', 'alternate', 'rel'), $attribs);

		//create the pathway
		if ($task == 'catarchive') {
			$pathway 	= & $mainframe->getPathWay();
			$pathway->addItem( JText::_( 'ARCHIVE' ).' - '.$category->catname, JRoute::_('index.php?view=category&layout=events&task=catarchive&cid='.$cid));
		} else {
			$pathway 	= & $mainframe->getPathWay();
			$pathway->addItem( $category->catname, JRoute::_('index.php?view=category&layout=events&cid='.$cid));
		}

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		// Create the pagination object
		$page = $total - $limit;

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		//create the form links
		if ($task == 'catarchive') {
			$link = JRoute::_( 'index.php?view=category&layout=events&task=catarchive&cid='.$category->id );
		} else {
			$link = JRoute::_( 'index.php?view=category&layout=events&cid='.$category->id );
		}


		//Generate Categorydescription
		if (empty ($category->catdescription)) {
			$catdescription = JText::_( 'NO DESCRIPTION' );
		} else {
			//execute plugins
			$category->text	= $category->catdescription;
			$category->title 	= $category->catname;
			JPluginHelper::importPlugin('content');
			$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$category, &$params, 0 ));
			$catdescription = $category->text;
		}

		if(JRequest::getVar('layout', 'default') == 'details') {
			foreach($rows as $row) {
				$row_out[$row->catid][] = $row;
			}
			$rows = $row_out;
		}

		//create select lists
		$lists	= $this->_buildSortLists($elsettings);
		$this->assign('lists'     , $lists);

		$this->assignRef('rows' , 		$rows);
		$this->assignRef('noevents' , 	$noevents);
		$this->assignRef('category' , 	$category);
		$this->assignRef('print_link' , 	$print_link);
		$this->assignRef('params' , 		$params);
		$this->assignRef('dellink' , 		$dellink);
		$this->assignRef('task' , 		$task);
		$this->assignRef('catdescription' , $catdescription);
		$this->assignRef('live_site' , 	$live_site);
		$this->assignRef('link' , 		$link);
		$this->assignRef('cid' , 		$categid);
		$this->assignRef('pageNav' , 		$pageNav);
		$this->assignRef('page' , 		$page);
		$this->assignRef('elsettings' , 	$elsettings);
		$this->assignRef('item' , 		$item);

		parent::display($tpl);
	}

	/**
	 * Manipulate Data
	 *
	 * @since 0.9
	 */
	function &getRows($rows = '')
	{
		global $mainframe;

		if(!is_array($rows)) {
			$rows = $this->rows;
		}

		if (!count( $rows ) ) {
			return;
		}
		$k = 0;
		for($i = 0; $i <  count($rows); $i++)
		{
			//initialise
			$displaydate = null;
			$displaytime = null;

			$row =& $rows[$i];

			//Format date
			$date = strftime( $this->elsettings->formatdate, strtotime( $row->dates ));
			if (!$row->enddates) {
				$displaydate = $date;
			} else {
				$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
				$displaydate = $date.' - '.$enddate;
			}

			//Format time
			if ($this->elsettings->showtime == 1) {
				if ($row->times) {
					$time = strftime( $this->elsettings->formattime, strtotime( $row->times ));
					$time = $time.' '.$this->elsettings->timename;
					$displaytime = '<br />'.$time;

				}
				if ($row->endtimes) {
					$endtime = strftime( $this->elsettings->formattime, strtotime( $row->endtimes ));
					$endtime = $endtime.' '.$this->elsettings->timename;
					$displaytime = '<br />'.$time.' - '.$endtime;

				}
			}

			if ($displaytime) {
				$row->displaytime = $displaytime;
			} else {
			//	$row->displaytime = '<br />-';
				$row->displaytime = '';
			}

			$row->displaydate = $displaydate;
			$row->odd   = $k;
			$k = 1 - $k;
		}

		return $rows;
	}

	function _buildSortLists($elsettings)
	{
		// Table ordering values
		$filter_order		= JRequest::getVar('filter_order');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir');

		$filter				= JRequest::getVar('filter');
		$filter_type		= JRequest::getVar('filter_type');

		$sortselects = array();
		$sortselects[]	= JHTML::_('select.option', 'title', $elsettings->titlename );
		$sortselects[] 	= JHTML::_('select.option', 'venue', $elsettings->locationname );
		$sortselects[] 	= JHTML::_('select.option', 'city', $elsettings->cityname );
		$sortselect 	= JHTML::_('select.genericlist', $sortselects, 'filter_type', 'size="1" class="inputbox"', 'value', 'text', $filter_type );

		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		$lists['filter'] 		= $filter;
		$lists['filter_type'] 	= $sortselect;

		return $lists;
	}
}
?>