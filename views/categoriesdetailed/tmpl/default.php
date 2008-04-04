<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2008 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
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

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div id="eventlist" class="el_categoriesdetailed">
<p class="buttons">
	<?php
		if ( !$this->params->get( 'popup' ) ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params );
			echo ELOutput::archivebutton( $this->params, $this->task );
		endif;
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</p>

<?php if ($this->params->get('show_page_title')) : ?>

<h1 class="componentheading">
<?php echo $this->escape($this->pagetitle); ?>
</h1>

<?php endif;

foreach($this->categories as $category) :
?>
	<h2 class="eventlist cat<?php echo $category->id; ?>">
		<?php echo $this->escape($category->catname); ?>
	</h2>

<div class="cat<?php echo $category->id; ?> floattext">

	<div class="catimg">
	  	<?php
	  		echo JHTML::_('link', JRoute::_('index.php?view=categoryevents&id='.$category->slug), $category->image);
		?>
		<p>
			<?php
				echo JText::_( 'EVENTS' ).': ';
				if ($this->task == 'archive') :
					echo JHTML::_('link', JRoute::_('index.php?view=categoryevents&id='.$category->slug.'&task=archive'), $category->assignedevents);
				else:
					echo JHTML::_('link', JRoute::_('index.php?view=categoryevents&id='.$category->slug), $category->assignedevents);
				endif;
			?>
		</p>
	</div>

	<div class="catdescription"><?php echo $category->catdescription; ?>
		<p>
			<?php
				if ($this->task == 'archive') :
					echo JHTML::_('link', JRoute::_('index.php?view=categoryevents&id='.$category->slug.'&task=archive'), JText::_( 'SHOW ARCHIVE' ));
				else:
					echo JHTML::_('link', JRoute::_('index.php?view=categoryevents&id='.$category->slug), JText::_( 'SHOW EVENTS' ));
				endif;
			?>
		</p>
	</div>
	<br class="clear" />

</div>

<!--table-->
<?php
//TODO: move out of template
$this->rows		= & $this->model->getEventdata( $category->id );
$this->categoryid = $category->id;

echo $this->loadTemplate('table');

endforeach;
?>

<!--pagination-->

<div class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks(); ?>
</div>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>

<!--copyright-->

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
</div>