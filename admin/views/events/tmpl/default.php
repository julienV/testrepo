<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td><img src="<?php echo $this->live_site.'/administrator/components/com_eventlist/assets/images/evlogo.png'; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>
		  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'EVENTS'); ?>::</font></td>
			</tr>
		</table>

		<table class="adminform">
			<tr>
			<td width="100%">
				<?php
				echo JText::_( 'SEARCH' );
				echo $this->lists['filter'];
				?>
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
				<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'DATE' ), 'a.dates', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'Start' ), 'a.times', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'EVENT TITLE' ), 'a.title', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'loc.club', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'loc.city', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CATEGORY' ), 'cat.catname', $this->lists ); ?></th>
			    <th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
				<th class="title"><?php echo JText::_( 'CREATION' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'REGISTERED USERS' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php
			$k = 0;
			for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = &$this->rows[$i];

				//Prepare date
				$date = strftime( $this->elsettings->formatdate, strtotime( $row->dates ));
				
				if (!$row->enddates) {
					$displaydate = $date;
				} else {
					$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
					$displaydate = $date.' - <br />'.$enddate;
				}

				//Prepare time
				if (!$row->times) {
					$displaytime = '-';
				} else {	
					$time = strftime( $this->elsettings->formattime, strtotime( $row->times ));
					$displaytime = $time.' '.$this->elsettings->timename;
				}
				$link 		= 'index.php?option=com_eventlist&controller=events&task=editevent&cid[]='.$row->id;
				$checked 	= JCommonHTML::CheckedOutProcessing( $row, $i );
				$published 	= JCommonHTML::PublishedProcessing( $row, $i );
   			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td>
					<?php
					if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
						echo $displaydate;
					} else {
						?>
						<a href="<?php echo $link; ?>" title="Edit Event">
						<?php echo $displaydate; ?>
						</a>
						<?php
					}
					?>
				</td>
				<td><?php echo $displaytime; ?></td>
				<td><?php echo htmlspecialchars($row->title, ENT_QUOTES) ? htmlspecialchars($row->title, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->club, ENT_QUOTES) ? htmlspecialchars($row->club, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->city, ENT_QUOTES) ? htmlspecialchars($row->city, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->catname, ENT_QUOTES) ? htmlspecialchars($row->catname, ENT_QUOTES) : '-'; ?></td>
				<td align="center"><?php echo $published; ?></td>
				<td>
					<?php echo JText::_( 'AUTHOR' ).': '; ?><a href="<?php echo 'index.php?option=com_users&task=edit&hidemainmenu=1&cid[]='.$row->uid; ?>"><?php echo $row->editor; ?></a><br />
					<?php echo JText::_( 'EMAIL' ).': '; ?><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a><br />
					<?php
					$delivertime = JHTML::Date( $row->deliverdate, DATE_FORMAT_LC2 );
					$edittime = JHTML::Date( $row->modified, DATE_FORMAT_LC2 );
					$image 		= JAdminMenus::ImageCheck( 'icon-16-info.png', '/templates/'. $this->template .'/images/menu/', NULL, NULL, 'info' );
					$overlib 	= JText::_( 'CREATED AT' ).': '.$delivertime.'<br />';
					$overlib	.= JText::_( 'WITH IP' ).': '.$row->deliverip.'<br />';
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_( 'EDITED AT' ).': '.$edittime.'<br />';
						$overlib 	.= JText::_( 'EDITED FROM' ).': '.$row->modifier.'<br />';
					}
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('EVENT STATS'); ?>::<?php echo $overlib; ?>">
						<?php echo $image; ?>
					</span>
				</td>
				<td align="center">
					<?php
					if ($row->registra == 1) {
						$linkreg 	= 'index.php?option=com_eventlist&view=attendees&rcid='.$row->id;
					?>
						<a href="<?php echo $linkreg; ?>" title="Edit Users">
						<?php echo $row->regCount; ?>
						</a>
					<?php
					}else {
					?>
						<img src="images/publish_x.png" width="16" height="16" border="0" alt="Registration disabled" />
					<?php
					}
					?>
				</td>
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
		<input type="hidden" name="controller" value="events" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
	</form>