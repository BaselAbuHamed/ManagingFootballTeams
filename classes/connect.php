<?php

$dbhost = 'localhost:3307';
$dbusername = 'root';
$dbuserpassword = '';
$default_dbname = 'football_db';
$MYSQL_ERRNO = '';
$MYSQL_ERROR = '';

    function db_connect($dbname = '', $username = '', $password = ''){
        global $dbhost, $dbusername, $dbuserpassword, $default_dbname;
        global $MYSQL_ERRNO, $MYSQL_ERROR;
    
        try {
    
            if (empty($dbname)) {
                $dbname = $default_dbname;
            }
    
            /*
            * Create the pdo object
            * host: is the host name
            * dbname: database name
            */
    
            $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbuserpassword);
    
            return $pdo;
    
        } catch (PDOException $e) {
            $MYSQL_ERRNO = 0;
            $MYSQL_ERROR = $e->getMessage();
            return 0;
        }
    }
    
    function sql_error() {
       global $MYSQL_ERRNO, $MYSQL_ERROR;
    
       if(empty($MYSQL_ERROR)) {
          $MYSQL_ERRNO = mysql_errno();
          $MYSQL_ERROR = mysql_error();
       }
       return "$MYSQL_ERRNO: $MYSQL_ERROR";
    }
    
    function error_message($msg) {
       html_header();
       echo "<SCRIPT>alert(\"Error: $msg\");history.go(-1)</SCRIPT>";
       html_footer();
       exit;
    }
    
    /**
     * @param $field
     * @param $pdo PDO
     * @return array|int
     */
    function enum_options($field, $pdo) {
    
        $query = "SHOW COLUMNS FROM user LIKE '". $field ."'";
        $result = $pdo->query($query);
        $query_data = $result->fetch();
    
        $match = [];
    
        if (preg_match("('.*')", $query_data["Type"], $match)) {
            $enum_str = str_replace("'", "", $match[0]);
            $enum_options = explode(',', $enum_str);
            return $enum_options;
        }
    
        return 0;
    }
