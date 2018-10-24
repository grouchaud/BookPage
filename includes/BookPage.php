<?php

use GroupsPage\GroupsPageCore;

class BookPage
{
    // Répertorions des fonctions de rappel "render callbacks" avec l'analyseur syntaxique.
    public static function onParserSetup(&$parser)
    {
        // Créer une fonction hook associé au mot magique "example" avec renderExample()
        $parser->setFunctionHook('BookPageButton', array(
            'BookPage',
            'addBookButtonParser'
        ));
    }

    public static function addBookButtonParser($input, $name)
    {
        $title = $input->getTitle();
        $out = '<li id="ca-bookpage"><a href="/w/index.php/'. $title->getText() .'?action=bookpage&amp;format=single">BookPage</a></li>';
        return array(
            $out,
            'noparse' => true,
            'isHTML' => true
        );
    }

    // Voir le résultat de {{#example:}}.
    public static function onUnknownAction($action, $article)
    {
        global $wgOut, $wgUser, $wgRequest, $wgPdfBookExportRequestDownload;
        global $wgUploadDirectory, $wgPdfBookExportErrorLog;
        
        
        if ($action == "savepage"){

        }
        if ($action == "cancelpage"){
            echo "cancel";
            $title = $article->getTitle();
            $name = $title->getBaseText();
            
            $save = 'images/books/save'.$pageTitle->getBaseText();
            $handle = fopen($save, 'r') or die('Cannot open file:  ' . $save);
            $edit = fread($handle, filesize($save));
           
            $wikiPage = new WikiPage($title);
            $pageContent = ContentHandler::makeContent($edit, $pageTitle);
            $wikiPage->doEditContent($pageContent, "Page Edited by Special BookPage", EDIT_UPDATE);
            $save = 'images/books/save'.$pageTitle->getBaseText();
            header("Refresh:0;");
        }
        if ($action == "bookpage") {
     
            $title = $article->getTitle();
            $name = $title->getBaseText();
            echo $name;
            $bookTitle = Title::newFromText($name, NS_BOOK);
            if ($bookTitle->isKnown()) {
                // REMPLACER PAR SPECIALPAGE REORDER QUAND TERMINEE
                header('Location: http://dokit/w/index.php/Special:BookPage?groupName='.$bookTitle->getBaseText());
                exit();
            } else {
                $newWikiPage = new WikiPage( $bookTitle );
                $bookSummary = "";
                $arr = GroupsPageCore::getInstance()->getMemberPages($title);
                
                foreach($arr as $titles){
                    $bookSummary.="*".$titles->getText()."\n";                        
                }
                
                $pageContent = ContentHandler::makeContent( $bookSummary, $bookTitle );
                $newWikiPage->doEditContent( $pageContent,
                    "Page created automatically by BookPage function" );
                header('Location: http://dokit/w/index.php/Special:BookPage?groupName='.$bookTitle->getBaseText());
                exit();
                
            }
        }
    }
}