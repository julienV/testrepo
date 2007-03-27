<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
				var form = document.adminForm;

			if (pressbutton == 'cancelvenue') {
				submitform( pressbutton );
				return;
			}

  			if (form.club.value == "") {
    			alert("<?php echo JText::_( 'ERROR ADD VENUE' ); ?>");
   	 			form.club.focus();
    			return false;
  			}
			if (form.street.value == "") {
    			alert("<?php echo JText::_( 'ERROR ADD STREET' ); ?>");
    			form.street.focus();
    			return false;
  			}
			if (form.plz.value == "") {
    			alert("<?php echo JText::_( 'ERROR ADD ZIP' ); ?>");
    			form.plz.focus();
    			return false;
  			}
			if (form.city.value == "") {
    			alert("<?php echo JText::_( 'ERROR ADD CITY' ); ?>");
    			form.city.focus();
    			return false;
  			}
			if (form.country.value == "") {
    			alert("<?php echo JText::_( 'ERROR ADD COUNTRY' ); ?>");
    			form.country.focus();
    			return false;
  			}

  			<?php
			// JavaScript for extracting editor text
			echo $this->editor->save( 'locdescription' );
			?>
			submitform(pressbutton);
		}

		var tastendruck = false
		function rechne(restzeichen)
		{
			maximum = <?php echo $this->elsettings->datdesclimit; ?>

			if (restzeichen.locdescription.value.length > maximum)
          	{
          		restzeichen.locdescription.value = restzeichen.locdescription.value.substring(0, maximum)
          		links = 0
          	}
  			else
          	{
        		links = maximum - restzeichen.locdescription.value.length
         	}
 			restzeichen.zeige.value = links
  		}
		function berechne(restzeichen)
   		{
  			tastendruck = true
  			rechne(restzeichen)
  		}
	</script>

	<form enctype="multipart/form-data" name="adminForm" action="<?php echo JRoute::_('index.php') ?>" method="post" onsubmit="javascript:setgood();">

		<table class="adminform" width="100%">
		<tr>
			<td>
				<div style="float: left;">
					<label for="club">
						<?php echo JText::_( 'VENUE' ).':'; ?>
					</label>
					<input name="club" value="<?php echo $this->row->club; ?>" size="55" maxlength="50" />
					&nbsp;&nbsp;&nbsp;
				</div>

				<div style="float: right;">
				<button type="button" onclick="submitbutton('savevenue')">
					<?php echo JText::_('SAVE') ?>
				</button>
				<button type="button" onclick="submitbutton('cancelvenue')" />
					<?php echo JText::_('CANCEL') ?>
				</button>
				</div>
			</td>
		</tr>
		</table>
		<br />

		<fieldset>
		<legend><?php echo JText::_('ADDRESS'); ?></legend>

		<table class="adminform" width="100%">
		  	<tr>
  				<td><?php echo JText::_( 'STREET' ).':'; ?></td>
				<td><input name="street" value="<?php echo $this->row->street; ?>" size="55" maxlength="50" /></td>
		  	</tr>
  			<tr>
  			  	<td><?php echo JText::_( 'ZIP' ).':'; ?></td>
  			  	<td><input name="plz" value="<?php echo $this->row->plz; ?>" size="15" maxlength="10" /></td>
		  	</tr>
  			<tr>
  				<td><?php echo JText::_( 'CITY' ).':'; ?></td>
  				<td><input name="city" value="<?php echo $this->row->city; ?>" size="55" maxlength="50" />
				</td>
  			</tr>
  			<tr>
  				<td><?php echo JText::_( 'STATE' ).':'; ?></td>
  				<td><input name="state" value="<?php echo $this->row->state; ?>" size="55" maxlength="50" />
				</td>
  			</tr>
  			<tr>
  			  	<td><?php echo JText::_( 'COUNTRY' ).':'; ?></td>
  			  	<td>
					<input name="country" value="<?php echo $this->row->country; ?>" size="4" maxlength="3" />&nbsp;
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('COUNTRY HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
				</td>
		  	</tr>
  			<tr>
    			<td><?php echo JText::_( 'WEBSITE' ).':'; ?></td>
    			<td>
    				<input name="url" value="<?php echo $this->row->url; ?>" size="55" maxlength="150" />&nbsp;
    				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('WEBSITE HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
    			</td>
  			</tr>
		</table>

		</fieldset>

		<?php
			if (( $this->elsettings->imageenabled == 2 ) || ($this->elsettings->imageenabled == 1)) {
		?>

		<fieldset>
		<legend><?php echo JText::_('IMAGE'); ?></legend>

		<table class="adminform" width="100%">
		<tr>
			<td>
				<?php
					if (!empty($this->row->locimage)) :
						if (file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$this->row->locimage)) :
						?>
						<a href="javascript:void window.open('<?php echo $this->limage['originalloc']; ?>','Popup','width=<?php echo $this->limage['widthloc']; ?>,height=<?php echo $this->limage['heightloc']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">
							<img src="<?php echo $this->limage['thumbloc']; ?>" width="<?php echo $this->limage['thumbwidthloc']; ?>" height="<?php echo $this->dimage['thumbheightloc']; ?>" alt="<?php echo $this->row->club; ?>" />
						</a>
					<?php
					//No thumbnail? Then take the in the settings specified values for the original
					else : ?>
						<img src="<?php echo $this->limage['originalloc']; ?>" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>">
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
		<?php } ?>

		<fieldset>
		<legend><?php echo JText::_('DESCRIPTION'); ?></legend>
		<?php

		//wenn usertyp min editor wird editor ausgegeben ansonsten textfeld
		if ( $this->editoruser ) {
			echo $this->editor->display('locdescription', $this->row->locdescription, '655', '400', '70', '15', false );
		} else {
		?>
			<textarea style="width:100%;" rows="10" name="locdescription" class="inputbox" wrap="VIRTUAL" onkeyup="berechne(this.form)"></textarea><br />
			<?php echo JText::_('NO HTML'); ?><br />
			<input disabled value="<?php echo $this->elsettings->datdesclimit; ?>" size="4" name="zeige" /><?php echo JText::_('AVAILABLE')." "; ?><br />
			<a href="javascript:rechne(document.adminForm);"><?php echo JText::_('REFRESH'); ?></a>

		<?php
			}
		?>

		</fieldset>

		<input type="hidden" name="option" value="<?php echo $option ; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid ; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->row->id ; ?>" />
		<input type="hidden" name="returnview" value="<?php echo $this->returnview; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="curimage" value="<?php echo $this->row->locimage; ?>" />
		<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
		<input type="hidden" name="task" value="savevenue" />
	</form>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>