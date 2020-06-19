<?php
namespace Drupal\test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Entity;
use Drupal\test\Events\PatientSignUpEvent;

class AppointmentForm extends FormBase{

    public function getFormId(){

        return 'appointment_form';

    }
    public function submitForm(array &$form, FormStateInterface $form_state){
        //Get all the form values
        $time = $form_state->getValue('time');
        $specialties = $form_state->getValue('specialties');
        $specialties_to_string = implode(" ",$specialties);


        //figure this out later. For now it will give too many errors to solve
        //$doctor = $user->doctor; //Create either a field Doctor or a reference to a doctor in the patient User
        //$user->firstname;
        //$user->lastname;
        
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        \Drupal::service('messenger')->addMessage('username ' . $user->getUsername());
        dpm($time->format('Y-m-d\TH:i:s'));
        $node = Node::create([
          'type' => 'appointment',
          'title' => $user->getUsername() . ' ' . $time,
          'field_appointment_doctor' => $user->get('field_your_doctor'),
          'field_appointment_patient' => $user,
          'field_appointment_date' => $time->format('Y-m-d\TH:i:s'),
          'field_appointment_specialty' => $specialties,
          //'reason' => $reason,
        ]);
        
        $node->save();

        //This event will give a notification to the doctor. Maybe instead of rendering everything through a view
        //$event_dispatcher = \Drupal::service('event_dispatcher');
        //$event_dispatcher->dispatch(PatientSignUpEvent::EVENT_NAME, $event);
        //$form_state->setRedirect('block.patient', ['data' => $specialties_to_string]);
        
        
    }


    public function validateForm(array &$form, FormStateInterface $form_state){
        $specialties = $form_state->getValue('specialties');
        $time = $form_state->getValue('time');
        $fieldname = '';
        $fieldarray = [];

        if(empty($specialties)){
        
            $fieldarray[] = '';
        }
        if(empty($time)){
      
            $fieldarray[] = 'time';
        }
        if(empty($specialties)&&empty($time)){
            $form_state->setError($form, $this->t("Not a valid: " . $fieldarray['0'] . $fieldarray['1']));
        }
        else if(!empty($fieldarray['0'])){
            $form_state->setErrorByName($fieldarray['0'], $this->t("Not a valid: " . $fieldarray['0']));
        }else{
            \Drupal::service('messenger')->addMessage('this is validated');
        }
        
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        //We will need to get the User who submitted for the appointment 
        
          //This section will be used to filter doctors eventually
          $entity_type_manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
          $loaded_terms = $entity_type_manager->loadTree('specialty');
          
          
          $all_specialties = [];

          foreach($loaded_terms as $terms){
           
            $all_specialties[$terms->tid] = $terms->name;
          
          }

          $form['specialties'] = [
            '#type' => 'checkboxes',
            '#options' => $all_specialties,
            '#title' => $this->t('Which health conditions are you seeking to remidiate?'),
            
          ];

          //In the future we could get a list of available times the doctor has for that day
          $form['time'] = [
            '#type' => 'datetime',
            '#title' => $this->t('Please select a time you could see the doctor'),
            
          ];
          
          $form['reason'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Why do you need to see the doctor?'),
            
          ];

          $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
          ];
        return $form;
      }
}