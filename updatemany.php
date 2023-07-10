<?php

// Configuration
if (is_file('config.php')) {
    require_once('config.php');
}
/*
 * WebSiteToYou License
 * Each line should be prefixed with  * 
 */
// Error Reporting
error_reporting(E_ALL);

// Check Version
if (version_compare(phpversion(), '8.0.0', '<')) {
    exit('PHP8+ Required');
}

if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];

    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check if SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
    $_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $_SERVER['HTTPS'] = true;
} else {
    $_SERVER['HTTPS'] = false;
}

// Check IP if forwarded IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];
}

// WBS Autoloader
require_once(DIR_SYSTEM . 'library/autoloader.php');
$autoloader = new \WebSiteToYou\System\Library\Autoloader();
$autoloader->register('WebSiteToYou\\' . APPLICATION, DIR_APPLICATION);
$autoloader->register('WebSiteToYou\System', DIR_SYSTEM);

require_once(DIR_SYSTEM . 'vendor.php');

// Registry
$registry = new \WebSiteToYou\System\Library\Registry();
$registry->set('autoloader', $autoloader);

//MongoDb
$MongoDb = new \WebSiteToYou\System\Library\DB(DRIVER, HOSTNAME, DATABASE, USERNAME, PASSWORD,PORT);
$registry->set('MongoDb', $MongoDb);

class index {

    protected $registry;

    public function __construct(\WebSiteToYou\System\Library\Registry $registry) {
        $this->registry = $registry;
        $this->index();
    }

    public function index() {

        print_r($this->update());
    }

    public function update() {


        $where = ['qty' => ['$lt' => 30]];
        $update = [
            '$set' => ['title' => 'MongoDbMany44'],
            '$currentDate' => ['lastModified' => true]
        ];

        return $this->registry->MongoDb->updateMany('testdb', $where, $update);
    }

}

new index($registry);
