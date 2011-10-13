<?php
/**
 * State Machine main class
 * Holds one state with list of authorized user groups and next allowed states
 */
class State
{

  ##################
  # Static functions
  ##################

  /**
   * Holds array of all states
   */
  public static $states = array();


  /**
   * Factory to build new state
   */
  public static function build($data)
  {
    $state = new self($data['name'], $data['permissions']);
    self::$states[$data['name']] = $state;
  }

  /**
   * Get state by name
   */
  public static function byName($name)
  {
    return self::$states[$name];
  }

  /**
   * Register allowed transisions for a state
   */
  public static function transition($data)
  {
    $state = self::byName($data['from']);
    if (!isset($state)) {
      throw new Exception("StateTransitionError: Unknown FROM-state: " . $data['from']);
      return;
    }
    foreach ($data['to'] as $name) {
      $to_state = self::byName($name);
      if (isset($to_state))
        $state->addNextState($to_state);
      else
        throw new Exception("StateTransitionError: Unknown TO-state: $name");
    }
  }

  ##################
  # Instance stuff
  ##################

  /*
   * Name of the state
   */
  public $name;

  /*
   * array of groups allowed to modify this state
   */
  public $groups;

  /*
   * Array of states this state can transform to
   */
  public $nextStates;
  
  function __construct($name, $groups)
  {
    $this->name = $name;
    $this->groups = $groups;
  }

  /*
   * Set the list of next states
   * @param $states - list of States instants
   */
  public function setNextStates($states)
  {
    $this->nextStates = array();
    foreach ($states as $state) {
      $this->nextStates[$state->name] = $state;
    }
  }

  /**
   * Add an allowed next state
   */
  public function addNextState($state)
  {
    $this->nextStates[$state->name] = $state;
  }

  /*
   * Get next state by name
   */
  public function nextState($name)
  {
    $nextState = $this->nextStates[$name];
    return isset($nextState) ? $nextState : false;
  }

  /*
   * Return list of next state names
   */
  public function nextStateNames()
  {
    return array_keys($this->nextStates);
  }

  /*
   * Check if given state name is allowed next state
   */
  public function isNextState($state_name)
  {
    return in_array($state_name, $this->nextStateNames());
  }

  /**
   * Check current user group authorization
   */
  public function isAuthorized($group)
  {
    return in_array($group, $this->groups);
  }

  /*
   * Check if given user/group is authorized to set next state
   * AND that next state is valid for this state
   */
  public function isAuthorizedForNextState($group, $state_name)
  {
    $nextState = $this->nextState($state_name);
    return $nextState && $nextState->isAuthorized($group);
  }
}
