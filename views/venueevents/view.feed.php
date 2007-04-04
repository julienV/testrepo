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
	 * Creates the Event Feed of the Venue
	 *
	 * @since 0.9
	 */
	function display( )
	{
		global $mainframe;

		$doc 		= & JFactory::getDocument();
		$elsettings = ELHelper::config();

		// Get some data from the model
		$rows 		= & $this->get('Data');
		$limit 	= '10';

		JRequest::setVar('limit', $limit);

		foreach ( $rows as $row )
		{
			// strip html from feed item title
			$title = htmlspecialchars( $row->title );
			$title = html_entity_decode( $title );
			
			// strip html from feed item category
			$category = htmlspecialchars( $row->catname );
			$category = html_entity_decode( $category );
			
			//Format date
			$date = strftime( $elsettings->formatdate, strtotime( $row->dates ));
			if (!$row->enddates) {
				$displaydate = $date;
			} else {
				$enddate 	= strftime( $elsettings->formatdate, strtotime( $row->enddates ));
				$displaydate = $date.' - '.$enddate;
			}
			
			//Format time
			if ($row->times) {
				$time = strftime( $elsettings->formattime, strtotime( $row->times ));
				$time = $time.' '.$elsettings->timename;
				$displaytime = $time;	
			}
			if ($row->endtimes) {
				$endtime = strftime( $elsettings->formattime, strtotime( $row->endtimes ));
				$endtime = $endtime.' '.$elsettings->timename;
				$displaytime = $time.' - '.$endtime;
			}

			// url link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = JURI::base().'index.php?option=com_eventlist&view=details&did='. $row->id;
			$link = JRoute::_( $link );

			// feed item description text
			$description = JText::_( 'TITLE' ).': '.$title.'<br />';
			$description .= JText::_( 'VENUE' ).': '.$row->venue.' / '.$row->city.'<br />';
			$description .= JText::_( 'CATEGORY' ).': '.$category.'<br />';
			$description .= JText::_( 'DATE' ).': '.$displaydate.'<br />';
			$description .= JText::_( 'TIME' ).': '.$displaytime.'<br />';
			$description .= JText::_( 'DESCRIPTION' ).': '.$row->datdescription;
			
			@$created = ( $row->created ? date( 'r', strtotime($row->created) ) : '' );
			
			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= $created;
			$item->category   	= $category;

			// loads item info into rss array
			$doc->addItem( $item );
		}
	}
}
?>