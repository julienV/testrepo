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
 * HTML View class for the Venueevents View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewVenueevents extends JView
{
	/**
	 * Creates the Venueevents View
	 *
	 * @since 0.9
	 */
	function display( $tpl = null )
	{
		global $Itemid, $mainframe, $option;

		$document 	= & JFactory::getDocument();
		$elsettings = & ELHelper::config();
		$uri 		= & JFactory::getURI();

		//get menu information
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
		$live_site 		= $mainframe->getCfg('live_site');
		$locatid		= JRequest::getVar('locatid', 0, '', 'int');
		$pop			= JRequest::getVar('pop', 0, '', 'int');

		$rows 		= & $this->get('Data');
		$venue	 	= & $this->get('Venue');
		$total 		= & $this->get('Total');

		if ($venue->id == 0)
		{
			return JError::raiseError( 404, JText::sprintf( 'Venue #%d not found', $locatid ) );
		}

		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

		// Add needed scripts if the lightbox effect is enabled
		if ($elsettings->lightbox == 1) {
  			$document->addScript('components/com_eventlist/assets/js/slimbox.js');
  			$document->addStyleSheet('components/com_eventlist/assets/css/slimbox.css', 'text/css', 'screen');
		}

		//Get image
		$limage = ELImage::venueimage($live_site, $venue->locimage, $elsettings->imagewidth, $elsettings->imagehight, $elsettings->imageprob, $elsettings->gddisabled);

		//add alternate feed link
		$link    = 'feed.php?option=com_eventlist&amp;Itemid='.$Itemid.'&amp;view=venueevents&amp;locatid='.$venue->id;
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink($link.'&amp;format=rss', 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink($link.'&amp;format=atom', 'alternate', 'rel', $attribs);

		//set Page title
		$document->setTitle( $item->name.' - '.$venue->club );
		$document->setMetadata('keywords', $venue->meta_keywords );
		$document->setDescription( strip_tags($venue->meta_description) );

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);
		$pathway->addItem( $venue->club, JRoute::_('index.php?option='.$option.'&view=venueevents&locatid='.$locatid));

		//Printfunction
		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		if ($params->def('page_title', 1)) {
			$params->def('header', $item->name);
		}

		if ( $pop ) {
			$params->set( 'popup', 1 );
		}

		$print_link = $live_site. '/index2.php?option=com_eventlist&amp;Itemid='. $Itemid .'&amp;view=venueevents&amp;locatid='. $venue->id .'&amp;pop=1';

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		//Generate Venuedescription
		if (empty ($venue->locdescription)) {
			$venuedescription = JText::_( 'NO DESCRIPTION' );
		} else {
			//execute plugins
			$venue->text	= $venue->locdescription;
			$venue->title 	= $venue->club;
			JPluginHelper::importPlugin('content');
			$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$venue, &$params, 0 ));
			$venuedescription = $venue->text;
		}

		//build the url
        if(strtolower(substr($venue->url, 0, 7)) != "http://") {
        	$venue->url = 'http://'.$venue->url;
        }

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		$link = 'index.php?option=com_eventlist&amp;Itemid='.$Itemid.'&amp;view=venueevents&amp;locatid='.$venue->id;
		$page = $total - $limit;

		//create select lists
		$lists	= $this->_buildSortLists();
		$this->assign('lists'     , $lists);

		$this->assignRef('rows' , 					$rows);
		$this->assignRef('noevents' , 				$noevents);
		$this->assignRef('venue' , 					$venue);
		$this->assignRef('print_link' , 			$print_link);
		$this->assignRef('params' , 				$params);
		$this->assignRef('dellink' , 				$dellink);
		$this->assignRef('pop' , 					$pop);
		$this->assignRef('limage' , 				$limage);
		$this->assignRef('venuedescription' , 		$venuedescription);
		$this->assignRef('live_site' , 				$live_site);
		$this->assignRef('link' , 					$link);
		$this->assignRef('locatid' , 				$locatid);
		$this->assignRef('pageNav' , 				$pageNav);
		$this->assignRef('page' , 					$page);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('request_url',				$uri->toString());


		parent::display($tpl);
	}

	/**
	 * Manipulate Data
	 *
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

	function _buildSortLists()
	{
		// Table ordering values
		$filter_order		= JRequest::getVar('filter_order');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir');

		$filter				= JRequest::getVar('filter');

		$html= '';

		if ($filter_order_Dir == 'DESC') {
			$lists['order_Dir'] = 'ASC';
		} else {
			$lists['order_Dir'] = 'DESC';
		}

		$lists['order'] 		= $filter_order;
		$lists['filter'] 		= $filter;
		$lists['filter_type'] 	= $html;

		return $lists;
	}
}
?>