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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<table width="<?php echo $this->elsettings->tablewidth; ?>" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
<thead>
			<tr>
				<th width="<?php echo $this->elsettings->datewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->datename; ?></th>
				<?php
				if ($this->elsettings->showtitle == 1) :
				?>
				<th width="<?php echo $this->elsettings->titlewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->titlename; ?></th>
				<?php
				endif;
				if ($this->elsettings->showlocate == 1) :
				?>
				<th width="<?php echo $this->elsettings->locationwidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->locationname; ?></th>
				<?php
				endif;
				if ($this->elsettings->showcity == 1) :
				?>
				<th width="<?php echo $this->elsettings->citywidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->cityname; ?></th>
				<?php
				endif;
				if ($this->elsettings->showstate == 1) :
				?>
				<th width="<?php echo $this->elsettings->statewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->statename; ?></th>
				<?php
				endif;
				if ($this->elsettings->showcat == 1) :
				?>
				<th width="<?php echo $this->elsettings->catfrowidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->catfroname; ?></th>
				<?php
				endif;
				?>
			</tr>
</thead>

<tbody>
	<?php
	$this->rows = $this->getRows();
	if (!$this->rows) :
	?>
		<tr class="no_events"><td><?php echo JText::_( 'NO EVENTS' ); ?></td></tr>
		<?php
	else :

	foreach ($this->rows as $row) :
		?>
  			<tr class="sectiontableentry<?php echo ($row->odd +1 ) . $this->params->get( 'pageclass_sfx' ); ?>" >
    			<td width="<?php echo $this->elsettings->datewidth; ?>" align="left">
    			<strong><?php echo $row->displaydate; ?></strong>
				<?php
				if ($this->elsettings->showtime == 1) :
					echo $row->displaytime;
				endif;
				?>
				</td>
				<?php
				//Link to details
				$detaillink = JRoute::_( 'index.php?view=details&did='. $row->slug );
				//title
				if (($this->elsettings->showtitle == 1 ) && ($this->elsettings->showdetails == 1) ) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" align="left" valign="top"><a href="<?php echo $detaillink ; ?>"> <?php echo $row->title ? $row->title : '-'; ?></a></td>
				<?php
				endif;
				if (( $this->elsettings->showtitle == 1 ) && ($this->elsettings->showdetails == 0) ) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" align="left" valign="top"><?php echo $row->title ? $row->title : '-'; ?></td>
				<?php
				endif;

				if ($this->elsettings->showlocate == 1) :
				?>
					<td width="<?php echo $this->elsettings->locationwidth; ?>" align="left" valign="top">
				<?php
					if ($this->elsettings->showlinkvenue == 1 ) :
							echo $row->locid != 0 ? "<a href='".JRoute::_("index.php?view=venueevents&locatid=$row->venueslug")."'>".$row->venue."</a>" : '-';
						else :
							echo $row->locid ? $row->venue : '-';
						endif;
				?>
					</td>
				<?php
				endif;
				if ($this->elsettings->showcity == 1) :
				?>
					<td width="<?php echo $this->elsettings->citywidth; ?>" align="left" valign="top"><?php echo $row->city ? $row->city : '-'; ?></td>
				<?php
				endif;

				if ($this->elsettings->showstate == 1) :
				?>
					<td width="<?php echo $this->elsettings->statewidth; ?>" align="left" valign="top"><?php echo $row->state ? $row->state : '-'; ?></td>
				<?php
				endif;

				if ($this->elsettings->showcat == 1) :
					if ($this->elsettings->catlinklist == 1) :
					?>
						<td width="<?php echo $this->elsettings->catfrowidth; ?>" align="left" valign="top">
							<a href="<?php echo JRoute::_('index.php?view=categoryevents&categid='.$row->categoryslug) ; ?>">
								<?php echo $row->catname ? $row->catname : '-' ; ?>
							</a>
						</td>
					<?php else : ?>
						<td width="<?php echo $this->elsettings->catfrowidth; ?>" align="left" valign="top">
							<?php echo $row->catname ? $row->catname : '-'; ?>
						</td>
				<?php
					endif;
				endif;
				?>
			</tr>
  		<?php
		endforeach;
		endif;
		?>
</tbody>
</table>