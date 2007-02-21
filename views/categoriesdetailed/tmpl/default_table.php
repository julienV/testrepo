<?php
/**
 * @version 0.9 $Id$
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
				if ($this->elsettings->infobuttonwidth != '') :
				?>
				<td width="<?php echo $this->elsettings->infobuttonwidth; ?>" class="sectiontableheader" align="left"><?php echo $this->elsettings->infobuttonname; ?></td>
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
	//	$k = 0;
	//	for ($i=0, $n=count($this->rows); $i < $n; $i++) {
	//	$row = $this->rows[$i];
	$this->rows =& $this->getRows();
	if (!$this->rows) :
		?>
		<tr align="center"><td><?php echo JText::_( 'NO EVENTS' ); ?></td></tr>
		<?php
	else :

	foreach ($this->rows as $row) :
		//alternating colors
		//$tabclass = array( 'sectiontableentry1', 'sectiontableentry2' );

		?>
  			<!--<tr class="<?php // echo $tabclass[$k]; ?>">-->
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
				$detaillink = JRoute::_( 'index.php?option=com_eventlist&amp;view=details&amp;did='. $row->id );
				//title
				if (($this->elsettings->showtitle == 1 ) && (($this->elsettings->showdetails == 1) || ($this->elsettings->showdetails == 3)) ) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" align="left" valign="top"><a href="<?php echo $detaillink ; ?>"> <?php echo $row->title ? $row->title : '-'; ?></a></td>
				<?php
				endif;
				if (( $this->elsettings->showtitle == 1 ) && (($this->elsettings->showdetails == 2) || ($this->elsettings->showdetails == 0)) ) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" align="left" valign="top"><?php echo $row->title ? $row->title : '-'; ?></td>
				<?php
				endif;

				//Infomation icon
				if ($this->elsettings->infobuttonwidth != '') :
					if (($this->elsettings->showdetails == 2) || ($this->elsettings->showdetails == 3)) :
						if (empty ($row->datdescription) && empty($row->locdescription)) :
				?>
							<td width="<?php echo $this->elsettings->infobuttonwidth; ?>" align="center" valign="top">
							<img src="<?php echo $live_site."/components/com_eventlist/assets/images/images/information_no.png"; ?>" width="16" height="16"  name="image" alt="<?php JText::_( 'SHOW DETAILS' ); ?>" />
							</td>
				<?php
						else :
				?>
							<td width="<?php echo $this->elsettings->infobuttonwidth; ?>" align="center" valign="top">
							<a href="<?php echo $detaillink ; ?>"><img src="<?php echo $live_site."/components/com_eventlist/assets/images/information.png"; ?>" width="16" height="16"  name="image" alt="<?php JText::_( 'SHOW DETAILS' ); ?>" /></a>
							</td>
				<?php
						endif;
					endif;
				endif;
				if ($this->elsettings->showlocate == 1) :
				?>
					<td width="<?php echo $this->elsettings->locationwidth; ?>" align="left" valign="top">
				<?php
					if ($this->elsettings->showlinkclub == 1 ) :
							echo $row->locid != 0 ? "<a href='".JRoute::_("index.php?option=com_eventlist&amp;view=venueevents&amp;locatid=$row->locid")."'>".$row->club."</a>" : '-';
						else :
							echo $row->locid ? $row->club : '-';
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
							<a href="<?php echo JRoute::_('index.php?option=com_eventlist&amp;view=categoryevents&amp;categid='.$row->catid) ; ?>">
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
  		<?php // $k = 1 - $k; }
		endforeach;
		endif;
		?>
</table>
<br />