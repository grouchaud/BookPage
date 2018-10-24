<?php
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'savePage':
            savePage();
            break;
        case 'cancelPage':
            cancelPage();
            break;
    }
}

function savePage() {
    echo "The savePage function is called.";
    exit;
}

function cancelPage() {
    echo "The cancelPage function is called.";
    exit;
}
?>