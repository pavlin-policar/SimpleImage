<?php

namespace Hiperbola\SimpleImage;


class Resizer {

  protected $image = null;

  protected $newImage = null;

  /**
   * Generated image path.
   *
   * @var string
   */
  protected $imagePath;

  /**
   * Clean up any potential memory leaks.
   */
  public function __destruct() {
    @imagedestroy($this->image);
    @imagedestroy($this->newImage);
  }

  /**
   * Resize image with given options.
   *
   * @param resource $image
   * @param array    $options
   */
  public function resizeImage($image, array $options) {
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
   * @param string $imagePath
   * @return array
   */
  public static function getDimensions($imagePath) {
    $info = getimagesize($imagePath);
    return [
        $info[0],
        $info[1]
    ];
  }
}