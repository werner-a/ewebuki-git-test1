<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-add.inc.php,v 1.1 2005/03/25 19:37:28 chaot Exp $";
// "leer - add funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    if ( $rechte[$cfg["right"]] == "" || $rechte[$cfg["right"]] == -1 ) {

        // page basics
        // ***

        #if ( count($HTTP_POST_VARS) == 0 ) {
        #} else {
            $form_values = $HTTP_POST_VARS;
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["bloged"]["entries"], $form_values );

        // form elemente erweitern
        $element["name"] = "<input type=\"text\" maxlength=\"40\" class=\"\" name=\"name\"  value=\"\"";
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
        $ausgaben["form_aktion"] = $cfg["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

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

                #$kick = array( "PHPSESSID", "form_referer", "send", "avail", "content", "name" );
                #foreach($HTTP_POST_VARS as $name => $value) {
                #    if ( !in_array($name,$kick) ) {
                #        if ( $sqla != "" ) $sqla .= ",";
                #        $sqla .= " ".$name;
                #        if ( $sqlb != "" ) $sqlb .= ",";
                #        $sqlb .= " '".$value."'";
                #    }
                #}

                // Sql um spezielle Felder erweitern
                #$sqla .= ", pass";
                #$sqlb .= ", password('".$checked_password."')";

                function create( $number ) {
                global $cfg, $db, $header, $debugging, $HTTP_POST_VARS;

                $sqla  = "lang";
                $sqlb  = "'de'";

                $sqla .= ", label";
                $sqlb .= ", 'inhalt'";

                $sqla .= ", tname";
                $sqlb .= ", '1692582295.".$number."'";

                $sqla .= ", crc32";
                $sqlb .= ", '-1'";

                $sqla .= ", ebene";
                $sqlb .= ", '/blog'";

                $sqla .= ", kategorie";
                $sqlb .= ", '".$number."'";


                if ( $HTTP_POST_VARS["content"] == "" ) {
                    $content  = "[!]".date("Y-m-d G:i:s")."[/!]\n";
                    $content .= "[H1]".sprintf("%06d",$number).". Eintrag[/H1]\n";
                    $content .= "[P=teaser]".$number.". Teaser zum Thema[/P]\n";
                    $content .= "[H2]".$number.". Unter�berschrift[/H2]\n";
                    $content .= "[P]".$number.". Textinhalt ohne Ende[/P]\n";
                } else {
                    $content  = "[!]".date("Y-m-d G:i:s")."[/!]\n";
                    $content .= $HTTP_POST_VARS["content"];
                }

                $sqla .= ", content";
                $sqlb .= ", '".$content."'";

                $sql = "insert into ".$cfg["db"]["bloged"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["basis"]."/list.html";

                }

                if ( $HTTP_POST_VARS["name"] == "make" ) {
                    for ( $i = 1; $i <= 1000; $i++ ) {
                        create($i);
                    }
                } else {
                    create($HTTP_POST_VARS["name"]);
                }

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