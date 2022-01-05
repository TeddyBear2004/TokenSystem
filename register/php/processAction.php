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
                die("Es gab einen Fehler bei der Verbindung zur Datenbank.");


            if ($row = $result->fetch_row()) {
                $uuid = username_to_uuid($name);
                if ($uuid == "") {
                    echo "Fehler: Der Minecraft Name wurde nicht gefunden.";

                } else {
                    $uuid = substr_replace($uuid, "-", 8, 0);
                    $uuid = substr_replace($uuid, "-", 13, 0);
                    $uuid = substr_replace($uuid, "-", 18, 0);
                    $uuid = substr_replace($uuid, "-", 23, 0);

                    $query = "SELECT 1 FROM bansystem_whitelist WHERE player_id = '" . $uuid . "'";
                    if (!($result = $con->query($query)))
                        die("Es gab einen Fehler bei der Verbindung zur Datenbank.");

                    if (!$result->fetch_row()) {
                        $query = sprintf("INSERT INTO bansystem_whitelist (token_id, player_id) VALUE ('%s', '%s')", $row[0], $uuid);

                        if (!($con->query($query)))
                            die("Es gab einen Fehler bei der Verbindung zur Datenbank.");
                        else {
                            echo sprintf("Erfolg: %s wurde gewhitelisted.", $name);

                            $query = "INSERT INTO bansystem_player (uuid, name) VALUE ('%s', '%s')" . sprintf($uuid, $name);
                            $con->query($query);
                        }
                    } else {
                        echo "Du bist bereits gewhitelisted.";
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

