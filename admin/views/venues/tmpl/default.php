<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
  		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'VENUES' ); ?>::</font></td>
	</tr>
</table>

<table class="adminform">
	<tr>
		<td width="100%">
			 <?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap"><?php $this->lists['state']; ?></td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'Num' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
			<th align="left" class="title"><?php JHTML::element('grid_sort', 'VENUE', 'l.venue', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th align="left" class="title"><?php echo JText::_( 'WEBSITE' ); ?></th>
			<th align="left" class="title"><?php JHTML::element('grid_sort', 'CITY', 'l.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
			<th class="title"><?php echo JText::_( 'CREATION' ); ?></th>
		    <th width="80" colspan="2"><?php JHTML::element('grid_sort', 'REORDER', 'l.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
			$link 		= 'index.php?option=com_eventlist&controller=venues&task=edit&cid[]='. $row->id;
			$checked 	= JCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= JCommonHTML::PublishedProcessing( $row, $i );
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td><?php echo $checked; ?></td>
			<td align="left">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
					echo htmlspecialchars($row->venue, ENT_QUOTES);
				} else {
					?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'EDIT VENUE' ); ?>">
					<?php echo htmlspecialchars($row->venue, ENT_QUOTES); ?>
					</a>
				<?php
				}
				?>
			</td>
			<td align="left">
				<?php
				if ($row->url) {
				?>
					<a href="<?php echo htmlspecialchars($row->url, ENT_QUOTES); ?>" target="_blank">
						<?php
						if (strlen(htmlspecialchars($row->url, ENT_QUOTES)) > 35) {
							echo substr( htmlspecialchars($row->url, ENT_QUOTES), 0 , 35).'...';
						} else {
							echo htmlspecialchars($row->url, ENT_QUOTES);
						}
						?>
					</a>
				<?php
				} else {
					echo  '-';
				}
				?>
			</td>
			<td align="left"><?php echo htmlspecialchars($row->city, ENT_QUOTES) ? htmlspecialchars($row->city, ENT_QUOTES) : '-'; ?></td>
			<td align="center"><?php echo $published;?></td>
			<td>
				<?php echo JText::_( 'AUTHOR' ).': '; ?><a href="<?php echo 'index.php?option=com_users&task=edit&hidemainmenu=1&cid[]='.$row->created_by; ?>"><?php echo $row->author; ?></a><br />
				<?php echo JText::_( 'EMAIL' ).': '; ?><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a><br />
				<?php
				$delivertime 	= JHTML::Date( $row->created, DATE_FORMAT_LC2 );
				$edittime 		= JHTML::Date( $row->modified, DATE_FORMAT_LC2 );
				$image 			= JAdminMenus::imageCheckAdmin( 'icon-16-info.png', '/templates/'. $this->template .'/images/menu/', NULL, NULL, 'info' );
				$overlib 		= JText::_( 'CREATED AT' ).': '.$delivertime.'<br />';
				$overlib		.= JText::_( 'WITH IP' ).': '.$row->author_ip.'<br />';
				if ($row->modified != '0000-00-00 00:00:00') {
					$overlib 	.= JText::_( 'EDITED AT' ).': '.$edittime.'<br />';
					$overlib 	.= JText::_( 'EDITED FROM' ).': '.$row->editor.'<br />';
				}
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VENUE STATS'); ?>::<?php echo $overlib; ?>">
					<?php echo $image; ?>
				</span>
			</td>
			<td align="right">
				<?php
				echo $this->pageNav->orderUpIcon( $i, true, 'orderup', 'Move Up', $this->ordering );
				?>
			</td>
			<td align="left">
				<?php
				echo $this->pageNav->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $this->ordering );
				?>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<td colspan="9">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tfoot>
</table>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="venues" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>