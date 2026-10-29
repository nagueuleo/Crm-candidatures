<?php require 'config.php';if(!empty($_SESSION['uid'])){header('Location: app.php');exit;}$err='';$reg=isset($_GET['register']);
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!hash_equals(csrf(),$_POST['csrf']??''))$err='Session expirée, rechargez la page.';
 else{$em=strtolower(trim($_POST['email']??''));$pw=$_POST['password']??'';
  if(!filter_var($em,FILTER_VALIDATE_EMAIL)||strlen($pw)<8)$err='Email valide et mot de passe de 8 caractères minimum requis.';
  elseif($reg){$q=db()->prepare('SELECT 1 FROM users WHERE email=?');$q->execute([$em]);
   if($q->fetch())$err='Cet email est déjà utilisé.';
   else{$nm=trim($_POST['name']??'')?:'Candidat';db()->prepare('INSERT INTO users(name,email,password) VALUES(?,?,?)')->execute([$nm,$em,password_hash($pw,PASSWORD_DEFAULT)]);
    session_regenerate_id(true);$_SESSION['uid']=db()->lastInsertId();$_SESSION['name']=$nm;header('Location: app.php');exit;}}
  else{$q=db()->prepare('SELECT * FROM users WHERE email=?');$q->execute([$em]);$u=$q->fetch();
   if($u&&password_verify($pw,$u['password'])){session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['name']=$u['name'];header('Location: app.php');exit;}
   $err='Identifiants incorrects.';}}}
?>
<!doctype html>
<html lang="fr" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MonWorkD'bord<?=$reg?'Inscription':'Connexion'?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="bg-pro">
    <div class="row g-0 min-vh-100">
        <aside class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5">
            <a href="index.php"
                class="brand d-flex align-items-center gap-2 fw-bold fs-4 text-decoration-none text-white"><span
                    class="logo"><i class="bi bi-rocket-takeoff"></i></span>MonWorkD'bord</a>
            <div>
                <h2 class="display-6 fw-bold">Toutes vos candidatures,<br><span class="grad">enfin sous contrôle.</span>
                </h2>
                <ul class="list-unstyled vstack gap-3 mt-4 text-muted fs-6">
                    <li><i class="bi bi-kanban me-2 text-info"></i>Pipeline visuel en glisser-déposer</li>
                    <li><i class="bi bi-graph-up-arrow me-2 text-info"></i>Dashboard et statistiques en temps réel</li>
                    <li><i class="bi bi-bell me-2 text-info"></i>Relances et historique des échanges</li>
                    <li><i class="bi bi-buildings me-2 text-info"></i>Fiches entreprises avec logo</li>
                </ul>
            </div>
            <small class="text-muted">© <?=date('Y')?> MonWorkD'bord </small>
        </aside>
        <main class="col-lg-6 d-grid place-items-center p-3 p-md-5">
            <div class="glass p-4 p-md-5 w-100" style="max-width:440px">
                <a href="index.php" class="d-inline-flex align-items-center gap-1 small text-decoration-none mb-3"><i
                        class="bi bi-arrow-left"></i> Accueil</a>
                <h1 class="h3 fw-bold mb-1"><?=$reg?'Créer votre espace':'Bon retour'?></h1>
                <p class="text-muted mb-4">
                    <?=$reg?'Commencez à suivre vos candidatures en 30 secondes.':'Connectez-vous pour retrouver vos candidatures.'?>
                </p>
                <?php if($err):?><div class="alert alert-danger d-flex gap-2" role="alert"><i
                        class="bi bi-exclamation-triangle"></i><?=e($err)?></div><?php endif?>
                <form method="post" class="vstack gap-3" id="af"><input type="hidden" name="csrf" value="<?=csrf()?>">
                    <?php if($reg):?><div><label class="form-label" for="n">Nom</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-person"
                                    aria-hidden="true"></i></span><input id="n" name="name" class="form-control"
                                required autocomplete="name" autofocus></div>
                    </div><?php endif?>
                    <div><label class="form-label" for="e">Email</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"
                                    aria-hidden="true"></i></span><input id="e" type="email" name="email"
                                class="form-control" required autocomplete="email" <?=$reg?'':'autofocus'?>></div>
                    </div>
                    <div><label class="form-label" for="p">Mot de passe</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"
                                    aria-hidden="true"></i></span><input id="p" type="password" name="password"
                                class="form-control" required minlength="8"
                                autocomplete="<?=$reg?'new':'current'?>-password"><button type="button"
                                class="btn btn-icon" id="tg" aria-label="Afficher le mot de passe"><i
                                    class="bi bi-eye"></i></button></div><?php if($reg):?><div class="form-text">8
                            caractères minimum.</div><?php endif?>
                    </div>
                    <button class="btn btn-brand w-100" id="sb"><?=$reg?'Créer mon compte':'Se connecter'?></button>
                </form>
                <p class="text-center mt-4 mb-2"><a
                        href="<?=$reg?'login.php':'login.php?register=1'?>"><?=$reg?'Déjà inscrit ? Connexion':'Pas de compte ? Inscription'?></a>
                </p>
                <p class="text-center small text-muted mb-0"><i class="bi bi-shield-lock"></i> Connexion sécurisée ·
                    mots de passe chiffrés</p>
            </div>
        </main>
    </div>
    <script>
    const p = document.getElementById('p'),
        t = document.getElementById('tg');
    t.onclick = () => {
        const s = p.type === 'password';
        p.type = s ? 'text' : 'password';
        t.innerHTML = '<i class="bi bi-eye' + (s ? '-slash' : '') + '"></i>';
        t.setAttribute('aria-label', s ? 'Masquer le mot de passe' : 'Afficher le mot de passe')
    };
    document.getElementById('af').onsubmit = () => {
        const b = document.getElementById('sb');
        setTimeout(() => {
            b.disabled = true;
            b.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Veuillez patienter…'
        }, 0)
    };
    </script>
</body>

</html>