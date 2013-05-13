<?php
/**
 * Create a set of users
 *
 * Copied and adapted from https://raw.github.com/FMCorz/mdk/master/scripts/users.php
 */

define('CLI_SCRIPT', true);
require(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir . '/filelib.php');

// Overridding.
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

// Create all the users.
while ($i < $nusers) {

    $user[0] = $role . $i;  // username.
    $user[1] = 'moodle';    // password.
    $user[2] = 'Student'    // firstname.
    $user[3] = $i;          // lastname.
    $user[4] = $role . $i '@moodlemoodle.org';

    if ($DB->record_exists('user', array('username' => $user[0], 'deleted' => 0))) {
        continue;
    }

    mtrace('Creating user ' . $user[0]);
    $u = create_user_record($user[0], $user[1]);
    $u->firstname = $user[2];
    $u->lastname = $user[3];
    $u->email = $user[4];
    $u->city = 'Perth';
    $u->country = 'AU';
    $u->lang = 'en';

    $DB->update_record('user', $u);
}

mtrace('End');
