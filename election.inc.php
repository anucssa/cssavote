<?php

require_once('settings.inc.php');

// use composer's autoload mechanism
require_once('vendor/autoload.php');

// setup database connections
Pheasant::setup('mysql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME');

use \Pheasant\Types;

class Candidate extends \Pheasant\DomainObject
{
    public function properties()
    {
        return array(
            'candidate_id'      => new Types\Integer(11, 'primary auto_increment'),
            'name'              => new Types\String(255, 'required'),
        )
    }
}

class Election {
    // Create a new Election instance.
    //
    // Input:
    //     $title - title of the election (string)
    //     $num_positions - number of positions to be elected (integer)
    function __construct($title, $num_positions) {
        $this->title = $title;
        $this->num_positions = $num_positions;
    }
    
    $candidates = array();
    
    // Add a new candidate.
    //
    // Input:
    //     $candidate - new candidate (Candidate)
    //
    // Returns: nothing
    //
    function add_candidate($candidate) {
        $candidates[$candidate->properties()['name']] = $candidate;
    }
    
    // Delete a candidate.
    //
    // Input:
    //     $candidate_name - name of candidate
    //
    // Returns: true if successful, false if unsuccessful (boolean)
    function delete_candidate($candidate_name) {
        try {
            unset($candidates[$candidate->properties()['name']]);
            return true;
        } catch {
            return false;
        }
    }
}

class ElectionSet {
    $elections = array();
}

class VotingCode {
    
}

function load_elections

?>