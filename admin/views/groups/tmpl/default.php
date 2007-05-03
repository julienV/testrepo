<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'GROUPS'); ?>::</font></td>
	</tr>
</table>

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' );?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'Num' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
			<th width="20%" class="title"><?php JHTML::_('grid.sort', 'GROUP NAME', 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JText::_( 'DESCRIPTION' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];

			$link 		= 'index.php?option=com_eventlist&controller=groups&task=edit&cid[]='.$row->id;
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td><?php echo $checked; ?></td>
			<td>
				<?php
					if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
						echo htmlspecialchars($row->name);
					} else {
				?>
				<a href="<?php echo $link; ?>" title="Edit Group">
				<?php echo htmlspecialchars($row->name); ?>
				</a>
				<?php } ?>
			</td>
			<td><?php echo htmlspecialchars($row->description); ?></td>
		</tr>
		<?php $k = 1 - $k;  } ?>

	</tbody>

	<tfoot>
			<td colspan="11">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
	</tfoot>
</table>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="controller" value="groups" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>