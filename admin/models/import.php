<?php
/**
 * @version 1.1 $Id: eventlist.php 663 2008-05-09 22:31:40Z schlu $
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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Home Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelImport extends JModel
{
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}
    
  /**
   * return __eventlist_events table fields name
   *
   * @return array
   */
  function getEventFields()
  {
    $tables = array('#__eventlist_events');
    $tablesfields = $this->_db->getTableFields($tables);

    return array_keys($tablesfields['#__eventlist_events']);
  }
  
  
  /**
   * import data corresponding to fieldsname into events table
   *
   * @param array $fieldsname
   * @param array $data the records
   * @param boolean $replace replace if id already exists
   * @param boolean $usevenuename use venuename instead of locid field
   * @param boolean $createvenue create venue if name not found
   * @return int number of records inserted
   */
  function eventsimport($fieldsname, &$data, $replace = true, $usevenuename = false, $createvenue= true)
  {
    global $mainframe;
        
    $ignore = array();
    if (!$replace) {
      $ignore[] = 'id';
    }
    $rec = 0;
    // parse each row
    foreach ($data AS $row) 
    {
      $values = array();
      // parse each specified field and retrieve corresponding value for the record
      foreach ($fieldsname AS $k => $field){
        $values[$field] = $row[$k];
      }
      
      $object =& JTable::getInstance('eventlist_events', '');
      
      //print_r($values);exit;
      $object->bind($values, $ignore);      
    
      // Make sure the data is valid
      if (!$object->check()) {
        $this->setError($object->getError());
        echo JText::_('Error check: ') . $object->getError()."\n";
        continue;
      }
  
      // Store it in the db
      if (!$object->store()) {
        echo JText::_('Error store: ') .  $this->_db->getErrorMsg()."\n";
        continue;
      }
    
      // print_r($object); exit;
      // we need to update the categories-events table too
      // store cat relation
      $query = 'DELETE FROM #__eventlist_cats_event_relations WHERE itemid = '.$object->id;
      $this->_db->setQuery($query);
      $this->_db->query();

      if (isset($values['categories']))
      {
        $cats = explode(',', $values['categories']);
        if (count($cats))
        {
          foreach($cats as $cat)
          {
            $query = 'INSERT INTO #__eventlist_cats_event_relations (`catid`, `itemid`) VALUES(' . $cat . ',' . $object->id . ')';
            $this->_db->setQuery($query);
            $this->_db->query();
          }
        }
      }
      $rec++;
    }
    
    // force the cleanup to update the imported events status
    $settings =& JTable::getInstance('eventlist_settings', '');
    $settings->load(1);
    $settings->lastupdate = 0;
    $settings->store();    
    
    return $rec;
  }
}
?>