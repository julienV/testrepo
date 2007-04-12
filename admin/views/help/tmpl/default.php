<?php defined('_JEXEC') or die('Restricted access'); ?>

<table class="adminlist" width="100%">
	<tr>
		<td><img src="<?php echo $this->live_site."/administrator/components/com_eventlist/assets/images/evlogo.png"; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>  
		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'HELP' ); ?>::</font></td>
	</tr>
</table>

<br />
		  
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td align="left">
			<?php // echo JText::_( 'Search' ); ?>
			<input class="text_area" type="hidden" name="option" value="com_eventlist" />
			<input type="text" name="search" id="search" value="<?php echo $this->helpsearch;?>" class="inputbox" />
			<input type="submit" value="<?php echo JText::_( 'Go' ); ?>" class="button" />
			<input type="button" value="<?php echo JText::_( 'Reset' ); ?>" class="button" onclick="f=document.adminForm;f.search.value='';f.submit()" />
		</td>
		<td style="text-align:right" width="70%">
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/el.intro.html'; ?>" target='helpFrame'><?php echo JText::_( 'HOME' ); ?></a>
			|
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/helpsite/el.gethelp.html'; ?>" target='helpFrame'><?php echo JText::_( 'GET HELP' ); ?></a>
			|
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/helpsite/el.givehelp.html'; ?>" target='helpFrame'><?php echo JText::_( 'GIVE HELP' ); ?></a>
			|
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/helpsite/el.credits.html'; ?>" target='helpFrame'><?php echo JText::_( 'CREDITS' ); ?></a>
			|
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/helpsite/el.changelog.html'; ?>" target='helpFrame'><?php echo JText::_( 'CHANGELOG' ); ?></a>
			|
			<a href="<?php echo $this->live_site.'/administrator/components/com_eventlist/assets/gpl.txt'; ?>" target='helpFrame'><?php echo JText::_( 'LICENCE' ); ?></a>
		</td>
	</tr>
	<tr valign="top">
		<td align="left">
				
			<?php
			echo $this->pane->startPane("det-pane");
			$title = JText::_( 'SCREEN HELP' );
			echo $this->pane->startPanel( $title, 'registra' );
			?>
			<table class="adminlist">
				<?php
				foreach ($this->toc as $k=>$v) {
					echo '<tr>';
					echo '<td>';
					echo JHTML::Link($this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/'.$k, $v, array('target' => "'helpFrame'"));
					echo '</td>';
					echo '</tr>';
				}
				?>
			</table>
				
			<?php
			echo $this->pane->endPanel();
			echo $this->pane->endPane();
		  	?>
		</td>  
		<td>
			<iframe name="helpFrame" src="<?php echo $this->live_site.'/administrator/components/com_eventlist/help/'.$this->langTag.'/el.intro.html'; ?>" class="helpFrame" frameborder="0"></iframe>
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
</form> 

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>