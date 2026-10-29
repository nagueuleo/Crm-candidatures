# JobTrack — CRM de candidatures
1. Importer `schema.sql` dans MySQL (phpMyAdmin ou `mysql < schema.sql`).
2. Adapter les identifiants dans `config.php`.
3. Placer le dossier sous Apache/Nginx (PHP 8+) ou lancer `php -S localhost:8000`.
4. Ouvrir `index.php` (page d'accueil) puis « Créer un compte ». L'application se trouve dans `app.php`.

**Mise à jour depuis la v1** : importer uniquement `migration_v2.sql`. Nouvelle installation : `schema.sql` suffit. Le dossier `uploads/logos/` doit être accessible en écriture.
