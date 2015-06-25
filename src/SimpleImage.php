<?php

namespace Hiperbola\SimpleImage;

/**
 * Class SimpleImage
 *
 * Handles resizing of image and some other useful options that come in handy when creating slide shows and various
 * thumbnails, so resizing does not have to be done manually.
 *
 * @author  Pavlin Policar
 * @package Hiperbola\SimpleImage
 */
class SimpleImage {

  /**
   * Absolute path to image that is to be displayed
   *
   * @var string
   */
  private $basedir;

  /**
   * Image file name
   *
   * @var string
   */
  private $fileName;

  /**
   * Full absolute image path
   *
   * @var string
   */
  private $fullPath;

  /**
   * Image extension
   *
   * @var string
   */
  private $extension;

  /**
   * Various options for image
   *
   * @var array
   */
  private $options;

  /**
   * Image stored in memory
   *
   * @var resource
   */
  private $image;

  private $newImage;

  /**
   * Create new simple image instance with options, execution in constructor.
   *
   * @param string $basedir
   * @param string $fileName
   * @param array  $options
   */
  function __construct($basedir, $fileName, $options = []) {
    $this->basedir = $basedir;
    $this->fileName = $fileName;
    $this->fullPath = $this->basedir . '/' . $this->fileName;
    $this->extension = strtolower(substr($fileName, strpos($fileName, '.') + 1));
    $this->options = $options;

    $this->process();
  }

  /**
   * Clean up any images still in memory to avoid any short term memory leaks
   */
  public function __destruct() {
    imagedestroy($this->image);
    imagedestroy($this->newImage);
  }

  /**
   * Display image in correct format.
   */
  private function process() {
    echo '<pre>';
    $fileName = $this->fullPath;
    $this->image = $this->openImage($fileName);
    $this->resizeImage($this->image);
    $this->dumpImage($this->image);
  }

  /**
   * Open existing image from disk.
   *
   * @param $filename
   * @return resource
   */
  private function openImage($filename) {
    switch ($this->extension) {
      case 'jpg':
      case 'jpeg':
        $img = @imagecreatefromjpeg($filename);
        break;
      case 'png':
        $img = @imagecreatefrompng($filename);
        break;
      case 'gif':
        $img = @imagecreatefromgif($filename);
        break;
    }
    return $img;
  }

  private function resizeImage($image) {
    list($width, $height) = $this->getDimensions($image);
    list($newWidth, $newHeight) = $this->getSizeByFixedWidth($width, $height, 800);

    $this->newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $this->image = $this->newImage;
    $this->newImage = null;

    imagejpeg($this->image, "huehue.jpg");
  }

  /**
   * Get size of new image if height is to remain the same and aspect ratio is to be kept.
   *
   * @param $width
   * @param $height
   * @param $newHeight
   * @return array
   */
  private function getSizeByFixedHeight($width, $height, $newHeight) {
    $ratio = $width / $height;
    $newWidth = $newHeight * $ratio;
    return [$newWidth, $newHeight];
  }

  /**
   * Get size of new image if width is to remain the same and aspect ratio is to be kept.
   *
   * @param $width
   * @param $height
   * @param $newWidth
   * @return array
   */
  private function getSizeByFixedWidth($width, $height, $newWidth) {
    $ratio = $height / $width;
    $newHeight = $newWidth * $ratio;
    return [$newWidth, $newHeight];
  }

  /**
   * Get image dimensions
   *
   * @return array
   */
  private function getDimensions() {
    $info = getimagesize($this->fullPath);
    return [
      $info[0],
      $info[1]
    ];
  }

  /**
   * Return image to browser to display.
   *
   * @param resource $img
   */
  private function dumpImage($img) {
    switch ($this->extension) {
      case 'jpg':
      case 'jpeg':
        header('Content-Type: image/jpeg');
        imagejpeg($img);
        break;
      case 'png':
        header('Content-Type: image/png');
        imagepng($img);
        break;
      case 'gif':
        header('Content-Type: image/gif');
        imagegif($img);
        break;
    }
  }
}