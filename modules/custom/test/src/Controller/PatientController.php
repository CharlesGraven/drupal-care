<?php
namespace Drupal\test\Controller;

use Drupal\Core\Controller\ControllerBase;
class PatientController extends ControllerBase{

    //pass output to my template
    public function pageAction($doctors){
        
        //$myform = \Drupal::formBuilder()->getForm('Drupal\test\Form\PatientForm');
        // If you want modify the form:
        //$myform['field']['#value'] = 'From my controller';

        $build = [
            '#theme' => 'doctor',
        ];

        if(!empty($doctors)){
            $build['#doctors'] = $doctors;
        }

        return $build;
    }
}