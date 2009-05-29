<?php
/**
 * @version 1.0 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
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
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Categoryevents Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCalendar extends JModel
{
    /**
     * Events data array
     *
     * @var array
     */
    var $_data = null;

    /**
     * Tree categories data array
     *
     * @var array
     */
    var $_categories = null;

    /**
     * Events total
     *
     * @var integer
     */
    var $_total = null;

    /**
     * Pagination object
     *
     * @var object
     */
    var $_pagination = null;

    /**
     * The reference date
     *
     * @var int unix timestamp
     */
    var $_date = 0;

    /**
     * Constructor
     *
     * @since 0.9
     */
    function __construct()
    {
        parent::__construct();

        $app = & JFactory::getApplication();

        $this->setdate(time());

        // Get the paramaters of the active menu item
        $params = & $app->getParams();
    }

    function setdate($date)
    {
        $this->_date = $date;
    }

    /**
     * Method to get the events
     *
     * @access public
     * @return array
     */
    function & getData()
    {

        // Lets load the content if it doesn't already exist
        if ( empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList( $query );

            $multi = array();

			foreach($this->_data as $item)
			{
				
				$item->categories = $this->getCategories($item->id);
				
				if(!is_null($item->enddates) ) {
					if( $item->enddates != $item->dates) {
					
						$day = $item->start_day;

						for ($counter = 0; $counter <= $item->datediff-1; $counter++)
						{
							$day++;
						
							//next day:
							$nextday = mktime(0, 0, 0, $item->start_month, $day, $item->start_year);
							//echo strftime('%Y-%m-%d', $nextday).' - ';
							
							//ensure we only generate days of current month
							if (strftime('%m', $this->_date) == strftime('%m', $nextday)) {
								$multi[$counter] = clone $item;
								$multi[$counter]->dates = strftime('%Y-%m-%d', $nextday);
								
								//add generated days to data
								$this->_data = array_merge($this->_data, $multi);
							}
							//unset temp array holding generated days before working on the next multiday event
							unset($multi);
						}
						//$this->_data = array_merge($this->_data, $multi);
					}
				}

				//remove events without categories (users have no access to them)
				if (empty($item->categories)) {
					unset($item);
				}
			}
			
			//$this->_data = array_merge($this->_data, $multi);

		}

        return $this->_data;
    }

    /**
     * Total nr of events
     *
     * @access public
     * @return integer
     */
    function getTotal()
    {
        // Lets load the total nr if it doesn't already exist
        if ( empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    /**
     * Method to get a pagination object for the events
     *
     * @access public
     * @return integer
     */
    function getPagination()
    {
        // Lets load the content if it doesn't already exist
        if ( empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    /**
     * Build the query
     *
     * @access private
     * @return string
     */
    function _buildQuery()
    {
        // Get the WHERE and ORDER BY clauses for the query
        $where = $this->_buildCategoryWhere();
		$orderby = $this->_buildCategoryOrderby();

        //Get Events from Database
        $query = 'SELECT DATEDIFF(a.enddates, a.dates) AS datediff, a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, a.created, l.venue, l.city, l.state, l.url,'
        .' DAYOFMONTH(a.dates) AS start_day, YEAR(a.dates) AS start_year, MONTH(a.dates) AS start_month,'
        .' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
        .' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug'
        .' FROM #__eventlist_events AS a'
        .' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
        .$where
		.$orderby
        ;

        return $query;
    }

    /**
     * Method to build the WHERE clause
     *
     * @access private
     * @return array
     */
    function _buildCategoryWhere()
    {
        $app = & JFactory::getApplication();

        // Get the paramaters of the active menu item
        $params = & $app->getParams();

        $task = JRequest::getWord('task');

        // First thing we need to do is to select only the published events
        if ($task == 'archive')
        {
            $where = ' WHERE a.published = -1 ';
        } else
        {
            $where = ' WHERE a.published = 1 ';
        }

        // only select events within specified dates. (chosen month)
        $monthstart = mktime(0, 0, 1, strftime('%m', $this->_date), 1, strftime('%Y', $this->_date));
        $monthend = mktime(0, 0, -1, strftime('%m', $this->_date)+1, 1, strftime('%Y', $this->_date));
        
        //$where .= ' AND a.dates >= "'.strftime('%Y-%m-%d', $monthstart).'"';
        //$where .= ' AND a.dates <= "'.strftime('%Y-%m-%d', $monthend).'"';
        
        $where .= ' AND (a.dates BETWEEN (\''.strftime('%Y-%m-%d', $monthstart).'\') AND (\''.strftime('%Y-%m-%d', $monthend).'\'))';
        $where .= ' OR (a.enddates BETWEEN (\''.strftime('%Y-%m-%d', $monthstart).'\') AND (\''.strftime('%Y-%m-%d', $monthend).'\'))';

        /*
         * If we have a filter, and this is enabled... lets tack the AND clause
         * for the filter onto the WHERE clause of the content item query.
         */
        if ($params->get('filter'))
        {
            $filter = JRequest::getString('filter', '', 'request');
            $filter_type = JRequest::getWord('filter_type', '', 'request');

            if ($filter)
            {
                // clean filter variables
                $filter = JString::strtolower($filter);
                $filter = $this->_db->Quote('%'.$this->_db->getEscaped($filter, true).'%', false);
                $filter_type = JString::strtolower($filter_type);

                switch($filter_type)
                {
                    case 'title':
                        $where .= ' AND LOWER( a.title ) LIKE '.$filter;
                        break;

                    case 'venue':
                        $where .= ' AND LOWER( l.venue ) LIKE '.$filter;
                        break;

                    case 'city':
                        $where .= ' AND LOWER( l.city ) LIKE '.$filter;
                        break;
                }
            }
        }
        return $where;
    }
	
	function _buildCategoryOrderby()
	{
		$orderby 	= ' ORDER BY a.dates, a.times';
		
		return $orderby;
	}

    /**
     * Method to get the Categories
     *
     * @access public
     * @return integer
     */
    function getCategories($id)
    {
    	$user = & JFactory::getUser();
        $gid = (int)$user->get('aid');

        
        $query = 'SELECT c.id, c.catname, c.access, c.color, c.published, c.checked_out AS cchecked_out,'
        . ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug'
        . ' FROM #__eventlist_categories AS c'
        . ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.catid = c.id'
        . ' WHERE rel.itemid = '.(int)$id
		. ' AND c.published = 1'
        . ' AND c.access  <= '.$gid;
        ;

        $this->_db->setQuery($query);

        $this->_categories = $this->_db->loadObjectList();
        
        return $this->_categories;
    }
}
?>
