<?php

/**
 * Script to enrol users randomly to the X site courses
 */

$ncourses = 10;  // The number of courses we are going to enrol users on.
$usernameprefix = 'teacherjmeter';  // The prefix of the users we are going to assing courses.
$roleid = '3';  // The role to be assigned in those courses.

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../config.php');

// Overridding as we don't know moodle config contents.
// Not very reliable as moodle can re-override it.
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

$plugin = enrol_get_plugin('manual');

$courses = $DB->get_records('course');
$nsitecourses = count($courses);
$courseidsarray = array_keys($courses);  // Course id - incremental number relation.

$users = $DB->get_records_sql("SELECT id, username FROM {user} WHERE username like '$usernameprefix%'");

foreach ($users as $user) {

    mtrace('Enrolling user ' . $user->username);

    // Obtains an associative array with id => id
    $randomcourseids = array();
    while (count($randomcourseids) < $ncourses) {
        $randomcourseid = $courseidsarray[rand(0, ($nsitecourses - 1))];
        $randomcourseids[$randomcourseid] = $randomcourseid;
    }

    foreach ($randomcourseids as $courseid) {

        if (!$instances = $DB->get_records('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'))) {
            // Unlinkely but possible.
            debugging("No enrol instances in $courseid");  
            continue;
        }
        $instance = reset($instances);

        $plugin->enrol_user($instance, $user->id, $roleid);
    }
}

mtrace('End');
