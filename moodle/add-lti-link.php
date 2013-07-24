<?php

/**
 * Example usage of the Moodle ltilib
 *
 * @author @gryglbrt
 */

//make sure the user is in moodle and has a session
defined('MOODLE_INTERNAL') || die;
require_login();

// Given some LTI link data like the json below

// {
// "id":6765,
// "description":"Access your some interesting content in Some LTI tool provider.",
// "extensions":[
// 	{
// 		"id":445634,
// 		"parameters":[
// 			{"name":"visibleforstudents","value":"true","id":1276}],
// 		"createdDate":1337478804000,
// 		"modifiedDate":1337478804000,
// 	"platform":"moodle"}],
// "icon":null,
// "title":"Interesting content",
// "createdDate":1337478804000,
// "modifiedDate":1337478804000,
// "launchUrl":"https://sometoolprovider.com/lti",
// "customParameters":[
// 	{"name":"a","value":"b","id":222},
// 	{"name":"c","value":"d","id":911}],
// "secureLaunchUrl":null,
// "secureIcon":null,
// "vendor":{
// 	"name":"Some LTI tool provider",
// 	"id":100,
// 	"description":"Some tool provider provides some tools.",
// 	"url":"http://sometoolprovider.com/",
// 	"code":"stp",
// 	"createdDate":1337478794000,
// 	"modifiedDate":1337478794000,
// 	"contactEmail":"admin@sometoolprovider.com"}
// }

global $COURSE, $PAGE;
require_once('path to ltilib.php');
// include the modinfolib to handle rebuilding the cache below
require_once($CFG->dirroot.'/lib/modinfolib.php');

// assume $response represents the json above
$ltilink = json_decode($response);
add_ltilink($ltilink, $COURSE->id);
//now that the link is added
//rebuild the course cache and redirect back to the page
rebuild_course_cache($COURSE->id);
redirect($PAGE->url);

