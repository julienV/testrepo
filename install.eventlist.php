<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Executes additional installation processes
 *
 * @since 0.1
 */
function com_install() {

	global $mainframe;

	$db 		= & JFactory::getDBO();
	$live_site 	= $mainframe->getCfg('live_site');

	jimport( 'joomla.filesystem.folder' )
?>

<center>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td valign="top">
    		<img src="<?php echo $live_site.'/administrator/components/com_eventlist/assets/images/evlogo.png'; ?>" height="108" width="250" alt="Event List Logo" align="left">
		</td>
		<td valign="top" width="100%">
       	 	<strong>EventList</strong><br/>
        	<font class="small">by Christoph Lukes <a href="http://www.schlu.net" target="_blank">schlu.net </a><br/>
        	Released under the terms and conditions of the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU General Public License</a>.
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<code>Installation process:<br />
			<?php
			// Check for existing /images/eventlist directory
        	if ($direxists = JFolder::exists( JPATH_SITE.'/images/eventlist' )) {
        		echo "<font color='green'>FINISHED:</font> Directory /images/eventlist exists. Skipping creation.<br />";
       		} else {
        		echo "<font color='orange'>Note:</font> Directory /images/eventlist is NOT existing. Trying to create them.<br />";

        		//Image folder creation
        		if ($makedir1 = JFolder::create( JPATH_SITE.'/images/eventlist')) {
        			echo "<font color='green'>FINISHED:</font> Directory /images/eventlist created.<br />";
        		} else {
        			echo "<font color='red'>ERROR:</font> Directory /images/eventlist NOT created.<br />";
        		}

        		if ($makedir2 = JFolder::create(JPATH_SITE.'/images/eventlist/events')) {
        			echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/events created.<br />";
        		} else {
        			echo "<font color='red'>ERROR:</font> Directory /images/eventlist/events NOT created.<br />";
        		}
        		if ($makedir3 = JFolder::create( JPATH_SITE.'/images/eventlist/events/small')) {
        			echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/events/small created.<br />";
        		} else {
        			echo "<font color='red'>ERROR:</font> Directory /images/eventlist/events/small NOT created.<br />";
        		}
        		if ($makedir4 = JFolder::create( JPATH_SITE.'/images/eventlist/venues')) {
        			echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/venues created.<br />";
        		} else {
        			echo "<font color='red'>ERROR:</font> Directory /images/eventlist/venues NOT created.<br />";
        		}
        		if ($makedir5 = JFolder::create( JPATH_SITE.'/images/eventlist/venues/small')) {
        			echo "<font color='green'>FINISHED:</font> Directory /images/eventlist/venues/small created.<br />";
        		} else {
        			echo "<font color='red'>ERROR:</font> Directory /images/eventlist/venues/small NOT created.<br />";
        		}
			}
        	?>

			<br />

			<?php
			if (($direxists) || ($makedir1)) {
			?>
				<font color="green"><b>Joomla! EventList 0.9 ALPHA Installed Successfully!</b></font><br />
				Ensure that EventList have write access in the above shown directories! Have Fun.
				</code>
			<?php
			} else {
			?>
				<font color="red">
				<b>Joomla! EventList 0.9 ALPHA could NOT be installed successfully!</b>
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
					If they do not exist, create them and ensure EventList have write access to this directories.<br />
					If you don't so, you prevent EventList from functioning correctly. (You can't upload images).
				</code>
			<?php
			}
			?>
		</td>
	</tr>
</table>

</center>
<?php
}
?>