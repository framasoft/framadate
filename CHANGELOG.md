# Changelog de framadate

## Version 0.9.3 (Antonin - Olivier - Nikos)
    - Fix : Traduction de textes en Italien
    - Fix : Empêchement de la suppression de la dernière colonne
    - Fix : Possiblité de supprimer des colonnes contenant des caractères spéciaux (par exemple "&")
    - Fix : Correction de l'exemple d'URL rewriting (des efforts restent à faire)
    - Amélioration : (Mode chacun son vote) Possiblité d'éditer son vote directement après un vote
    - Amélioration : Message plus parlant lors de la création d'une colonne
## Version 0.9.2 (Olivier)
    - Fix : Completion d'un manque de contrôle sur les ID
## Version 0.9.1 (JosephK - Olivier - Antonin - Michael - Paul)
    - Fix : Correction des lenteurs de défilement
    - Fix : Arrêt du défilement auto à gauche qu'on clique sur un choix
    - Fix : Traductions Français/Anglais/Allemand
    - Fix : Fin du tri automatique des colonnes (ex: 10h < 9h)
    - Fix : Option à la création masquée dans certains cas
    - Fix : Le nom peut maintenant contenir des caractères spéciaux (ex: , - ')
    - Fix : Correction mineure de la doc d'installation
    - Fix : Interdiction du caractère "," dans choix d'un vote
    - Fix : Correction de la suppression de choix contenant des caractères spéciaux
    - Fix : Correction du contrôle pour empêcher de supprimer le dernier choix d'un sondage
    - Fix : Taille du champs "Votre nom" agrandie
    - Technique : Ajout de logs
## Version 0.9 (JosephK - Olivier - Antonin - ...)
    - Technique : Réorganisation des classes
    - Technique : Découpage en MVC + Installation de Smarty
    - Technique : Refonte de l'accès aux données + Remplacement de Adodb par PDO
    - Technique : Définition claire des couches Service et Repository
    - Technique : Utilisation de la librairie open source o80-i18n pour la gestion des langue
    - Amélioration : Refonte de l'administration
    - Amélioration : Formulaire de recherche pour trouver des sondages
    - Amélioration : Notification de l'utilisation si JAvascript ou les cookies sont désactivés
    - Amélioration : Découpage en 2 options pour recevoir ou non les nouveaux vote et commentaires
    - Amélioration : Utilisation de jetons CSRF sur certaines actions
    - Amélioration : Nouvelle option de sondage "Chaque participant peut modifier son propre vote"
    - Amélioration : Nouvelle option de sondage "Vote caché, seul le créateur du sondage peu voir les résultats"
    - Amélioration : Auto-cmoplétion des champs de dates (15/5 peut devenir 15/05/2015 ou 15/05/2016 en fonction de la date actuelle)
    - Amélioration : Nouvelle page pour retrouver ses sondages par mail
    - Amélioration : Mise à jour des fichiers .md pour faciliter la collaboration
    - Amélioration : Le nom de l'auteur et la date d'expiration sont modifiables
    - Amélioration : Le nom de vote est modifiable
    - Amélioration : Affichage du comptage des "Si nécessaire" entre parenthèses
    - Amélioration : Page d'installation
    - Fix : Purge en 2 étapes → 1. Verrouillage du sondage → 2. 60 jours plus tard suppression du sondage
    - Fix : Date d'expiration qui devient nulle quand on ajoute une colonne
    - Fix : clic/focus sur oui/non/si nécessaire → retour à gauche de la barre de scroll sur Chromium
    - Fix : Perte de ses votes quand le nom entré n'est pas valide
    - Fix : Certains sondages sont créé avec un ID déjà existant
    - Fix : 2 choix minimum sont nécessaires pour créer un sondage
    - Fix : Ajout de la police d'écriture Déjà Vu
    - Fix : Mémorisation de la langue entre l'application et l'administration
    - Fix : Bug à la création d'un sondage sans Javascript ou sans Cookies
    - Fix : Erreur d'url avec les noms de domaine contenant "admin"
    - Fix : Mise à jour de la doc d'installation
    
## Version 0.8 (juillet 2014 Pascal Chevrel - Armony Altinier - JosephK)
	- Améliorations sur l'accessibilité
	- Améliorations sur l'ergonomie
	- Améliorations sur l'internationalisation (nombreuses phrases en français dans le code)
	- Découpage chaines de langue pour virer le code html
	- Remise en place de l'export CSV
	- Remise en place de get_server_name() pour permettre l'installation dans un sous dossier, en https ou sur un port différent
	- Ajout Authors.md + en-têtes refaits
	- Fix bug changement de langues en mode URL rewriting (requête GET passée en formulaire POST)
	- Fix bug 2 boutons valider lorsqu'on édite un vote
	- Fix focus javascript sur "Votre nom"
	- Nettoyage + Bootstrap
	- Ajout vote Oui/Non/Si nécessaire
	- Formulaire simplifié pour l'ajout de colonne date (horaire libre)
	- Restructuration
	- Fix (partiel) bug modification du premier vote en tapant Entrée

## Version 0.7 (mars 2013)
	- Fix : le sondage supprimé n'était pas forcément le sondage sélectionné (cfévrier)
	- Fix : suppression de STUDS_DIR pour éviter toute confusion
	- Fix : corrections l'en-tete et de l'encodage des e-mails (cfévrier)
	- Fix : rend Optionnelle l'utilisation de la variable "REMOTE_USER" (cfévrier)
	- Amélioration : ne faire apparaitre dans l'admin que les sondage actifs ou expirés depuis x mois (pyg)
	- Amélioration : ajout d'un champs date_creation dans la table "sondage" (pyg)
	- Amélioration : permet de faire fonctionner gettext avec le serveur de dev de PHP5.4 + enlève code commenté depuis des années (pascalchevrel)
	- Fix : enlève les appels à get_server_name() partout sauf dans un appel à sendMail(), réécriture de la fonction pour cet usage (pascalchevrel)
	- Amélioration : remplacement des define() par des const plus concis (pascalchevrel)
	- Amélioration : possibilité de faire des liens directs vers les types de sondages à créer (pascalchevrel)
	- Amélioration : meilleure intégration de la framanav (pyg)
	- Amélioration : nombreuses modifications CSS pour un meilleur affichage (pyg)

## Changelog des 22 et 23 juin (pyg@framasoft.net)
	- très nombreuses modifications CSS
	- ajout de buttons.css pour des boutons plus propres 
	- ajout de print.css pour une impression sans la classe "corps"
	- refonte de la page d'accueil
	- ajout de la framanav
	- qq retouches dans les fichiers .po
	- date de destruction passée de 2j à 30j
	- ajout de l'adresse à transmettre
	- ajout d'un bouton imprimer
	- généralisation des stripslashes
	- fix d'un bug sur une requete (suppression). Reste la seconde partie : https://github.com/leblanc-simon/OpenSondage/issues/8
	- modification du titre en image
	- ajout de htmlspecialchars_decode avec param ENT_QUOTES pour l'envoi des emails

## Changelog du 21 juin 2011 (pyg@framasoft.net)
	- très nombreuses modifications CSS
	- modification adminstuds.php : ajout de classes aux formulaires et ajout de stripslashes à l'affichage (TODO: à généraliser)
	- modification infos_sondages.php : simplification du tableau de choix, ajouts de CSS, ajouts de labels pour faciliter la selection

## Changelog version 0.6.7 (mai 2011)
	- fork du projet STUdS (https://sourcesup.cru.fr/projects/studs/) de la version trunk du 15 mai 2011)
    - reprise par Simon Leblanc
    - nettoyage du code (indentation, cohérence de la convention de codage)
    - suppression des warning php
    - résolution d'une faille de sécurité par injection SQL
    - résolution d'une faille de sécurité par injection mail
    - correction dans le fichier de langue (merci à Julien Reitzel)
    - possibilité de mettre un texte libre pour les horaires
    - version Framasoft

# Les dernières améliorations de STUdS
	Changelog version 0.6.6 (XXX 2011) :
	- internationalisation avec gettext
	- abstraction de la base de données avec ADOdb
	- support de mysql (fichier d'initialisation disponible)
	- meilleure compatibilité avec le mode strict de PHP
	- factorisation de code et de CSS
	- moins de boutons de formulaire, plus de liens <a href>

	Changelog version 0.6.5 (juin 2010) :
	- Changement de deux icones dans la creation d'un sondage.

	Changelog version 0.6.4 (mars 2010) :
	- Corrections de bug

	Changelog version 0.6.3 (janvier 2010) :
	- Corrections de bug

	Changelog version 0.6.2 (novembre 2009) :
	- Correction dans l'affichage des bandeaux,
	- Modification de la partie "A propos",
	- Préparation à l'authentification,
	- De UdSification de l'application dans certains fichiers.

	Changelog version 0.6.1 (octobre 2009) :
	- Corrections d'erreurs dans les traductions et d'oublis de traduction dans certaines pages.

	Changelog version 0.6 (août 2009) :
	- Mise sous la licence CeCILL-B du code source de STUdS,
	- Passage de STUdS en encodage UTF8,
	- Ajout des icones des menus dans toutes les pages et non pas seulement sur la page d'accueil,
	- Correction d'un bug lors du rajout d'une colonne dans l'interface d'administration des sondages.

	Changelog version 0.5 (février 2009) :
	- Traduction de STUdS en anglais, allemand et espagnol,
	- Changement de la CSS avec ajout du logo de l'Université de Strasbourg,
	- Possibilité d'ajouter un commentaire pour les sondés.
	
	Changelog version 0.4 (janvier 2009) :
	- Possibilité de faire un export PDF pour envoyer la lettre de convocation à la date de réunion,
	- Possibilité de rajouter des colonnes dans la partie administration de sondage,
	- Correction de bugs d'affichage avec les caractères ' et " .

	Changelog version 0.3 (novembre 2008) :
	- Possibilité de faire un export CSV pour exploiter le sondage dans un tableur,
	- Mise en place d'un repository Subversion pour partager les nouvelles versions de STUdS,
	- Amélioration de la CSS pour un meilleur affichage,
	- Modification du code source pour le rendre portable vers une autre machine.
	
	Changelog version 0.2 (novembre 2008) :
	- Lors de la création d'un sondage DATE, classement des dates par ordre croissant,
	- Lors de la création d'un sondage DATE, accepter les horaires au format "8h" ou "8H",
	- Lors de la création d'un sondage DATE, possibilité de copier des horaires entre les dates,
	- Lors d'une modification de ligne, cocher les cases initialement choisies et non pas des cases vides,
	- Changement du format d'affichage des dates pour un formatage type : "Mardi 13/06",
	- Meilleure visualisation des choix les plus votés,
	- Possibilité pour l'administrateur du sondage de choisir de recevoir un mail d'alerte à chaque participation d'un sondé,
	- Remplacement des boutons de formulaire par des images moins austères,
	- Correction de quelques petits bugs d'affichage,
	- Possibilité de rajouter des cases supplémentaires lors de la création d'un sondage AUTRE,
	- Possibilité de rajouter des cases supplémentaires lors de la création d'un sondage DATE.
