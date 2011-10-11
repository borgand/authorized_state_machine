Authorized State Machine for PHP
================================

Super simple Authorized State machine for PHP.

This script allows you to define a simple state machine that has:

  1. List of groups/users allowed to modify this state
  2. List of allowed next states


USAGE
-----

Define states and permissions

    State::build(array('name' => 'New idea',
        'permissions' => array('owner', 'manager')));

    State::build(array('name' => 'Approved',
        'permissions' => array('owner', 'manager')));

    State::build(array('name' => 'Rejected', 
        'permissions' => array('owner', 'manager')));


Define transitions between states
**NB! This throws exceptions if it encouters any states previously undefined.**

    State::transition(array(
          'from' => "New idea",
          'to' => array(
            "Approved",
            "Rejected"
          )
        ));


Test the machine

    $state = State::byName('New Idea');
    print_r($state->nextStateNames());

    // Will be true
    var_dump($state->isAuthorized('manager'));
    var_dump($state->isNextState('Approved'));
    var_dump($state->isAuthorizedForNextState('manager','Approved'));

    // Will be false
    var_dump($state->isNextState('Edited'));
    var_dump($state->isAuthorizedForNextState('editor','Edited'));
