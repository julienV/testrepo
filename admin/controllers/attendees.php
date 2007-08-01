<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

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
class EventListControllerAttendees extends EventListController
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

	/**
	 * Delete attendees
	 *
	 * @return true on sucess
	 * @access private
	 * @since 0.9
	 */
	function remove()
	{
		global $option;

		$cid 	= JRequest::getVar('cid');
		$rcid 	= JRequest::getVar('rcid');
		$total 	= count( $cid );

		$model = $this->getModel('attendees');

		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$msg = $total.' '.JText::_( 'REGISTERED USERS DELETED');

		$this->setRedirect( 'index.php?option='. $option .'&view=attendees&rcid='.$rcid, $msg );
	}
}
?>