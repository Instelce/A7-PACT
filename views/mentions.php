<?php

$this->title = "Mentions légales";
$this->css = "main.css";

$titre = '#';
$titre2 = '##';
$titre3 = '###';
$titre4 = '####';
$titre5 = '#####';
$tiret = '-';
$grandtiret = '---';
$ex = '!';
$cro = "[";
$lignevide = ' ';

$markdownFile = __DIR__ . '/../.docs/mentions.md';
if (file_exists($markdownFile)) {
    $contenu = file_get_contents($markdownFile);
} else {
    die("Le fichier Markdown n'existe pas.");
}
?>

<div class="flex flex-col gap-4 mb-8">
    
    <?php
    $lignes = explode("\n", $contenu);

    foreach ($lignes as $ligne) {
        // Supprimer les espaces inutiles pour une détection fiable
        $ligne = trim($ligne);
        // Supprimer les erreurs pour les apostrophes '
        $ligne = html_entity_decode($ligne, ENT_QUOTES, 'UTF-8');
    
        // Traitement des titres de niveau 1 à 5
        if (substr($ligne, 0, strlen($titre5)) === $titre5) {
            echo "<br><h5 class=\"heading-5 font-title\">" . trim(substr($ligne, strlen($titre5))) . "</h5>\n";
            continue;
        }
        if (substr($ligne, 0, strlen($titre4)) === $titre4) {
            echo "<br><h4 class=\"heading-3 font-title\">" . trim(substr($ligne, strlen($titre4))) . "</h4>\n";
            continue;
        }
        if (substr($ligne, 0, strlen($titre3)) === $titre3) {
            echo "<h3 class=\"heading-2 font-title\">" . trim(substr($ligne, strlen($titre3))) . "</h3>\n";
            continue;
        }
        if (substr($ligne, 0, strlen($titre2)) === $titre2) {
            echo "<h2 class=\"heading-2 font-title\">" . trim(substr($ligne, strlen($titre2))) . "</h2>\n";
            continue;
        }
        if (substr($ligne, 0, strlen($titre)) === $titre) {
            echo "<h1 class=\"heading-1 font-title\">" . trim(substr($ligne, strlen($titre))) . "</h1>\n";
            continue;
        }
    
        // Traitement des espaces (grands tirets)
        if (substr($ligne, 0, strlen($grandtiret)) === $grandtiret) {
            echo "<br>" . trim(substr($ligne, strlen($grandtiret)));
            continue;
        }

        // Traitement des tirets
        if (substr($ligne, 0, strlen($tiret)) === $tiret) {
            echo "<li>" . trim(substr($ligne, strlen($tiret))) . "</li>\n";
            continue;
        }
    
        // Traitement des liens simples
        if (substr($ligne, 0, strlen($cro)) === $cro) {
            preg_match("/\[(.*?)\]\((.*?)\)/", $ligne, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                echo "<p><a href='" . $matches[2] . "'>" . $matches[1] . "</a></p>\n";
            }
            continue;
        }

        // Traitement des paragraphes simples (y compris les liens Markdown dans les paragraphes)
        if (!empty($ligne)) {
            // Remplacement des liens Markdown dans les paragraphes
            $ligne = preg_replace_callback(
                "/\[(.*?)\]\((.*?)\)/",
                function ($matches) {
                    $texte = htmlentities($matches[1]);
                    $lien = htmlentities($matches[2]);
                    return "<a href='$lien'>$texte</a>";
                },
                htmlspecialchars($ligne) // Transformation HTML pour les caractères spéciaux
            );

            // Affichage du paragraphe avec les liens convertis
            echo "<p>" . $ligne . "</p>\n";
        }
    }
    ?>    

</div>
