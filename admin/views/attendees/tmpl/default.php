<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
		  	<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
		  	<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'REGISTERED USER' ); ?>::</font></td>
		</tr>
	</table>

	<br />

	<table class="adminlist" cellspacing="1">
		<tr>
		  	<td>
		  		<?php $datum = strftime($this->elsettings->formatdate,strtotime( $this->event->dates )); ?>
				<b><?php echo JText::_( 'DATE' ).':'; ?></b>&nbsp;<?php echo $datum; ?><br />
				<b><?php echo JText::_( 'EVENT TITLE' ).':'; ?></b>&nbsp;<?php echo $this->event->title; ?>
			</td>
		  </tr>
	</table>

	<br />

	<table class="adminform">
		<tr>
			 <td width="100%">
			 	<?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
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
				<th class="title"><?php echo JHTML::_('grid.sort', 'NAME', 'u.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'USERNAME', 'u.username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JText::_( 'EMAIL' ); ?></th>
				<th class="title"><?php echo JText::_( 'IP ADDRESS' ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'REGDATE', 'r.uregdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'USER ID', 'r.uid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JText::_( 'REMOVE USER' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			$k = 0;
			for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = &$this->rows[$i];
   			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->rid; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="<?php echo JRoute::_( 'index.php?option=com_users&task=edit&cid[]='.$row->uid ); ?>"><?php echo $row->name; ?></a></td>
				<td>
					<a href="<?php echo JRoute::_( 'index.php?option=com_users&task=edit&cid[]='.$row->uid ); ?>"><?php echo $row->username; ?></a>
				</td>
				<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
				<td><?php echo $row->uip; ?></td>
				<td><?php echo JHTML::Date( $row->uregdate, JText::_( 'DATE_FORMAT_LC2' ) ); ?></td>
				<td><?php echo $row->uid; ?></td>
				<td><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','removeuser')"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Delete" /></a></td>
			</tr>
			<?php $k = 1 - $k;  } ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>

	<p class="copyright">
		<?php echo ELAdmin::footer( ); ?>
	</p>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_eventlist" />
	<input type="hidden" name="controller" value="attendees" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="rcid" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>