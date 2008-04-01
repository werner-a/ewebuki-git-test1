<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menu-convert.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    function make_id($url) {
        global $db;
        $leer[] = "";
        $test = split("/",$url);
        $cleaned_up = array_diff($test, $leer);

        $data["mid"] = 0;
        foreach ( $cleaned_up as $value ) {
            $sql = "SELECT *
                      FROM site_menu
                     WHERE entry = '".$value."'
                       AND refid = ".$data["mid"];
            $result = $db -> query($sql);
            if ( $db -> num_rows($result) == 1 ) {
                $data = $db -> fetch_array($result,1);
            } else {
                break;
            }
        }
        return $data["mid"];
    }

    function make_ebene($mid, $ebene="") {
        # call: make_ebene(refid);
        global $db, $cfg;
        $sql = "SELECT refid, entry
                FROM site_menu
                WHERE mid='".$mid."'";
        $result = $db -> query($sql);
        $array = $db -> fetch_array($result,$nop);
        $ebene = "/".$array["entry"].$ebene;
        if ( $array["refid"] != 0 ) {
            $ebene = make_ebene($array["refid"],$ebene);
        }
        return $ebene;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>