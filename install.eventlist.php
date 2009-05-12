<?php
/**
 * @version 1.1 $Id$
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

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Executes additional installation processes
 *
 * @since 0.1
 */
function com_install() {

	//load libraries
	$db = & JFactory::getDBO();
	jimport( 'joomla.filesystem.folder' )
?>

<center>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td valign="top">
    		<img src="<?php echo 'components/com_eventlist/assets/images/evlogo.png'; ?>" height="108" width="250" alt="EventList Logo" align="left">
		</td>
		<td valign="top" width="100%">
       	 	<strong>EventList</strong><br/>
        	<font class="small">by <a href="http://www.schlu.net" target="_blank">schlu.net </a><br/>
        	Released under the terms and conditions of the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License</a>.
        	</font>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			
			<?php
			//intitialize some vars
			$direxists 		= 0;
			$makedir 		= 0;
			$update_error 	= 0;
			
			//check if update or fresh install
			//detect if catsid field is available in events table. If yes, 1.0 was installed
			$query = 'DESCRIBE #__eventlist_events catsid';
			$db->setQuery($query);
			$doupdate11 = $db->loadResult();
			
			if (!$doupdate11) {
				echo '<code><strong>Installation Status:</strong><br />';
			
				// Check for existing /images/eventlist directory
				if ($direxists = JFolder::exists( JPATH_SITE.'/images/eventlist' )) {
					echo "<font color='green'>FINISHED:</font> Directory /images/eventlist exists. Skipping creation.</font><br />";
				} else {
					echo "<font color='orange'>Note:</font> The Directory /images/eventlist does NOT exist. EventList will try to create them.</font><br />";
				
					//Image folder creation
					if ($makedir = JFolder::create( JPATH_SITE.'/images/eventlist')) {
						echo "<font color='green'>FINISHED:</font> Directory /images/eventlist created.</font><br />";
					} else {
						echo "<font color='red'>ERROR:</font> Directory /images/eventlist NOT created.</font><br />";
					}
					if (JFolder::create(JPATH_SITE.'/images/eventlist/events')) {
						echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/events created.</font><br />";
					} else {
						echo "<font color='red'>ERROR:</font> Directory /images/eventlist/events NOT created.</font><br />";
					}
					if (JFolder::create( JPATH_SITE.'/images/eventlist/events/small')) {
						echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/events/small created.</font><br />";
					} else {
						echo "<font color='red'>ERROR:</font> Directory /images/eventlist/events/small NOT created.</font><br />";
					}
					if (JFolder::create( JPATH_SITE.'/images/eventlist/venues')) {
						echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/venues created.</font><br />";
					} else {
						echo "<font color='red'>ERROR:</font> Directory /images/eventlist/venues NOT created.</font><br />";
					}
					if (JFolder::create( JPATH_SITE.'/images/eventlist/venues/small')) {
						echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/venues/small created.</font><br />";
					} else {
						echo "<font color='red'>ERROR:</font> Directory /images/eventlist/venues/small NOT created.</font><br />";
					}
				}
				
				//check if default values are available -> means update
				//TODO: none settings means fresh install so clean up with the version check for 1.0 catsid field (skip version checks)
				$query = 'SELECT * FROM #__eventlist_settings WHERE id = 1';
				$db->setQuery($query);
				$settingsresult = $db->loadResult();
						
				if (!$settingsresult) {
					//Set the default setting values -> fresh install
					$query = "INSERT INTO #__eventlist_settings VALUES (1, 0, 1, 0, 1, 1, 1, 0, '', '', '100%', '15%', '25%', '20%', '20%', 'Date', 'Title', 'Venue', 'City', '%d.%m.%Y', '%H.%M', 'h', 1, 0, 1, 1, 1, 1, 1, 2, -2, 0, 'example@example.com', 0, '1000', -2, -2, -2, 1, '20%', 'Type', 1, 1, 1, 1, '100', '100', '100', 0, 1, 0, 0, 1, 2, 2, -2, 1, 0, -2, 1, 0, 0, '[title], [a_name], [catsid], [times]', 'The event titled [title] starts on [dates]!', 0, 'State', 0, '', 0, 1, 0, '1174491851', '', '')";
					$db->setQuery($query);
					if (!$db->query()) {
						echo "<font color='red'>Error loading default setting values. Please apply changes manually!</font><br />";
					} else {
	          			echo "<font color='green'>Successfully loaded default setting values.</font><br />";
					}
				}
				
				
			} else {
				echo '<br /><strong>Update Status:</strong><br />';
						
				#############################################################################
				#																			#
				#		Database Update Logic for EventList 1.0 to EventList 1.1 Beta		#
				#																			#
				#############################################################################
				
				echo '<br /><strong>Currently installed Version: 1.0! Database outdated! Try to update to Version 1.1...</strong><br />';
				
				//update current settings
				$query = 'ALTER TABLE #__eventlist_settings' 
							.' CHANGE imagehight imageheight VARCHAR( 20 ) NOT NULL,'
							.' ADD reg_access tinyint(4) NOT NULL AFTER regname'
								;
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error updating settings table. Please apply changes manually!</font><br />";
				} else {
	       	   		echo "<font color='green'>Successfully updated settings table.</font><br />";
				}
				
				//update events table
				
				//add new fields
				$query = 'ALTER TABLE #__eventlist_events'
							.' ADD recurrence_limit INT NOT NULL AFTER recurrence_counter,'
							.' ADD recurrence_limit_date DATE NOT NULL AFTER recurrence_limit,'
							.' ADD recurrence_first_id int(11) NOT NULL default \'0\' AFTER meta_description,'
							.' ADD recurrence_byday VARCHAR( 20 ) NOT NULL AFTER recurrence_limit_date,'
							.' ADD version int(11) unsigned NOT NULL default \'0\' AFTER modified_by,'
							.' ADD hits int(11) unsigned NOT NULL default \'0\' AFTER unregistra'
							;
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error adding new fields to events table. Please apply changes manually!</font><br />";
				} else {
	    	     	echo "<font color='green'>Successfully added new fields to events table.</font><br />";
				}
				
				//converting fields to new schema
				$query = 'UPDATE #__eventlist_events'
							.' SET recurrence_limit_date = recurrence_counter'
							;
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error converting recurrence: Step 1. Please apply changes manually!</font><br />";
				} else {
	          		echo "<font color='green'>Successfully converted recurrence: Step 1.</font><br />";
				}
				
				$query = 'ALTER TABLE #__eventlist_events' 
							.' CHANGE recurrence_counter recurrence_counter INT NOT NULL DEFAULT \'0\''
							;
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error converting recurrence: Step 2. Please apply changes manually!</font><br />";
				} else {
		       		echo "<font color='green'>Successfully converted recurrence: Step 2.</font><br />";
				}
					
				//convert category structure
				$query = 'SELECT id, catsid FROM #__eventlist_events';
				$db->setQuery($query);
				$categories = $db->loadObjectList();
				
				$err = 0;
				foreach ($categories AS $category) {
					$query = 'INSERT INTO #__eventlist_cats_event_relations VALUES ('.$category->catsid.', '.$category->id.', \'\')';
					$db->setQuery($query);
					if (!$db->query()) {
						$err++;
					}
				}
				
				if ($err) {
					$update_error++;
					echo "<font color='red'>Error converting to new category structure. Please apply changes manually!</font><br />";
				} else {
		       		echo "<font color='green'>Successfully converted to new category structure.</font><br />";
				}
					
				//remove catsid field from events table
				$query = 'ALTER TABLE #__eventlist_events DROP catsid';
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error removing outdated fields from events table. Please apply changes manually!</font><br />";
				} else {
	          		echo "<font color='green'>Successfully removed unneeded fields from events table.</font><br />";
				}
				
				//update venues table
				$query = 'ALTER TABLE #__eventlist_venues'
							.' ADD latitude float default NULL,'
							.' ADD longitude float default NULL,'
							.' ADD version int(11) unsigned NOT NULL default \'0\''
							;
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error adding new fields to venuess table. Please apply changes manually!</font><br />";
				} else {
	       			echo "<font color='green'>Successfully added new fields to venues table.</font><br />";
				}
				
				//update categories table
				$query = 'ALTER TABLE #__eventlist_categories'
							.' ADD color varchar(20) NOT NULL default \'\''
							;
				
				$db->setQuery($query);
				if (!$db->query()) {
					$update_error++;
					echo "<font color='red'>Error adding new fields to categories table. Please apply changes manually!</font><br />";
				} else {
	       			echo "<font color='green'>Successfully added new fields to categories table.</font><br />";
				}
				
				#############################################################################
				#																			#
				#	END: Database Update Logic for EventList 1.0 to EventList 1.1 Beta		#
				#																			#
				#############################################################################
			}
						
			echo '<br />';
			
			//Installation report
			if (!$doupdate11) {
				if ($direxists || $makedir) {
			?>
				<font color="green"><strong>Joomla! EventList Installed Successfully!</strong></font><br />
				Ensure that EventList has write access to the directories shown above! Have Fun.
				</code>
				<?php
				} else {
				?>
				<font color="red">
				<strong>Joomla! EventList could NOT be installed successfully!</strong>
				</font>
				<br /><br />
				Please check following directories:<br />
				</code>
				<ul>
					<li>/images/eventlist</li>
					<li>/images/eventlist/events</li>
					<li>/images/eventlist/events/small</li>
					<li>/images/eventlist/venues</li>
					<li>/images/eventlist/venues/small</li>
				</ul>
				<br />

				<code>
					If they do not exist, create them and ensure EventList has write access to these directories.<br />
					If you don't so, you prevent EventList from functioning correctly. (You can't upload images).
				</code>
			<?php
				}
			} else {
				if (!$update_error) {
					echo '<br /><font color="green"><strong>Joomla! EventList Updated Successfully!</strong></font></code>';
				} else {
					echo '<br /><font color="red"><strong>Joomla! EventList could NOT be Updated successfully! In total '.$update_error.' Errors occured. Aplly the needed changes manually!</strong></font>';
				}
			}			
			?>
		</td>
	</tr>
</table>

</center>
<?php
}
?>