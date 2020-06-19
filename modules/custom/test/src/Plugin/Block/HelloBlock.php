<?php

namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Block\BlockBase;

 /**
  * short description of block
  *
  * @Block(
  *   id = "custom_module_block_machine_name",
  *   admin_label = @Translation("Admin interface Label"),
  *   category = @Translation("Custom Module or Category Name")
  * )
  */
class HelloBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#markup' => $this->t('The fax number is @number!', array('@number' => $fax_number)),
    );  
  }

}