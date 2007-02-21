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
 * HTML View class for the Categoriesdetailed View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategoriesdetailed extends JView
{
	/**
	 * Creates the Categoriesdetailed View
	 *
	 * @since 0.9
	 */
	function display( $tpl = null )
	{
		global $Itemid, $mainframe, $option;

		$document 	= & JFactory::getDocument();
		$elsettings = & ELHelper::config();
		$model 		= $this->getModel();

		$categories	= & $this->get('Data');
		$total 		= & $this->get('Total');

		//cleanup events
		ELHelper::cleanevents( $elsettings->lastupdate );

		//add css file
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		//get menu information
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		$live_site	= $mainframe->getCfg('live_site');

		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');
		$limit			= JRequest::getVar('limit', $params->get('cat_num'), '', 'int');

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);

		//set Page title
		$mainframe->setPageTitle( $item->name );
		$mainframe->addMetaTag( 'title' , $item->name );

		//Druckfunktion
		$pop	= JRequest::getVar('pop', 0, '', 'int');

		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		if ( $pop ) {
			$params->set( 'popup', 1 );
		}

		if ($params->def('page_title', 1)) {
			$params->def('header', $item->name);
		}

		$print_link = $live_site. '/index2.php?option=com_eventlist&amp;Itemid='. $Itemid .'&amp;pop=1';

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		//add alternate feed link
		$link    = 'feed.php?option=com_eventlist&amp;Itemid='.$Itemid.'&amp;view=eventlist';
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink($link.'&amp;format=rss', 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink($link.'&amp;format=atom', 'alternate', 'rel', $attribs);

		$page = $total - $limit;

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		$link = 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view=categoriesdetailed';

	//	$this->assignRef('rows' , 					$rows);
		$this->assignRef('categories' , 			$categories);
		$this->assignRef('print_link' , 			$print_link);
		$this->assignRef('params' , 				$params);
		$this->assignRef('dellink' , 				$dellink);
	//	$this->assignRef('menu' , 					$menu);
		$this->assignRef('pop' , 					$pop);
		$this->assignRef('live_site' , 				$live_site);
		$this->assignRef('model' , 					$model);
		$this->assignRef('pageNav' , 				$pageNav);
		$this->assignRef('page' , 					$page);
		$this->assignRef('link' , 					$link);
		$this->assignRef('elsettings' , 			$elsettings);

		parent::display($tpl);

	}//function end

	/**
	 * Manipulate Data
	 *
	 * @since 0.9
	 */
	function getRows()
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
			if (!$row->enddates) {
				$displaydate = $date;
			} else {
				$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
				$displaydate = $date.' - '.$enddate;
			}

			//Format time
			unset($displaytime);
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
			
			if (isset($displaytime)) {
				$row->displaytime = $displaytime;
			} else {
				$row->displaytime = '<br />-';
			}

			$row->displaydate = $displaydate;
			$row->odd   = $k;
			$k = 1 - $k;
		}

		return $this->rows;
	}
}
?>