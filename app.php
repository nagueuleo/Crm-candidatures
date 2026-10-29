<?php require 'config.php';auth();?>
<!doctype html>
<html lang="fr" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf" content="<?=csrf()?>">
    <title>MonWorkD'bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
    tailwind.config = {
        corePlugins: {
            preflight: false
        }
    }
    </script>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <header class="glass sticky-top">
        <div class="container-xxl d-flex align-items-center gap-2 py-2 flex-wrap">
            <span class="brand d-flex align-items-center gap-2 fw-bold fs-5"><span class="logo"><i
                        class="bi bi-rocket-takeoff"></i></span>MonWorkD'bord</span>
            <nav class="nav nav-pills gap-1 ms-lg-3" id="tabs" aria-label="Vues"><button class="nav-link active"
                    data-v="dash"><i class="bi bi-speedometer2"></i> Dashboard</button><button class="nav-link"
                    data-v="board"><i class="bi bi-kanban"></i> Pipeline</button><button class="nav-link"
                    data-v="list"><i class="bi bi-table"></i> Liste</button><button class="nav-link" data-v="comps"><i
                        class="bi bi-buildings"></i> Entreprises</button></nav>
            <div class="ms-auto d-flex gap-2 align-items-center"><input id="q" type="search" class="form-control"
                    placeholder="Rechercher…" aria-label="Rechercher">
                <button id="theme" class="btn btn-icon" aria-label="Changer de thème"><i
                        class="bi bi-moon-stars"></i></button>
                <button id="csv" class="btn btn-icon" aria-label="Exporter en CSV"><i
                        class="bi bi-download"></i></button>
                <a href="logout.php" class="btn btn-icon" aria-label="Déconnexion"><i
                        class="bi bi-box-arrow-right"></i></a>
                <button id="add" class="btn btn-brand"><i class="bi bi-plus-lg"></i><span
                        class="d-none d-sm-inline ms-1">Nouvelle</span></button>
            </div>
        </div>
    </header>
    <main class="container-xxl py-4">
        <h1 class="h4 fw-bold mb-3">Bonjour <?=e($_SESSION['name'])?> 👋</h1>
        <div id="stats" class="row g-3 mb-3"></div>
        <div id="late"></div>
        <section id="dash" class="row g-3">
            <div class="col-lg-4">
                <div class="glass p-3">
                    <h2 class="h6">Répartition par statut</h2>
                    <div class="chart-box"><canvas id="c1" role="img" aria-label="Répartition par statut"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="glass p-3">
                    <h2 class="h6">Candidatures sur 6 mois</h2>
                    <div class="chart-box"><canvas id="c2" role="img" aria-label="Candidatures par mois"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="glass p-3">
                    <h2 class="h6">Par secteur d'entreprise</h2>
                    <div class="chart-box"><canvas id="c3" role="img" aria-label="Candidatures par secteur"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="glass p-3">
                    <h2 class="h6">Prochaines relances</h2>
                    <ul id="up" class="list-unstyled mb-0"></ul>
                </div>
            </div>
        </section>
        <section id="comps" class="d-none" aria-label="Entreprises"></section>
        <section id="board" class="board d-none" aria-label="Pipeline"></section>
        <section id="list" class="glass p-2 d-none">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Poste</th>
                            <th>Statut</th>
                            <th>Lieu</th>
                            <th>Relance</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="rows"></tbody>
                </table>
            </div>
        </section>
    </main>
    <div class="modal fade" id="m" tabindex="-1" aria-labelledby="mt" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form id="f" class="modal-content glass">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="mt">Candidature</h2><button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body"><input type="hidden" name="id">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Entreprise *</label><input name="company"
                                list="cl" class="form-control" required autocomplete="off"><datalist id="cl"></datalist>
                        </div>
                        <div class="col-md-6"><label class="form-label">Poste *</label><input name="position"
                                class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Statut</label><select name="status"
                                class="form-select"></select></div>
                        <div class="col-md-6"><label class="form-label">Contrat</label><select name="contract"
                                class="form-select">
                                <option value="">—</option>
                                <option>CDI</option>
                                <option>CDD</option>
                                <option>Alternance</option>
                                <option>Stage</option>
                                <option>Freelance</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Lieu</label><input name="location"
                                class="form-control" placeholder="Paris / Remote"></div>
                        <div class="col-md-6"><label class="form-label">Salaire</label><input name="salary"
                                class="form-control" placeholder="45–50 k€"></div>
                        <div class="col-12"><label class="form-label">Lien de l'offre</label><input name="url"
                                type="url" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Contact</label><input name="contact_name"
                                class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Email du contact</label><input
                                name="contact_email" type="email" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Date de candidature</label><input
                                name="applied_at" type="date" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Date de relance</label><input name="follow_up"
                                type="date" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" rows="3"
                                class="form-control"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Priorité</label><select name="priority"
                                class="form-select">
                                <option value="1">Basse</option>
                                <option value="2">Moyenne</option>
                                <option value="3">Haute</option>
                            </select></div>
                        <div class="col-12" id="tl">
                            <hr>
                            <h3 class="h6">Historique des échanges</h3>
                            <div class="d-flex gap-2 mb-2"><select id="at" class="form-select" style="max-width:150px"
                                    aria-label="Type">
                                    <option>note</option>
                                    <option>appel</option>
                                    <option>email</option>
                                    <option>entretien</option>
                                    <option>relance</option>
                                </select><input id="ac" class="form-control" placeholder="Compte-rendu…"
                                    aria-label="Compte-rendu"><button type="button" id="aa"
                                    class="btn btn-brand">Ajouter</button></div>
                            <ul id="al" class="list-unstyled small mb-0"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between"><button type="button" id="del"
                        class="btn btn-outline-danger"><i class="bi bi-trash"></i> Supprimer</button><button
                        class="btn btn-brand">Enregistrer</button></div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="cm" tabindex="-1" aria-labelledby="cmt" aria-hidden="true">
        <div class="modal-dialog">
            <form id="cf" class="modal-content glass">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="cmt">Entreprise</h2><button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body vstack gap-3"><input type="hidden" name="id">
                    <div class="d-flex align-items-center gap-3"><span id="lp"></span><label
                            class="form-label w-100 mb-0">Logo (JPG, PNG, WebP · 2 Mo max)<input type="file" name="logo"
                                accept="image/jpeg,image/png,image/webp" class="form-control mt-1"></label></div>
                    <label class="form-label">Nom *<input name="name" class="form-control mt-1" required></label>
                    <div class="row g-3"><label class="form-label col-6">Secteur<input name="sector"
                                class="form-control mt-1" placeholder="Tech, Santé…"></label><label
                            class="form-label col-6">Ville<input name="city" class="form-control mt-1"></label></div>
                    <label class="form-label">Site web<input name="website" type="url" class="form-control mt-1"
                            placeholder="https://"></label><label class="form-label">Notes<textarea name="notes"
                            rows="3" class="form-control mt-1"></textarea></label>
                </div>
                <div class="modal-footer justify-content-between"><button type="button" id="cdel"
                        class="btn btn-outline-danger"><i class="bi bi-trash"></i> Supprimer</button><button
                        class="btn btn-brand">Enregistrer</button></div>
            </form>
        </div>
    </div>
    <div id="toast" class="glass px-3 py-2 d-none" role="status"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="assets/app.js"></script>
</body>

</html>