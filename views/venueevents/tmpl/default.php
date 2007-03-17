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
		if ( !$this->pop ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->item->id, $this->dellink, $this->params, 'venueevents' );
		endif;
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</span>
<?php if ($this->params->get('page_title')) : ?>
	<h1 class='componentheading'><?php echo $this->params->get('header').' - '.$this->venue->club; ?></h1>
<?php endif; ?>

<!--Venue-->
<div id="eventlist" class="el_venueevents">
			<?php //cell for flyer
				if (!empty($this->venue->locimage)) :
					if (file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$this->venue->locimage)) :

						if ($this->elsettings->lightbox == 0) :
						?>
						<a class="flyer" href="javascript:void window.open('<?php echo $this->limage['originalloc']; ?>','Popup','width=<?php echo $this->limage['widthloc']; ?>,height=<?php echo $this->limage['heightloc']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">
						<?php
						 else :
						?>
						<a href="<?php echo $this->limage['originalloc']; ?>" class="flyer" rel="lightbox" title="<?php echo $this->venue->club; ?>">
						<?php endif; ?>

						<img src="<?php echo $this->limage['thumbloc']; ?>" width="<?php echo $this->limage['thumbwidthloc']; ?>" height="<?php echo $this->limage['thumbheightloc']; ?>" alt="location image" title="<?php echo JText::_( 'CLICK TO ENLARGE' ); ?>" />
						</a>
						<?php else : ?>
						<img src="<?php echo $this->limage['originalloc']; ?>" class="flyer" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>" />
					<?php
					endif;
				else :
					echo '';
				endif;


				//Link to map
				switch ($this->elsettings->showmapserv) :
					case 0:
					break;

					case 1:
  						if ($this->elsettings->map24id != '') :
						?>
							<a class="flyer" href="http://link2.map24.com/?lid=<?php echo $this->elsettings->map24id ?>&maptype=JAVA&width0=2000&street0=<?php echo $this->venue->street ?>&zip0=<?php echo $this->venue->plz ?>&city0=<?php echo $this->venue->city ?>&country0=<?php echo $this->venue->country ?>&sym0=10280&description0=<?php echo $this->venue->club ?>" target="_blank">
							<img src="http://img.map24.com/map24/link2map24/de/show_address_7.gif" border=0 alt="Map24" />
							</a>
						<?php
						endif;
					break;

					case 2:
					?>
					<dl class="location floattext">
						<dt class="anfahrt">XXXXAnfahrtXXXX</dt>
							<dd class="map">
								<a href="http://maps.google.com/maps?q=<?php echo $this->venue->street; ?>+<?php echo $this->venue->city ?>+<?php echo $this->venue->plz ?>+<?php echo $this->venue->country ?>" title="<?php echo JText::_( 'MAP' ); ?>" target="_blank"><?php echo JText::_( 'MAP' ); ?></a>
							</dd>
					</dl>
				<?php
					break;
				endswitch; //switch ende
				?>
            <dl class="location floattext">
			 <dt class="club"><?php echo $this->elsettings->locationname.':'; ?></dt>
				<dd class="club">
					<?php echo $this->venue->club; ?>
					
				<?php
				if (!empty($this->venue->url)) :
				?>
					&nbsp; - &nbsp;
					<a href="<?php echo $this->venue->url; ?>" target="_blank"> <?php echo JText::_( 'WEBSITE' ); ?></a>
				<?php
				endif;
				?>
				</dd>

			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>

  			<?php if ( $this->venue->street ) : ?>
  			<dt class="club_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
			<dd class="club_street">
    			<?php echo $this->venue->street; ?>
			</dd>
			<?php endif; ?>
			
			<?php if ( $this->venue->plz ) : ?>
  			<dt class="club_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
			<dd class="club_plz">
    			<?php echo $this->venue->plz; ?>
			</dd>
			<?php endif; ?>
 
			<?php if ( $this->venue->city ) : ?>
    		<dt class="club_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    		<dd class="club_city">
    			<?php echo $this->venue->city; ?>
    		</dd>
    		<?php endif; ?>

    		<?php if ( $this->venue->state ) : ?>
			<dt class="club_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
			<dd class="club_state">
    			<?php echo $this->venue->state; ?>
			</dd>
			<?php endif; ?>
			
			<?php if ( $this->venue->country ) : ?>
			<dt class="club_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    		<dd class="club_country">
    			<?php echo $this->venue->country; ?>
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

<?php if (( $this->page > 0 ) && ( !$this->pop )) : ?>
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
