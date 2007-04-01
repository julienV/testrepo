<?php defined('_JEXEC') or die('Restricted access'); ?>

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
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'loc.venue', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CATEGORY' ), 'cat.catname', $this->lists ); ?></th>
				<th class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'loc.city', $this->lists ); ?></th>
				<th class="title"><?php echo JText::_( 'CREATION' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			$k = 0;
			for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = &$this->rows[$i];
				$date		= strftime( $this->elsettings->formatdate, strtotime( $row->dates ));

				if (!$row->enddates) {
					$displaydate = $date;
				} else {
					$enddate 	= strftime( $this->elsettings->formatdate, strtotime( $row->enddates ));
					$displaydate = $date.' - <br />'.$enddate;
				}

				//Don't display 0 time
				if (!$row->times) {
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
				<td><?php echo htmlspecialchars($row->venue, ENT_QUOTES) ? htmlspecialchars($row->venue, ENT_QUOTES) : '-'; ?></td>
				<td><?php echo htmlspecialchars($row->catname, ENT_QUOTES); ?></td>
				<td><?php echo htmlspecialchars($row->city, ENT_QUOTES) ? htmlspecialchars($row->city, ENT_QUOTES) : '-'; ?></td>
				<td>
					<?php echo JText::_( 'AUTHOR' ).': '; ?><a href="<?php echo 'index.php?option=com_users&task=edit&hidemainmenu=1&cid[]='.$row->created_by; ?>"><?php echo $row->author; ?></a><br />
					<?php echo JText::_( 'EMAIL' ).': '; ?><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a><br />
					<?php
					$delivertime = JHTML::Date( $row->created, DATE_FORMAT_LC2 );
					$edittime = JHTML::Date( $row->modified, DATE_FORMAT_LC2 );
					$image 		= JAdminMenus::ImageCheck( 'icon-16-info.png', '/templates/'. $this->template .'/images/menu/', NULL, NULL, 'info' );
					$overlib 	= JText::_( 'CREATED AT' ).': '.$delivertime.'<br />';
					$overlib	.= JText::_( 'WITH IP' ).': '.$row->author_ip.'<br />';
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_( 'EDITED AT' ).': '.$edittime.'<br />';
						$overlib 	.= JText::_( 'EDITED FROM' ).': '.$row->editor.'<br />';
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