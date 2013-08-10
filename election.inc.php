<?php

require_once('settings.inc.php');
require_once('db.inc.php');

class Candidate {
    // Create a new Candidate instance.
    //
    // Input:
    //     $candidate_id - candidate's database ID (integer)
    //     $name - candidate's name (string)
    //
    // Returns: new Candidate instance
    function __construct($candidate_id, $name) {
        $this->candidate_id = $candidate_id;
        $this->name = $name;
    }
}

class Election {
    // Create a new Election instance.
    //
    // Input:
    //     $election_id - election's database ID (integer)
    //     $title - title of the election (string)
    //     $num_positions - number of positions to be elected (integer)
    //     $created - time of election creation (datetime)
    //     $start_time - time of start of polling (datetime)
    //     $end_time - time of end of polling (datetime)
    //
    // Returns: new Election instance
    function __construct($election_id, $title, $num_positions, $created, $start_time, $end_time) {
        $this->election_id = $election_id;
        $this->title = $title;
        $this->num_positions = $num_positions;
        $this->created = $created;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->candidates = array();
    }
    
    // Add a new candidate.
    //
    // Input:
    //     $candidate - new candidate (Candidate)
    //
    // Returns: nothing
    //
    function add_candidate($candidate) {
        $this->candidates[] = $candidate;
    }
    
    // Delete a candidate.
    //
    // Input:
    //     $candidate_id - ID of candidate
    //
    // Returns: nothing
    function delete_candidate($candidate_id) {
        foreach ($this->candidates as $index => $candidate) {
            if ($candidate->candidate_id == $candidate_id) {
                unset($this->candidates[$index]);
            }
        }
    }
}

class VotingCode {
    // Create a new VotingCode instance.
    //
    // Input:
    //    $code - the voting code (string)
    //    $created - time of creation (datetime)
    //    $status - VOTING STTJGRGRHGUREHGUIREJGIRJGRWIJWUU FIXME TODO
    //    $used - time FIXME TODO
    //
    // Returns: new VotingCode instance
    function __construct($code, $created, $status) {
        $this->code = $code;
        $this->created = $created;
        $this->status = $status;
        $this->elections = array();
    }
    
    // Add a new election.
    //
    // Input:
    //     $election - new election (Election)
    //
    // Returns: nothing
    //
    function add_election($election) {
        $this->elections[] = $election;
    }
    
    // Delete an election.
    //
    // Input:
    //     $candidate_name - name of candidate
    //
    // Returns: nothing
    function delete_election($election_id) {
        foreach ($this->elections as $index => $election) {
            if ($election->election_id == $election_id) {
                unset($this->elections[$index]);
            }
        }
    }
}


// Load everything from the database.
//
// Returns: nothing
function load_data() {
    global $mysqli, $DB_TABLE_PREFIX;
    global $candidates, $elections, $votingcodes;
    
    // Load candidates
    $query = q("SELECT * FROM " . $DB_TABLE_PREFIX . "candidates;");
    $query->execute();
    $result = $query->get_result();
    
    $candidates = array();
    
    foreach ($result as $row) {
        $candidate = Candidate($row->candidate_id, $row->name);
        $candidates[$candidate->candidate_id] = $candidate;
    }
    
    
    // Load elections
    $query = q("SELECT * FROM " . $DB_TABLE_PREFIX . "elections;");
    $query->execute();
    $result = $query->get_result();
    
    $elections = array();
    
    foreach ($result as $row) {
        $election = Election($row['election_id'], $row['title'], $row['num_positions'], $row['created'], $row['start_time'], $row['end_time']);
        $query = q("SELECT * FROM " . $DB_TABLE_PREFIX . "candidates_elections WHERE election_id = ?;");
        $query->bind_param('i', $election->election_id);
        $query->execute();
        $result_candidates = $query->get_result();
        foreach ($result_candidates as $row_result_candidates) {
            try {
                $election->add_candidate($candidates[$row_result_candidates->candidate_id]);
            } catch (Exception $e) {
                die('Error adding Candidate to Election');
            }
        }
        $elections[$election->election_id] = $election;
    }
    
    
    // Load voting codes
    $query = q("SELECT * FROM " . $DB_TABLE_PREFIX . "voting_codes;");
    $query->execute();
    $result = $query->get_result();
    
    $voting_codes = array();
    
    foreach ($result as $row) {
        $voting_code = VotingCode($row['code'], $row['created'], $row['status']);
        $query = q("SELECT * FROM " . $DB_TABLE_PREFIX . "elections_votingcodes WHERE code = ?;");
        $query->bind_param('s', $voting_code->code);
        $query->execute();
        $result_elections = $query->get_result();
        foreach ($result_elections as $row_result_elections) {
            try {
                $votingcode->add_election($elections[$row_result_elections->election_id]);
            } catch (Exception $e) {
                die('Error adding Election to VotingCode');
            }
        }
        $votingcodes[$votingcode->code] = $votingcode;
    }
}
