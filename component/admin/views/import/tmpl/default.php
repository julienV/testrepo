<?php
/**
 * @version 1.0 $Id$
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

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
  <fieldset>
    <legend><?php echo JText::_('IMPORT EVENTS'); ?></legend>
	    <?php echo JText::_( 'IMPORT INSTRUCTIONS' ) ?>
			<ul>
			<li>
			<?php echo JText::_( "IMPORT COLUMNNAMESEVENTS" ); ?>
			</li>
			<li>
			<?php echo JText::_( "IMPORT FIRSTROW" ); ?>
			</li>
			<li>
			<?php echo JText::_( "IMPORT CSVFORMAT" ); ?>
			</li>
      <li>
      <?php echo JText::_( "IMPORT CATEGORIES DESC" ); ?>
      </li>
			<li>
			<?php echo JText::_( "IMPORT POSSIBLECOLUMNS" ) . 'categories, ' . implode(", ",$this->eventfields); ?>
			</li>
			</ul>
      <table>
        <tr>
          <td>    
            <label for="file">
                <?php echo JText::_( 'IMPORT SELECTCSV' ).':'; ?>
            </label>
          </td>
          <td>
			      <input type="file" id="event-file-upload" accept="text/*" name="Fileevents" />
			      <input type="submit" id="event-file-upload-submit" value="<?php echo JText::_('IMPORT START'); ?>" onclick="document.getElementsByName('task')[0].value='csveventimport';return true;"/>
			      <span id="upload-clear"></span>
          </td>
        </tr>
        <tr>
          <td>    
            <label for="replace_events">
                <?php echo JText::_( 'IMPORT REPLACEIFEXISTS' ).':'; ?>
            </label>
          </td>
          <td>
            <?php
            $html = JHTML::_('select.booleanlist', 'replace_events', 'class="inputbox"', 0 );
            echo $html;
            ?>      
          </td>
        </tr>
      </table>
  </fieldset>
  
  <fieldset>
    <legend><?php echo JText::_('IMPORT CATEGORIES'); ?></legend>
      <?php echo JText::_( 'IMPORT INSTRUCTIONS' ) ?>
      <ul>
      <li>
      <?php echo JText::_( "IMPORT COLUMNNAMESCATEGORIES" ); ?>
      </li>
      <li>
      <?php echo JText::_( "IMPORT FIRSTROW" ); ?>
      </li>
      <li>
      <?php echo JText::_( "IMPORT CSVFORMAT" ); ?>
      </li>
      <li>
      <?php echo JText::_( "IMPORT POSSIBLECOLUMNS" ).implode(", ",$this->catfields); ?>
      </li>
      </ul>
      <table>
        <tr>
          <td>    
            <label for="file">
                <?php echo JText::_( 'IMPORT SELECTCSV' ).':'; ?>
            </label>
          </td>
          <td>
            <input type="file" id="cat-file-upload" accept="text/*" name="Filecats" />
            <input type="submit" id="cat-file-upload-submit" value="<?php echo JText::_('IMPORT START'); ?>" onclick="document.getElementsByName('task')[0].value='csvcategoriesimport';return true;"/>
            <span id="upload-clear"></span>
          </td>
        </tr>
        <tr>
          <td>    
            <label for="replace_cats">
                <?php echo JText::_( 'IMPORT REPLACEIFEXISTS' ).':'; ?>
            </label>
          </td>
          <td>
            <?php
            $html = JHTML::_('select.booleanlist', 'replace_cats', 'class="inputbox"', 0 );
            echo $html;
            ?>      
          </td>
        </tr>
      </table>
  </fieldset>
	<input type="hidden" name="option" value="com_eventlist" />
	<input type="hidden" name="view" value="import" />
	<input type="hidden" name="controller" value="import" />
	<input type="hidden" name="task" value="" />
</form>

<p class="copyright">
  <?php echo ELAdmin::footer( ); ?>
</p>