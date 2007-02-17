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
 * HTML View class for the Simplelist View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewSimplelist extends JView
{
	/**
	 * Creates the Simple List View
	 *
	 * @since 0.9
	 */
	function display( $tpl = null )
	{

		global $mainframe;

		$document 	= & JFactory::getDocument();
		$elsettings = & ELHelper::config();

		// Get the menu object of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		//cleanup events
		ELHelper::cleanevents( $elsettings->lastupdate );

		//add css file
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		// Request variables
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');
		$limit			= JRequest::getVar('limit', $params->get('display_num'), '', 'int');

		$rows = & $this->get('Data');
		$total = & $this->get('Total');

		$live_site 	= $mainframe->getCfg('live_site');

		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

		//Print function
		$pop		= JRequest::getVar('pop', 0, '', 'int');

		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		if ($params->def('page_title', 1)) {
			$params->def('header', $item->name);
		}

		if ( $pop ) {//If printpopup set true
			$params->set( 'popup', 1 );
		}

		$print_link = $live_site. '/index.php?option=com_eventlist&amp;tmpl=component&amp;pop=1';

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);

		//Set Page title
		if (!$item->name) {
			$document->setTitle($item->name);
			$document->setMetadata( 'keywords' , $item->name );
		}

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		//add alternate feed link
		$link    = 'feed.php?option=com_eventlist&amp;view=simplelist&amp;Itemid='.$Itemid;
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink($link.'&amp;format=rss', 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink($link.'&amp;format=atom', 'alternate', 'rel', $attribs);

		// Create the pagination object
		$page = $total - $limit;

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		$link = 'index.php?view=simplelist';

		//create select lists
		$lists	= $this->_buildSortLists($elsettings);
		$this->assign('lists'     , $lists);

		$this->assignRef('rows' , 					$rows);
		$this->assignRef('noevents' , 				$noevents);
		$this->assignRef('print_link' , 			$print_link);
		$this->assignRef('params' , 				$params);
		$this->assignRef('dellink' , 				$dellink);
		$this->assignRef('pop' , 					$pop);
		$this->assignRef('pageNav' , 				$pageNav);
		$this->assignRef('page' , 					$page);
		$this->assignRef('link' , 					$link);
		$this->assignRef('elsettings' , 			$elsettings);

		parent::display($tpl);

	}//function ListEvents end

	/**
	 * Manipulate Data
	 *
	 * @access public
	 * @return object $rows
	 * @since 0.9
	 */
	function &getRows()
	{
		global $mainframe, $Itemid;

		if (!count( $this->rows ) ) {
			return;
		}

		$k = 0;
		for($i = 0; $i <  count($this->rows); $i++)
		{
			$row =& $this->rows[$i];

			//Format date
			$date = strftime( $this->elsettings->formatdate, strtotime( $row->dates ));
			if ($row->enddates == '0000-00-00') {
				$displaydate = $date;
			} else {
				$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
				$displaydate = $date.' - '.$enddate;
			}

			//Format time
			$time = strftime( $this->elsettings->formattime, strtotime( $row->times ));
			$time = $time.' '.$this->elsettings->timename;
			$endtime = strftime( $this->elsettings->formattime, strtotime( $row->endtimes ));
			$endtime = $endtime.' '.$this->elsettings->timename;

			if ($this->elsettings->showtime == 1) {
				if ($row->times != '00:00:00') {
					$displaytime = '<br />'.$time;
				}
				if ($row->endtimes != '00:00:00') {
					$displaytime = '<br />'.$time.' - '.$endtime;
				}
				$row->displaytime = $displaytime;
			}

			$row->displaydate = $displaydate;
			$row->odd   = $k;
			$k = 1 - $k;
		}

		return $this->rows;
	}

	/**
	 * Method to build the sortlists
	 *
	 * @access private
	 * @return array
	 * @since 0.9
	 */
	function _buildSortLists($elsettings)
	{
		// Table ordering values
		$filter_order		= JRequest::getVar('filter_order');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir');

		$filter				= JRequest::getVar('filter');
		$filter_type		= JRequest::getVar('filter_type');

		$sortselects = array();
		$sortselects[]	= JHTMLSelect::option( 'title', $elsettings->titlename );
		$sortselects[] 	= JHTMLSelect::option( 'venue', $elsettings->locationname );
		$sortselects[] 	= JHTMLSelect::option( 'city', $elsettings->cityname );
		$sortselect 	= JHTMLSelect::genericList( $sortselects, 'filter_type', 'size="1" class="inputbox"', 'value', 'text', $filter_type );

		if ($filter_order_Dir == 'DESC') {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}

		$lists['order'] 		= $filter_order;
		$lists['filter'] 		= $filter;
		$lists['filter_type'] 	= $sortselect;

		return $lists;
	}
}
?>