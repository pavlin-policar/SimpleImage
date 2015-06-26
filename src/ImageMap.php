<?php

namespace Hiperbola\SimpleImage;

use PDO;

/**
 * Class ImageMap
 *
 * @package Hiperbola\SimpleImage
 */
class ImageMap {

  /**
   * @var PDO
   */
  protected $map = null;

  /**
   * Class constructor.
   * Connect to database.
   */
  public function __construct() {
    $this->connect();
  }

  /**
   * @param $imageName
   * @param $fileName
   * @param $width
   * @param $height
   */
  public function insert($imageName, $fileName, $width, $height) {
    $sth = $this->map->prepare(
        'INSERT INTO image_map (imageName, fileName, width, height) VALUES (:imageName, :fileName, :width, :height)'
    );
    $sth->bindParam(':imageName', $imageName);
    $sth->bindParam(':fileName', $fileName);
    $sth->bindParam(':width', $width);
    $sth->bindParam(':height', $height);
    $sth->execute();
  }

  /**
   * @param string  $imageName Image name to be stored, recommended use absolute path.
   * @param integer $width     Default to 0, 0 being the default image without any resizing.
   * @param integer $height    Default to 0, 0 being the default image without any resizing.
   * @return mixed
   */
  public function get($imageName, $width = 0, $height = 0) {
    $query = 'SELECT * FROM image_map WHERE imageName = :imageName';
    if ($width !== 0) {
      $query .= ' AND width = :width';
    }
    if ($height !== 0) {
      $query .= ' AND height = :height';
    }
    $sth = $this->map->prepare($query);
    $sth->bindParam(':imageName', $imageName);

    if ($width !== 0) {
      $sth->bindParam(':width', $width, PDO::PARAM_INT);
    }
    if ($height !== 0) {
      $sth->bindParam(':height', $height, PDO::PARAM_INT);
    }
    $sth->execute();
    return $sth->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Connect to database, create schema if it does not yet exist.
   */
  protected function connect() {
    if ($this->map === null) {
      $this->map = new PDO('sqlite:image_map.sqlite3');
      $this->map->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->map->exec(
          "CREATE TABLE IF NOT EXISTS image_map (id INTEGER PRIMARY KEY, imageName TEXT, fileName TEXT, width INTEGER, height INTEGER)"
      );
    }
  }
}