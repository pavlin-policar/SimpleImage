<?php

namespace Hiperbola\SimpleImage;

/**
 * Class Resizer
 *
 * Handles actual resizing of images.
 *
 * @package Hiperbola\SimpleImage
 */
class Resizer
{
    /**
     * Original image.
     *
     * @var resource
     */
    protected $image = null;

    /**
     * Working copy of image.
     *
     * @var resource
     */
    protected $newImage = null;

    /**
     * Indicator of whether or not to keep aspect ratio (0 - false | 1 true).
     *
     * @var int
     */
    protected $keepAspectRatio = 1;

    /**
     * Generated image path.
     *
     * @var string
     */
    protected $imagePath;

    /**
     * Clean up any potential memory leaks.
     */
    public function __destruct()
    {
        @imagedestroy($this->image);
        @imagedestroy($this->newImage);
    }

    /**
     * Resize image with given options.
     *
     * @param resource $image
     * @param array    $options
     * @return resource
     */
    public function resizeImage($image, array $options)
    {
        list($width, $height) = static::getDimensions($image);
        list($newWidth, $newHeight) = $this->getNewSizes($options, $width, $height);

        $this->newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($this->newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width,
            $height);

        $this->image = $this->newImage;
        $this->newImage = null;

        return $this->image;
    }

    /**
     * Return 1 if aspect ratio is preserved, 0 if it is not.
     *
     * @return int
     */
    public function keepAspectRatio()
    {
        return $this->keepAspectRatio;
    }

    /**
     * Get calculated sizes of new resized image.
     *
     * @param array   $options
     * @param integer $currentWidth
     * @param integer $currentHeight
     * @return array
     */
    private function getNewSizes($options, $currentWidth, $currentHeight)
    {
        if ($options['width'] === 0 && $options['height'] !== 0) {
            return $this->getSizeByFixedHeight($currentWidth, $currentHeight, $options['height']);
        } else if ($options['width'] !== 0 && $options['height'] === 0) {
            return $this->getSizeByFixedWidth($currentWidth, $currentHeight, $options['width']);
        } else {
            $this->keepAspectRatio = 0;
            return [$options['width'], $options['height']];
        }
    }

    /**
     * Get size of new image if height is to remain the same and aspect ratio is to be kept.
     *
     * @param $width
     * @param $height
     * @param $newHeight
     * @return array
     */
    private function getSizeByFixedHeight($width, $height, $newHeight)
    {
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
    private function getSizeByFixedWidth($width, $height, $newWidth)
    {
        $ratio = $height / $width;
        $newHeight = $newWidth * $ratio;
        return [$newWidth, $newHeight];
    }

    /**
     * Get image dimensions
     *
     * @param string|resource $image
     * @return array
     */
    public static function getDimensions($image)
    {
        if (is_string($image)) {
            $info = getimagesize($image);
            return [
                $info[0],
                $info[1]
            ];
        } else if (is_resource($image)) {
            return [
                imagesx($image),
                imagesy($image)
            ];
        }
    }
}