<?php
/**
 * @version 1.1 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
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

defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>

<script type="text/javascript">

	window.addEvent( "domready", function()
	{  
   		var hits = new eventscreen('hits', {id:<?php echo $this->row->id ? $this->row->id : 0; ?>, task:'gethits'});
    	hits.fetchscreen();
	});

	function reseter(task, id, div)
	{	
		var res = new eventscreen();
    	res.reseter( task, id, div );
	}

	function submitbutton(task)
	{

		var form = document.adminForm;
		var datdescription = <?php
		echo $this->editor->getContent ( 'datdescription' );
		?>

		if (task == 'cancel') {
			submitform( task );
		} else if (form.dates.value == ""){
			alert( "<?php echo JText::_ ( 'ADD DATE' );	?>" );
		} else if (form.title.value == ""){
			alert( "<?php echo JText::_ ( 'ADD TITLE' ); ?>" );
			form.title.focus();
		} else if (!form.dates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_ ( 'DATE WRONG' ); ?>");
		} else if (form.enddates.value !="" && !form.enddates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_ ( 'ENDDATE WRONG' );	?>");
		/*
		} else if (form.times.value == "" && form.endtimes.value != "") {
			alert("<?php echo JText::_ ( 'ADD TIME' ); ?>");
			form.times.focus();
		} else if (form.times.value != "" && !form.times.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_ ( 'TIME WRONG' ); ?>");
			form.times.focus();
		} else if (form.endtimes.value != "" && !form.endtimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_ ( 'TIME WRONG' ); ?>");
			form.endtimes.focus();
		*/
		
		} else if (form.cid.selectedIndex == -1) {
			alert( "<?php echo JText::_ ( 'CHOOSE CATEGORY' );?>" );
		} else if (form.locid.value == ""){
			alert( "<?php echo JText::_ ( 'CHOOSE VENUE' );	?>" );
		} else {
			<?php
			echo $this->editor->save ( 'datdescription' );
			?>
			$("meta_keywords").value = $keywords;
			$("meta_description").value = $description;
			submit_unlimited();

			submitform( task );
		}
	}
