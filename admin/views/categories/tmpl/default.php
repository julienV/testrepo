<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
		  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'CATEGORIES' ); ?>::</font></td>
			</tr>
		</table>

		<table class="adminform">
			<tr>
			 <td width="100%">
			  	<?php echo JText::_( 'SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
			  <?php
			  echo $this->lists['state'];
				?>
				</td>
			</tr>
			</table>

			<table class="adminlist" cellspacing="1">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( 'Num' ); ?></th>
				<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th align="left" class="title"><?php JHTML::_('grid.sort', 'CATEGORY', 'catname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
				<th align="left" class="title"><?php JHTML::_('grid.sort', 'ACCESS', 'c.access', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			    <th align="left" class="title"><?php JHTML::_('grid.sort', 'GROUP', 'gr.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="80"><?php JHTML::_('grid.sort', 'REORDER', 'c.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			    <th width="1%"><?php JHTML::_('grid.order', $this->rows, 'filesave.png', 'saveordercat' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->rows); $i < $n; $i++) {
				$row = $this->rows[$i];

				$link 		= 'index.php?option=com_eventlist&controller=categories&task=edit&cid[]='. $row->id;
				$grouplink 	= 'index.php?option=com_eventlist&controller=groups&task=editgroup&cid[]='. $row->groupid;
				$published 	= JHTML::_('grid.published', $row, $i );
				$access 	= JHTML::_('grid.access', $row, $i );
				$checked 	= JHTML::_('grid.checkedout', $row, $i );
   			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td width="7"><?php echo $checked; ?></td>
				<td align="left">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
					echo htmlspecialchars($row->catname, ENT_QUOTES);
				} else {
				?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'EDIT CATEGORY' ); ?>">
					<?php echo htmlspecialchars($row->catname, ENT_QUOTES); ?>
					</a>
				<?php
				}
				?>
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>
				<td align="center">
				<?php
				echo $access;
				?>
				</td>
				<td align="center">
					<?php
					if ($row->catgroup) {
					?>
						<a href="<?php echo $grouplink; ?>" title="<?php echo JText::_( 'EDIT GROUP' ); ?>">
							<?php echo htmlspecialchars($row->catgroup, ENT_QUOTES); ?>
						</a>
					<?php
					} else {
						echo '-';
					}
					?>
				</td>
				<td class="order" colspan="2">
					<span><?php echo $this->pageNav->orderUpIcon( $i, true, 'orderup', 'Move Up', $this->ordering ); ?></span>

					<span><?php echo $this->pageNav->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $this->ordering );?></span>

					<?php $disabled = $this->ordering ?  '' : '"disabled=disabled"'; ?>

					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
				</td>
			</tr>
				<?php $k = 1 - $k; } ?>
			<tbody>
			<tfoot>
				<td colspan="8">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tfoot>
		</table>

		<p class="copyright">
			<?php echo ELAdmin::footer( ); ?>
		</p>

			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="controller" value="categories" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>