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
   * Display image in correct format.
   */
  private function process() {
    $fileName = $this->fullPath;
    $this->image = $this->openImage($fileName);
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