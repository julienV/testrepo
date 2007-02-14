<form action="index.php?option=com_eventlist&amp;view=venueelement&amp;tmpl=component" method="post" name="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			 <?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="7"><?php echo JText::_( 'Num' ); ?></th>
			<th align="left" class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'l.club', $this->lists, 'venueelement' ); ?></th>
			<th align="left" class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'l.city', $this->lists, 'venueelement' ); ?></th>
			<th align="left" class="title"><?php echo JText::_( 'COUNTRY' ); ?></th>
			<th class="title"><?php echo JText::_( 'PUBLISHED' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="left">
				<a style="cursor:pointer" onclick="window.parent.elSelectVenue('<?php echo $row->id; ?>', '<?php echo $row->club; ?>');">
				<?php echo htmlspecialchars($row->club, ENT_QUOTES); ?>
				</a>
			</td>
			<td align="left"><?php echo $row->city; ?></td>
			<td align="left"><?php echo $row->country; ?></td>
			<td>
				<?php $img = $row->published ? 'tick.png' : 'publish_x.png'; ?>
				<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="" />
			</td>
		</tr>

		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<td colspan="6">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tfoot>

</table>

<?php echo ELAdmin::footer( ); ?>

<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>