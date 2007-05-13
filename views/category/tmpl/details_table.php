<?php
/**
 * @version 0.9 $Id: default_table.php 65 2007-04-14 15:38:33Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
?>

<table width="<?php echo $this->elsettings->tablewidth; ?>" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
			<tr>
				<td width="<?php echo $this->elsettings->datewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->datename; ?></td>
				<?php
				if ($this->elsettings->showtitle == 1) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->titlename; ?></td>
				<?php
				endif;
				if ($this->elsettings->showlocate == 1) :
				?>
				<td width="<?php echo $this->elsettings->locationwidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->locationname; ?></td>
				<?php
				endif;
				if ($this->elsettings->showcity == 1) :
				?>
				<td width="<?php echo $this->elsettings->citywidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->cityname; ?></td>
				<?php
				endif;
				if ($this->elsettings->showstate == 1) :
				?>
				<td width="<?php echo $this->elsettings->statewidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->statename; ?></td>
				<?php
				endif;
				if ($this->elsettings->showcat == 1) :
				?>
				<td width="<?php echo $this->elsettings->catfrowidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->catfroname; ?></td>
				<?php
				endif;
				?>
			</tr>
	</table>

	<table width="<?php echo $this->elsettings->tablewidth; ?>"  border="0" cellspacing="0" cellpadding="0" summary="eventlist">
	<?php
	$rows = $this->getRows($this->row);
	if (!$rows) :
	?>
		<tr align="center"><td><?php echo JText::_( 'NO EVENTS' ); ?></td></tr>
		<?php
	else :

	foreach ($rows as $row) :
		?>
  			<tr class="sectiontableentry<?php echo ($row->odd +1 ) . $this->params->get( 'pageclass_sfx' ); ?>" >
    			<td width="<?php echo $this->elsettings->datewidth; ?>" align="left">
    			<b><?php echo $row->displaydate; ?></b>
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
							<a href="<?php echo JRoute::_('index.php?view=category&layout=details&cid='.$row->categoryslug) ; ?>">
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
</table>
<br />