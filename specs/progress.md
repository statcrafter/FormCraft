# Suivi d'avancement du projet - FormCraft (XLSForm Builder)

Ce document sert à suivre l'état d'avancement du développement, les tâches accomplies, et les prochaines étapes.

**Dernière mise à jour :** 23 décembre 2025

---

## 🟢 Phase 1 : Configuration et Infrastructure

### 1.1 Setup technique
- [x] Installation Laravel 12
- [x] Configuration Inertia.js + Vue 3
- [x] Configuration Tailwind CSS
- [x] Installation Fortify (Backend Auth)
- [x] Initialisation shadcn-vue (`components.json` présent)
- [x] Installation VueDraggable / SortableJS
- [ ] Création des vues d'authentification (Login/Register) pour Fortify/Inertia
- [ ] Configuration des environnements (SQLite pour dev)

### 1.2 Architecture base de données
- [x] Migration : Table `users` (existante, à vérifier)
- [x] Migration : Table `forms` (JSON structure)
- [x] Migration : Table `form_versions`
- [ ] Migration : Table `form_templates`
- [ ] Migration : Table `form_shares`
- [ ] Migration : Table `choice_lists`
- [x] Modèles Eloquent correspondants

---

## 🟡 Phase 2 : Système de Gestion des Formulaires

### 2.1 CRUD Formulaires de base
- [x] Route et Contrôleur pour Dashboard (FormController)
- [x] Création d'un nouveau formulaire (Modal + Backend)
- [x] Liste des formulaires (Index Vue)
- [x] Navigation Sidebar vers les formulaires
- [ ] Suppression / Archivage

### 2.2 Métadonnées
- [ ] Édition des paramètres XLSForm (`form_title`, `form_id`, etc.)

### 2.3 Gestion des versions
- [ ] Logique de sauvegarde automatique
- [ ] Système de versionning

---

## 🟢 Phase 3 : Types de Questions - Partie 1 (Input Basiques)
- [x] Interface Drag-and-Drop (Éditeur visuel avec Pinia + VueDraggable)
- [x] Composant : Text / Integer / Decimal
- [x] Composant : Select One / Select Multiple (avec éditeur d'options)
- [x] Composant : Rank
- [x] Gestion des choix (listes simples)

## 🟢 Phase 4 : Types de Questions - Partie 2 (Médias et Géo)
- [x] Composant : Image / Audio / Video / File
- [x] Composant : Geopoint / Geotrace / Geoshape
- [x] Composant : Date / Time / Datetime

---

## 🟢 Phase 5 : Types de Questions - Partie 3 (Spéciales)
- [x] Composant : Note / Acknowledge
- [x] Composant : Calculate (avec éditeur XPath basique dans properties)
- [x] Composant : Barcode / Range (Range pas encore implémenté mais type dispo)

---

## 🟢 Phase 6 : Structure et Organisation
- [x] Groupes (Begin/End) avec imbrication récursive
- [x] Répétitions (Repeats)
- [x] Arborescence du formulaire (via NestedQuestionList)

---

## 🟢 Phase 7 : Logique et Conditions
- [x] Relevant (Visibilité conditionnelle) via LogicBuilder visuel
- [x] Constraint (Validation) via champ XPath
- [x] Required (Obligatoire)
- [x] Génération automatique de XPath pour les règles visuelles

## 🟢 Phase 10 : Médias et Assets
- [x] Infrastructure backend (Table `form_assets`)
- [x] Upload d'images et audios par question
- [x] Bibliothèque de médias par formulaire
- [ ] Optimisation/Compression des images (à venir)

## 🟢 Phase 13 : Export et Génération
- [x] Export XLSForm (.xlsx) via Maatwebsite Excel
- [x] Génération des feuilles `survey`, `choices` et `settings`
- [x] Support des groupes et répétitions dans l'export
- [ ] Export XML natif (ODK)

---

## Notes Techniques & Décisions
- **DB** : SQLite utilisé pour le développement local.
- **UI** : shadcn-vue pour les composants d'interface.
- **Auth** : Laravel Fortify gère le backend, vues à implémenter avec Inertia.
- **Forms** : Stockés en JSON dans la base de données. Structure à définir.
