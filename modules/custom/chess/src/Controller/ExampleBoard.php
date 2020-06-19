<?php
namespace Drupal\chess\Controller;

use Drupal\Core\Controller\ControllerBase;
class ExampleBoard extends ControllerBase{

    //pass output to my template
    public function content(){
        
        $output = [
            '#theme' => 'chess_board',
        ];

        return $output;
    }
}