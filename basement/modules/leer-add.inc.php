<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - add funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["leer"]["right"] == "" || $rechte[$cfg["leer"]["right"]] == -1 ) {

        // page basics
        // ***

        #if ( count($HTTP_POST_VARS) == 0 ) {
        #} else {
            $form_values = $HTTP_POST_VARS;
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["leer"]["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = "";
        $element["extension2"] = "";

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["leer"]["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["leer"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["extension1"] != ""
                || $HTTP_POST_VARS["extension2"] != "" ) ) {

            // form eigaben pr�fen
            form_errors( $form_options, $HTTP_POST_VARS );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send", "avail" );
                foreach($HTTP_POST_VARS as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= " ".$name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$sqla .= ", pass";
                #$sqlb .= ", password('".$checked_password."')";

                $sql = "insert into ".$cfg["leer"]["db"]["leer"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["leer"]["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
