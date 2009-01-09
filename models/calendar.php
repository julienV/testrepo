<?php
/**
 * @version 1.0 $Id: categoryevents.php 662 2008-05-09 22:28:53Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2008 Christoph Lukes
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
     * category data array
     *
     * @var array
     */
    var $_category = null;

    /**
     * Tree categories data array
     *
     * @var array
     */
    var $_categories_ids = null;


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

        global $mainframe;

        $id = JRequest::getInt('id');
        $this->setId((int)$id);

        $this->setdate(time());

        // Get the paramaters of the active menu item
        $params = & $mainframe->getParams();
    }

    function setdate($date)
    {
        $this->_date = $date;
    }

    /**
     * Method to set the category id
     *
     * @access	public
     * @param	int	category ID number
     */
    function setId($id)
    {
        // Set new category ID and wipe data
        $this->_id = $id;
        $this->_data = null;
    }

    /**
     * Method to get the events
     *
     * @access public
     * @return array
     */
    function & getData()
    {
        $pop = JRequest::getBool('pop');

        // Lets load the content if it doesn't already exist
        if ( empty($this->_data))
        {
            $query = $this->_buildQuery();

            if ($pop)
            {
                $this->_data = $this->_getList($query);
            } else
            {
                $this->_data = $this->_getList($query);
            }
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

        //Get Events from Database
        $query = 'SELECT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, a.created, l.id, l.venue, l.city, l.state, l.url, c.catname, c.id AS catid, c.color, '
        .' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
        .' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug,'
        .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug'
        .' FROM #__eventlist_events AS a'
        .' INNER JOIN #__eventlist_cats_event_relations AS rel ON a.id = rel.itemid'
        .' INNER JOIN #__eventlist_categories AS c ON c.id = rel.catid'
        .' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
        .$where
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
        global $mainframe;

        $user = & JFactory::getUser();
        $gid = (int)$user->get('aid');

        // Get the paramaters of the active menu item
        $params = & $mainframe->getParams();

        $task = JRequest::getWord('task');

        // First thing we need to do is to select only the published events
        if ($task == 'archive')
        {
            $where = ' WHERE a.published = -1 ';
        } else
        {
            $where = ' WHERE a.published = 1 ';
        }

        // filter on categories: selected catagory /selected + child categories
        if ($this->_id)
        {
            if ($params->get('displayChilds'))
            {
                $where .= ' AND c.id IN ('.implode(',', $this->getChilds()).')';
            }
            else
            {
                $where .= ' AND c.id = '.$this->_id;
            }
        }

        // only select events assigned to category the user has access to
        $where .= ' AND c.access <= '.$gid;

        // only select events within specified dates.
        $monthstart = mktime(0, 0, 1, strftime('%m', $this->_date), 1, strftime('%Y', $this->_date));
        $monthend = mktime(0, 0, -1, strftime('%m', $this->_date)+1, 1, strftime('%Y', $this->_date));
        $where .= ' AND a.dates >= "'.strftime('%Y-%m-%d', $monthstart).'"';
        $where .= ' AND a.dates <= "'.strftime('%Y-%m-%d', $monthend).'"';

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

    /**
     * Method to get the Category
     *
     * @access public
     * @return integer
     */
    function getCategory()
    {
        $query = 'SELECT *,'
        .' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
        .' FROM #__eventlist_categories'
        .' WHERE id = '.$this->_id;

        $this->_db->setQuery($query);

        $_category = $this->_db->loadObject();

        return $_category;
    }


    /**
     * Method to get the Categories
     *
     * @access public
     * @return integer
     */
    function getCategories()
    {
        if ($this->_categories)
        {
            return $this->_categories;
        }

        $categories = $this->getChilds();

        $query = 'SELECT id, catname, color, '
        .' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
        .' FROM #__eventlist_categories'
        .' WHERE id IN ('.implode(',', $categories).')'
        .'ORDER BY ordering ASC';

        $this->_db->setQuery($query);

        $this->_categories = $this->_db->loadObjectList();

        return $this->_categories;
    }

    /**
     * return childs of this category
     *
     * @return unknown
     */
    function getChilds()
    {
        if ($this->_categories_ids)
        {
            return $this->_categories_ids;
        }

        $query = ' SELECT id, parent_id '
        .' FROM #__eventlist_categories '
        .' WHERE published = 1 ';

        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();

        $catsintree = array ();
        if (!$this->_id)
        {
            // if no category selected, select them all
            foreach ($rows AS $r)
            {
                $catsintree[] = $r->id;
            }
        }
        else
        {
            // filter categories if a top category was specified
            // establish the hierarchy of the categories
            $children = array ();

            //print_r($rows);exit;
            // first pass - collect direct children of each category
            foreach ($rows as $v)
            {
                $pt = $v->parent_id;
                $list = @$children[$pt]?$children[$pt]: array ();
                array_push($list, $v);
                $children[$pt] = $list;
            }
            $list = $this->_getAllChildren($children, $this->_id);

            foreach ($list AS $r)
            {
                $catsintree[] = $r->id;
            }
            $catsintree[] = $this->_id;
        }
        $this->_categories_ids = $catsintree;
        return $catsintree;
    }

    /**
     * Recursive function to get all categories belonging to subtree of a category.
     *
     * @param array $children
     * @param int $catid
     * @return array
     */
    function _getAllChildren( & $children, $catid)
    {
        $childs = array ();
        if ( isset ($children[$catid]) && count($children[$catid]))
        {
            //$childs = $children[$catid];
            foreach ($children[$catid]AS $child)
            {
                $childs[] = $child;
                $childs = array_merge($childs, $this->_getAllChildren($children, $child->id));
            }
        }
        return $childs;
    }
}
?>
