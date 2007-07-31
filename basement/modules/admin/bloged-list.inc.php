<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-sub.inc.php,v 1.1.1.1 2003/10/06 19:12:23 chaot Exp $";
// "short description";
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

        // funktions bereich
        // ***
        $sql = "SELECT *
                  FROM ".$cfg["db"]["bloged"]["entries"]."
                 WHERE ".$cfg["db"]["bloged"]["key"]." LIKE '1692582295.%'
              ORDER BY ".$cfg["db"]["bloged"]["order"];                 ;
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["db"]["bloged"]["rows"], $parameter, 1, 4, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];


        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            $preg = "|\[[^\]]+\](.*)\[/[^\]]+\]|U";
            preg_match_all($preg, $data["content"], $match, PREG_PATTERN_ORDER );
            #$debugging["ausgabe"] .= "<pre>".print_r($match,True)."</pre>";

            // tabellen farben wechseln
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }
            $dataloop["list"][$match[1][1]]["color"] = $cfg["color"]["set"];

            $dataloop["list"][$match[1][1]]["teaser"] = $match[1][1];

            $dt = $match[1][0];
            $dtn = sprintf("%02d.%02d.%04d %02d:%02d ",
                         substr($dt, 8, 2),
                         substr($dt, 5, 2),
                         substr($dt, 0, 4),
                         substr($dt, 11, 2),
                         substr($dt, 14, 2));
            #list($jahr, $monat, $tag) = explode("-", $datum);

            $dataloop["list"][$match[1][1]]["date"] = $dtn;


            #http://dev0/auth/cms/edit,develop,1692582295.3,inhalt.html

            #$dataloop["list"][$match[1][1]]["editlink"] = $cfg["basis"]."/edit,".$data["id"].".html";
            $dataloop["list"][$match[1][1]]["editlink"] = $pathvars["virtual"]."/cms/edit,develop,".$data["tname"].",inhalt.html";
            $dataloop["list"][$match[1][1]]["edittitel"] = "#(edittitel)";

            #$dataloop["list"][$match[1][1]]["deletelink"] = $cfg["basis"]."/delete,".$data["id"].".html";
            $dataloop["list"][$match[1][1]]["deletelink"] = $cfg["basis"]."/delete,".$data["tname"].".html";
            $dataloop["list"][$match[1][1]]["deletetitel"] = "#(deletetitel)";

        }
        #echo sprintf("<pre>%s</pre>",print_r($dataloop,True));
        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (edittitel) #(edittitel)<br />";
            $ausgaben["inaccessible"] .= "# (deletetitel) #(deletetitel)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>