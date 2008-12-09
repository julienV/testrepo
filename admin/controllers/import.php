<?php
/**
 * @version 1.1 $Id: attendees.php 663 2008-05-09 22:31:40Z schlu $
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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * EventList Component Attendees Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerImport extends EventListController
{
	/**
	 * Constructor
	 *
	 *@since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	function csvimport()
	{	
    $replace = JRequest::getVar('replace', 0, 'post', 'int');
    $object = & JTable::getInstance('eventlist_events', '');
    $object_fields = get_object_vars($object);
    // add additional fields
    $object_fields['categories'] = '';
    
    $msg = '';
    if ( $file = JRequest::getVar( 'Filedata', null, 'files', 'array' ) )
    {
      $handle = fopen($file['tmp_name'],'r');
      if(!$handle) die('Cannot open uploaded file.');      
      // get fields, on first row of the file
      $fields = array();
      if ( ($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
        $numfields = count($data);
        for ($c=0; $c < $numfields; $c++) {
          // here, we make sure that the field match one of the fields of eventlist_venues table or special fields,
          // otherwise, we don't add it
          if ( array_key_exists($data[$c], $object_fields) ) {
            $fields[$c]=$data[$c];
          }
        }
      }
      // If there is no validated fields, there is a problem...
      if ( !count($fields) ) {
        $msg .= "<p>Error parsing column names. Are you sure this is a proper csv export ?<br />try to export first to get an example of formatting</p>\n";
        $this->setRedirect( 'index.php?option=com_eventlist&view=events', $msg, 'error' );
        return;
      }
      else {
        $msg .= "<p>".$numfields." fields found in first row</p>\n";
        $msg .= "<p>".count($fields)." fields were kept</p>\n";
      }
      
      // Now get the records, meaning the rest of the rows.
      $records = array();
      $row = 1;
      while ( ($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
        $num = count($data);
        if ($numfields != $num) {
          $msg .= "<p>Wrong number of fields ($num) line $row<br /></p>\n";
        }
        else {
          $r = array();
          // only extract columns with validated header, from previous step.
          foreach ($fields as $k => $v) {
            $r[] = $this->_formatcsvfield($v, $data[$k]);
          }
          $records[] = $r;
        }
        $row++;
      }
      fclose($handle);
      $msg .= "<p>total records found: ".count($records)."<br /></p>\n";
         
      // database update
      if (count($records)) {
        $model = $this->getModel('import');
        $result = $model->eventsimport($fields, $records, $replace, $uselocname, $createloc);
        $msg .= "<p>total added records: ".$result."<br /></p>\n";
      }
      $this->setRedirect( 'index.php?option=com_eventlist&view=import', $msg ); 
    }
    else {
      parent::display();
    }
  }
  
  /**
   * handle specific fields conversion if needed
   *
   * @param string column name
   * @param string $value
   * @return string
   */
  function _formatcsvfield($type, $value)
  {
    switch($type)
    {
      case 'dates':
      case 'enddates':
      case 'recurrence_counter':
        if ($value != '') {
          //strtotime does a good job in converting various date formats...
          $date = strtotime($value);
          $field = strftime('%Y-%m-%d',$date);
        }
        else {
          $field = null;
        }
        break;
      default:
        $field = $value;
        break;
    }
    return $field;
  }
  
}
?>