<?php
namespace Drupal\test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Entity;
use Drupal\test\Events\PatientSignUpEvent;

class ConfirmationForm extends FormBase{

    public function getFormId(){

        return 'confirmation_form';

    }
    public function submitForm(array &$form, FormStateInterface $form_state){
        $id = \Drupal::routeMatch()->getParameter('user'); 

        $user = \Drupal\user\Entity\User::load($id->id());
        $user->field_confirmed->value = True;
        $user->save();
    
        \Drupal::service('messenger')->addMessage($user->getUsername() . ' has been confirmed to the healthcare system.');
    }


    public function validateForm(array &$form, FormStateInterface $form_state){
        
        
    }
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['#theme'] = 'confirmation_form';

        $user = \Drupal::routeMatch()->getParameter('user'); 

        $form['nid'] = array(
            '#type' => 'label',
            '#title' => $user->id(),
        );
        
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Confirm'),
          ];

        return $form;
    }
}