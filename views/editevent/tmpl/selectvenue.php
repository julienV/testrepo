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

<h1 class='componentheading'>
	<?php
		echo JText::_('SELECTVENUE');
	?>
</h1>

<div class="clear"></div>

<form action="<?php echo JRoute::_('index.php?option=com_eventlist&task=selectvenue&tmpl=component') ?>" method="post" name="adminForm">

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
	<tr>
		<td align="left" width="100%" nowrap="nowrap">
			<?php echo JText::_( 'SEARCH' ).': ';
			echo $this->searchfilter;
			?>
				<input type="text" name="filter" id="filter" value="<?php echo $this->filter;?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="document.adminForm.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('filter').value='';document.adminForm.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td align="right" width="100%" nowrap="nowrap">
			<?php
				echo '&nbsp;&nbsp;&nbsp;'.JText::_('Display Num').'&nbsp;';
				echo $this->pageNav->getLimitBox();
			?>
		</td>
	</tr>
</table>

<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
		<tr>
			<td width="7" class="sectiontableheader" align="left"><?php echo JText::_( 'Num' ); ?></td>
			<td align="left" class="sectiontableheader" align="left"><?php JCommonHTML :: tableOrdering( JText::_( 'VENUE' ), 'l.venue', $this->lists, 'selectvenue' ); ?></td>
			<td align="left" class="sectiontableheader" align="left"><?php JCommonHTML :: tableOrdering( JText::_( 'CITY' ), 'l.city', $this->lists, 'selectvenue' ); ?></td>
			<td align="left" class="sectiontableheader" align="left"><?php echo JText::_( 'COUNTRY' ); ?></td>
		</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="eventlist">
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
	</tr>
		<?php $k = 1 - $k; } ?>
</table>

<p class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks(); ?>
</p>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>

<p class="copyright">
<?php echo ELOutput::footer( );	?>
</p>

<input type="hidden" name="task" value="selectvenue" />
<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>