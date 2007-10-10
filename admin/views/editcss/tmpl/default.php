<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
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

defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left" /></td>
		  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'EDIT CSS' ); ?>::</font></td>
			</tr>
		</table>

		<br />

		<table cellpadding="1" cellspacing="1" border="0" width="100%" class="adminlist">
		<tr>
			<td width="260">
				<span class="componentheading"><?php echo JText::_( 'CSSFILE IS' ); ?> :
				<b><?php echo is_writable($this->css_path) ? '<font color="green"> '. JText::_( 'Writeable' ) .'</font>' : '<font color="red"> '. JText::_( 'Unwriteable' ) .'</font>' ?></b>
				</span>
			</td>
			<?php

		jimport('joomla.filesystem.path');
		if (JPath::canCHMOD($this->css_path))
		{
			if (is_writable($this->css_path))
			{
				?>
				<td>
					<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
					<label for="disable_write"><?php echo JText::_( 'Make unwriteable after saving' ); ?></label>
				</td>
				<?php

			} else {
				?>
				<td>
					<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
					<label for="enable_write"><?php echo JText::_( 'Override write protection while saving' ); ?></label>
				</td>
				<?php

			} // if
		} // if
		?>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th>
				<?php echo $this->css_path; ?>
			</th>
		</tr>
		<tr>
			<td>
				<textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea>
			</td>
		</tr>
		</table>

		<p class="copyright">
			<?php echo ELAdmin::footer( ); ?>
		</p>

		<input type="hidden" name="filename" value="<?php echo $this->filename; ?>" />
		<input type="hidden" name="path" value="<?php echo $this->path; ?>" />
		<input type="hidden" name="option" value="com_eventlist" />
		<input type="hidden" name="task" value="" />
		</form>