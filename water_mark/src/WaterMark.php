<?php

namespace Drupal\water_mark;

use Drupal;
use Drupal\file\Entity\File;

/**
 * Defines Watermark Class.
 *
 * @ImageToolkitOperation(
 *   id = "gd_add_watermark",
 *   toolkit = "gd",
 *   operation = "add_watermark",
 *   label = @Translation("Add Watermark"),
 *   description = @Translation("Adds a watermark to the image.")
 * )
 */
class WaterMark {

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->user = Drupal::currentUser();
    $this->config = Drupal::config('water_mark.watermarksettings');
  }

  /**
   * Create a water marked image.
   *
   * @param string $imgUri
   *   Image internal uri.
   *
   * @return string
   *   Returns uri of water marked image.
   */
  public function createfromUri($imgUri) {
    $user_id = $this->user->id();

    // Setting conf values. Anonymous get admin configured values.
    $this->values = ($user_id == 0) ? $this->config->get(1) : $this->config->get($user_id);

    // Setting default values as Admin conf values.
    if (!$this->values) {
      $this->values = $this->config->get(1);
    }

    $destination = $this->getNewDestination($imgUri);
    $image = Drupal::service('image.factory')->get($imgUri);
    if ($this->values['watermark_type'] == 'image') {
      $status = $this->applyWatermark($image, $destination);
    }
    elseif ($this->values['watermark_type'] == 'text') {
      $status = $this->textOverlay($image, $destination);
    }
    return ($status) ? $destination : FALSE;
  }

  /**
   * Apply watermark on image.
   */
  private function applyWatermark($image, $destination) {
    $values = $this->values;
    $mime = mime_content_type($image->getSource());
    $marge_right = $values['watermark_image']['margin_right'];
    $marge_bottom = $values['watermark_image']['margin_bottom'];
    switch ($mime) {
      case 'image/png':
        header('Content-type: image/png');
        $stamp = $this->getWaterMark($values['watermark_image']['image'][0]);
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);
        $im = imagecreatefrompng($image->getSource());
        $resource = imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
        $st = imagepng($im, $destination);
        break;

      case 'image/jpeg':
        header('Content-type: image/jpeg');
        $stamp = $this->getWaterMark($values['watermark_image'][0]);
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);
        $im = imagecreatefromjpeg($image->getSource());
        $resource = imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
        $st = imagejpeg($im, $destination);
        break;

      default:
        return FALSE;
    }

    imagedestroy($im);
    imagedestroy($stamp);
    return $st;
  }

  /**
   * Get watermark image.
   */
  private function getWaterMark($fid) {
    $image = File::load($fid);
    $uri = $image->getFileUri();
    $mime = $image->getMimeType();
    $created = $image->get('created')->value;
    switch ($mime) {
      case 'image/png':
        $image = imagecreatefrompng($uri);
        break;

      case 'image/jpeg':
        $image = imagecreatefromjpeg($uri);
        break;

      default:
        return FALSE;
    }
    return $image;
  }

  /**
   * Get watermarked image destination.
   */
  private function getNewDestination($imgUri) {
    $user_id = $this->user->id();
    $name = basename($imgUri);
    if (!file_exists(str_replace($name, "WATERMARKED/$user_id/", $imgUri))) {
      mkdir(str_replace($name, "WATERMARKED/$user_id/", $imgUri), 0777, TRUE);
    }
    $destination = str_replace($name, "WATERMARKED/$user_id/$name", $imgUri);
    return $destination;
  }

  /**
   * Add text overlay to image.
   */
  private function textOverlay($image, $destination) {
    $values = $this->values;
    $text = $values['watermark_text']['text'];
    $fontsize = $values['watermark_text']['fontsize'];
    $angle = $values['watermark_text']['angle'];
    $x = $values['watermark_text']['x'];
    $y = $values['watermark_text']['y'];
    $mime = mime_content_type($image->getSource());
    $font_path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'water_mark') . '/fonts/watermarkfont.ttf';
    switch ($mime) {
      case 'image/jpeg':
        header('Content-type: image/jpeg');
        $im = imagecreatefromjpeg($image->getSource());
        $white = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, $fontsize, $angle, $x, $y, $white, $font_path, $text);
        imagejpeg($im, $destination);
        $im = imagecreatefrompng($image->getSource());
        break;

      case 'image/png':
        header('Content-type: image/png');
        $im = imagecreatefrompng($image->getSource());
        $white = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, $fontsize, $angle, $x, $y, $white, $font_path, $text);
        imagepng($im, $destination);
        break;

      default:
        return FALSE;
    }

    // Clear Memory.
    imagedestroy($im);
    return TRUE;
  }

}
