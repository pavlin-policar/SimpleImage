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
   * Image map class to act as a cache.
   *
   * @var ImageMap
   */
  private $imageMap = null;

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
   * Default options for image processing
   *
   * @var array
   */
  private $options = [
      'height' => 0,
      'width'  => 0
  ];

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
   * @param        $optionString
   * @internal param string $options
   */
  public function __construct($basedir, $fileName, $optionString) {
    $this->imageMap = new ImageMap();
    $this->basedir = $basedir;
    $this->fileName = $fileName;
    $this->fullPath = $this->basedir . '/' . $this->fileName;
    $this->extension = strtolower(substr($fileName, strpos($fileName, '.') + 1));

    $this->options = array_merge($this->options, $this->parseOptions($optionString));
    $this->processRequest();
  }

  /**
   * Clean up any images still in memory to avoid any short term memory leaks.
   */
  public function __destruct() {
    @imagedestroy($this->image);
  }

  /**
   * Display image in correct format.
   */
  private function processRequest() {
    $imageData = $this->findImageData();

    // miss in cache
    if (empty($imageData)) {
      $this->storeImage($this->options);
    }

    $imageData = $this->findImageData();
    if (empty($imageData)) {
      throw new \RuntimeException('Was not able to store cache entry for image.');
    }

    $this->image = $this->openImage($imageData['fileName']);
    $this->dumpImage($this->image);
  }

  /**
   * Get image data from image map
   *
   * @return array
   */
  private function findImageData() {
    return $this->imageMap->get($this->fullPath, $this->options['width'], $this->options['height']);
  }

  /**
   * Store image into cache, create resized image if necessary.
   *
   * @param array $options
   */
  private function storeImage(array $options) {
    // if both axles set to 0 we store default image
    if ($options['width'] === 0 && $options['height'] === 0) {
      $this->imageMap->insert($this->fullPath, $this->fullPath, 0, 0);
    }
    // we generate new resized image and store that to cache
    else {
      $resizer = new Resizer();
      $resizer->resizeImage($this->image, $this->options);
      $filePath = $resizer->getImagePath();
      list($width, $height) = Resizer::getDimensions($filePath);
      $this->imageMap->insert($filePath, $filePath, $width, $height);
    }
  }

  /**
   * Extract options from uri string parameters
   *
   * @param $optionString
   * @return array
   */
  private function parseOptions($optionString) {
    $options = [];
    $optionValues = explode('/', $optionString);
    foreach ($optionValues as $value) {
      $temp = explode(':', $value);
      switch ($temp[0]) {
        case 'w':
        case 'width':
          $options['width'] = $temp[1];
          break;
        case 'h':
        case 'height':
          $options['height'] = $temp[1];
      }
    }
    return $options;
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