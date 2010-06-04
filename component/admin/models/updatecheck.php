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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Updatecheck Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelUpdatecheck extends JModel
{
	/**
	 * Updatedata in array
	 *
	 * @var array
	 */
	var $check = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Fetch the version from the schlu.net server
	 * TODO: Cleanup
	 */
	 function getUpdate()
	 {
	 	$url = 'http://update.schlu.net/eventlist_update.xml';
		$data = '';
		$check = array();
		$check['connect'] = 0;
		$check['version_current'] = '1.1';
		$check['versionread_current'] = '1.1';

		//try to connect via cURL
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = @curl_init();
			
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			//http code is greater than or equal to 300 ->fail
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//timeout of 5s just in case
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
						
			$data = @curl_exec($ch);
						
			@curl_close($ch);
		}

		//try to connect via fsockopen
		if(function_exists('fsockopen') && $data == '') {

			$errno = 0;
			$errstr = '';

			//timeout handling: 5s for the socket and 5s for the stream = 10s
			$fsock = @fsockopen("update.schlu.net", 80, $errno, $errstr, 5);
		
			if ($fsock) {
				@fputs($fsock, "GET /quickfaq_update.xml HTTP/1.1\r\n");
				@fputs($fsock, "HOST: update.schlu.net\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");
        
				//force stream timeout...bah so dirty
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				 
				$get_info = false;
				while (!@feof($fsock))
				{
					if ($get_info)
					{
						$data .= @fread($fsock, 1024);
					}
					else
					{
						if (@fgets($fsock, 1024) == "\r\n")
						{
							$get_info = true;
						}
					}
				}        	
				@fclose($fsock);
				
				//need to chack data cause http error codes aren't supported here
				if(!strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>')) {
					$data = '';
				}
			}
		}

	 	//try to connect via fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {
		
			//set socket timeout
			ini_set('default_socket_timeout', 5);
			
			$handle = @fopen ($url, 'r');
			
			//set stream timeout
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			
			$data	= @fread($handle, 1000);
			
			@fclose($handle);
		}
						
		/* try to connect via file_get_contents..k..a bit stupid
		if(function_exists('file_get_contents') && ini_get('allow_url_fopen') && $data == '') {
			$data = @file_get_contents($url);
		}
		*/
		
		if( $data && strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>') ) {
			$xml = & JFactory::getXMLparser('Simple');
			$xml->loadString($data);
			
			$version 				= & $xml->document->version[0];
			$check['version'] 		= & $version->data();
			$versionread 			= & $xml->document->versionread[0];
			$check['versionread'] 	= & $versionread->data();
			$released 				= & $xml->document->released[0];
			$check['released'] 		= & $released->data();
			$info 					= & $xml->document->info[0];
			$check['info'] 			= & $info->data();
			$download 				= & $xml->document->download[0];
			$check['download']		= & $download->data();
			$notes					= & $xml->document->notes[0];
			$check['notes']			= & $notes->data();
			$changes				= & $xml->document->changes[0];
			$check['changes']		= explode(';', $changes->data());
			
			$check['connect'] 		= 1;
		//	$check['enabled'] 		= 1;
			
			$check['current'] 		= version_compare( $check['version_current'], $check['version'] );
		}
		
		return $check;
	 }
}
?>