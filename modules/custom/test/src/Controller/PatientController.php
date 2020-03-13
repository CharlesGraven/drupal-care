<?php
namespace Drupal\test\Controller;

use Drupal\Core\Controller\ControllerBase;
class PatientController extends ControllerBase{

    //pass output to my template
    public function pageAction(){
        
        $myform = \Drupal::formBuilder()->getForm('Drupal\test\Form\DoctorForm');
        // If you want modify the form:
        //$myform['field']['#value'] = 'From my controller';

        $build = [
            '#theme' => 'form-template',
            '#form' => $myform,
        ];

        return $build;
    }
}