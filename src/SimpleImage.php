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
   * Default directory of cached images.
   *
   * @var string
   */
  private $cacheDirectory = 'cache/';

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
   * @param string $optionString
   * @internal param string $options
   */
  public function __construct($basedir, $fileName, $optionString = '') {
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
    $imageData = $this->findImageData($this->fullPath, $this->options);


    if (!$imageData) {
      $this->generateAndStoreImage($this->fullPath, $this->options);
    }

    $imageData = $this->findImageData($this->fullPath, $this->options);
    if (!$imageData) {
      throw new \RuntimeException('Was not able to store cache entry for image.');
    }

    $this->fullPath = $imageData['fileName'];

    $this->image = $this->openImage($this->fullPath);
  }

  /**
   * Get image data from image map, return false if miss
   *
   * @param string $imageName
   * @param array  $options
   * @return array|bool
   */
  private function findImageData($imageName, array $options) {
    $aspectRatio = $options['width'] !== 0 && $options['height'] !== 0 ? 0 : 1;
    $data = $this->imageMap->get($imageName, $options['width'], $options['height'], $aspectRatio);
    return empty($data) ? false : $data;
  }

  /**
   * Store image into cache, create resized image if necessary.
   *
   * @param string $imageName
   * @param array  $options
   */
  private function generateAndStoreImage($imageName, array $options) {
    // if both axles set to 0 we simply store default image
    if ($options['width'] === 0 && $options['height'] === 0) {
      $this->imageMap->insert($imageName, $imageName, 0, 0, 1);
    } // we generate new resized image and store that to cache
    else {
      $resizer = new Resizer();
      // first we need the default image to be loaded
      $default = new SimpleImage($this->basedir, $this->fileName);

      // resize image with given options and store it onto disk
      $this->image = $resizer->resizeImage($default->image, $options);

      // generate cached image file name
      list($width, $height) = Resizer::getDimensions($this->image);
      $fileName = $this->generateFileName($this->fullPath, $width, $height);

      $this->storeImage($this->image, $fileName);

      // insert entry into cache
      $this->imageMap->insert($imageName, $fileName, $width, $height, $resizer->keepAspectRatio());
    }
  }

  /**
   * Generate cached image file path.
   *
   * @param string  $imageName
   * @param integer $width
   * @param integer $height
   * @return mixed|string
   */
  private function generateFileName($imageName, $width, $height) {
    $imageName = preg_replace('/\//', '_', $imageName);
    $imageName = preg_replace('/:/', '', $imageName);
    $extension = substr($imageName, strpos($imageName, '.') + 1);
    $imageName = substr($imageName, 0, strpos($imageName, '.'));
    $imageName = $imageName . '_' . $width . '_' . $height . '.' . $extension;
    $imageName = $this->cacheDirectory . $imageName;
    return $imageName;
  }

  /**
   * Store image to disk.
   *
   * @param resource $image
   * @param string   $fileName
   */
  private function storeImage($image, $fileName) {
    if (!file_exists($this->cacheDirectory)) {
      mkdir($this->cacheDirectory);
    }
    switch ($this->extension) {
      case 'jpg':
      case 'jpeg':
        imagejpeg($image, $fileName);
        break;
      case 'png':
        imagepng($image, $fileName);
        break;
      case 'gif':
        imagegif($image, $fileName);
        break;
    }
  }

  /**
   * Extract options from uri string parameters
   *
   * @param string $optionString
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
   * @param string $filename
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
      default:
        throw new \RuntimeException('Image type not supported.');
    }
    return $img;
  }

  /**
   * Return image to browser to display.
   */
  public function dumpImage() {
    switch ($this->extension) {
      case 'jpg':
      case 'jpeg':
        header('Content-Type: image/jpeg');
        imagejpeg($this->image);
        break;
      case 'png':
        header('Content-Type: image/png');
        imagepng($this->image);
        break;
      case 'gif':
        header('Content-Type: image/gif');
        imagegif($this->image);
        break;
    }
  }
}