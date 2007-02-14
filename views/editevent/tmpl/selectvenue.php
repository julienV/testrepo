<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="post" name="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_( 'SEARCH' );
			echo $this->searchfilter;
			?>
			<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td>
			<?php echo $this->pageNav->getLimitBox( $this->link ); ?>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="7"><?php echo JText::_( 'Num' ); ?></th>
			<th align="left" class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'club', $this->lists, 'selectvenue' ); ?></th>
			<th align="left" class="title"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'l.city', $this->lists, 'selectvenue' ); ?></th>
			<th align="left" class="title"><?php echo JText::_( 'COUNTRY' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo  $i+1; ?></td>
			<td align="left">

				<a style="cursor:pointer" onclick="window.parent.elSelectVenue('<?php echo $row->id; ?>', '<?php echo $row->club; ?>');">
						<?php echo htmlspecialchars($row->club, ENT_QUOTES); ?>
				</a>

			</td>
			<td align="left"><?php echo $row->city; ?></td>
			<td align="left"><?php echo $row->country; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
	</tbody>

	<tfoot>
		<td colspan="5">
			<?php echo $this->pageNav->getPagesLinks( $this->link ); ?>
		</td>
	</tfoot>
</table>

<?php echo ELOutput::footer( );	?>

<input type="hidden" name="task" value="selectvenue" />
<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>