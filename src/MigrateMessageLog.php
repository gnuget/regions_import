<?php

namespace Drupal\regions_import;

use Drupal\migrate\MigrateMessageInterface;

/**
 * Class MigrateMessage.
 */
class MigrateMessageLog implements MigrateMessageInterface {

  /**
   * Save the Migrate message in the log.
   *
   * {@inheritdoc}
   *
   * @see drush_log()
   */
  public function display($message, $type = 'status') {
    $type = ($type == 'status') ? 'notice' : 'warning';
    \Drupal::logger('regions_import')->log($type, $message);
  }

}
