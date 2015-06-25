<?php

namespace Hiperbola\SimpleImage;

/**
 * Class Defaults
 *
 * Option reference:
 *  => max-axis:          maximum width or height that can be taken up on the larger axis
 *  => max-x:             maximum x axis that can be taken up
 *  => max-y:             maximum y axis that can be taken up
 *  => x:                 fixed width (useful when image is to be stretched)
 *  => y:                 fixed height (useful when image is to be stretched)
 *  => keep-aspect-ratio: keep aspect ratio (default: true)
 *
 * @package Hiperbola\SimpleImage
 */
class Defaults {
  /**
   * Different default builtin sizes see option reference for specs
   *
   * @var array
   */
  public static $sizes = [
      'xs' => [
          'max-axis' => 240
      ],
      's'  => [
          'max-axis' => 640
      ],
      'm'  => [
          'max-axis' => 1024
      ],
      'l'  => [
          'max-axis' => 1900
      ],
      'xl' => [
          'max-axis' => 2880
      ]
  ];
}