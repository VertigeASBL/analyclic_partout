<?php
/**
 * Utilisations de pipelines par Analyclic Partout
 *
 * @plugin     Analyclic Partout
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Analyclic_partout\Pipelines
 */


/**
 * Remplacer les liens [->docXX] pour compter les téléchargements
 *
 * @pipeline pre_liens
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function analyclic_partout_pre_liens($flux) {

	$flux = preg_replace_callback(_RACCOURCI_LIEN, 'expanser_lien_document_compteur', $flux);

	return $flux;
}

/**
 * Ajouter des traitements automatiques sur des balises
 *
 * @pipeline declarer_tables_interfaces
 * @param  array $interfaces Données du pipeline
 * @return array             Données du pipeline
 */
function analyclic_partout_declarer_tables_interfaces($interfaces) {

	$interfaces = anaclic_ajouter_traitement_automatique(
		$interfaces,
		'traiter_URL_DOCUMENT_compteur(%s)',
		'URL_DOCUMENT'
	);

	$interfaces = anaclic_ajouter_traitement_automatique(
		$interfaces,
		'traiter_URL_DOCUMENT_compteur(%s)',
		'FICHIER'
	);

	return $interfaces;
}
