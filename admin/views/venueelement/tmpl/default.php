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

defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_eventlist&amp;view=venueelement&amp;tmpl=component" method="post" name="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			 <?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="7"><?php echo JText::_( 'Num' ); ?></th>
			<th align="left" class="title"><?php echo JHTML::_('grid.sort', 'VENUE', 'l.venue', $this->lists['order_Dir'], $this->lists['order'], 'venueelement' ); ?></th>
			<th align="left" class="title"><?php echo JHTML::_('grid.sort', 'CITY', 'l.city', $this->lists['order_Dir'], $this->lists['order'], 'venueelement' ); ?></th>
			<th align="left" class="title"><?php echo JText::_( 'COUNTRY' ); ?></th>
			<th class="title"><?php echo JText::_( 'PUBLISHED' ); ?></th>
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
			<td align="left">
				<a style="cursor:pointer" onclick="window.parent.elSelectVenue('<?php echo $row->id; ?>', '<?php echo $row->venue; ?>');">
				<?php echo htmlspecialchars($row->venue, ENT_QUOTES); ?>
				</a>
			</td>
			<td align="left"><?php echo $row->city; ?></td>
			<td align="left"><?php echo $row->country; ?></td>
			<td>
				<?php $img = $row->published ? 'tick.png' : 'publish_x.png'; ?>
				<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="" />
			</td>
		</tr>

		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<td colspan="6">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tfoot>

</table>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>