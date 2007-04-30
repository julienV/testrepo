<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php?option=com_eventlist&amp;view=categoryelement&amp;tmpl=component" method="post" name="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' ); ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap"><?php  echo $this->lists['state']; ?></td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="7"><?php echo JText::_( 'Num' ); ?></th>
			<th align="left" class="title"><?php JHTML::element('grid_sort', 'CATEGORY', 'catname', $this->lists['order_Dir'], $this->lists['order'], 'categoryelement' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'ACCESS' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++) {
			$row = $this->rows[$i];

			//TODO: translate
			if (!$row->access) {
				$access = 'Public';
			} else if ($row->access == 1) {
				$access = 'Registered';
			} else {
				$access = 'Special';
			}
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td width="7"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="left">
				<a style="cursor:pointer" onclick="window.parent.elSelectCategory('<?php echo $row->id; ?>', '<?php echo $row->catname; ?>');">
					<?php echo htmlspecialchars($row->catname, ENT_QUOTES); ?>
				</a>
			</td>
			<td align="center"><?php echo $access; ?></td>
			<td align="center">
				<?php
				$img = $row->published ? 'tick.png' : 'publish_x.png';
				$alt = $row->published ? 'Published' : 'Unpublished';
				?>
				<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
			</td>
		</tr>
			<?php $k = 1 - $k; } ?>
	<tbody>
	<tfoot>
		<td colspan="4">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tfoot>
</table>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<input type="hidden" name="task" value="">
<input type="hidden" name="tmpl" value="component">
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>