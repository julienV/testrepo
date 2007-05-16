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
		<?php echo $this->venue->venue; ?>
	</h1>
<?php endif; ?>

<!--Venue-->
<div id="eventlist" class="el_venueevents">

	<?php //flyer
	echo ELOutput::flyer( $this->venue, $this->elsettings, $this->limage );
	echo ELOutput::mapicon( $this->venue, $this->elsettings );
	?>

	<dl class="location floattext">
		<?php if (!empty($this->venue->url)) : ?>
		<dt class="venue"><?php echo JText::_( 'WEBSITE' ).':'; ?></dt>
			<dd class="venue">
					<a href="<?php echo $this->venue->url; ?>" target="_blank"> <?php echo $this->venue->urlclean; ?></a>
			</dd>
		<?php endif; ?>

		<?php if ( $this->elsettings->showdetailsadress == 1 ) : ?>

  			<?php if ( $this->venue->street ) : ?>
  			<dt class="venue_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
			<dd class="venue_street">
    			<?php echo $this->venue->street; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $this->venue->plz ) : ?>
  			<dt class="venue_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
			<dd class="venue_plz">
    			<?php echo $this->venue->plz; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $this->venue->city ) : ?>
    		<dt class="venue_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    		<dd class="venue_city">
    			<?php echo $this->venue->city; ?>
    		</dd>
    		<?php endif; ?>

    		<?php if ( $this->venue->state ) : ?>
			<dt class="venue_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
			<dd class="venue_state">
    			<?php echo $this->venue->state; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $this->venue->country ) : ?>
			<dt class="venue_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    		<dd class="venue_country">
    			<?php echo $this->venue->countryimg ? $this->venue->countryimg : $this->venue->country; ?>
    		</dd>
    		<?php endif; ?>
		<?php
		endif; //showdetails ende
		?>
	</dl>

	<?php
  	if ($this->elsettings->showlocdescription == 1) :
	?>

		<h2 class="location_desc"><?php echo JText::_( 'DESCRIPTION' ); ?></h2>
	  		<div class="location_desc">
	  			<?php echo $this->venuedescription;	?>
				<br /><br />
			</div>

	<?php endif; ?>

	<!--table-->

	<?php echo $this->loadTemplate('table'); ?>

	<input type="hidden" name="option" value="com_eventlist" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="view" value="venueevents" />
	<input type="hidden" name="locatid" value="<?php echo $this->locatid; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->item->id;?>" />
	</form>

</div>

<!--pagination-->

<?php if (( $this->page > 0 ) && ( !$this->params->get( 'popup' ) )) : ?>
	<p class="pageslinks">
		<?php echo $this->pageNav->getPagesLinks(); ?>
	</p>

	<p class="pagescounter">
		<?php echo $this->pageNav->getPagesCounter(); ?>
	</p>
<?php endif; ?>

<!--copyright-->

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
