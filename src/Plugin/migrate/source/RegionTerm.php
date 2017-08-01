<?php

namespace Drupal\regions_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use \ArrayObject;

/**
 * This is an example of a simple SQL-based source plugin. Source plugins are
 * classes which deliver source data to the processing pipeline. For SQL
 * sources, the SqlBase class provides most of the functionality needed - for
 * a specific migration, you are required to implement the three simple public
 * methods you see below.
 *
 * This annotation tells Drupal that the name of the MigrateSource plugin
 * implemented by this class is "beer_term". This is the name that the migration
 * configuration references with the source "plugin" key.
 *
 * @MigrateSource(
 *   id = "region_term"
 * )
 */
class RegionTerm extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    // File handler using our custom header-rows-respecting extension of SPLFileObject.
    $file = file_get_contents($this->configuration['path']);
    $json = json_decode($file, TRUE);

    $regions = $this->extractData($json);

    $regions = new ArrayObject($regions);
    return $regions->getIterator();
  }

  public function extractData($array, $parent = NULL) {
    $regions = [];
    foreach ($array as $key => $region) {
      $regions[] = [
        'path' => '/regions/' . $region['path'],
        'name' => $region['name'],
        'parent' => $parent
      ];
      // Include the children.
      if (isset($region['children'])) {
        $regions = array_merge($regions, $this->extractData($region['children'], $region['name']));
      }
    }

    return $regions;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'RegionTermSource';
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    /**
     * This method simply documents the available source fields provided by
     * the source plugin, for use by front-end tools. It returns an array keyed
     * by field/column name, with the value being a translated string explaining
     * to humans what the field represents. You should always
     */
    $fields = [
      'id' => $this->t('Source ID'),
      'name' => $this->t('name'),
      'path' => $this->t('path'),
      'region_parent' => $this->t('Region parent'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    /**
     * This method indicates what field(s) from the source row uniquely identify
     * that source row, and what their types are. This is critical information
     * for managing the migration. The keys of the returned array are the field
     * names from the query which comprise the unique identifier. The values are
     * arrays indicating the type of the field, used for creating compatible
     * columns in the map tables that track processed items.
     */
    return [
      'name' => [
        'type' => 'string',
      ],
    ];
  }

}
