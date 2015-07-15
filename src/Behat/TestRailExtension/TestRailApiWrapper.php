<?php
/**
 * Created by PhpStorm.
 * User: eugen.degtiarov
 * Date: 7/9/15
 * Time: 2:42 AM
 */
require "testrail.php";

/**
 * Class TestRailApiWrapper
 */
class TestRailApiWrapper
{
    /**
     * @param $testrail_username
     * @param $testrail_password
     * @param $testrail_url
     * @param $testrail_testrun_name
     * @param $testrail_testrun_description
     * @param $testrail_project_id
     * @param $testrail_testplain_id
     * @param $testrail_log_results
     */
    public static function set_testrun_context($testrail_username, $testrail_password, $testrail_url, $testrail_testrun_name, $testrail_testrun_description, $testrail_project_id, $testrail_testplain_id, $testrail_log_results)
    {
        TestRailApiWrapper::$testrail_username = $testrail_username;
        TestRailApiWrapper::$testrail_password = $testrail_password;
        TestRailApiWrapper::$testrail_url = $testrail_url;
        TestRailApiWrapper::$testrail_log_results = $testrail_log_results;
        TestRailApiWrapper::$testrail_testplan_id = $testrail_testplain_id;
        TestRailApiWrapper::$testrail_project_id = $testrail_project_id;
        TestRailApiWrapper::$testrail_testrun_name = $testrail_testrun_name . " " . time();
        TestRailApiWrapper::$testrail_testrun_description = $testrail_testrun_description;

        TestRailApiWrapper::$testrail_context = new TestRailAPIClient(TestRailApiWrapper::$testrail_url);
        TestRailApiWrapper::$testrail_context->set_user(TestRailApiWrapper::$testrail_username);
        TestRailApiWrapper::$testrail_context->set_password(TestRailApiWrapper::$testrail_password);

    }

    /**
     *
     */
    public static function create_new_testrun()
    {
        # Build dict
        $data = array(
            "suite_id" => TestRailApiWrapper::$testrail_testplan_id,
            "name" => TestRailApiWrapper::$testrail_testrun_name,
            "description" => TestRailApiWrapper::$testrail_testrun_description,
            "include_all" => true);
        $response = TestRailApiWrapper::$testrail_context->send_post("add_run/" . TestRailApiWrapper::$testrail_project_id, $data);
        TestRailApiWrapper::$testrail_testrun_id = $response["id"];
    }

    /**
     *
     */
    public static function set_testrun($testrun_id)
    {
        TestRailApiWrapper::$testrail_testrun_id = $testrun_id;
    }

    /**
     * @param $testcase_id
     * @param $status_id
     * @param $defect_description
     */
    public static function log_testcase_result($testcase_id, $status_id, $defect_description)
    {
        if ($status_id == "passed") {
            $status_id_code = 1;
        } elseif ($status_id == "blocked") {
            $status_id_code = 2;
        } elseif ($status_id == "untested") {
            $status_id_code = 3;
        } elseif ($status_id == "retest") {
            $status_id_code = 4;
        } elseif ($status_id == "failed") {
            $status_id_code = 5;
        } else $status_id_code = 4;

        # Build dict
        $data = array(
            "results" => [array(
                "case_id" => $testcase_id,
                "status_id" => $status_id_code,
                "comment" => $defect_description,
                "defects" => "")]);
        $response = TestRailApiWrapper::$testrail_context->send_post("add_results_for_cases/" . TestRailApiWrapper::$testrail_testrun_id, $data);
    }

    private static $testrail_context;

    private static $testrail_username;

    private static $testrail_password;

    private static $testrail_url;

    private static $testrail_log_results;

    private static $testrail_testplan_id;

    private static $testrail_testrun_id;

    private static $testrail_project_id;

    private static $testrail_testrun_name;

    private static $testrail_testrun_description;
}
