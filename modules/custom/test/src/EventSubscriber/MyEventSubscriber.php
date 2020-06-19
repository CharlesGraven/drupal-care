<?php

namespace Drupal\test\EventSubscriber;

use Drupal\node\NodeInterface;
use Drupal\user\Entity;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\test\Events\PatientSignUpEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;

class MyEventSubscriber implements EventSubscriberInterface{

    use StringTranslationTrait;

    public function alertAdministrators(PatientSignUpEvent $node){
        //$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

        $patient = $node->getNode();
        //these methods directly access a node's variables
        //Which means they will probably need to change once I end up using users
        
        //$email = $patient->getEmail;
        //$userName = $patient->getUsername;

        $url = Url::fromRoute('confirmation_form.confirmation_form', ['user'=>$patient->id()]);
        $link = Link::fromTextAndUrl(t('Confirmation'), $url);
        $link = $link->toRenderable(); 

        \Drupal::service('messenger')->addMessage('username ' . $patient->getUsername());
        $node = Node::create([
          'type' => 'alert',
          'title' => $patient->getUsername() . ' reason for alert',
          //'field_open' => 'register/' . $patient->get('uid')->value],
          'field_open' => ['uri'=> 'internal:/' . $url->getInternalPath(), 'title'=>'Confirmation'],
        ]);
        $node->save();
    }

    public static function getSubscribedEvents(){
        $events = [];
        $events[PatientSignUpEvent::EVENT_NAME][] = array('alertAdministrators');
        return $events;
    }
}