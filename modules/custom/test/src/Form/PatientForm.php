<?php
namespace Drupal\test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Entity;
use Drupal\test\Events\PatientSignUpEvent;

class PatientForm extends FormBase{

    public function getFormId(){

        return 'patient_form';

    }
    public function submitForm(array &$form, FormStateInterface $form_state){
        //Get all the form values
        $firstname = $form_state->getValue('first_name');
        $lastname = $form_state->getValue('last_name');
        $specialties = $form_state->getValue('specialties');
        $specialties_to_string = implode(" ",$specialties);
        //Messenger Service for testing
        $output = $this->t('%first_name %last_name %field_health_issues',[
            '%first_name' => $firstname,
            '%last_name' => $lastname,
            '%field_health_issues' => $specialties_to_string,
        ]);
        \Drupal::service('messenger')->addMessage($output);

        $username = $firstname . '' . $lastname;
        $password = 'password';

        
        $ids = \Drupal::entityQuery('user')->condition('roles', 'doctor')->
        condition('field_specialty', $specialties, 'IN')->execute();

        $users = \Drupal\user\Entity\User::loadMultiple($ids);
        $userlist = [];
        $count = 0;
	      foreach($users as $user){
          //$username = $user->get('name')->value;
          //$uid = $user->get('uid')->value;
          //$userlist[$uid] = $username;
          $userlist[$count] = $user->get('uid')->value;
          $count++;
        }
        $doctor = \Drupal\user\Entity\User::load($userlist['0']);

        $user = \Drupal\user\Entity\User::create();
        $user->setPassword($password);
        //$user->enforceIsNew();
        $user->setEmail('Willdolater@procrastination.com');
        $user->setUsername($username);
        $user->addRole('patient');
        $user->field_specialty = $specialties;
        $user->field_your_doctor = $doctor->get('uid')->value;
        $user->activate();
        $user->save();

        $patient_list = $doctor->field_patient_list->referencedEntities();
        array_push($patient_list, $user);
        $doctor->set('field_patient_list', $patient_list);
        $doctor->save();

        //login the new user after they submit the form
        //Edit permississons for the form
        user_login_finalize($user);

        //$doctor= \Drupal\user\Entity\User::load($userlist['0']);
        //$doctor->set('field_patient_list', $user); //add the user to the existing list of patients

        //Begin Custom Event Code
        $event = new PatientSignUpEvent($user); 
        $event_dispatcher = \Drupal::service('event_dispatcher');
        $event_dispatcher->dispatch(PatientSignUpEvent::EVENT_NAME, $event);

        //Work on this redirect today
        //$form_state->setRedirect('block.patient');
        $form_state->setRedirect('block.patient', ['doctors' => $doctor->get('name')->value]);
        
    }


    public function validateForm(array &$form, FormStateInterface $form_state){
        $firstname = $form_state->getValue('first_name');
        $lastname = $form_state->getValue('last_name');
        $fieldname = '';
        $fieldarray = [];

        if(empty($firstname)){
        
            $fieldarray[] = 'first_name';
        }
        if(empty($lastname)){
      
            $fieldarray[] = 'last_name';
        }
        if(empty($firstname)&&empty($lastname)){
            $form_state->setError($form, $this->t("Not a valid: " . $fieldarray['0'] . $fieldarray['1']));
        }
        else if(!empty($fieldarray['0'])){
            $form_state->setErrorByName($fieldarray['0'], $this->t("Not a valid: " . $fieldarray['0']));
        }else{
            \Drupal::service('messenger')->addMessage('this is validated');
        }
        
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['first_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('First Name'),
        ];
        $form['last_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
          ];

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
          $form['medrecords'] = [
            '#type' => 'file',
            '#title' => $this->t('Please upload your current medical records here...'),
            
          ];
          
          $form['comments'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Any additional medications or health issues you might have can be written here...'),
            
          ];

          $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
          ];
        return $form;
      }
}