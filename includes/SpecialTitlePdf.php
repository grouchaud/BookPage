<?php

class SpecialTitlePdf extends SpecialPage
{

    public function __construct()
    {
        parent::__construct('TitlePdf');
    }

    function execute($par)
    {
        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();

        // Déclaration de mes variables
        $group = $request->getText('group');
        $titre = $request->getText('titre');
        if ($titre == null || $group == null) {
            $output->setStatusCode(404);
            $output->setPageTitle('Erreur 404');
            $output->addHTML("Erreur 404, la page demandée n'existe pas, ou est nulle.");
        } else {
            $pageTitle = \Title::newFromText('Book:' . $group);
            $wikiPage = new WikiPage($pageTitle);
            $revision = $wikiPage->getRevision();
            $content = $revision->getContent(Revision::RAW);
            $index = ContentHandler::getContentText($content);
            $output->addHTML("<h1>" . $titre . "</h1>");

            $matches = array();
            preg_match("/\**" . $titre . "/", $index, $matches);
            $rank = $matches[0];
            $rank = str_replace($titre, "", $rank);

            $index = preg_split("/.*\*\**" . $titre . "/", $index)[1];

            while ($rank != "") {
                $index = preg_split("/\n" . preg_quote($rank) . "\w/", $index)[0];
                $rank = substr($rank, 1);
            }
            $output->addHTML("<div>" . $index . "</div>");

            /*
             * $index = preg_split($titre, $index)[1];
             * echo 'index = ';
             * echo $index;
             * $rank = strstr($index, $titre, true);
             * $ranktmp = "";
             * $array = str_split($rank);
             * foreach ($array as $char){$ranktmp .="\*";}
             * $ranktmp = substr($ranktmp, 2);
             * echo ' rank= ';
             * echo $ranktmp;
             *
             * $tab = preg_split("/\n".$ranktmp."\w/",$index);
             * $tab[1] = ltrim($tab[1], substr($titre, 1));
             * echo ' TABLEAU : ';
             * print_r($tab);
             * $output->addHTML("<div>" . $tab[1] . "</div>");
             */
        }
    }
}

