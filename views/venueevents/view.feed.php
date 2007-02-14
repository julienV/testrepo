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
		global $mainframe, $Itemid;

		$doc 	= & JFactory::getDocument();

		// Get some data from the model
		$rows 		= & $this->get('Venueevents');
		$limit 	= '10';

		JRequest::setVar('limit', $limit);

		foreach ( $rows as $row )
		{
			// strip html from feed item title
			$title = htmlspecialchars( $row->title );
			$title = html_entity_decode( $title );

			// url link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = 'index.php?option=com_eventlist&Itemid='. $Itemid .'view=details&did='. $row->id;
			$link = sefRelToAbs( $link );

			// strip html from feed item description text
			$description = $row->datdescription;
			@$date = ( $row->dates ? date( 'r', strtotime($row->dates.''.$row->times) ) : '' );

			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= $date;
			$item->category   	= $row->catname;

			// loads item info into rss array
			$doc->addItem( $item );
		}
	}
}
?>