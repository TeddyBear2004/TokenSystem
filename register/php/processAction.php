<?php
include "db.php";
include "mc-api.php";

function filterSQLInjection($in)
{
    $in2 = (string)$in;
    $in2 = str_replace("'", "", $in2);
    $in2 = str_replace('"', "", $in2);
    return $in2;
}

function whitelist()
{
    if (isset($_GET["name"], $_GET["token"])) {
        $name = filterSQLInjection($_GET["name"]);
        $token = filterSQLInjection($_GET["token"]);

        if ($name == "" || $token == "") {
            echo "Fehler: Du hast entweder den Token oder den Namen nicht ausgefüllt.";
            exit();
        } else {

            $con = getCon();

            $query = sprintf("SELECT token_id "
                . "FROM bansystem_token "
                . "WHERE bansystem_token.token_id "
                . "NOT IN (SELECT bansystem_whitelist.token_id FROM bansystem_whitelist) "
                . "AND token = '%s'", $token);

            if (!($result = $con->query($query)))
                die($con->error);


            if ($row = $result->fetch_row()) {
                $uuid = username_to_uuid($name);
                if ($uuid == "")
                    echo "Fehler: Der Minecraft Name wurde nicht gefunden.";

                else {
                    $uuid = substr_replace($uuid, "-", 8, 0);
                    $uuid = substr_replace($uuid, "-", 11, 0);
                    $uuid = substr_replace($uuid, "-", 18, 0);
                    $uuid = substr_replace($uuid, "-", 23, 0);

                    $query = sprintf("INSERT INTO bansystem_whitelist (token_id, player_id) VALUE ('%s', '%s')", $row[0], $uuid);

                    if (!($con->query($query)))
                        die($con->error);
                    else {
                        echo sprintf("Erfolg: %s (UUID: %s) wurde gewhitelisted.", $name, $uuid);
                    }
                }
            } else
                echo "Fehler: Der Token den du angegeben hast ist ungültig.";
        }
    } else
        echo "Fehler: Name oder Token nicht angegeben";
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    whitelist();
} else
    echo "Scheinbar gab es ein Problem bei der Übertragung.";

