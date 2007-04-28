<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Holds the logic for image manipulation
 *
 * @package Joomla
 * @subpackage EventList
 */
class ELImage {

	/**
	* Creates a Thumbnail of an image
	*
 	* @author Christoph Lukes
	* @since 0.9
 	*
 	* @param string $file The path to the file
	* @param string $save The targetpath
	* @param string $width The with of the image
	* @param string $height The height of the image
	* @return true when success
	*/
	function thumb($file, $save, $width, $height)
	{
		//GD-Lib > 2.0 only!
		@unlink($save);
		$infos = @getimagesize($file);

		// keep proportions
		$iWidth = $infos[0];
		$iHeight = $infos[1];
		$iRatioW = $width / $iWidth;
		$iRatioH = $height / $iHeight;

		if ($iRatioW < $iRatioH) {
			$iNewW = $iWidth * $iRatioW;
			$iNewH = $iHeight * $iRatioW;
		} else {
			$iNewW = $iWidth * $iRatioH;
			$iNewH = $iHeight * $iRatioH;
		}

		//Don't resize images which are smaller than thumbs
		if ($infos[0] < $width && $infos[1] < $height) {
			$iNewW = $infos[0];
			$iNewH = $infos[1];
		}

		if($infos[2] == 1) {
			/*
			* Image is typ gif
			*/
			$imgA = imagecreatefromgif($file);
			$imgB = imagecreatetruecolor($iNewW,$iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagegif($imgB, $save);

		} elseif($infos[2] == 2) {
			/*
			* Image is typ jpg
			*/
			$imgA = imagecreatefromjpeg($file);
			$imgB = imagecreatetruecolor($iNewW,$iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagejpeg($imgB, $save);

		} elseif($infos[2] == 3) {
			/*
			* Image is typ png
			*/
			$imgA = imagecreatefrompng($file);
			$imgB = imagecreatetruecolor($iNewW, $iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagepng($imgB, $save);
		} else {
			return false;
		}
		return true;
	}

	/**
	* Determine the GD version
	* Code from php.net
	*
	* @since 0.9
	* @param int
	*
	* @return int
	*/
	function gdVersion($user_ver = 0)
	{
		if (! extension_loaded('gd')) {
			return;
		}
		static $gd_ver = 0;

		// Just accept the specified setting if it's 1.
		if ($user_ver == 1) {
			$gd_ver = 1;
			return 1;
		}
		// Use the static variable if function was called previously.
		if ($user_ver !=2 && $gd_ver > 0 ) {
			return $gd_ver;
		}
		// Use the gd_info() function if possible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		// If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $match[0];
	}

	/**
	* Creates image information of an image
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param string $image The image name
	* @param array $settings
	* @param string $type event or venue
	*
	* @return imagedata if available
	*/
	function flyercreator($image, $settings, $type= 'venue')
	{
		//define the environment based on the type
		if ($type == 'event') {
			$folder		= 'events';
		} else {
			$folder 	= 'venues';
		}

		if ( $image ) {

			//Create thumbnail if enabled and it does not exist already
			if ($settings->gddisabled == 1 && !file_exists(JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image)) {

				$filepath 	= JPATH_SITE.'/images/eventlist/'.$folder.'/'.$image;
				$save 		= JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image;

				ELImage::thumb($filepath, $save, $settings->imagewidth, $settings->imagehight);
			}

			//set paths
			$dimage['original'] = 'images/eventlist/'.$folder.'/'.$image;
			$dimage['thumb'] 	= 'images/eventlist/'.$folder.'/small/'.$image;

			//get imagesize of the original
			$iminfo = @getimagesize('images/eventlist/'.$folder.'/'.$image);

			//if the width or height is too large this formula will resize them accordingly
			if (($iminfo[0] > $settings->imagewidth) || ($iminfo[1] > $settings->imagehight)) {

				$iRatioW = $settings->imagewidth / $iminfo[0];
				$iRatioH = $settings->imagehight / $iminfo[1];

				if ($iRatioW < $iRatioH) {
					$dimage['width'] 	= round($iminfo[0] * $iRatioW);
					$dimage['height'] 	= round($iminfo[1] * $iRatioW);
				} else {
					$dimage['width'] 	= round($iminfo[0] * $iRatioH);
					$dimage['height'] 	= round($iminfo[1] * $iRatioH);
				}

			} else {

				$dimage['width'] 	= $iminfo[0];
				$dimage['height'] 	= $iminfo[1];

			}

			if (file_exists(JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image)) {

				//get imagesize of the thumbnail
				$thumbiminfo = @getimagesize('images/eventlist/'.$folder.'/small/'.$image);
				$dimage['thumbwidth'] 	= $thumbiminfo[0];
				$dimage['thumbheight'] 	= $thumbiminfo[1];

			}
			return $dimage;
		}
		return false;
	}
}
?>