<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<span class="buttons">
	<?php
		if ( !$this->params->get( 'popup' ) ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params, 'venueevents' );
		endif;

		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</span>

<?php if ($this->params->get('page_title')) : ?>

	<h1 class='componentheading'>
		<?php echo $this->params->get('header'); ?>
	</h1>

<?php endif; ?>

<div id="eventlist" class="el_venue">
<!--Venue-->

<?php foreach($this->rows as $row) : ?>


	<h2 class="eventlist">
		<a href="<?php echo JRoute::_('index.php?view=venueevents&locatid='.$row->slug); ?>"><?php echo $row->venue; ?></a>
	</h2>

			<?php
				echo ELOutput::flyer( $row, $this->elsettings, $row->limage );
				echo ELOutput::mapicon( $row, $this->elsettings );
			?>

            <dl class="location floattext">
				<dt class="venue_website"><?php echo JText::_( 'WEBSITE' ).':'; ?></dt>
   				<dd class="venue_website">
				<?php
				if (($this->elsettings->showdetlinkvenue == 1) && (!empty($row->url))) :
				?>
					<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo JText::_( 'WEBSITE' ); ?></a>
				<?php
				else :
					echo JText::_( 'NO WEBSITE' );
				endif;
				?>
				</dd>

			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>

  			<?php if ( $row->street ) : ?>
  			<dt class="venue_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
			<dd class="venue_street">
    			<?php echo $row->street; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $row->plz ) : ?>
  			<dt class="venue_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
			<dd class="venue_plz">
    			<?php echo $row->plz; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $row->city ) : ?>
    		<dt class="venue_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    		<dd class="venue_city">
    			<?php echo $row->city; ?>
    		</dd>
    		<?php endif; ?>

    		<?php if ( $row->state ) : ?>
			<dt class="venue_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
			<dd class="venue_state">
    			<?php echo $row->state; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $row->country ) : ?>
			<dt class="venue_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    		<dd class="venue_country">
    			<?php echo $row->countryimg ? $row->countryimg : $row->country; ?>
    		</dd>
    		<?php endif; ?>

    		<dt class="venue_assignedevents"><?php echo JText::_( 'EVENTS' ).':'; ?></dt>
    		<dd class="venue_assignedevents">
    			<?php echo $row->assignedevents; ?>
    		</dd>
		<?php
		endif;
		?>

	<?php
  		if ($this->elsettings->showlocdescription == 1) :
		?>
		<dt class="location_desc"><?php echo JText::_( 'DESCRIPTION' ); ?></dt>
  		<dd class="location_desc">
  			<?php echo $row->locdescription; ?>
  		</dd>

		<?php endif; ?>
	</dl>
	<?php endforeach; ?>
</div>

<!--pagination-->
<?php if (( $this->page > 0 ) && ( !$this->params->get( 'popup' ) )) : ?>
<p class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks($this->link); ?>
</p>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>
<?php endif; ?>

<!--copyright-->
<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>