<?php
namespace Drupal\test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Entity;

class PatientForm extends FormBase{

    public function getFormId(){

        return 'patient_form';

    }
    public function submitForm(array &$form, FormStateInterface $form_state){
        $firstname = $form_state->getValue('first_name');
        $lastname = $form_state->getValue('last_name');
        $specialties = $form_state->getValue('specialties');
        $specialties_to_string = implode(" ",$specialties);
        $output = $this->t('%first_name %last_name %field_health_issues',[
            '%first_name' => $firstname,
            '%last_name' => $lastname,
            '%field_health_issues' => $specialties_to_string,
        ]);
        \Drupal::service('messenger')->addMessage($output);
        $node = Node::create([
          'type' => 'patient',
          'title' => $firstname . ' ' . $lastname,
          'field_first' => $firstname,
          'field_last_name' => $lastname,
          'field_health_issues' => $specialties,
        ]);
        
        $node->save();
        //$form_state->setRedirect('block.patient', ['data' => $specialties_to_string]);
        //echo($node);
        
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

      function test_example_form_alter(&$form, FormStateInterface $form_state, $form_id) {

          $form['first_name']['title'] = t('This text has been altered by hooks_example_form_alter().');

      }


}