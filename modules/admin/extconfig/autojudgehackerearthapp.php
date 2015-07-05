<?php

/* ========================================================================
 * Open eClass
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2014  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ========================================================================
 */

require_once 'autojudgeapp.php';
require_once 'include/simplehtmldom/simple_html_dom.php';

class AutojudgeHackerearthApp extends AutojudgeApp {
    public function compile(AutoJudgeConnectorInput $input) {
        //set POST variables
        $url           = 'http://api.hackerearth.com/code/run/';
        $fields_string = null;
        $fields        = array(
            'client_secret' => ExtAppManager::getApp(get_class($this))->getParam('key')->value(),
            'input'         => $input->input,
            'source'        => urlencode($input->code),
            'lang'          => $input->lang,
        );

        // url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        // Remove last '&' character;
        rtrim($fields_string, '&');

        // Open curl connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // Execute post
        $result = json_decode(curl_exec($ch), true);
        // Close curl connection
        curl_close($ch);

        $output = new AutoJudgeConnectorResult();
        $output->compileStatus = $result['compile_status'];
        $output->output = trim($result['run_status']['output']);

        return $output;
    }

    public function getConfigFields() {
        return array(
            'key' => 'API Key',
        );
    }

    public function getServiceURL() {
        return 'hackerearth.com';
    }

    public function getSupportedLanguages() {
        return array(
            'C' => 'c',
            'CPP' => 'cpp',
            'CPP11' => 'cpp11',
            'CLOJURE' => 'clj',
            'CSHARP' => 'cs',
            'JAVA' => 'java',
            'JAVASCRIPT' => 'js',
            'HASKELL' => 'hs',
            'PERL' => 'pl',
            'PHP' => 'php',
            'PYTHON' => 'py',
            'RUBY' => 'rb',
        );
    }

    public function supportsInput() {
        return true;
    }
}