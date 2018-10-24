<?php

class SpecialBookPage extends SpecialPage
{

    public function __construct()
    {
        parent::__construct('BookPage');
    }

    function execute($par)
    {
        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();

        // Déclaration de mes variables
        $groupName = $request->getText('groupName');
        $pageTitle = \Title::newFromText('Book:' . $groupName);

        // Si le nom de la page est vide ou inexistant alors on affiche le message d'erreur
        if ($pageTitle === null || ! $pageTitle->exists()) {
            $output->setStatusCode(404);
            $output->setPageTitle('Erreur 404');
            $output->addHTML("Erreur 404, la page demandée n'existe pas, ou est nulle.");
        } else {

            function get_data($url)
            {
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $data = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Request Error:' . curl_error($ch);
                }
                curl_close($ch);
                return $data;
            }

            $wikiPage = new WikiPage($pageTitle);
            $revision = $wikiPage->getRevision();
            $content = $revision->getContent(Revision::RAW);
            $textor = ContentHandler::getContentText($content);
            $text = $textor;
            $text = ltrim($text, '*');
            $arr = preg_split('/\*+/', $text);

            $output->addHtml('<form method="post" autocomplete="on">
			<h3>Nouveau Titre !</h3>  </br>Nom : <input type="text" name="name"> </br>Titre ou Tuto Parent : <input type="text" name="parent">
			<input class="site-button btn" type="submit">
		</form></br></br>');
            if (isset($_POST["name"]) && strpos($textor, "*".$_POST["name"]."\n")==FALSE) {
                $p1 = "*";
                $p2 = $textor;
                if ($_POST["parent"] !== "" && strpos($textor, "*".$_POST["parent"]."\n") !== FALSE) {
                    $p1 .= strstr($textor, '*'.$_POST["parent"]."\n", true) . "*".$_POST["parent"]."\n*";
                    $p2 = str_replace('*'.$_POST["parent"]."\n", "", strstr($textor, '*'.$_POST["parent"]."\n"));
                }
                $edit = $p1 . "*" . $_POST["name"] . "\n". $p2;
                $pageContent = ContentHandler::makeContent($edit, $pageTitle);
                $wikiPage->doEditContent($pageContent, "Page Edited by Special BookPage", EDIT_UPDATE);
                $save = 'images/books/save'.$pageTitle->getBaseText();
                $handle = fopen($save, 'w') or die('Cannot open file:  ' . $save);
                fwrite($handle, $edit);
                header("Refresh:0;");
            }
            $output->addHTML("<div id='reordergroup-alert' class='alert' style='display:none'></div>");

            $output->addHTML('<div id="tutorials-list" data-grouppage="' . \MWNamespace::getCanonicalName($pageTitle->getNamespace()) . ':' . $pageTitle->getDBKey() . '">');

            foreach ($arr as $line) {
                $output->addHTML('<div class="grabbable" id="item_' . $line . '">' . $line . '<i class="fa fa-arrows-v"></i></div>');
            }

            $output->addHTML('</div>');

            // save

            $output->addHTML('<button id="gp-special-save" class="site-button btn"><i class="fa fa-spinner fa-spin upl_loading" style="display:none"></i>' . wfMessage('gp-special-save')->parse() . '</button>');

            // save and export pdf
            $output->addHTML('<button id="gp-special-savePdf" class="site-button btn"><i class="fa fa-spinner fa-spin upl_loading" style="display:none"></i>' . wfMessage('gp-special-savePdf')->parse() . '</button>');
            // cancel
            $output->addHTML('<button id="gp-special-cancel"  class="site-button btn" >' . wfMessage('gp-special-cancel')->parse() . '</button>');

            $output->addJsConfigVars("groupspageLink", \Linker::link(\Title::newFromText('Group:' . $pageTitle->getDBkey()), $this->msg('gp-special-groupspage-link')));

            $output->addModules('ext.reordergroup.js');
            $output->addModuleStyles('ext.reordergroup.css');
        }
    }
}