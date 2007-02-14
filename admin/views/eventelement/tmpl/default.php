<form action="index.php?option=com_eventlist&amp;view=eventelement&amp;tmpl=component" method="post" name="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['state'];	?>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'Num' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'EVENT TITLE' ), 'a.title', $this->lists, 'eventelement' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'DATE' ), 'dates', $this->lists, 'eventelement' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'Start' ), 'a.times', $this->lists, 'eventelement' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'loc.club', $this->lists, 'eventelement' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'cat.catname', $this->lists, 'eventelement' ); ?></th>
			<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CATEGORY' ), 'loc.city', $this->lists, 'eventelement' ); ?></th>
		    <th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
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
			<td>
				<a style="cursor:pointer" onclick="window.parent.elSelectEvent('<?php echo $row->id; ?>', '<?php echo $row->title; ?>');">
					<?php echo htmlspecialchars($row->title, ENT_QUOTES); ?>
				</a>
			</td>
			<td>
				<?php
					//Format date
					$date = strftime( $this->elsettings->formatdate, strtotime( $row->dates ));
					if ($row->enddates == '0000-00-00') {
						$displaydate = $date;
					} else {
						$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
						$displaydate = $date.' - '.$enddate;
					}

					echo $displaydate;
				?>
			</td>
			<td>
				<?php
					//Format time
					$time = strftime( $this->elsettings->formattime, strtotime( $row->times ));
					$time = $time.' '.$timename;

					$endtime = strftime( $this->elsettings->formattime, strtotime( $row->endtimes ));
					$endtime = $endtime.' '.$timename;

					if ($row->times != '00:00:00') {
						$displaytime = '<br />'.$time;
					}

					if ($row->endtimes != '00:00:00') {
						$displaytime = '<br />'.$time.' - '.$endtime;
					}
					echo $displaytime ? $displaytime : '-';
				?>
			</td>
			<td><?php echo $row->club ? $row->club : '-'; ?></td>
			<td><?php echo $row->city ? $row->city : '-'; ?></td>
			<td><?php echo $row->catname ? $row->catname : '-'; ?></td>
			<td align="center">
				<?php $img = $row->published ? 'tick.png' : 'publish_x.png'; ?>
				<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="" />
			</td>
		</tr>
			<?php $k = 1 - $k; } ?>
	</tbody>

	<tfoot>
		<td colspan="8">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tfoot>
</table>

<?php echo ELAdmin::footer( ); ?>

<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>