</script>
<?php
//Set the info image
$infoimage = JHTML::image ( 'components/com_eventlist/assets/images/icon-16-hint.png', JText::_ ( 'NOTES' ) );
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
	<td valign="top">
		<table class="adminform">
			<tr>
				<td>
					<label for="title"><?php echo JText::_ ( 'EVENT TITLE' ) . ':'; ?></label>
				</td>
				<td>
					<input class="inputbox" name="title" value="<?php echo $this->row->title; ?>" size="50" maxlength="100" id="title" />
				</td>
				<td>
					<label for="published"><?php echo JText::_ ( 'PUBLISHED' ) . ':'; ?></label>
				</td>
				<td>
					<?php
					$html = JHTML::_ ( 'select.booleanlist', 'published', '', $this->row->published );
					echo $html;
					?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="alias"><?php echo JText::_ ( 'Alias' ) . ':'; ?></label>
				</td>
				<td colspan="3">
					<input class="inputbox" type="text" name="alias" id="alias" size="50" maxlength="100" value="<?php echo $this->row->alias; ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="venueid"><?php echo JText::_ ( 'VENUE' ) . ':'; ?></label>
				</td>
				<td colspan="3">
					<?php echo $this->venueselect; ?>
				</td>
			</tr>
		</table>

		<table class="adminform">
			<tr>
				<td>
						<?php
						// parameters : areaname, content, hidden field, width, height, rows, cols, buttons
						echo $this->editor->display ( 'datdescription', $this->row->datdescription, '100%;', '550', '75', '20', array ('pagebreak', 'readmore' ) );
						?>
				</td>
			</tr>
		</table>
	</td>

	<td valign="top" width="320px" style="padding: 7px 0 0 5px">
		<?php
		// used to hide "Reset Hits" when hits = 0
		if (! $this->row->hits) {
			$visibility = 'style="display: none; visibility: hidden;"';
		} else {
			$visibility = '';
		}
		?>
		<table width="100%"
			style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
			<?php if ($this->row->id) { ?>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'ID' ); ?>:</strong>
				</td>
				<td>
					<?php echo $this->row->id; ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'STATE' ); ?></strong>
				</td>
				<td>
					<?php
					echo $this->row->published > 0 ? JText::_ ( 'PUBLISHED' ) : ($this->row->published < 0 ? JText::_ ( 'ARCHIVED' ) : JText::_ ( 'DRAFT UNPUBLISHED' ));
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'HITS' );	?></strong>
				</td>
				<td>
					<div id="hits"></div>
					<span <?php	echo $visibility; ?>>
						<input name="reset_hits" type="button" class="button" value="<?php echo JText::_ ( 'RESET' );?>" onclick="reseter('resethits', '<?php echo $this->row->id;?>', 'hits')" />
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'REVISED' ); ?></strong>
				</td>
				<td>
					<?php echo $this->row->version . ' ' . JText::_ ( 'TIMES' ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'CREATED AT' );?></strong>
				</td>
				<td>
					<?php
					if ($this->row->created == $this->nullDate) {
						echo JText::_ ( 'NEW EVENT' );
					} else {
						echo JHTML::_ ( 'date', $this->row->created, JText::_ ( 'DATE_FORMAT_LC2' ) );
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'EDITED AT' ); ?></strong>
				</td>
				<td>
					<?php
					if ($this->row->modified == $this->nullDate) {
						echo JText::_ ( 'NOT MODIFIED' );
					} else {
						echo JHTML::_ ( 'date', $this->row->modified, JText::_ ( 'DATE_FORMAT_LC2' ) );
					}
					?>
				</td>
			</tr>
		</table>

		<table width="100%"	style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
			<tr>
				<td>
					<strong><?php echo JText::_ ( 'CATEGORIES' ); ?></strong>
					<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' ); ?>::<?php echo JText::_ ( 'CATEGORIES NOTES' );?>">
						<?php echo $infoimage; ?>
					</span>
				</td>
				<td>
						<?php echo $this->Lists ['category']; ?>
				</td>
			</tr>
		</table>
		
		<?php
		$title = JText::_ ( 'DETAILS' );
		echo $this->pane->startPane ( "det-pane" );
		echo $this->pane->startPanel ( $title, 'date' );
		?>
		<table>
			<tr>
				<td>
					<label for="dates">
							<?php
							echo JText::_ ( 'DATE' ) . ':';
							?>
					</label>
				</td>
				<td>
					<?php
					echo JHTML::_ ( 'calendar', $this->row->dates, "dates", "dates" );
					?>
           		</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' ); ?>::<?php echo JText::_ ( 'FORMAT DATE' );?>">
						<?php echo $infoimage; ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="enddates">
						<?php echo JText::_ ( 'ENDDATE' ) . ':'; ?>
					</label>
				</td>
				<td>
					<?php echo JHTML::_ ( 'calendar', $this->row->enddates, "enddates", "enddates" );?>
           		</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' );?>::<?php echo JText::_ ( 'FORMAT DATE' );?>">
						<?php echo $infoimage; ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="times">
							<?php echo JText::_ ( 'EVENT TIME' ) . ':';	?>
					</label>
				</td>
				<td>
					<?php					
					echo ELAdmin::buildtimeselect(23, 'starthours', substr( $this->row->times, 0, 2 )).' : ';
					echo ELAdmin::buildtimeselect(59, 'startminutes', substr( $this->row->times, 4, 6 ));
					?>
				</td>
				<td>
			  		<?php if ($this->elsettings->showtime == 1) { ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' );?>::<?php echo JText::_ ( 'FORMAT TIME' );?>">
							<?php echo $infoimage;?>
						</span>
			  		<?php } else { ?>
			  			<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' );?>::<?php echo JText::_ ( 'FORMAT TIME OPTIONAL' );?>">
							<?php echo $infoimage;?>
						</span>
			  			<?php }	?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="endtimes">
							<?php echo JText::_ ( 'END TIME' ) . ':';?>
					</label>
				</td>
				<td>
					<?php					
					echo ELAdmin::buildtimeselect(23, 'endhours', substr( $this->row->endtimes, 0, 2 )).' : ';
					echo ELAdmin::buildtimeselect(59, 'endminutes', substr( $this->row->endtimes, 4, 6 ));
					?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_ ( 'NOTES' );?>::<?php echo JText::_ ( 'FORMAT TIME OPTIONAL' );?>">
						<?php echo $infoimage;?>
					</span>
				</td>
			</tr>
		</table>
		
		<?php
		$title = JText::_ ( 'REGISTRATION' );
		echo $this->pane->endPanel ();
		echo $this->pane->startPanel ( $title, 'registra' );
		?>
		<table>
			<tr>
				<td>
					<label for="registra"><?php	echo JText::_ ( 'ENABLE REGISTRATION' ) . ':';?></label>
				</td>
				<td>
					<?php
					$html = JHTML::_ ( 'select.booleanlist', 'registra', '', $this->row->registra );
					echo $html;
					?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="unregistra"><?php echo JText::_ ( 'ENABLE UNREGISTRATION' ) . ':';?></label>
				</td>
				<td>
					<?php
					$html = JHTML::_ ( 'select.booleanlist', 'unregistra', '', $this->row->unregistra );
					echo $html;
					?>
				</td>
			</tr>
		</table>
		
		<?php
		$title = JText::_ ( 'IMAGE' );
		echo $this->pane->endPanel ();
		echo $this->pane->startPanel ( $title, 'image' );
		?>
		<table>
			<tr>
				<td>
					<label for="image">	<?php echo JText::_ ( 'CHOOSE IMAGE' ) . ':'; ?></label>
				</td>
				<td>
					<?php echo $this->imageselect;?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<img src="../images/M_images/blank.png" name="imagelib"	id="imagelib" width="80" height="80" border="2" alt="Preview" />
					<script	language="javascript" type="text/javascript">
						if (document.forms[0].a_imagename.value!=''){
							var imname = document.forms[0].a_imagename.value;
							jsimg='../images/eventlist/events/' + imname;
							document.getElementById('imagelib').src= jsimg;
						}
					</script> 
					<br />
				</td>
			</tr>
		</table>
			
		<?php
		$title = JText::_ ( 'RECURRING EVENTS' );
		echo $this->pane->endPanel ();
		echo $this->pane->startPanel ( $title, 'recurrence' );
		?>
		<table width="100%" height="200px">
			<tr>
				<td width="40%">
					<?php echo JText::_ ( 'RECURRENCE' ); ?>:
				</td>
				<td width="60%">
					<?php echo $this->Lists['recurrence_type']; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" id="recurrence_output">&nbsp;</td>
			</tr>
			<tr id="counter_row" style="display: none;">
				<td>
					<?php echo JText::_ ( 'RECURRENCE COUNTER' );?>:
				</td>
				<td>
					<?php echo JHTML::_ ( 'calendar', ($this->row->recurrence_limit_date != '0000-00-00') ? $this->row->recurrence_limit_date : JText::_ ( 'UNLIMITED' ), "recurrence_limit_date", "recurrence_limit_date" );?>
					<a href="#" onclick="include_unlimited('<?php echo JText::_ ( 'UNLIMITED' );?>'); return false;">
						<img src="../components/com_eventlist/assets/images/unlimited.png" width="16" height="16" alt="<?php echo JText::_ ( 'UNLIMITED' );	?>" />
					</a>
				</td>
			</tr>
			<tr>
				<td><br /><br /></td>
			</tr>
		</table>
		
		<br />
		
		<input type="hidden" name="recurrence_number" id="recurrence_number" value="<?php echo $this->row->recurrence_number;?>" />
    <input type="hidden" name="recurrence_byday" id="recurrence_byday" value="<?php echo $this->row->recurrence_byday;?>" />
		<script
			type="text/javascript">
			<!--
				var $select_output = new Array();
				$select_output[1] = "<?php
				echo JText::_ ( 'OUTPUT DAY' );
				?>";
				$select_output[2] = "<?php
				echo JText::_ ( 'OUTPUT WEEK' );
				?>";
				$select_output[3] = "<?php
				echo JText::_ ( 'OUTPUT MONTH' );
				?>";
				$select_output[4] = "<?php
				echo JText::_ ( 'OUTPUT WEEKDAY' );
				?>";

				var $weekday = new Array();
				$weekday[0] = new Array("MO", "<?php	echo JText::_ ( 'MONDAY' );	?>");
				$weekday[1] = new Array("TU", "<?php  echo JText::_ ( 'TUESDAY' ); ?>");
				$weekday[2] = new Array("WE", "<?php  echo JText::_ ( 'WEDNESDAY' ); ?>");
				$weekday[3] = new Array("TH", "<?php  echo JText::_ ( 'THURSDAY' ); ?>");
				$weekday[4] = new Array("FR", "<?php  echo JText::_ ( 'FRIDAY' ); ?>");
				$weekday[5] = new Array("SA", "<?php  echo JText::_ ( 'SATURDAY' ); ?>");
				$weekday[6] = new Array("SU", "<?php  echo JText::_ ( 'SUNDAY' ); ?>");

				var $before_last = "<?php
				echo JText::_ ( 'BEFORE LAST' );
				?>";
				var $last = "<?php
				echo JText::_ ( 'LAST' );
				?>";
			-->
			</script>
			
			<?php
			$title = JText::_ ( 'METADATA INFORMATION' );
			echo $this->pane->endPanel ();
			echo $this->pane->startPanel ( $title, 'meta' );
			?>
			<table>
			<tr>
				<td>
					<input class="inputbox" type="button" onclick="insert_keyword('[title]')" value="<?php echo JText::_ ( 'EVENT TITLE' );	?>" />
					<input class="inputbox" type="button" onclick="insert_keyword('[a_name]')" value="<?php	echo JText::_ ( 'VENUE' );?>" />
					<input class="inputbox" type="button" onclick="insert_keyword('[categories]')" value="<?php	echo JText::_ ( 'CATEGORIES' );?>" />
					<input class="inputbox" type="button" onclick="insert_keyword('[dates]')" value="<?php echo JText::_ ( 'DATE' );?>" />
				
					<p>
						<input class="inputbox" type="button" onclick="insert_keyword('[times]')" value="<?php echo JText::_ ( 'EVENT TIME' );?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[enddates]')" value="<?php echo JText::_ ( 'ENDDATE' );?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[endtimes]')" value="<?php echo JText::_ ( 'END TIME' );?>" />
					</p>
					<br />
					<label for="meta_keywords">
						<?php echo JText::_ ( 'META KEYWORDS' ) . ':';?>
					</label>
					<br />
						<?php
						if (! empty ( $this->row->meta_keywords )) {
							$meta_keywords = $this->row->meta_keywords;
						} else {
							$meta_keywords = $this->elsettings->meta_keywords;
						}
						?>
					<textarea class="inputbox" name="meta_keywords" id="meta_keywords" rows="5" cols="40" maxlength="150" onfocus="get_inputbox('meta_keywords')" onblur="change_metatags()"><?php echo $meta_keywords; ?></textarea>
				</td>
			<tr>
			<tr>
				<td>
					<label for="meta_description">
						<?php echo JText::_ ( 'META DESCRIPTION' ) . ':';?>
					</label>
					<br />
					<?php
					if (! empty ( $this->row->meta_description )) {
						$meta_description = $this->row->meta_description;
					} else {
						$meta_description = $this->elsettings->meta_description;
					}
					?>
					<textarea class="inputbox" name="meta_description" id="meta_description" rows="5" cols="40" maxlength="200"	onfocus="get_inputbox('meta_description')" onblur="change_metatags()"><?php echo $meta_description;?></textarea>
				</td>
			</tr>
				<!-- include the metatags end-->
		</table>
		<script type="text/javascript">
		<!--
			starter("<?php
			echo JText::_ ( 'META ERROR' );
			?>");	// window.onload is already in use, call the function manualy instead
		-->
		</script>
		<?php
		echo $this->pane->endPanel ();
		echo $this->pane->endPane ();
		?>
		</td>
	</tr>
</table>

<?php
echo JHTML::_ ( 'form.token' );
?>
<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="controller" value="events" />
<input type="hidden" name="view" value="event" />
<input type="hidden" name="task" value="" />
<?php if ($this->task == 'copy') {?>
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="created" value="" />
	<input type="hidden" name="author_ip" value="" />
	<input type="hidden" name="created_by" value="" />
	<input type="hidden" name="version" value="" />
	<input type="hidden" name="hits" value="" />
<?php } else {	?>
	<input type="hidden" name="id" value="<?php	echo $this->row->id;?>" />
	<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
	<input type="hidden" name="author_ip" value="<?php echo $this->row->author_ip;?>" />
	<input type="hidden" name="created_by" value="<?php	echo $this->row->created_by;?>" />
	<input type="hidden" name="version" value="<?php echo $this->row->version;?>" />
	<input type="hidden" name="hits" value="<?php echo $this->row->hits; ?>" />
<?php } ?>
</form>

<p class="copyright">
	<?php echo ELAdmin::footer (); ?>
</p>

<?php
//keep session alive while editing
JHTML::_ ( 'behavior.keepalive' );
?>