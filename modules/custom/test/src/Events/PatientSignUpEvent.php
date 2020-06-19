<?php

namespace Drupal\test\Events;

use Drupal\node\NodeInterface;
use Drupal\user\Entity;
use Symfony\Component\EventDispatcher\Event;

class PatientSignUpEvent extends Event{

  const EVENT_NAME = 'patient_signup';
  /**
   * The patient
   */
  public $node;

  /**
   * Constructs the object.
   */
  public function __construct(\Drupal\user\Entity\User $node) {
    $this->node = $node;
  }

  public function getNode(){
    return $this->node;
  }
}