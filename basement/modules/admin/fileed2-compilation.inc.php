<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - list funktion";
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

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich
        // ***

        // funktion zum sortieren der bildern
        // vorlaeufig hier abgelegt
        function _compare($a, $b) {
            return ($a["sort"] < $b["sort"]) ? -1 : 1;
        }

        // dropdown bauen lassen
        $dataloop["groups"] = compilationlist($environment["parameter"][1]);

        if ( is_numeric($_GET["compID"]) ){
            // umwandeln der GET-Variable in environment-parameter
            $header = $cfg["basis"]."/compilation,".$_GET["compID"].".html";
            header("Location: ".$header);
        } elseif ( !isset($environment["parameter"][1]) ){
            // falls weder GET-Variable noch environment-parameter gesetzt, wird zur ersten compilation gesprungen
            $buffer = current($dataloop["groups"]);
            $header = $cfg["basis"]."/compilation,".$buffer["id"].".html";
            header("Location: ".$header);
        }

        // TODO: hier kommt die schnellsuche hin
        $ausgaben["search"] = "";

        // vor- und zurueck-links
        $vor = "";
        $zurueck = "";
        $aktuell = "";
        $i = 0;
        foreach ( $dataloop["groups"] as $value ){
            if ( $aktuell != "" ){
                $vor = $value["id"];
                break;
            }
            if ( $value["id"] == $environment["parameter"][1] ){
                $aktuell = $environment["parameter"][1];
                $ausgaben["compilation"] = "#".$value["id"].": ".$value["name"];
            }
            if ( $aktuell == "" ) {
                $zurueck = $value["id"];
            }
            $i++;
        }
        $ausgaben["result"] = count($dataloop["groups"]);
        if ( $vor != "" ){
            $hidedata["vor"]["link"] = $cfg["basis"]."/compilation,".$vor.".html";
        }
        if ( $zurueck != "" ){
            $hidedata["zurueck"]["link"] = $cfg["basis"]."/compilation,".$zurueck.".html";
        }
        // bei der wievielten compilation stehen wir
        $ausgaben["aktuell"] = $i;
        // wieviele compilation gibt es

        // dataloop mit den bildern, file-dataloop wird gebaut
        $sql = "SELECT *
                  FROM site_file
                 WHERE fhit
                  LIKE '%#p".$environment["parameter"][1]."%' ORDER BY fid";
        $result = $db -> query($sql);

        // dataloop wird ueber eine share-funktion aufgebaut
        filelist($result,$environment["parameter"][1]);
        // sortieren der bilder mittels uasort-funktion (s.o.)
        if ( count($dataloop["list"]) > 0 ) {
            uasort($dataloop["list"],"_compare");
        }
        // anzahl der enthaltenen bilder
        $ausgaben["count"]  = count($dataloop["list"]);

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/compilation.html";
        $ausgaben["form_break"]  = $cfg["basis"]."/list.html";
        $ausgaben["edit"]        = $cfg["basis"]."/collect,".$environment["parameter"][1].".html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["path"] = str_replace($pathvars["virtual"],"",$cfg["basis"]);
        $mapping["main"] = crc32($cfg["path"]).".compilation";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (img_plural) #(img_plural)<br />";
            $ausgaben["inaccessible"] .= "# (img_sing) #(img_sing)<br />";

            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes_sing) #(answerc_yes_sing)<br />";
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