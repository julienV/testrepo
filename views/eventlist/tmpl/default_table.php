<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
?>
<script language="javascript" type="text/javascript">

	function tableOrdering( order, dir, view )
	{
		var form = document.adminForm;

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( view );
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_eventlist&view=eventlist') ?>" method="post" name="adminForm">

<?php if ($this->params->get('filter') || $this->params->get('display')) : ?>
<table width="<?php echo $this->elsettings->tablewidth; ?>" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
	<tr>
		<?php if ($this->params->get('filter')) : ?>
		<td align="left" width="100%" nowrap="nowrap">
				<?php
					echo JText::_('Filter').'&nbsp;';
					echo $this->lists['filter_type'].'&nbsp;';
				?>

				<input type="text" name="filter" id="filter" value="<?php echo $this->lists['filter'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="document.adminForm.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('filter').value='';document.adminForm.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<?php endif; ?>
		<?php if ($this->params->get('display')) : ?>
		<td align="right" width="100%" nowrap="nowrap">
			<?php
				echo '&nbsp;&nbsp;&nbsp;'.JText::_('Display Num').'&nbsp;';
				echo $this->pageNav->getLimitBox();
			?>
		</td>
		<?php endif; ?>
	</tr>
</table>
<br />
<?php endif; ?>

<table width="<?php echo $this->elsettings->tablewidth; ?>" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
			<tr>
				<td width="<?php echo $this->elsettings->datewidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->datename, 'a.dates', $this->lists ); ?></td>
				<?php
				if ($this->elsettings->showtitle == 1) :
				?>
				<td width="<?php echo $this->elsettings->titlewidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->titlename, 'a.title', $this->lists ); ?></td>
				<?php
				endif;
				if ($this->elsettings->showlocate == 1) :
				?>
				<td width="<?php echo $this->elsettings->locationwidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->locationname, 'l.club', $this->lists ); ?></td>
				<?php
				endif;
				if ($this->elsettings->showcity == 1) :
				?>
				<td width="<?php echo $this->elsettings->citywidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->cityname, 'l.city', $this->lists ); ?></td>
				<?php
				endif;
				if ($this->elsettings->showstate == 1) :
				?>
				<td width="<?php echo $this->elsettings->statewidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->statename, 'l.state', $this->lists ); ?></td>
				<?php
				endif;
				if ($this->elsettings->showcat == 1) :
				?>
				<td width="<?php echo $this->elsettings->catfrowidth; ?>" class="sectiontableheader" align="left"><?php JCommonHTML::tableOrdering( $this->elsettings->catfroname, 'c.catname', $this->lists ); ?></td>
				<?php
				endif;
				?>
			</tr>
	</table>

	<table width="<?php echo $this->elsettings->tablewidth; ?>"  border="0" cellspacing="0" cellpadding="0" summary="eventlist">
	<?php
	if ($this->noevents == 1) :
		?>
		<tr align="center"><td><?php echo JText::_( 'NO EVENTS' ); ?></td></tr>
		<?php
	else :

	$this->rows =& $this->getRows();

	foreach ($this->rows as $row) :
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
				$detaillink = JRoute::_( 'index.php?option=com_eventlist&view=details&did='. $row->slug );
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
					if ($this->elsettings->showlinkclub == 1 ) :
							echo $row->locid != 0 ? "<a href='".JRoute::_('index.php?option=com_eventlist&view=venueevents&locatid='.$row->venueslug)."'>".$row->club."</a>" : '-';
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
							<a href="<?php echo JRoute::_('index.php?option=com_eventlist&view=categoryevents&categid='.$row->categoryslug) ; ?>">
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