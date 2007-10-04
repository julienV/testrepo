<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
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

// no direct access
defined('_JEXEC') or die('Restricted access');
//TODO: Add reoccuring events
?>

<script language="javascript" type="text/javascript">
		Window.onDomReady(function(){
			document.formvalidator.setHandler('date',
				function (value) {
					if(value=="") {
						return true;
					} else {
						timer = new Date();
						time = timer.getTime();
						regexp = new Array();
						regexp[time] = new RegExp('[0-9]{4}-[0-1][0-9]-[0-3][0-9]','gi');
						return regexp[time].test(value);
					}
				}
			);
			document.formvalidator.setHandler('time',
				function (value) {
					if(value=="") {
						return true;
					} else {
						timer = new Date();
						time = timer.getTime();
						regexp = new Array();
						regexp[time] = new RegExp('[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}','gi');
						return regexp[time].test(value);
					}
				}
			);
			document.formvalidator.setHandler('venue',
				function (value) {
					timer = new Date();
					time = timer.getTime();
					regexp = new Array();
					regexp[time] = new RegExp('^[0-9]{1}[0-9]{0,}$');
					return regexp[time].test(value);
				}
			);
			document.formvalidator.setHandler('catsid',
				function (value) {
					if(value=="") {
						return true;
					} else {
						timer = new Date();
						time = timer.getTime();
						regexp = new Array();
						regexp[time] = new RegExp('^[1-9]{1}[0-9]{0,}$');
						return regexp[time].test(value);
					}
				}
			);
		});

		function submitbutton( pressbutton ) {


			if (pressbutton == 'cancelevent' || pressbutton == 'addvenue') {
				submitform( pressbutton );
				return;
			}

			var form = document.adminForm;
			var validator = document.formvalidator;
			var title = $(form.title).getValue();
			title.replace(/\s/g,'');

			if ( title.length==0 ) {
   				alert("<?php echo JText::_( 'ADD TITLE', true ); ?>");
   				validator.handleResponse(false,form.title);
   				form.title.focus();
   				return false;
  			} else if ( form.dates.value=="" ) {
   				alert("<?php echo JText::_( 'ADD DATE', true ); ?>");
   				validator.handleResponse(false,form.dates);
   				form.dates.focus();
   				return false;
  			} else if ( validator.validate(form.dates) === false ) {
   				alert("<?php echo JText::_( 'DATE WRONG', true ); ?>");
   				validator.handleResponse(false,form.dates);
   				form.dates.focus();
   				return false;
  			} else if ( validator.validate(form.enddates) === false ) {
  				alert(validator.validate(form.enddates));
  				alert("<?php echo JText::_( 'DATE WRONG', true ); ?>");
    			validator.handleResponse(false,form.enddates);
  				form.enddates.focus();
  				return false;
  			} else if ( validator.validate(form.times) === false ) {
    			alert("<?php echo JText::_( 'TIME WRONG', true ); ?>");
    			validator.handleResponse(false,form.times);
    			form.times.focus();
    			return false;
			} else if ( validator.validate(form.endtimes) === false ) {
  				alert("<?php echo JText::_( 'ENDTIME WRONG', true ); ?>");
    			validator.handleResponse(false,form.endtimes);
  				form.endtimes.focus();
  				return false;
			} else if ( validator.validate(form.catsid ) === false ) {
    			alert("<?php echo JText::_( 'SELECT CATEGORY', true ); ?>");
    			validator.handleResponse(false,form.catsid);
    			form.catsid.focus();
    			return false;
  			} else if ( validator.validate(form.locid) === false ) {
    			alert("<?php echo JText::_( 'SELECT VENUE', true ); ?>");
    			validator.handleResponse(false,form.locid);
    			form.locid.focus();
    			return false;
  			} else {
  			<?php
			// JavaScript for extracting editor text
				echo $this->editor->save( 'datdescription' );
			?>
				submitform(pressbutton);

				return true;
			}
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

	<form enctype="multipart/form-data" name="adminForm" action="<?php echo JRoute::_('index.php') ?>" method="post" class="form-validate">

	<table class="adminform" width="100%">
		<tr>
			<td>
				<div style="float: left;">
					<label for="title">
						<?php echo JText::_( 'TITLE' ).':'; ?>
					</label>
					<input class="inputbox required" type="text" id="title" name="title" value="<?php echo $this->escape($this->row->title); ?>" size="65" maxlength="60" />
					&nbsp;&nbsp;&nbsp;
				</div>
				<div style="float: right;">
				<button type="submit" class="submit" onclick="return submitbutton('saveevent')">
					<?php echo JText::_('SAVE') ?>
				</button>
				<button type="reset" class="button cancel" onclick="submitbutton('cancelevent')">
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
			<td><?php echo JText::_( 'VENUE' ).':'; ?></td>
			<td>
				<?php echo $this->venueselect; ?>
				&nbsp;
				<input class="inputbox" type="button" onclick="elSelectVenue(0, '<?php echo JText::_('NO VENUE'); ?>' );" value="<?php  echo JText::_('NO VENUE'); ?>" />
				&nbsp;
				<?php
				//show location submission link
				if ( $this->delloclink == 1 && !$this->row->id ) :
				?>
					<button type="button" onclick="submitbutton('addvenue')" /><?php echo JText::_( 'DELIVER NEW VENUE' ); ?></button>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_( 'CATEGORY' ).':'; ?>
			</td>
			<td>
				<?php
					$html = JHTML::_('select.genericlist', $this->categories, 'catsid','size="1" class="inputbox required validate-catsid"', 'value', 'text', $this->row->catsid );
					echo $html;
				?>
			</td>
		</tr>
		<tr>
			<td><label for="dates"><?php echo JText::_( 'DATE' ).':'; ?></label>
			</td>
			<td>
				<?php echo JHTML::_('calendar', $this->row->dates, "dates", "dates"); ?>
				&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('DATE HINT'); ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'ENDDATE' ).':'; ?>
			</td>
			<td>
            	<?php echo JHTML::_('calendar', $this->row->enddates, "enddates", "enddates"); ?>
				&nbsp;
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
				<input class="inputbox validate-time" name="times" value="<?php echo substr($this->row->times, 0, 5); ?>" size="15" maxlength="8" />&nbsp;
				<?php if ( $this->elsettings->showtime == 1 ) : ?>
			   		<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('TIME HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
			  	<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('ENDTIME HINT'); ?>">
						<?php echo $this->infoimage; ?>
					</span>
			   	<?php endif;?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_( 'ENDTIME' ).':'; ?>
			</td>
			<td>
				<input class="inputbox validate-time" name="endtimes" value="<?php echo substr($this->row->endtimes, 0, 5); ?>" size="15" maxlength="8" />&nbsp;
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
			  	$html = JHTML::_('select.booleanlist', 'registra', 'class="inputbox"', $this->row->registra );
				echo $html;
				?>
			</td>
		</tr>
		<?php
		//register end
		endif;

		if ( $this->elsettings->showfrounregistra == 2 ) :
		?>
		<tr>
			<td><?php echo JText::_( 'SUBMIT UNREGISTER' ).':'; ?></td>
			<td>
			<?php
			$html = JHTML::_('select.booleanlist', 'unregistra', 'class="inputbox"', $this->row->unregistra );
			echo $html;
			?>
			</td>
		</tr>
		<?php
		//unregister end
		endif;
		?>
	</table>
	</fieldset>

	<?php
	//registration end
	endif;
	?>

	<fieldset>
	<legend><?php echo JText::_('RECURRENCE'); ?></legend>
	<table width="100%">
					<tr>
						<td width="50%"><?php echo JText::_( 'RECURRENCE' ); ?>:</td>
						<td width="50%">
						  <select id="recurrence_select" name="recurrence_select" size="1">
						    <option value="0"><?php echo JText::_( 'NOTHING' ); ?></option>
						    <option value="1"><?php echo JText::_( 'DAYLY' ); ?></option>
						    <option value="2"><?php echo JText::_( 'WEEKLY' ); ?></option>
						    <option value="3"><?php echo JText::_( 'MONTHLY' ); ?></option>
						    <option value="4"><?php echo JText::_( 'WEEKDAY' ); ?></option>
						  </select>
						</td>
					</tr>
					<tr>
						<td colspan="2" id="recurrence_output">&nbsp;</td>
					</tr>
					<tr id="counter_row" style="display:none;">
						<td><?php echo JText::_( 'RECURRENCE COUNTER' ); ?>:</td>
						<td>
					        <?php echo JHTML::_('calendar', ($this->row->recurrence_counter)? $this->row->recurrence_counter: "0000-00-00", "recurrence_counter", "recurrence_counter"); ?>
					        <span class="editlinktip hasTip" title="<?php echo JText::_('FORMAT DATE'); ?>::<?php echo JText::_('RECURRENCE COUNTER TIP'); ?>">
								<?php echo $this->infoimage; ?>
							</span>
						</td>
					<tr>
					<tr>
						<td><br/></td>
					</tr>
				</table>
				<br/>
			<input type="hidden" name="recurrence_number" id="recurrence_number" value="<?php echo $this->row->recurrence_number; ?>" />
			<input type="hidden" name="recurrence_type" id="recurrence_type" value="<?php echo $this->row->recurrence_type; ?>" />
			<script type="text/javascript">
			<!--
				var $select_output = new Array();
				$select_output[1] = "<?php echo JText::_( 'OUTPUT DAY' ); ?>";
				$select_output[2] = "<?php echo JText::_( 'OUTPUT WEEK' ); ?>";
				$select_output[3] = "<?php echo JText::_( 'OUTPUT MONTH' ); ?>";
				$select_output[4] = "<?php echo JText::_( 'OUTPUT WEEKDAY' ); ?>";

				var $weekday = new Array();
				$weekday[0] = "<?php echo JText::_( 'MONDAY' ); ?>";
				$weekday[1] = "<?php echo JText::_( 'TUESDAY' ); ?>";
				$weekday[2] = "<?php echo JText::_( 'WEDNESDAY' ); ?>";
				$weekday[3] = "<?php echo JText::_( 'THURSDAY' ); ?>";
				$weekday[4] = "<?php echo JText::_( 'FRIDAY' ); ?>";
				$weekday[5] = "<?php echo JText::_( 'SATURDAY' ); ?>";
				$weekday[6] = "<?php echo JText::_( 'SUNDAY' ); ?>";
				start_recurrencescript();
			-->
			</script>
		</fieldset>

	<?php if (( $this->elsettings->imageenabled == 2 ) || ($this->elsettings->imageenabled == 1)) : ?>
	<fieldset>
	<legend><?php echo JText::_('IMAGE'); ?></legend>
	<table class="adminform" width="100%">
		<tr>
			<td>
				<?php
				if ($this->row->datimage) :
					echo ELOutput::flyer( $this->row, $this->elsettings, $this->dimage, 'event' );
				else :
					echo JHTML::_('image', 'images/cancel.png', 'no image');
				endif;
  			  	?>
			</td>
			<td>
				<input class="inputbox <?php echo $this->elsettings->imageenabled == 2 ? 'required' : ''; ?>" name="userfile" id="userfile" type="file" />&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('MAX IMAGE FILE SIZE').' '.$this->elsettings->sizelimit.' kb'; ?>">
					<?php echo $this->infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CURRENT IMAGE' ); ?></td>
			<td><?php echo JText::_( 'SELECTED IMAGE' ); ?></td>
		</tr>
		</table>
		</fieldset>
		<?php endif; ?>
		<fieldset>
		<legend><?php echo JText::_('DESCRIPTION'); ?></legend>

		<?php
		//if usertyp min editor then editor else textfield
		if ($this->editoruser) :
			echo $this->editor->display('datdescription', $this->row->datdescription, '100%', '400', '70', '15', array('pagebreak', 'readmore') );
		else :
		?>
			<textarea style="width:100%;" rows="10" name="datdescription" class="inputbox" wrap="virtual" onkeyup="berechne(this.form)"><?php echo $this->row->datdescription; ?></textarea><br />
			<?php echo JText::_( 'NO HTML' ); ?><br />
			<input disabled value="<?php echo $this->elsettings->datdesclimit; ?>" size="4" name="zeige" /><?php echo JText::_( 'AVAILABLE' ); ?><br />
			<a href="javascript:rechne(document.adminForm);"><?php echo JText::_( 'REFRESH' ); ?></a>
		<?php endif; ?>

		</fieldset>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="returnview" value="<?php echo $this->returnview; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="author_ip" value="<?php echo $this->row->author_ip; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="curimage" value="<?php echo $this->row->datimage; ?>" />
		<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
		<input type="hidden" name="task" value="" />
	</form>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>