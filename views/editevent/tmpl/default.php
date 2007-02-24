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

		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {

				var form = document.adminForm;

				if (pressbutton == 'cancelevent') {
					submitform( pressbutton );
					return;
				}
  				if (form.dates.value == "") {
    				alert("<?php echo JText::_( 'ADD DATE' ); ?>");
    				form.dates.focus();
    				return false;
  				}
				var s = form.dates.value;
				var erg = s.match(/20[0-9]{2}-[0-1][0-9]-[0-3][0-9]/gi);
				if(!erg) {
    				alert("<?php echo JText::_( 'DATE WRONG' ); ?>");
    				form.dates.focus();
    				return false;
  				}
  				if (form.enddates.value != "") {
  					var es = form.enddates.value;
					var ergl = es.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi);
					if(!ergl) {
    					alert("<?php echo JText::_( 'DATE WRONG' ); ?>");
    					form.enddates.focus();
    					return false;
  					}
  				}
				<?php
				if ( $this->elsettings->showtime == 1 ) {
				?>
					if (form.times.value == "") {
    					alert("<?php echo JText::_( 'ADD TIME' ); ?>");
    					form.times.focus();
    					return false;
  					}
  				<?php
				} ?>
  				if ( form.times.value != "") {
					var t = form.times.value;
					var erg2 = t.match(/[0-2][0-9]:[0-5][0-9]/gi);
					if(!erg2) {
    					alert("<?php echo JText::_( 'TIME WRONG' ); ?>");
    					form.times.focus();
    					return false;
  					}
				}

				if ( form.endtimes.value != "") {
					var t = form.endtimes.value;
					var erg3 = t.match(/[0-2][0-9]:[0-5][0-9]/gi);
					if(!erg3) {
    					alert("<?php echo JText::_( 'ENDTIME WRONG' ); ?>");
    					form.endtimes.focus();
    					return false;
  					}
				}

				if (form.title.value == "") {
    				alert("<?php echo JText::_( 'ADD TITLE' ); ?>");
    				form.title.focus();
    				return false;
  				}
				if (form.catsid.value == "0") {
    				alert("<?php echo JText::_( 'SELECT CATEGORY' ); ?>");
    				form.catsid.focus();
    				return false;
  				}
				if (form.locid.value == "0") {
    				alert("<?php echo JText::_( 'SELECT VENUE' ); ?>");
    				form.locid.focus();
    				return false;
  				}

  		<?php
		// JavaScript for extracting editor text
		echo $this->editor->save( 'datdescription' );
		?>
					submitform(pressbutton);
		}


		var tastendruck = false
		function rechne(restzeichen)
		{

			maximum = <?php echo $this->elsettings->datdesclimit; ?>

			if (restzeichen.datdescription.value.length > maximum) {
				restzeichen.datdescription.value = restzeichen.datdescription.value.substring(0, maximum)
				links = 0
			} else {
				links = maximum - restzeichen.datdescription.value.length
			}
			restzeichen.zeige.value = links
		}

		function berechne(restzeichen)
   		{
  			tastendruck = true
  			rechne(restzeichen)
   		}
	</script>

	<form enctype="multipart/form-data" name="adminForm" action="index.php" method="post" onsubmit="javascript:setgood();">

		<table class="adminform" width="100%">
		<tr>
			<td>
				<div style="float: left;">
					<label for="title">
						<?php echo JText::_( 'TITLE' ).':'; ?>
					</label>
					<input class="inputbox" type="text" id="title" name="title" value="<?php echo $this->row->title; ?>" size="65" maxlength="60" />
					&nbsp;&nbsp;&nbsp;
				</div>
				<div style="float: right;">
				<button type="button" onclick="submitbutton('saveevent')">
					<?php echo JText::_('SAVE') ?>
				</button>
				<button type="button" onclick="submitbutton('cancelevent')" />
					<?php echo JText::_('CANCEL') ?>
				</button>
				</div>
			</td>
		</tr>
		</table>

		<fieldset>
		<legend><?php echo JText::_('NORMAL INFO'); ?></legend>

		<table class="adminform" width="100%">
		<tr>
			<td><?php echo JText::_( 'VENUE' ).':'; ?>
			</td>
			<td>
			<?php
				//$html = JHTMLSelect::genericList( $this->locations, 'locid','size="1" class="inputbox"', 'value', 'text', $this->row->locid );
				//echo $html;
				echo $this->venueselect;
			?>
			&nbsp;<input class="inputbox" type="button" onclick="elSelectVenue(0, '<?php echo JText::_('NO VENUE'); ?>' );" value="<?php  echo JText::_('NO VENUE'); ?>" />
			<?php
				//show location submission link
				if ( $this->delloclink == 1 && !$this->row->id ) :
						$link = 'index.php?option=com_eventlist&amp;Itemid='.$Itemid.'&amp;Returnid='.$Itemid.'&amp;view=editvenue&amp;returnview='.$this->returnview;
						?>
						<a href="<?php echo $link ?>" target="_self">
						<?php echo JText::_( 'DELIVER NEW VENUE' ); ?></a>
				<?php endif; ?>
				<br /><br />
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_( 'CATEGORY' ).':'; ?>
			</td>
			<td>
				<?php
					$html = JHTMLSelect::genericList( $this->categories, 'catsid','size="1" class="inputbox"', 'value', 'text', $this->row->catsid );
					echo $html;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'DATE' ).':'; ?>
			</td>
			<td>
				<input id="dates" name="dates" value="<?php echo $this->row->dates; ?>" size="15" maxlength="10" />
				<input class="button" value="..." onclick="return showCalendar('dates', 'Y-m-d');" type="reset" />&nbsp;

				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('DATE HINT'); ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'ENDDATE' ).':'; ?>
			</td>
			<td>
				<?php if ($this->row->enddates == '0000-00-00') {
					$this->row->enddates ='';
				}
				?>
				<input id="enddates" name="enddates" value="<?php echo $this->row->enddates; ?>" size="15" maxlength="10" />
				<input class="button" value="..." onclick="return showCalendar('enddates', 'Y-m-d');" type="reset" />&nbsp;

				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('DATE HINT'); ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_( 'TIME' ).':'; ?>
			</td>
			<td>
				<input name="times" value="<?php echo substr($this->row->times, 0, 5); ?>" size="15" maxlength="8" />&nbsp;
				<b>
				<?php if ( $this->elsettings->showtime == 1 ) : ?>
			   		<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('TIME HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
			  	<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('ENDTIME HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
			   	<?php endif;?>
				</b>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_( 'ENDTIME' ).':'; ?>
			</td>
			<td>
				<?php
				if ($this->row->endtimes == '00:00:00') :
					$this->row->endtimes = '';
				else :
					$this->row->endtimes = substr($this->row->endtimes, 0, 5);
				endif;
				?>
				<input name="endtimes" value="<?php echo substr($this->row->endtimes, 0, 5); ?>" size="15" maxlength="8" />&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('ENDTIME HINT'); ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
	</table>
	</fieldset>

	<?php if ( $this->elsettings->showfroregistra == 2 ) : ?>
	<fieldset>
	<legend><?php echo JText::_('REGISTRATION'); ?></legend>
	<table>
		<?php if ( $this->elsettings->showfroregistra == 2 ) : ?>
		<tr>
			<td><?php echo JText::_( 'SUBMIT REGISTER' ).':'; ?></td>
			<td>
				<?php
			  	$html = JHTMLSelect::yesnoList( 'registra', 'class="inputbox"', $this->row->registra );
				echo $html;
				?>
			</td>
		</tr>
		<?php
		endif;//register end

		if ( $this->elsettings->showfrounregistra == 2 ) :
		?>
		<tr>
			<td><?php echo JText::_( 'SUBMIT UNREGISTER' ).':'; ?></td>
			<td>
			<?php
			$html = JHTMLSelect::yesnoList( 'unregistra', 'class="inputbox"', $this->row->unregistra );
			echo $html;
			?>
			</td>
		</tr>
		<?php endif;//unregister end ?>
	</table>
	</fieldset>
	<?php endif;//registration end ?>


	<?php if (( $this->elsettings->imageenabled == 2 ) || ($this->elsettings->imageenabled == 1)) : ?>
	<fieldset>
	<legend><?php echo JText::_('IMAGE'); ?></legend>
	<table class="adminform" width="100%">
		<tr>
			<td>
				<?php
					if (!empty($this->row->datimage)) :
						if (file_exists(JPATH_SITE.'/images/eventlist/events/small/'.$this->row->datimage)) :
						?>
							<a href="javascript:void window.open('<?php echo $this->dimage['original']; ?>','Popup','width=<?php echo $this->dimage['widthev']; ?>,height=<?php echo $this->dimage['heightev']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">
								<img src="<?php echo $this->dimage['thumb']; ?>" width="<?php echo $this->dimage['thumbwidthev']; ?>" height="<?php echo $this->dimage['thumbheightev']; ?>" alt="<?php echo $this->row->title; ?>" />
							</a>
						<?php
						//No thumbnail? Then take the in the settings specified values for the original
						else : ?>
							<img src="<?php echo $this->dimage['original']; ?>" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>">
					<?php
						endif;
				 else :
				 	?>
					<img src="<?php echo $this->live_site.'/images/cancel.png'; ?>" alt="no image"/>
					<?php
				endif;
  			  	?>
			</td>
			<td>
				<input name="userfile" class="inputbox" type="file" />&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('MAX IMAGE FILE SIZE').' '.$this->elsettings->sizelimit.' kb'; ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<?php /*
  			  		if ($this->row->id) {
  			  			echo JText::_( 'SELECT IMAGE EDIT' ).':';
  			  		} else {
  			  			echo JText::_( 'SELECT IMAGE' ).':';
  			  		}
  			  		*/
					echo JText::_( 'CURRENT IMAGE' );
  			  	?>
			</td>
			<td>
			<?php /*
				<input name="userfile" class="inputbox" type="file" /><b> <?php echo JText::_( 'MAX IMAGE FILE SIZE' ); ?><b> <?php echo $this->sizelimit; ?> kb</b>
				*/
  			  	echo JText::_( 'SELECTED IMAGE' );
  			  	?>
			</td>
		</tr>
		</table>
		</fieldset>
		<?php endif; ?>

		<fieldset>
		<legend><?php echo JText::_('DESCRIPTION'); ?></legend>

		<?php
		//if usertyp min editor then editor else textfield
		if ($this->editoruser) :
			echo $this->editor->display('datdescription', $this->row->datdescription, '100%', '400', '70', '15');
		else :
		?>
			<textarea style="width:100%;" rows="10" name="datdescription" class="inputbox" wrap="virtual" onkeyup="berechne(this.form)"><?php echo $this->row->datdescription; ?></textarea><br />
			<?php echo JText::_( 'NO HTML' ); ?><br />
			<input disabled value="<?php echo $this->elsettings->datdesclimit; ?>" size="4" name="zeige" /><?php echo JText::_( 'AVAILABLE' ); ?><br />
			<a href="javascript:rechne(document.adminForm);"><?php echo JText::_( 'REFRESH' ); ?></a>
		<?php endif; ?>

		</fieldset>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="returnview" value="<?php echo $this->returnview; ?>" />
		<input type="hidden" name="deliverdate" value="<?php echo $this->row->deliverdate; ?>" />
		<input type="hidden" name="curimage" value="<?php echo $this->row->datimage; ?>" />
		<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
		<input type="hidden" name="task" value="saveevent" />
	</form>