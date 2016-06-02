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

/**
 * Ajouter un traitement automatique sur une balise
 *
 * On peut restreindre l'application du traitement au balises appelées dans un
 * type de boucle via le paramètre optionnel $table.
 *
 * @param array $interfaces
 *    Les interfaces du pipeline declarer_tables_interfaces
 * @param string $traitement
 *    Un format comme pour sprintf, dans lequel le compilateur passera la valeur de la balise
 * @param string $balise
 *    Le nom de la balise à laquelle on veut appliquer le traitement
 * @param string $table (optionnel)
 *    Un type de boucle auquel on veut restreindre le traitement.
 */
function anaclic_ajouter_traitement_automatique($interfaces, $traitement, $balise, $table = 0) {

	$table_traitements = $interfaces['table_des_traitements'];

	if (! isset($table_traitements[$balise])) {
		$table_traitements[$balise] = array();
	}

	/* On essaie d'être tolérant sur le nom de la table */
	if ($table) {
		include_spip('base/objets');
		$table = table_objet($table);
	}

	if (isset($table_traitements[$balise][$table])) {
		$traitement_existant = $table_traitements[$balise][$table];
	}

	if (!isset($traitement_existant) or (! $traitement_existant)) {
		$traitement_existant = '%s';
	}

	$interfaces['table_des_traitements'][$balise][$table] = sprintf($traitement, $traitement_existant);

	return $interfaces;
}
