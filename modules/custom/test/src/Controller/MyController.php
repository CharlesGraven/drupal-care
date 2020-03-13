<?php
namespace Drupal\test\Controller;

use Drupal\Core\Controller\ControllerBase;
class MyController extends ControllerBase{

    //pass output to my template
    public function content($test){
        
        //$mytext = 'this isnt a default';
        //$test = \Drupal::request()->query->get('name');
        $output = [
            '#theme' => 'test_theme_hook'

        ];

        if(!empty($test)){
            $output['#variable1'] = $test;
        }

        return $output;
    }
}