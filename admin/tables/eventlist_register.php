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

defined('_JEXEC') or die('Restricted access');

/**
 * EventList registration Model class
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_register extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $rid 		= null;
	/** @var int */
	var $rdid 		= null;
	/** @var int */
	var $uid 		= null;
	/** @var string */
	var $urname 	= null;
	/** @var date */
	var $uregdate 	= null;
	/** @var string */
	var $uip 		= null;

	function eventlist_register(& $db) {
		parent::__construct('#__eventlist_register', 'rid', $db);
	}
}
?>