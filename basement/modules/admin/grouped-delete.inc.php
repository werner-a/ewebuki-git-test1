<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "grouped - delete funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        // ausgaben variablen bauen
        $sql = "SELECT * FROM ".$cfg["db"]["group"]["entries"]." WHERE ".$cfg["db"]["group"]["key"]."='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $field = $db -> fetch_array($result,$nop);
        foreach($field as $name => $value) {
            $ausgaben[$name] = $value;
        }

        // +++
        // funktions bereich fuer erweiterungen

        if ( $field["members"] != "" ) {
            $ausgaben["members"] = "";
            $hidedata["members"]["on"] = "on";

            $readMembers = explode(":",$field["members"]);
            foreach ( $readMembers as $value ) {
                $sql = "SELECT *
                          FROM ".$cfg["db"]["user"]["entries"]."
                         WHERE ".$cfg["db"]["user"]["key"]."='".$value."'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,$nop);

                ( $ausgaben["members"] == "" ) ? $trenner = "" : $trenner = ", ";
                $ausgaben["members"] .= $trenner.$data[$cfg["db"]["user"]["order"]];
            }
        }

        // funktions bereich fuer erweiterungen
        // ***

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/delete,".$environment["parameter"][1].".html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = "true";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result1) #(error_result1)<br />";
            $ausgaben["inaccessible"] .= "# (error_result2) #(error_result2)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        #n/a

        // +++
        // page basics


        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if ( $HTTP_POST_VARS["send"] != "" ) {

                // funktions bereich fuer erweiterungen
                // ***

                // +++
                // funktions bereich fuer erweiterungen

            // datensatz loeschen
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "DELETE FROM ".$cfg["db"]["group"]["entries"]."
                                WHERE ".$cfg["db"]["group"]["key"]."='".$environment["parameter"][1]."';";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
            }

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["basis"]."/list.html");
            }
            // +++
            // das loeschen wurde bestaetigt, loeschen!
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
