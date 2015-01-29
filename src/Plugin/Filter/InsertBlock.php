<?php

/**
 * @file
 * Contains \Drupal\filter\Plugin\Filter\InsertBlock.
 */

namespace Drupal\insert_block\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\block\Entity\Block;

/**
 * Provides a filter which allows to embed blocks.
 *
 * @Filter(
 *   id = "filter_insert_block",
 *   title = @Translation("Insert blocks"),
 *   description = @Translation("Allows embedding blocks in text."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class InsertBlock extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    Block::load('wunderhub_client_team');
    if (preg_match_all("/\[block:([^\]]+)+\]/", $text, $match)) {
      $raw_tags = $replacements = array();

      foreach ($match[1] as $key => $value) {
        $raw_tags[] = $match[0][$key];
        $block_id = $match[1][$key];
        /** @var \Drupal\block\BlockInterface $block */
        $block = Block::load($block_id);

        if ($block) {
          $replacement = entity_view($block, 'block');
          $replacements[] = \Drupal::service('renderer')->render($replacement);
        }
      }

      $text = str_replace($raw_tags, $replacements, $text);
    }

    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t('<a name="filter-insert_block"></a>You may use [block:<em>block_entity_id</em>] tags to display the contents of block. To discover block entity id, visit admin/structure/block and hover over a block\'s configure link and look in your browser\'s status bar. The last "word" you see is the block ID.');
    }
    else {
      return t('You may use <a href="@insert_block_help">[block:<em>block_entity_id</em>] tags</a> to display the contents of block.',
        ['@insert_block_help' => url('filter/tips/filter_insert_block', ['fragment' => 'filter-insert_block'])]);
    }
  }

}
