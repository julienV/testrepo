<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
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
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
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
				<th class="title"><?php echo JHTML::_('grid.sort', 'DATE', 'a.dates', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'EVENT TIME', 'a.times', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'EVENT TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'VENUE', 'loc.venue', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'CITY', 'loc.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'CATEGORY', 'cat.catname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			    <th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
				<th class="title"><?php echo JText::_( 'CREATION' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'REGISTERED USERS' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
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

				$link 			= 'index.php?option=com_eventlist&controller=events&task=edit&cid[]='.$row->id;
				$catlink 		= 'index.php?option=com_eventlist&controller=categories&task=edit&cid[]='.$row->catsid;
				$venuelink 		= 'index.php?option=com_eventlist&controller=venues&task=edit&cid[]='.$row->locid;

				$checked 	= JHTML::_('grid.checkedout', $row, $i );
				$published 	= JHTML::_('grid.published', $row, $i );
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
						<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'EDIT EVENT' ); ?>">
						<?php echo $displaydate; ?>
						</a>
						<?php
					}
					?>
				</td>
				<td><?php echo $displaytime; ?></td>
				<td>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'EDIT EVENT' ); ?>">
						<?php echo htmlspecialchars($row->title, ENT_QUOTES) ? htmlspecialchars($row->title, ENT_QUOTES) : '-'; ?>
					</a><br />
					<?php echo /*JText::_( 'ALIAS' ).': '.*/$row->alias; ?>
				</td>
				<td>
					<?php
					if ($row->venue) {
					?>
						<a href="<?php echo $venuelink; ?>" title="<?php echo JText::_( 'EDIT VENUE' ); ?>">
							<?php echo htmlspecialchars($row->venue, ENT_QUOTES); ?>
						</a>
					<?php
					} else {
						echo '-';
					}
					?>
				</td>
				<td><?php echo htmlspecialchars($row->city, ENT_QUOTES) ? htmlspecialchars($row->city, ENT_QUOTES) : '-'; ?></td>
				<td>
					<?php
					if ($row->catname) {
					?>
						<a href="<?php echo $catlink; ?>" title="<?php echo JText::_( 'EDIT CATEGORY' ); ?>">
							<?php echo htmlspecialchars($row->catname, ENT_QUOTES); ?>
						</a>
					<?php
					} else {
						echo '-';
					}
					?>
				</td>
				<td align="center"><?php echo $published; ?></td>
				<td>
					<?php echo JText::_( 'AUTHOR' ).': '; ?><a href="<?php echo 'index.php?option=com_users&task=edit&hidemainmenu=1&cid[]='.$row->created_by; ?>"><?php echo $row->author; ?></a><br />
					<?php echo JText::_( 'EMAIL' ).': '; ?><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a><br />
					<?php
					$created	 	= JHTML::Date( $row->created, JText::_( 'DATE_FORMAT_LC2' ) );
					$edited 		= JHTML::Date( $row->modified, JText::_( 'DATE_FORMAT_LC2' ) );
					$image 			= JHTML::_('image.site', 'icon-16-info.png', '/templates/'. $this->template .'/images/menu/', NULL, NULL, 'info' );
					$overlib 		= JText::_( 'CREATED AT' ).': '.$created.'<br />';
					$overlib		.= JText::_( 'WITH IP' ).': '.$row->author_ip.'<br />';
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_( 'EDITED AT' ).': '.$edited.'<br />';
						$overlib 	.= JText::_( 'EDITED FROM' ).': '.$row->editor.'<br />';
					}
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('EVENT STATS'); ?>::<?php echo $overlib; ?>">
						<?php echo $image; ?>
					</span>
				</td>
				<td align="center">
					<?php
					if ($row->registra == 1) {
						$linkreg 	= 'index.php?option=com_eventlist&view=attendees&cid[]='.$row->id;
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
				<td align="center"><?php echo $row->id; ?></td>
			</tr>
			<?php $k = 1 - $k;  } ?>

			</tbody>

			<tfoot>
				<td colspan="12">
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