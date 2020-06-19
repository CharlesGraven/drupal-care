<?php
namespace Drupal\test\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\test\TestService;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use Drupal\Core\Entity;

/*
* @Deprecated (for now)
* Doctors don't need a portal to login from now that 
* they're users. In fact, this form can be converted later to something else for the website
* Ideas: Doctor refferal sheet?
*
*/
class DoctorForm extends FormBase{

  protected $services;

  public function __construct(TestService $services) {
    $this->services = $services;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('test_uuid')
    );
  }

    public function getFormId(){

        return 'doctor_form';

    }
    public function submitForm(array &$form, FormStateInterface $form_state){
        $ID = $form_state->getValue('ID');
        $password = $form_state->getValue('password');
        
        $doctors = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'doctor','field_id' => $ID, 'field_password'=>$password]);
        
        $doctors = reset($doctors);
        $specialty = $doctors->get('field_specialty')->getValue();
        $specialty = reset($specialty);

        $patients = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'patient', 'field_health_issues'=>$specialty['target_id']]);
        $patients = reset($patients);
   
        $form_state->setRedirect('block.testing', ['test' => $patients->field_first->value]);
    }
    public function validateForm(array &$form, FormStateInterface $form_state){
        $ID = $form_state->getValue('ID');
        $password = $form_state->getValue('password');

        $doctor = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'doctor','field_id' => $ID, 'field_password'=>$password]);

        //Make sure there is information
        if(empty($doctor)){
          \Drupal::service('messenger')->addMessage('no such doctor');
        }else{
          \Drupal::service('messenger')->addMessage($doctor);
          
        }
        
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['ID'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Doctor ID'),
        ];
        //$form['markup'] = [
            //'#markup' => $this->services->myTest(),
        //];
          $form['password'] = [
            '#type' => 'password',
            '#title' => $this->t('password'),
          ];

          $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Go'),
          ];

        return $form;
      }

      private function renderPatients($specialty){
        $all_patients = array();
        $patients = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['Patient', 'health_issues'=>$specialty]);
        foreach($patients as $patient){
           
              $all_patients[] = $patient;
            
        }
        return $all_patients;
      }
}