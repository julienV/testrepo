<script language="javascript" type="text/javascript">
	function submitbutton(task)
	{

		var form = document.adminForm;

		if (task == 'datimupview')
		{
			var url='index.php?option=com_eventlist&task=datimupview&tmpl=component';
			document.popup.show(url, 700, 500, null);

		}
		else if (task == 'cancel') {
			submitform( task );
			return;
		}
		else if (form.dates.value == ""){
			alert( "<?php echo JText::_( 'ADD DATE'); ?>" );
		}
		else if (!form.dates.value.match(/20[0-9]{2}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_( 'DATE WRONG'); ?>");
			
			<?php
			if ( $this->elsettings->showtime == 1 ) {
				?>
			} else if (form.times.value == "") {
				alert("<?php echo JText::_( 'ADD TIME'); ?>");
			} else if (!form.times.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
				alert("<?php echo JText::_( 'TIME WRONG'); ?>");
			<?php } ?>
			
		} else if (form.catsid.value == "0"){
			alert( "<?php echo JText::_( 'CHOOSE CATEGORY'); ?>" );
		} else if (form.locid.value == ""){
			alert( "<?php echo JText::_( 'CHOOSE VENUE'); ?>" );
		} else {
			<?php
			echo $this->editor->save( 'datdescription' );
			?>
			submitform( task );
		}
	}
		</script>
		
		<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist">
			<tr>
		  		<td><img src="<?php echo $this->live_site."/administrator/components/com_eventlist/assets/images/evlogo.png"; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>  
		  		<td class="sectionname" align="right" width="100%">
		  			<font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">
		  			<?php 
		  			if ($this->task == 'copy') {
		  				echo '::'.JText::_( 'COPY EVENT').'::';
		  			} else {
		  				echo $this->row->id ? '::'.JText::_( 'EDIT EVENT').'::' : '::'.JText::_( 'ADD EVENT').'::';
		  			}
		  			?>
		  			</font>
		  		</td>
			</tr>
		</table>
		
<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
	<tr>
		<td valign="top">
			<table class="adminform">
				<tr>
					<td>
						<label for="title">
							<?php echo JText::_( 'EVENT TITLE' ).':'; ?>
						</label>
					</td>
					<td>
						<input name="title" value="<?php echo $this->row->title; ?>" size="50" maxlength="60" id="title">
					</td>
					<td>
						<label for="published">
							<?php echo JText::_( 'PUBLISHED' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						$html = JHTMLSelect::yesnoList( 'published', 'class="inputbox"', $this->row->published );
						echo $html;
						?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="clubid">
							<?php echo JText::_( 'VENUE' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo $this->venueselect.$this->venueadd;
						?>
						&nbsp;<input class="inputbox" type="button" onclick="elSelectVenue(0, '<?php echo JText::_('NO VENUE'); ?>' );" value="<?php echo JText::_('NO VENUE'); ?>" onblur="seo_switch()" />
					</td>
					<td>
						<label for="catid">
							<?php echo JText::_( 'CATEGORY' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo $this->Lists['category']
						?>
					</td>
				</tr>
			</table>
			
			<table class="adminform">
				<tr>
					<td>
						<?php
						// parameters : areaname, content, hidden field, width, height, rows, cols
						echo $this->editor->display( 'datdescription',  $this->row->datdescription, '100%;', '550', '75', '20' ) ;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" width="320px" style="padding: 7px 0 0 5px">
			<?php
			$title = JText::_( 'DETAILS' );
			echo $this->pane->startPane("det-pane");
			echo $this->pane->startPanel( $title, 'date' );

			//Set the info image
			$infoimage = JAdminMenus::ImageCheck( 'icon-16-hint.png', '../components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );
			?>
			<table>
				<tr>
					<td>
						<label for="dates">
							<?php echo JText::_( 'DATE' ).':'; ?>
						</label>
					</td>
					<td>
						<?php if ($this->row->dates == '0000-00-00') {
						$this->row->dates ='';
						}
						?>
						<input class="inputbox" type="text" name="dates" id="dates" size="15" maxlength="10" value="<?php echo $this->row->dates; ?>" /> 
            			<input type="reset" class="button" value="..." onclick="return showCalendar('dates', 'Y-m-d');" onblur="seo_switch()" />
           			</td>
            		<td>
            			<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('FORMAT DATE'); ?>">
							<?php echo $infoimage; ?>
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<label for="enddates">
						<?php echo JText::_( 'ENDDATE' ).':'; ?>
						</label>
					</td>
					<td>
						<?php if ($this->row->enddates == '0000-00-00') {
							$this->row->enddates ='';
						}
						?>
						<input class="inputbox" type="text" name="enddates" id="enddates" size="15" maxlength="10" value="<?php echo $this->row->enddates; ?>" /> 
            			<input type="reset" class="button" value="..." onclick="return showCalendar('enddates', 'Y-m-d');" onblur="seo_switch()" />
           			</td>
          		 	<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('FORMAT DATE'); ?>">
							<?php echo $infoimage; ?>
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<label for="times">
							<?php echo JText::_( 'EVENT TIME' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						if ($this->row->times == '00:00:00') {
							$this->row->times = '';
						} else {
							$this->row->times = substr($this->row->times, 0, 5);
						}
						?>
						<input name="times" value="<?php echo $this->row->times; ?>" size="15" maxlength="8" id="times">
					</td>
					<td>
			  			<?php if ( $this->elsettings->showtime == 1 ) { ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('FORMAT TIME'); ?>">
								<?php echo $infoimage; ?>
							</span>			  	
			  			<?php } else { ?>
			  				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('FORMAT TIME OPTIONAL'); ?>">
								<?php echo $infoimage; ?>
							</span>	
			  			<?php } ?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="endtimes">
							<?php echo JText::_( 'END TIME' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						if ($this->row->endtimes == '00:00:00') {
							$this->row->endtimes = '';
						} else {
							$this->row->endtimes = substr($this->row->endtimes, 0, 5);
						}
						?>
						<input name="endtimes" value="<?php echo $this->row->endtimes; ?>" size="15" maxlength="8" id="endtimes">
					</td>
					<td>
			  			<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('FORMAT TIME OPTIONAL'); ?>">
							<?php echo $infoimage; ?>
						</span>	
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_( 'REGISTRATION' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'registra' );
			?>
			<table>
				<tr>
					<td>
						<label for="registra">
							<?php echo JText::_( 'ENABLE REGISTRATION' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						$html = JHTMLSelect::yesnoList( 'registra', 'class="inputbox"', $this->row->registra );
						echo $html;
						?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="unregistra">
							<?php echo JText::_( 'ENABLE UNREGISTRATION' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						$html = JHTMLSelect::yesnoList( 'unregistra', 'class="inputbox"', $this->row->unregistra );
						echo $html;
						?>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_( 'IMAGE' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'image' );
			?>
			<table>
				<tr>
					<td>
						<label for="image">
							<?php echo JText::_( 'SELECTIMAGE' ).':'; ?>
						</label>
					</td>
					<td>
						<?php echo $this->imageselect; ?>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<script language="javascript" type="text/javascript">
						if (document.forms[0].a_imagename.value!=''){
							var imname = document.forms[0].a_imagename.value;
							jsimg='../images/eventlist/events/' + imname;
						} else {
							jsimg='../images/M_images/blank.png';
						}
						document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
						</script>
						<br />
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_( 'Metadata Information' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'meta' );
			?>
			<table>
				<tr>
					<td>
						<input class="inputbox" type="button" onclick="insert_keyword('[title]')" value="<?php echo JText::_( 'EVENT TITLE' ); ?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[a_name]')" value="<?php echo JText::_( 'VENUE' ); ?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[catsid]')" value="<?php echo JText::_( 'CATEGORY' ); ?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[dates]')" value="<?php echo JText::_( 'DATE' ); ?>" />
						<p><input class="inputbox" type="button" onclick="insert_keyword('[times]')" value="<?php echo JText::_( 'EVENT TIME' ); ?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[enddates]')" value="<?php echo JText::_( 'ENDDATE' ); ?>" />
						<input class="inputbox" type="button" onclick="insert_keyword('[endtimes]')" value="<?php echo JText::_( 'END TIME' ); ?>" /></p>
						<br/>
						<label for="meta_keywords">
							<?php echo JText::_( 'META KEYWORDS' ).':'; ?>
						</label>
						<br />
				
						<?php
						if (!empty($this->row->meta_keywords)) {
							$meta_keywords = $this->row->meta_keywords;
						}
						?>
			
						<textarea class="inputbox" name="meta_keywords" id="meta_keywords" rows="5" cols="40" maxlength="150" onfocus="get_inputbox('meta_keywords')" onblur="change_metatags()"><?php echo $meta_keywords; ?></textarea>
				</td>
			<tr>
			<tr>
				<td>
					<label for="meta_description">
						<?php echo JText::_( 'META DESCRIPTION' ).':'; ?>
					</label>
					<br />
					<?php 
					if (!empty($this->row->meta_description)) {
						$meta_description = $this->row->meta_description;
					}
					?>
				
					<textarea class="inputbox" name="meta_description" id="meta_description" rows="5" cols="40" maxlength="200" onfocus="get_inputbox('meta_description')" onblur="change_metatags()"><?php echo $meta_description; ?></textarea>
				</td>
			<tr>
			<!-- include the metatags end-->
		</table>
		<script type="text/javascript">
		<!--
			starter("<?php echo JText::_( 'META ERROR' ); ?>");	// da window.onload schon belegt wurde, wird die Funktion 'manuell' aufgerufen
		-->
		</script>
		<?php 
		echo $this->pane->endPanel();
		echo $this->pane->endPane(); ?>
		</td>
	</tr>
</table>

<?php echo ELAdmin::footer( ); ?>

<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="controller" value="events" />
<?php if ($this->task == 'copy') { ?>
	<input type="hidden" name="id" value="" />
<?php } else { ?>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<?php } ?>
<input type="hidden" name="task" value="" />
</form>