<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	  	<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td><img src="<?php echo $this->live_site.'/administrator/components/com_eventlist/assets/images/evlogo.png'; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>  
				<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'SETTINGS' ); ?>::</font></td>
			</tr>
 	 	</table>
 	 	<br />

    	<div id="elconfig-document">
			<div id="page-basic">
				<?php require_once(dirname(__FILE__).DS.'el.settings_basic.html'); ?>
			</div>

			<div id="page-usercontrol">
				<?php require_once(dirname(__FILE__).DS.'el.settings_usercontrol.html'); ?>
			</div>

			<div id="page-details">
				<?php require_once(dirname(__FILE__).DS.'el.settings_detailspage.html'); ?>
			</div>

			<div id="page-layout">
				<?php require_once(dirname(__FILE__).DS.'el.settings_layout.html'); ?>
			</div>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="id" value="1">
		<input type="hidden" name="lastupdate" value="<?php echo $this->elsettings->lastupdate; ?>">
		<input type="hidden" name="option" value="com_eventlist">
		</form>
		
		<p class="copyright">
			<?php echo ELAdmin::footer( ); ?>
		</p>