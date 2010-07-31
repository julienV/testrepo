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

defined('_JEXEC') or die('Restricted access');

?>
	<table class="noshow">
      <tr>
        <td width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'GENERAL LAYOUT SETTINGS'); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY TIME' ); ?>::<?php echo JText::_('DISPLAY TIME FRONT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY TIME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
          						echo JHTML::_('select.booleanlist', 'showtime', 'class="inputbox"', $this->elsettings->showtime );
							?>
						</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_FRONT_TABLE_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_FRONT_TABLE_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_FRONT_TABLE_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="tablewidth" value="<?php echo $this->elsettings->tablewidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			  </fieldset>
			  <fieldset class="adminform">
			<legend><?php echo JText::_( 'DATE COLUMN'); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<input type="text" name="datewidth" value="<?php echo $this->elsettings->datewidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="datename" value="<?php echo $this->elsettings->datename; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CITY COLUMN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY CITY FRONT' ); ?>::<?php echo JText::_('DISPLAY CITY FRONT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY CITY FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<?php
							$mode = 0;
							if ($this->elsettings->showcity == 1) {
							$mode = 1;
							} // if
							?>
							<input type="radio" id="showcity0" name="showcity" value="0" onclick="changecityMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showcity1" name="showcity" value="1" onclick="changecityMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="city1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="citywidth" value="<?php echo $this->elsettings->citywidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="city2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="cityname" value="<?php echo $this->elsettings->cityname; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'TITLE COLUMN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
	  				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY TITLE FRONT' ); ?>::<?php echo JText::_('DISPLAY TITLE FRONT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY TITLE FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
							$mode = 0;
							if ($this->elsettings->showtitle == 1) {
								$mode = 1;
							} // if
							?>
        					<input type="radio" id="showtitle0" name="showtitle" value="0" onclick="changetitleMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showtitle1" name="showtitle" value="1" onclick="changetitleMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="title1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="titlewidth" value="<?php echo $this->elsettings->titlewidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="title2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="titlename" value="<?php echo $this->elsettings->titlename; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>


        <td width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'VENUE COLUMN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
	  				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY VENUE FRONT' ); ?>::<?php echo JText::_('DISPLAY VENUE FRONT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY VENUE FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
							$mode = 0;
							if ($this->elsettings->showlocate == 1) {
								$mode = 1;
							} // if
							?>
     						<input type="radio" id="showlocate0" name="showlocate" value="0" onclick="changelocateMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showlocate1" name="showlocate" value="1" onclick="changelocateMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="locate1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="locationwidth" value="<?php echo $this->elsettings->locationwidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="locate2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="locationname" value="<?php echo $this->elsettings->locationname; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
					<tr id="locate3"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY LINK TO VENUE VIEW' ); ?>::<?php echo JText::_('DISPLAY LINK TO VENUE VIEW TIP'); ?>">
								<?php echo JText::_( 'DISPLAY LINK TO VENUE VIEW' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
          					echo JHTML::_('select.booleanlist', 'showlinkvenue', 'class="inputbox"', $this->elsettings->showlinkvenue );
        					?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'STATE COLUMN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY STATE FRONT' ); ?>::<?php echo JText::_('DISPLAY STATE FRONT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY STATE FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<?php
							$mode = 0;
							if ($this->elsettings->showstate == 1) {
							$mode = 1;
							} // if
							?>
							<input type="radio" id="showstate0" name="showstate" value="0" onclick="changestateMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showstate1" name="showstate" value="1" onclick="changestateMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="state1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="statewidth" value="<?php echo $this->elsettings->statewidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="state2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="statename" value="<?php echo $this->elsettings->statename; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EVENTLIST_SETTINGS_CATEGORY_COLUMN'); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
	  				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_FRONT' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_FRONT_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
							$mode = 0;
							if ($this->elsettings->showcat == 1) {
								$mode = 1;
							} // if
							?>
							<input type="radio" id="showcat0" name="showcat" value="0" onclick="changecatMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showcat1" name="showcat" value="1" onclick="changecatMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="cat1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="catfrowidth" value="<?php echo $this->elsettings->catfrowidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="cat2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="catfroname" value="<?php echo $this->elsettings->catfroname; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
					<tr id="cat3"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_LINK' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_LINK_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_CATEGORY_DISPLAY_LINK' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
        						echo JHTML::_('select.booleanlist', 'catlinklist', 'class="inputbox"', $this->elsettings->catlinklist );
        					?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			  </fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EVENTLIST_SETTINGS_ATTENDEES_COLUMN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_ATTENDEES_DISPLAY_FRONT' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_ATTENDEES_DISPLAY_FRONT_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_ATTENDEES_DISPLAY_FRONT' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<?php
							$mode = 0;
							if ($this->elsettings->showatte == 1) {
							$mode = 1;
							} // if
							?>
							<input type="radio" id="showatte0" name="showatte" value="0" onclick="changeatteMode(0)"<?php if (!$mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'NO' ); ?>
							<input type="radio" id="showatte1" name="showatte" value="1" onclick="changeatteMode(1)"<?php if ($mode) echo ' checked="checked"'; ?>/><?php echo JText::_( 'YES' ); ?>
       	 				</td>
      				</tr>
					<tr id="atte1"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_ATTENDEES_COLUMN_WIDTH' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_ATTENDEES_COLUMN_WIDTH_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_ATTENDEES_COLUMN_WIDTH' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="attewidth" value="<?php echo $this->elsettings->attewidth; ?>" size="5" maxlength="4" />
       	 				</td>
      				</tr>
					<tr id="atte2"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>::<?php echo JText::_('COM_EVENTLIST_SETTINGS_COLUMN_NAME_TIP'); ?>">
								<?php echo JText::_( 'COM_EVENTLIST_SETTINGS_COLUMN_NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<input type="text" name="attename" value="<?php echo $this->elsettings->attename; ?>" size="30" maxlength="25" />
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>
      </tr>
    </table>