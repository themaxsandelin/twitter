<?php

define("SERVER", "localhost");
define("USERNAME", "root");
define("PASSWORD", "root");
define("DATABASE", "twitter");
define("DSN", "mysql:host=".SERVER.";dbname=".DATABASE);

function DBConnect () {
    $con = new PDO(DSN, USERNAME, PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $con;
}

function getDBContent ($query) {
    try {
        $con = DBConnect();
        $stmt = $con->prepare($query);
        $stmt->execute();
        $array = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array[] = $row;
        }
        if ($array !== "") {
            return $array;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        print 'EROROR: ' . $e->getMessage();
    }
}

function updateDBContent ($query, $array) {
    try {
        $con = DBConnect();
        $stmt = $con->prepare($query);
        $i = 1;
        foreach ($array as $insert) {
            $stmt->bindValue($i++, $insert, PDO::PARAM_STR);
        }
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        print 'EROROR: ' . $e->getMessage();
    }
}
