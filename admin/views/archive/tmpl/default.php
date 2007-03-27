<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
		  	<td><img src="<?php echo $this->live_site."/administrator/components/com_eventlist/assets/images/evlogo.png"; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>
		  	<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'ARCHIVE' ); ?>::</font></td>
		</tr>
	</table>

	<table class="adminform">
		<tr>
			<td width="100%">
				<?php
				echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
				<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by title or enter article ID' );?>"/>
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
				<th class="title"><?php JCommonHTML :: tableOrdering(JText::_( 'DATE' ), 'a.dates', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'Start' ), 'a.times', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'EVENT TITLE' ) , 'a.title', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'loc.club', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CATEGORY' ), 'cat.catname', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'loc.city', $this->lists ); ?></th>
				<th class="title"><?php echo JText::_( 'FRONTEND' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			$k = 0;
			for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = &$this->rows[$i];
				$date		= strftime( $this->elsettings->formatdate, strtotime( $row->dates ));

				if ($row->enddates == '0000-00-00') {
					$displaydate = $date;
				} else {
					$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
					$displaydate = $date.' - <br />'.$enddate;
				}

				//Don't display 0 time
				if ($row->times == '00:00:00') {
					$time = '';
				} else {
					$time = strftime( $this->elsettings->formattime, strtotime( $row->times ));
					$time = $time.' '.$this->elsettings->timename;
				}
   			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td>
					<?php echo $displaydate; ?>
				</td>
				<td><?php echo $time; ?></td>
				<td><?php echo htmlspecialchars($row->title, ENT_QUOTES) ? htmlspecialchars($row->title, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->club, ENT_QUOTES) ? htmlspecialchars($row->club, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->catname, ENT_QUOTES); ?></td>
				<td><?php echo htmlspecialchars($row->city, ENT_QUOTES) ? htmlspecialchars($row->city, ENT_QUOTES) : '-'; ?></td>
				<td>
				<?php if (!empty($row->sendername)) { ?>
				<?php echo $row->sendername; ?><br />
				<?php echo $row->sendermail; ?><br />
				<?php echo $row->deliverip; ?><br />
				<?php
				$delivertime = strftime( '%c',$row->deliverdate + ( $this->TimeOffset*60*60 ) );
				echo $delivertime;
				}
				?>
				</td>
			</tr>
			<?php $k = 1 - $k;  } ?>
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
	<input type="hidden" name="option" value="com_eventlist" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="archive" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>