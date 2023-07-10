<?php

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

//Get Result Without Filter
        echo "Get Result Without Filter<pre>";
        foreach ($this->getQueryWithoutFilter()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithoutFilter()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithoutFilter()->num_rows);
        echo"</pre><br><br><br><br><br>";

        //Get Result With Filter
        echo "Get Result With Filter - Specify Equality Condition<pre>";
        foreach ($this->getQueryWithFilter()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithFilter()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithFilter()->num_rows);
        echo"</pre><br><br><br><br><br>";

        //Get Result With Filter2
        echo "Get Result With Filter 2 - Specify Conditions Using Query Operators<pre>";
        foreach ($this->getQueryWithFilter2()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithFilter2()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithFilter2()->num_rows);
        echo"</pre><br><br><br><br><br>";

        //Get Result With Filter 3
        echo "Get Result With Filter 3 - Specify AND Conditions<pre>";
        foreach ($this->getQueryWithFilter3()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithFilter3()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithFilter3()->num_rows);
        echo"</pre><br><br><br><br><br>";

        //Get Result With Filter 4
        echo "Get Result With Filter 4 - Specify OR Conditions<pre>";
        foreach ($this->getQueryWithFilter4()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithFilter4()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithFilter4()->num_rows);
        echo"</pre><br><br><br><br><br>";

        //Get Result With Filter 5
        echo "Get Result With Filter 5 - Specify AND as well as OR Conditions<pre>";
        foreach ($this->getQueryWithFilter5()->rows as $key => $value):
            print_r($value);
        endforeach;
        echo"</pre>";

        echo "<pre>";
        foreach ($this->getQueryWithFilter5()->row as $key => $value):
            print_r('[' . $key . '] => ' . $value . "<br>");
        endforeach;
        echo"</pre>";
        echo "<pre>";
        print_r("Number of Rows: " . $this->getQueryWithFilter5()->num_rows);
        echo"</pre><br><br><br><br><br>";
    }

    public function getQueryWithFilter5() {

        //corresponds to the following SQL statement: SELECT * FROM inventory WHERE status = "A" AND ( qty < 30 OR description LIKE "Mongo%")
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"(testdb)


        $filter = [
            'title' => 'MongoDb',
            '$or' => [
                ['qty' => ['$lt' => 30]],
                // Alternatively: ['item' => new \MongoDB\BSON\Regex('^p')]
                ['description' => ['$regex' => '^Mongo']],
            ],
        ];

        return $this->registry->MongoDb->query('testdb', $filter);
    }

    public function getQueryWithFilter4() {

        //corresponds to the following SQL statement: SELECT * FROM inventory WHERE status = "A" OR qty < 30
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"(testdb)

        $filter = ['$or' => [['title' => 'MongoDb'], ['qty' => ['$lt' => 30]],],];

        return $this->registry->MongoDb->query('testdb', $filter);
    }

    public function getQueryWithFilter3() {

        //corresponds to the following SQL statement: SELECT * FROM inventory WHERE status = "A" AND qty < 30
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"(testdb)

        $filter = [
            'title' => 'MongoDb',
            'qty' => ['$lt' => 30],
        ];

        return $this->registry->MongoDb->query('testdb', $filter);
    }

    public function getQueryWithFilter() {

        //corresponds to the following SQL statement: SELECT * FROM MongoDb WHERE title = "MongoDb"
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"(testdb)

        $filter = ['title' => "MongoDb"];

        return $this->registry->MongoDb->query('testdb', $filter);
    }

    public function getQueryWithFilter2() {

        //corresponds to the following SQL statement: SELECT * FROM inventory WHERE status in ("A", "D")"
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"(testdb)

        $filter = ['title' => ['$in' => ['MongoDb', 'MongoDb2']]];

        return $this->registry->MongoDb->query('testdb', $filter);
    }

    public function getQueryWithoutFilter() {

        //corresponds to the following SQL statement: SELECT * FROM MongoDb
        //Differn with SQL Statment Need Collection corresponds to SQL "tableName"
        return $this->registry->MongoDb->query('testdb');
    }

}

new index($registry);
