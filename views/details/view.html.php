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
 * HTML Details View class of the EventList component
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewDetails extends JView
{
	/**
	 * Creates the output for the details view
	 *
 	 * @since 0.9
	 */
	function display($tpl = null)
	{
		global $mainframe, $option;

		$live_site 	= $mainframe->getCfg('live_site');
		$document 	= & JFactory::getDocument();
		$user		= & JFactory::getUser();
		$elsettings = ELHelper::config();

		$row		= & $this->get('Details');
		$registers	= & $this->get('Registers');
		$regcheck	= & $this->get('Usercheck');

		//cleanup events
		ELHelper::cleanevents( $elsettings->lastupdate );

		if ($elsettings->comunsolution == 1) {
			$pics		= & $this->get('Avatars');
		} else {
			$pics = '';
		}
		
		//Check if the id exists
		if ($row->did == 0)
		{
			return JError::raiseError( 404, JText::sprintf( 'Event #%d not found', $row->did ) );
		}

		//Check if user has access to the details
		if ($elsettings->showdetails == 0) {
			return JError::raiseError( 403, JText::_( 'NO ACCESS' ) );
		}

		//add css file
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}</style><![endif]-->');

		// Add needed scripts if the lightbox effect is enabled
		if ($elsettings->lightbox == 1) {
  			$document->addScript('components/com_eventlist/assets/js/slimbox.js');
  			$document->addStyleSheet('components/com_eventlist/assets/css/slimbox.css', 'text/css', 'screen');
		}

		//get menu information
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		//Print
		$pop	= JRequest::getVar('pop', 0, '', 'int');
		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		if ($params->def('page_title', 1)) {
			$params->def('header', $item->name);
		}

		if ( $pop ) {
			$params->set( 'popup', 1 );
		}

		$print_link = JRoute::_('index.php?option=com_eventlist&view=details&did='. $row->did .'&pop=1&tmpl=component');

		//pathway
		$pathway 	= & $mainframe->getPathWay();
		$pathway->setItemName(1, $item->name);
		$pathway->addItem( JText::_( 'DETAILS' ). ' - '.$row->title, JRoute::_('index.php?option='.$option.'&view=details&did='.$row->did));

		//Get images
		$dimage = ELImage::eventimage($live_site, $row->datimage, $elsettings->imagewidth, $elsettings->imagehight, $elsettings->imageprob, $elsettings->gddisabled);
		$limage = ELImage::venueimage($live_site, $row->locimage, $elsettings->imagewidth, $elsettings->imagehight, $elsettings->imageprob, $elsettings->gddisabled);

		//Check user if he can edit
		$allowedtoeditevent = ELUser::editaccess($elsettings->eventowner, $row->created_by, $elsettings->eventeditrec, $elsettings->eventedit);
		$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $row->venueowner, $elsettings->venueeditrec, $elsettings->venueedit);

		//Generate Date
		$date 	= strftime( $elsettings->formatdate ,strtotime( $row->dates ));
		
		if ($row->times) {
		$time 	= strftime( $elsettings->formattime ,strtotime( $row->times ));
		}

		if (!$row->enddates) {
			$displaydate = $date.'<br />';
		} else {
			$enddate 	= strftime( $elsettings->formatdate, strtotime( $row->enddates ));
			$displaydate = $date.' - '.$enddate.'<br />';
		}

		//Generate Time
		if (( $elsettings->showtimedetails == 1 ) && ($row->times)) {
			$starttime = $time.' '.$elsettings->timename;

			if ($row->endtimes) {
				$endtime = strftime( $elsettings->formattime ,strtotime( $row->endtimes ));
				$endtime = ' - '.$endtime.' '.$elsettings->timename;
				$displaytime = $starttime.$endtime;
			} else {
				$displaytime = $starttime;
			}
		}

		//Timecheck for registration
		$jetzt = date("Y-m-d");
		$now = strtotime($jetzt);
		$date = strtotime($row->dates);
		$timecheck = $now - $date;

		//let's build the registration handling
		$formhandler  = 0;

		//is the user allready registered at the event
		if ( $regcheck ) {
			$formhandler = 3;
		} else {
			//no, he isn't
			$formhandler = 4;
		}

		//check if it is too late to register and overwrite $formhandler
		if ( $timecheck > 0 ) {
			$formhandler = 1;
		}

		//is the user registered at joomla and overwrite $formhandler if not
		if ( !$user->get('id') ) {
			$formhandler = 2;
		}

		//Generate Eventdescription
		if (($row->datdescription == '') || ($row->datdescription == '<br />')) {
			$eventdescription = JText::_( 'NO DESCRIPTION' ) ;
		} else {
			//Execute Plugins
			$row->text	= $row->datdescription;
			//$row->title = $row->title;
			JPluginHelper::importPlugin('content');
			$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$row, &$params, 0 ));
			$eventdescription = $row->text;
		}

		//Generate Venuedescription
		if (empty ($row->locdescription)) {
			$venuedescription = JText::_( 'NO DESCRIPTION' );
		} else {
			//execute plugins
			$row->text	=	$row->locdescription;
			//$row->title = $row->club;
			JPluginHelper::importPlugin('content');
			$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$row, &$params, 0 ));
			$venuedescription = $row->text;
		}

		//get email button
		if ($params->get('icons')) 	{
			$mailbutton = JAdminMenus::ImageCheck('emailButton.png', '/images/M_images/', NULL, NULL, JText::_('Mail to Friend'), JText::_('Mail to Friend'));
		} else {
			$mailbutton = '&nbsp;'.JText::_('Mail to Friend');
		}

		// generate Metatags
		$meta_keywords_content = "";
		if (!empty($row->meta_keywords)) {
			$keywords = explode(",",$row->meta_keywords);
			foreach($keywords as $keyword) {
				if ($meta_keywords_content != "") {
					$meta_keywords_content .= ", ";
				}
				if (ereg("[/[/]",$keyword)) {
					$keyword = trim(str_replace("[","",str_replace("]","",$keyword)));
					$meta_keywords_content .= $this->keyword_switcher($keyword, $row, $elsettings->formattime, $elsettings->formatdate);
				} else {
					$meta_keywords_content .= $keyword;
				}

			}
		}
		if (!empty($row->meta_description)) {
			$description = explode("[",$row->meta_description);
			$description_content = "";
			foreach($description as $desc) {
					$keyword = substr($desc, 0, strpos($desc,"]",0));
					if ($keyword != "") {
						$description_content .= $this->keyword_switcher($keyword, $row, $elsettings->formattime, $elsettings->formatdate);
						$description_content .= substr($desc, strpos($desc,"]",0)+1);
					} else {
						$description_content .= $desc;
					}

			}
		} else {
			$description_content = "";
		}

		//set page title and meta stuff
		$document->setTitle( $item->name.' - '.$row->title );
        $document->setMetadata('keywords', $meta_keywords_content );
        $document->setDescription( strip_tags($description_content) );

        //build the url
        if(strtolower(substr($row->url, 0, 7)) != "http://") {
        	$row->url = 'http://'.$row->url;
        }

		//assign vars to jview
		$this->assignRef('row', 					$row);
		$this->assignRef('mailbutton' , 			$mailbutton);
		$this->assignRef('params' , 				$params);
		$this->assignRef('allowedtoeditevent' , 	$allowedtoeditevent);
		$this->assignRef('allowedtoeditvenue' , 	$allowedtoeditvenue);
		$this->assignRef('dimage' , 				$dimage);
		$this->assignRef('limage' , 				$limage);
		$this->assignRef('displaytime' , 			$displaytime);
		$this->assignRef('displaydate' , 			$displaydate);
		$this->assignRef('print_link' , 			$print_link);
		$this->assignRef('eventdescription' , 		$eventdescription);
		$this->assignRef('venuedescription' , 		$venuedescription);
		$this->assignRef('registers' , 				$registers);
		$this->assignRef('pics' , 					$pics);
		$this->assignRef('formhandler',				$formhandler);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('item' , 					$item);

		parent::display($tpl);
	}

	/**
	 * structures the keywords
	 *
 	 * @since 0.9
	 */
	function keyword_switcher($keyword, &$row, $formattime, $formatdate) {
		switch ($keyword) {
			case "catsid":
				$content = $row->catname;
				break;
			case "a_name":
				$content = $row->club;
				break;
			case "times":
			case "endtimes":
				$content = strftime( $formattime ,strtotime( $row->$keyword ) );
				break;
			case "dates":
			case "enddates":
				$content = strftime( $formatdate ,strtotime( $row->$keyword ) );
				break;
			default:
				$content = $row->$keyword;
				break;
		}
		return $content;
	}
}
?>