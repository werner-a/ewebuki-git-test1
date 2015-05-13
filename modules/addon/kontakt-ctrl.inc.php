<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: kontakt-ctrl.inc.php 1131 2007-12-12 08:45:50Z chaot $";
  $Script["desc"] = "kontakt - kontroll funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2010 Werner Ammon ( wa<at>chaos.de )

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    // warnung ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    // path fuer die schaltflaechen anpassen
    if ( $cfg["kontakt"]["iconpath"] == "" ) $cfg["kontakt"]["iconpath"] = "/images/default/";

    // label bearbeitung aktivieren
    if ( isset($_GET["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // private function include loader
    if ( is_array($cfg["kontakt"]["function"][$environment["kategorie"]]) ) include $pathvars["moduleroot"].$cfg["kontakt"]["subdir"]."/".$cfg["kontakt"]["name"]."-functions.inc.php";

    // shared function include loader
    if ( is_array($cfg["kontakt"]["function"][$environment["kategorie"].",shared"]) ) {
        foreach ( $cfg["kontakt"]["function"][$environment["kategorie"].",shared"] as $value ) {
            include $pathvars["moduleroot"]."libraries/function_".$value.".inc.php";
        }
    }

    // global function include loader
    if ( is_array($cfg["kontakt"]["function"][$environment["kategorie"].",global"]) ) {
        foreach ( $cfg["kontakt"]["function"][$environment["kategorie"].",global"] as $value ) {
            include $pathvars["basicroot"]."libraries/function_".$value.".inc.php";
        }
    }

    // magic include loader
    if ( array_key_exists($environment["kategorie"], $cfg["kontakt"]["function"]) ) {
        include $pathvars["moduleroot"].$cfg["kontakt"]["subdir"]."/".$cfg["kontakt"]["name"]."-".$environment["kategorie"].".inc.php";
    } else {
        include $pathvars["moduleroot"].$cfg["kontakt"]["subdir"]."/".$cfg["kontakt"]["name"].".inc.php";
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>