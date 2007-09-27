<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "contented - edit funktion";
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



    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }

    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    $db->selectDb($database,FALSE);



    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1
       || $rechte["administration"] == -1 || $erlaubnis == -1 ) {


        // page basics
        // ***

        if ( count($_POST) == 0 ) {

            #$sql = "SELECT *
            #          FROM ".$cfg["db"]["leer"]["entries"]."
            #         WHERE ".$cfg["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";

            $sql = "SELECT html, content, changed, byalias
                      FROM ". SITETEXT ."
                     WHERE tname='".$environment["parameter"][2]."'
                       AND lang='".$environment["language"]."'
                       AND label='".$environment["parameter"][3]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            #$data = $db -> fetch_array($result, $nop);
            $form_values = $db -> fetch_array($result,1);


        } else {
            $form_values = $_POST;
        }

        // form options holen
        #$form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        #$element = form_elements( $cfg["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        #$element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        #$element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // funktion_content.inc.php zeile 181,182 reicht nicht (mehr)
        // eine funktion die nicht aufgerufen wird f�llt auch die variablen nicht
        if ( $defaults["section"]["label"] == "" ) $defaults["section"]["label"] = "inhalt";
        if ( $defaults["section"]["tag"] == "" ) $defaults["section"]["tag"] = "[H";

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$_SESSION["ebene"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$_SESSION["kategorie"].$debugging["char"];



        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last edit: ".$_SESSION["cms_last_edit"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last ebene: ".$_SESSION["cms_last_ebene"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last kategorie: ".$_SESSION["cms_last_kategorie"].$debugging["char"];;

        if ( isset($_SESSION["cms_last_edit"]) && $_GET["referer"] != "" ) {
            unset($_SESSION["cms_last_edit"]);

            $_SESSION["ebene"] = $_SESSION["cms_last_ebene"];
            $_SESSION["kategorie"] = $_SESSION["cms_last_kategorie"];

            unset($_SESSION["cms_last_ebene"]);
            unset($_SESSION["cms_last_kategorie"]);
        }

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "neue ebene    : ".$_SESSION["ebene"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "neue kategorie: ".$_SESSION["kategorie"].$debugging["char"];;



        // status anzeigen
        $ausgaben["ce_tem_db"]      = "#(db): ".$environment["parameter"][1];
        $ausgaben["ce_tem_name"]    = "#(template): ".$environment["parameter"][2];
        $ausgaben["ce_tem_label"]   = "#(label): ".$environment["parameter"][3];
        # $environment["parameter"][4] -> abschnitt bearbeiten -> war: datensatz in db gefunden

        $ausgaben["ce_tem_lang"]    = "#(language): ".$environment["language"];
        $ausgaben["ce_tem_convert"] = "#(convert): ".$environment["parameter"][5];


        // lock erzeugen, anzeigen
        if ( strstr($form_values["byalias"],"!") ) {
            $ausgaben["lock"] .= "lock by ".substr($form_values["byalias"],1)." @ ".$form_values["changed"];
            $ausgaben["class"] = "ta_lock";
        } else {
            $sql = "UPDATE ". SITETEXT ." set
                            byalias = '!".$_SESSION["alias"]."'
                        WHERE tname = '".$environment["parameter"][2]."'
                        AND  lang = '".$environment["language"]."'
                        AND label = '".$environment["parameter"][3]."'";
            $result  = $db -> query($sql);
            $ausgaben["lock"] .= "lock by ".$_SESSION["alias"];
            $ausgaben["class"] = "ta_norm";
        }


        // eWeBuKi tag schutz - sections 1
        if ( strpos( $form_values["content"], "[/E]") !== false ) {
            $preg = "|\[E\](.*)\[/E\]|Us";
            preg_match_all($preg, $form_values["content"], $match, PREG_PATTERN_ORDER );
            $mark = $defaults["section"]["tag"];
            $hide = "++";
            foreach ( $match[0] as $key => $value ) {
                $escape = str_replace( $mark, $hide, $match[1][$key]);
                $form_values["content"] = str_replace( $value, "[E]".$escape."[/E]", $form_values["content"]);
            }
        }


        // evtl. spezielle section
        $alldata = explode($defaults["section"]["tag"], $form_values["content"]);
        if ( $environment["parameter"][4] != "" ) {
            $form_values["content"] = $defaults["section"]["tag"].$alldata[$environment["parameter"][4]];
        }


        // eWeBuKi tag schutz - sections 2
        $form_values["content"] = str_replace( $hide, $mark, $form_values["content"]);



        /*
        / wenn preview gedrueckt wird, hidedata erzeugen und $form_values["content"] aendern
        /
        / so funktioniert das ganze nicht
        / (es wird nie gespeichert -> "edit" anstatt "save" in der aktion url)
        / der extra parameter in der aktion url und
        / die if abfrage die den save verhindert
        / hat mir nicht gefallen!
        */
        if ( $HTTP_POST_VARS["PREVIEW"]  ){
            $hidedata["preview"]["content"] = "#(preview)";
            $preview = intelilink($HTTP_POST_VARS["content"]);
            $preview = tagreplace($preview);
            $hidedata["preview"]["content"] .= nlreplace($preview);
            $form_values["content"] = $HTTP_POST_VARS["content"];
        }



        // convert tag 2 html
        switch ( $environment["parameter"][5] ) {
            case "html":
                // content nach html wandeln
                $form_values["content"] = tagreplace($form_values["content"]);
                // intelligenten link tag bearbeiten
                $form_values["content"] = intelilink($form_values["content"]);
                // newlines nach br wandeln
                $form_values["content"] = nlreplace($form_values["content"]);
                // html db value aendern
                $form_values["html"] = -1;
                break;
            case "tag":
                // content nach cmstag wandeln
                ###
                // html db value aendern
                $form_values["html"] = 0;
                break;
            default:
                $form_values["html"] = 0;
        }


        // eWeBuKi tag schutz part 3
        $mark_o = array( "#(", "g(", "#{", "!#" );
        $hide_o = array( "-1-", "-2-", "-3-", "-4-" );
        $form_values["content"] = str_replace( $mark_o, $hide_o, $form_values["content"]);


        // wie wird content verarbeitet
        if ( $form_values["html"] == "-1" ) {
            $ausgaben["ce_name"] = "content";
            $ausgaben["ce_inhalt"] = $form_values["content"];

            // epoz fix
            if ( $specialvars["wysiwyg"] == "epoz" ) {
                $sea = array("\\","\n","\r","'");
                $rep = array("\\\\","\\n","\\r","\\'");
                $ausgaben["ce_inhalt"] = str_replace( $sea, $rep, $ausgaben["ce_inhalt"]);
            }

            // template version
            $art = "-".$specialvars["wysiwyg"];
        } else {
            // ce editor bauen

            $ausgaben["name"] = "content";
            $ausgaben["eventh2"] = "onsubmit=\"chk('content',1000);\"";
            $ausgaben["eventh2"] = "onKeyDown=\"count('content',1000);\" onChange=\"chk('content',1000);\"";
            $ausgaben["inhalt"] = $form_values["content"];


            $ausgaben["tn"] = makece("ceform", "content", $form_values["content"]);

            // template version
            $art = "";
        }



        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $HTTP_GET_VARS["referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
        }



        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        #$ausgaben["form_aktion"] = $cfg["basis"]."/edit,".$environment["parameter"][1].",verify.html";
        #$ausgaben["form_break"] = $cfg["basis"]."/list.html";

        #$ausgaben["form_aktion"] = $cfg["basis"]."edit/save,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].".html";
        $ausgaben["form_aktion"] = $cfg["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].",,verify.html";
        #$ausgaben["form_abbrechen"] = $_SESSION["page"];
        $ausgaben["form_break"] = $cfg["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].",,unlock.html";


        // hidden values
        #$ausgaben["form_hidden"] .= "";
        $ausgaben["form_hidden"] .= $form_values["html"];

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify".$art;
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (upload) #(upload)<br />";
            $ausgaben["inaccessible"] .= "# (file) #(file)<br />";
            $ausgaben["inaccessible"] .= "# (files) #(files)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a


        // +++
        // page basics
        if ( $environment["parameter"][6] == "unlock" ) {

            // nur lock aufheben
            $sql = "UPDATE ". SITETEXT ." set
                            byalias = '".$_SESSION["alias"]."'
                        WHERE tname = '".$environment["parameter"][2]."'
                        AND  lang = '".$environment["language"]."'
                        AND label = '".$environment["parameter"][3]."'";
            $result  = $db -> query($sql);
            header("Location: ".$_SESSION["page"]."");

        } elseif ( $environment["parameter"][6] == "verify"
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["add"] != ""
                || $HTTP_POST_VARS["upload"] != "" ) ) {


            // form eingaben pr�fen
            form_errors( $form_options, $HTTP_POST_VARS );


            // gibt es bereits content?
            $sql = "SELECT html, content
                      FROM ". SITETEXT ."
                     WHERE tname='".$environment["parameter"][2]."'
                       AND lang='".$environment["language"]."'
                       AND label='".$environment["parameter"][3]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result, $nop);
            $content_exist = $db -> num_rows($result);


            // evtl. spezielle section
            if ( $environment["parameter"][4] != "" ) {

                // eWeBuKi tag schutz - sections 1
                if ( strpos( $data["content"], "[/E]") !== false ) {
                    $preg = "|\[E\](.*)\[/E\]|Us";
                    preg_match_all($preg, $data["content"], $match, PREG_PATTERN_ORDER );
                    $mark = $defaults["section"]["tag"];
                    $hide = "++";
                    foreach ( $match[0] as $key => $value ) {
                        $escape = str_replace( $mark, $hide, $match[1][$key]);
                        $data["content"] = str_replace( $value, "[E]".$escape."[/E]", $data["content"]);
                    }
                }

                $allcontent = explode($defaults["section"]["tag"], addslashes($data["content"]) );
                $content = "";
                foreach ($allcontent as $key => $value) {
                    if ( $key == $environment["parameter"][4] ) {
                        $length = strlen( $defaults["section"]["tag"] );
                        if ( substr($HTTP_POST_VARS["content"],0,$length) == $defaults["section"]["tag"] ) {
                            $content .= $defaults["section"]["tag"].substr($HTTP_POST_VARS["content"],$length);
                        } else {
                            $content .= $HTTP_POST_VARS["content"];
                        }
                    } elseif ( $key > 0 ) {
                        $content .= $defaults["section"]["tag"].$value;
                    } else {
                        $content .= $value;
                    }

                // eWeBuKi tag schutz - sections 2
                $content = str_replace( $hide, $mark, $content );

                }
            } else {
                $content = $HTTP_POST_VARS["content"];
            }


            // html killer :)
            if ( $specialvars["denyhtml"] == -1 ) {
                $content = strip_tags($content);
            }


            // space killer
            if ( $specialvars["denyspace"] == -1 ) {
                $pattern = "  +";
                while ( preg_match("/".$pattern."/", $content, $tag) ) {
                    $content = str_replace($tag[0]," ",$content);
                }
            }




            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {


                if ( $content_exist == 1 ) {
                    if ( $environment["parameter"][4] == "" && $HTTP_POST_VARS["content"] == "" ) {
                        $sql = "DELETE FROM ". SITETEXT ."
                                    WHERE  label ='".$environment["parameter"][3]."'
                                        AND  tname ='".$environment["parameter"][2]."'
                                        AND  lang = '".$environment["language"]."'";
                    } else {
                        $sql = "UPDATE ". SITETEXT ." set
                                        content = '".$content."',
                                        crc32 = '".$specialvars["crc32"]."',
                                        html = '".$HTTP_POST_VARS["html"]."',
                                        ebene = '".$_SESSION["ebene"]."',
                                    kategorie = '".$_SESSION["kategorie"]."',
                                        changed = '".date("Y-m-d H:i:s")."',
                                    bysurname = '".$_SESSION["surname"]."',
                                    byforename = '".$_SESSION["forename"]."',
                                        byemail = '".$_SESSION["email"]."',
                                        byalias = '".$_SESSION["alias"]."'
                                WHERE  label = '".$environment["parameter"][3]."'
                                AND  tname = '".$environment["parameter"][2]."'
                                AND  lang = '".$environment["language"]."'";
                    }
                } else {
                    $sql = "INSERT INTO ". SITETEXT ."
                                        (lang, crc32, label,
                                        tname, ebene, kategorie,
                                        html, content,
                                        changed, bysurname, byforename, byemail, byalias)
                                VALUES ( '".$environment["language"]."',
                                        '".$specialvars["crc32"]."',
                                        '".$environment["parameter"][3]."',
                                        '".$environment["parameter"][2]."',
                                        '".$_SESSION["ebene"]."',
                                        '".$_SESSION["kategorie"]."',
                                        '".$HTTP_POST_VARS["html"]."',
                                        '".$content."',
                                        '".date("Y-m-d H:i:s")."',
                                        '".$_SESSION["surname"]."',
                                        '".$_SESSION["forename"]."',
                                        '".$_SESSION["email"]."',
                                        '".$_SESSION["alias"]."')";
                }


//                 $kick = array( "PHPSESSID", "form_referer", "send" );
//                 foreach($HTTP_POST_VARS as $name => $value) {
//                     if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
//                         if ( $sqla != "" ) $sqla .= ", ";
//                         $sqla .= $name."='".$value."'";
//                     }
//                 }

                // Sql um spezielle Felder erweitern
                #$ldate = $HTTP_POST_VARS["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

//                 $sql = "update ".$cfg["db"]["leer"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                #if ( !$result ) die($db -> error("DB ERROR: "));
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                if ( $HTTP_POST_VARS["add"] || $HTTP_POST_VARS["upload"] > 0 ) {

                    $_SESSION["cms_last_edit"] = str_replace("save,", "edit,", $pathvars["requested"]);
                    $_SESSION["cms_last_referer"] = $ausgaben["form_referer"];
                    $_SESSION["cms_last_ebene"] = $_SESSION["ebene"];
                    $_SESSION["cms_last_kategorie"] = $_SESSION["kategorie"];

                    if ( $HTTP_POST_VARS["upload"] > 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/upload.html?anzahl=".$HTTP_POST_VARS["upload"]);
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }

                } else {
                    header("Location: ".$ausgaben["form_referer"]."");
                }

#                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>