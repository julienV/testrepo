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
 * EventList Component Venueevents Model
 *
 * @package Joomla 
 * @subpackage EventList
 * @since		0.9
 */
class EventListViewCategoryevents extends JView 
{	
	/**
	 * Creates the Event Feed of the Category
	 *
	 * @since 0.9
	 */
	function display( )
	{
		global $mainframe;

		$doc 	= & JFactory::getDocument();

		// Get some data from the model
		$rows 		= & $this->get('Categoryevents');
		$limit 	= '10';

		JRequest::setVar('limit', $limit);

		foreach ( $rows as $row )
		{
			// strip html from feed item title
			$title = htmlspecialchars( $row->title );
			$title = html_entity_decode( $title );

			// url link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = JURI::base().'index.php?option=com_eventlist&view=details&did='. $row->id;
			$link = JRoute::_( $link );

			// strip html from feed item description text
			$description = $row->datdescription;
			//@$date = ( $row->dates ? date( 'r', strtotime($row->dates.''.$row->times) ) : '' );
			@$date = ( $row->created ? date( 'r', strtotime($row->created) ) : '' );
			
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