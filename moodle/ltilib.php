<?php

/**
 * A library of IMS Learning Tool Interoperability related functions for Moodle
 *
 * @author @gryglbrt
 */

defined('MOODLE_INTERNAL') || die;

function add_ltilink($ltilink, $courseid) {
	global $DB, $CFG;
	require_once($CFG->dirroot.'/course/lib.php');
	$lti = new stdClass;
	$lti->course = $courseid;
	$lti->name = $ltilink->title;
	$lti->timecreated = time();
	$lti->timemodified = $lti->timecreated;
	$lti->typeid = 0;
	$lti->toolurl = $ltilink->launchUrl;

	//include the user's first/last name in the LTI launcn
	// 1 = true
	$lti->instructorchoicesendname = 1;
	//include the user's email address in the LTI launch
	$lti->instructorchoicesendemailaddr = 1;

	//moodle stores custom parameters
	// in a big string separated by line breaks
	$strCustomParams = '';

	$customparams = $ltilink->customParameters;

	foreach ($customparams as $customparam) {
		$name = $customparam->name;
		$value = $cp->value;

		if ($strCustomParams == NULL) {
			$strCustomParams = '';
		}
		$strCustomParams .= $name.'='.$value."\n";
	}

	if ($strCustomParams != null) {
		$lti->instructorcustomparameters = $strCustomParams;
	}

	// whether the launch should be done in a frame, new window, etc
	// 	define('LTI_LAUNCH_CONTAINER_DEFAULT', 1);
	// 	define('LTI_LAUNCH_CONTAINER_EMBED', 2);
	// 	define('LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS', 3);
	// 	define('LTI_LAUNCH_CONTAINER_WINDOW', 4);
	// 	define('LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW', 5);
	// this example opens in a new window
	$lti->launchcontainer = 4;
	$lti->resourcekey = //the oauth consumer key;
	$lti->password = //shared secret;
	$lti->debuglaunch = 0;
	$lti->showtitlelaunch = 0;
	$lti->showdescriptionlaunch = 0;
	$lti->icon = $ltilink->icon->url;
	
	// finally add the lti link to the database
	$lti->id = $DB->insert_record('lti', $lti);

	// now add the corresponding course module
	
	// get the lti module
	$ltimodule = $DB->get_record('modules', array('name' => 'lti'), '*', MUST_EXIST);
	
	$cm = new stdClass;
	$cm->course = $courseid;
	$cm->module = $ltimodule->id;
	$cm->instance = $lti->id;
	// 0 puts the link in the top section of the course
	$cm->section = 0;
	// we use the lti link id here but really it could be anything
	$cm->idnumber = $lti->id;
	$cm->added = time();
	$cm->score = 0;
	$cm->indent = 0;
	
	// these field determine if the course module is
	//  visible to students (1=true)
	$cm->visible = 1;
	$cm->visibleold = 1;
	
	$cm->groupmode = 0;
	$cm->groupingid = 0;
	$cm->groupmembersonly = 0;
	$cm->completion = 0;
	$cm->completionview = 0;
	$cm->completionexpected = 0;
	$cm->availablefrom = time() - 10;
	$cm->availableuntil = time() - 10;
	$cm->showavailability = 0;
	$cm->showdescription = 0;
	// finally create the course module
	$cm->coursemodule = add_course_module($cm);
	// add it to the section (0 in this case)
	// TODO - this function is deprecated
	$sectionid = add_mod_to_section($cm);
	$DB->set_field("course_modules", "section", $sectionid, array("id" => $cm->coursemodule));

	return $lti->id;
}
