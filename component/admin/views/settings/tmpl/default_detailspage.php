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
			<legend><?php echo JText::_( 'EVENTS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
	  				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY TIME' ); ?>::<?php echo JText::_('DISPLAY TIME TIP'); ?>">
								<?php echo JText::_( 'DISPLAY TIME' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
          						echo JHTML::_('select.booleanlist', 'showtimedetails', 'class="inputbox"', $this->elsettings->showtimedetails );
       						?>
       	 				</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY EVENT DESCRIPT' ); ?>::<?php echo JText::_('DISPLAY EVENT DESCRIPT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY EVENT DESCRIPT' ); ?>
							</span>
						</td>
       					<td valign="top">
		 					<?php
          						echo JHTML::_('select.booleanlist', 'showevdescription', 'class="inputbox"', $this->elsettings->showevdescription );
       						?>
       	 				</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY EVENT TITLE' ); ?>::<?php echo JText::_('DISPLAY EVENT TITLE TIP'); ?>">
								<?php echo JText::_( 'DISPLAY EVENT TITLE' ); ?>
							</span>
						</td>
       					<td valign="top">
		 					<?php
          						echo JHTML::_('select.booleanlist', 'showdetailstitle', 'class="inputbox"', $this->elsettings->showdetailstitle );
       						?>
       	 				</td>
      				</tr>
				</tbody>
			</table>
			</fieldset>
        
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'VENUES' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr valign="top">
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY VENUE DESCRIPT' ); ?>::<?php echo JText::_('DISPLAY VENUE DESCRIPT TIP'); ?>">
								<?php echo JText::_( 'DISPLAY VENUE DESCRIPT' ); ?>
							</span>
						</td>
       					<td valign="top">
		 					<?php
          						echo JHTML::_('select.booleanlist', 'showlocdescription', 'class="inputbox"', $this->elsettings->showlocdescription );
       						?>
       	 				</td>
      				</tr>
					<tr valign="top">
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY ADDRESS' ); ?>::<?php echo JText::_('DISPLAY ADDRESS TIP'); ?>">
								<?php echo JText::_( 'DISPLAY ADDRESS' ); ?>
							</span>
						</td>
       					<td valign="top">
		 					<?php
          						echo JHTML::_('select.booleanlist', 'showdetailsadress', 'class="inputbox"', $this->elsettings->showdetailsadress );
       						?>
       	 				</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY LINK TO VENUE' ); ?>::<?php echo JText::_('DISPLAY LINK TO VENUE TIP'); ?>">
								<?php echo JText::_( 'DISPLAY LINK TO VENUE' ); ?>
							</span>
						</td>
       					<td valign="top">
		 					<?php
          					$showlink = array();
							$showlink[] = JHTML::_('select.option', '0', JText::_( 'NO LINK' ) );
							$showlink[] = JHTML::_('select.option', '1', JText::_( 'LINK TO URL' ) );
							$showlink[] = JHTML::_('select.option', '2', JText::_( 'LINK TO VENUEVIEW' ) );
							$show = JHTML::_('select.genericlist', $showlink, 'showdetlinkvenue', 'size="1" class="inputbox"', 'value', 'text', $this->elsettings->showdetlinkvenue );
							echo $show;
          					?>
       	 				</td>
      				</tr>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'DISPLAY LINK TO MAP' ); ?>::<?php echo JText::_('DISPLAY LINK TO MAP TIP'); ?>">
								<?php echo JText::_( 'DISPLAY LINK TO MAP' ); ?>
							</span>
						</td>
       					<td valign="top">
							<?php
							$mode = 0;
							if ($this->elsettings->showmapserv == 1) {
								$mode = 1;
							} elseif ($this->elsettings->showmapserv == 2) {
								$mode = 2;
							}
							?>
							<select name="showmapserv" size="1" class="inputbox" onChange="changemapMode()">
  								<option value="0"<?php if ($this->elsettings->showmapserv == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_( 'NO MAP SERVICE' ); ?></option>
  								<option value="1"<?php if ($this->elsettings->showmapserv == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_( 'MAP24' ); ?></option>
  								<option value="2"<?php if ($this->elsettings->showmapserv == 2) { ?> selected="selected"<?php } ?>><?php echo JText::_( 'GOOGLEMAP' ); ?></option>
							</select>
       	 				</td>
      				</tr>
					<tr id="map24"<?php if ($mode != 1) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'REGISTER MAP24' ); ?>::<?php echo JText::_('REGISTER MAP24 TIP'); ?>">
								<?php echo JText::_( 'REGISTER MAP24' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<input type="text" name="map24id" value="<?php echo $this->elsettings->map24id; ?>" size="15" maxlength="10" />
       	 					<a href="http://www.map24.com/" target="_blank">map24.com</a>
						</td>
      				</tr>
      				<tr id="gapikey"<?php if ($mode != 2) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'GOOGLE MAP KEY' ); ?>::<?php echo JText::_('GOOGLE MAP KEY TIP'); ?>">
								<?php echo JText::_( 'GOOGLE MAP KEY' ); ?>
							</span>
						</td>
       					<td valign="top">
          					<input type="text" name="gmapkey" value="<?php echo $this->elsettings->gmapkey; ?>" size="25" maxlength="255" />
       	 					<a href="http://www.google.com/apis/maps/signup.html" target="_blank"><?php echo JText::_( 'REQUEST MAP KEY' ); ?></a>
						</td>
      				</tr>
				</tbody>
			</table>
		</fieldset>

		</td>
        <td width="50%">

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'REGISTRATION' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'TYPE REG NAME' ); ?>::<?php echo JText::_('TYPE REG NAME TIP'); ?>">
								<?php echo JText::_( 'TYPE REG NAME' ); ?>
							</span>
						</td>
       					<td valign="top">
       						<?php
		   					$regname = array();
							$regname[] = JHTML::_('select.option', '0', JText::_( 'USERNAME' ) );
							$regname[] = JHTML::_('select.option', '1', JText::_( 'NAME' ) );
							$nametype = JHTML::_('select.genericlist', $regname, 'regname', 'size="1" class="inputbox"', 'value', 'text', $this->elsettings->regname );
							echo $nametype;
        					?>
       	 				</td>
      				</tr>
	  				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM SOL' ); ?>::<?php echo JText::_('COM SOL TIP'); ?>">
								<?php echo JText::_( 'COM SOL' ); ?>
							</span>
						</td>
       					<td valign="top">
       		 				<?php
							$mode = 0;
							if ($this->elsettings->comunsolution == 1) {
								$mode = 1;
							} // if
							?>
       		 				<select name="comunsolution" size="1" class="inputbox" onChange="changeintegrateMode()">
  								<option value="0"<?php if ($this->elsettings->comunsolution == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_( 'DONT USE COM SOL' ); ?></option>
  								<option value="1"<?php if ($this->elsettings->comunsolution == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_( 'COMBUILDER' ); ?></option>
							</select>
       	 				</td>
      				</tr>
	  				<tr id="integrate"<?php if (!$mode) echo ' style="display:none"'; ?>>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'TYPE COM INTEGRATION' ); ?>::<?php echo JText::_('TYPE COM INTEGRATION TIP'); ?>">
								<?php echo JText::_( 'TYPE COM INTEGRATION' ); ?>
							</span>
						</td>
       					<td valign="top">
       						<?php
		   					$comoption = array();
							$comoption[] = JHTML::_('select.option', '0', JText::_( 'LINK PROFILE' ) );
							$comoption[] = JHTML::_('select.option', '1', JText::_( 'LINK AVATAR' ) );
							$comoptions = JHTML::_('select.genericlist', $comoption, 'comunoption', 'size="1" class="inputbox"', 'value', 'text', $this->elsettings->comunoption );
							echo $comoptions;
        					?>
       	 				</td>
      				</tr>
      				
      				
      				<tr>
	          			<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'FRONTEND DISPLAY' ); ?>::<?php echo JText::_('FRONTEND DISPLAY TIP'); ?>">
								<?php echo JText::_( 'FRONTEND DISPLAY' ); ?>
							</span>
						</td>
       					<td valign="top">
       						<?php
       						echo $this->accessLists['reg_access'];
							//var_dump($this->reg_access);
       						/*
		   					$reg_access = array();
							$reg_access[] = JHTML::_('select.option', '0', JText::_( 'USERNAME' ) );
							$reg_access[] = JHTML::_('select.option', '1', JText::_( 'NAME' ) );
							$show_attendees = JHTML::_('select.genericlist', $reg_access, 'reg_access', 'size="1" class="inputbox"', 'value', 'text', $this->elsettings->reg_access );
							echo $show_attendees;
							*/
        					?>
       	 				</td>
      				</tr>
	  				<tr>
      				
				</tbody>
			</table>
		</fieldset>
		
	</td>
  </tr>
</table>