<?php
/**
 * Fonction utiles au plugin Analyclic partout
 *
 * @plugin     analyclic_partout
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 */

/**
 * Remplacer un raccourci [->docXX] par une version qui compte les téléchargements
 *
 * @param mixed $matches : Un tableau de matches comme fourni par preg_replace_callback
 *
 * @return String : Le raccourci dans lequel on a remplacé le docXX par l'url
 *                  d'action ad hoc.
 */
function expanser_lien_document_compteur($matches) {

	$raccourci = $matches[0];
	$lien = end($matches);

	/* On ne compte pas les téléchargement depuis l'espace privé. */
	if (test_espace_prive()) {
		return $raccourci;
	}

	if (strpos($lien, 'doc') === 0) {
		$id_doc = substr($lien, 3);
		$url_doc_compteur = generer_url_action('telecharger', 'arg='.$id_doc, true);
		$raccourci = str_replace($lien, $url_doc_compteur, $raccourci);
	}

	return $raccourci;
}

/**
 * Traitement auto sur les urls de document
 *
 * On remplace l'url du document par l'url d'action ad hoc qui compte les
 * téléchargements
 */
function traiter_URL_DOCUMENT_compteur($url_doc) {

	/* On ne compte pas les téléchargements depuis l'espace privé. */
	if (test_espace_prive()) {
		return $url_doc;
	}

	/* On retrouve le fichier tel qu'enregistré dans la table spip_documents.
	 * Par chance, cette méthode fonctionne aussi avec les documents distants \o/ */
	$fichier = preg_replace(',.*'._DIR_IMG.',', '', $url_doc);

	include_spip('base/abstract_sql');
	$row = sql_fetsel('id_document, mode', 'spip_documents', 'fichier='.sql_quote($fichier));

	if ($row['id_document'] and ($row['mode'] === 'document')) {
		$url_doc = generer_url_action('telecharger', 'arg='.$row['id_document']);
	}

	return $url_doc;
}
