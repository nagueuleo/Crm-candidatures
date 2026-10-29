<?php require 'config.php';header('Content-Type: application/json');
if(empty($_SESSION['uid'])){http_response_code(401);exit('{"error":"Non connecté"}');}
$u=$_SESSION['uid'];$a=$_GET['a']??'list';$d=$_POST?:(json_decode(file_get_contents('php://input'),true)??[]);
if(!hash_equals(csrf(),$_SERVER['HTTP_X_CSRF']??'')){http_response_code(403);exit('{"error":"CSRF"}');}
$S=['a_postuler','postule','entretien','offre','refuse'];
function out($x){echo json_encode($x);exit;}
function fail($m,$c=422){http_response_code($c);out(['error'=>$m]);}
function q($sql,$p=[]){$s=db()->prepare($sql);$s->execute($p);return $s;}
function company($u,$n){$r=q('SELECT id FROM companies WHERE user_id=? AND name=?',[$u,$n])->fetch();if($r)return $r['id'];q('INSERT INTO companies(user_id,name) VALUES(?,?)',[$u,$n]);return db()->lastInsertId();}
function logo($old=null){if(empty($_FILES['logo']['tmp_name']))return $old;$f=$_FILES['logo'];
 $x=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'])]??null;
 if(!$x||$f['size']>2097152)fail('Logo : JPG, PNG ou WebP, 2 Mo maximum');
 @mkdir('uploads/logos',0755,true);$p='uploads/logos/'.bin2hex(random_bytes(8)).".$x";move_uploaded_file($f['tmp_name'],$p);if($old&&is_file($old))unlink($old);return $p;}
try{switch($a){
case 'list':out(q('SELECT a.*,c.logo,c.sector FROM applications a LEFT JOIN companies c ON c.id=a.company_id WHERE a.user_id=? ORDER BY a.updated_at DESC',[$u])->fetchAll());
case 'save':$f=['company','position','status','contract','location','salary','url','contact_name','contact_email','notes','applied_at','follow_up'];$v=[];
 foreach($f as $k)$v[$k]=trim($d[$k]??'')?:null;
 if(!$v['company']||!$v['position']||!in_array($v['status'],$S,true))fail('Entreprise et poste obligatoires');
 $v['priority']=in_array((int)($d['priority']??2),[1,2,3])?(int)$d['priority']:2;$v['company_id']=company($u,$v['company']);$f[]='priority';$f[]='company_id';
 if(!empty($d['id'])){$sql='UPDATE applications SET '.implode(',',array_map(fn($k)=>"$k=:$k",$f)).' WHERE id=:id AND user_id=:u';$v['id']=(int)$d['id'];}
 else $sql='INSERT INTO applications(user_id,'.implode(',',$f).') VALUES(:u,:'.implode(',:',$f).')';
 $v['u']=$u;q($sql,$v);out(['ok'=>1]);
case 'status':if(!in_array($d['status']??'',$S,true))fail('Statut invalide');q('UPDATE applications SET status=? WHERE id=? AND user_id=?',[$d['status'],(int)$d['id'],$u]);out(['ok'=>1]);
case 'delete':q('DELETE FROM applications WHERE id=? AND user_id=?',[(int)$d['id'],$u]);out(['ok'=>1]);
case 'companies':out(q('SELECT c.*,COUNT(a.id) n FROM companies c LEFT JOIN applications a ON a.company_id=c.id WHERE c.user_id=? GROUP BY c.id ORDER BY c.name',[$u])->fetchAll());
case 'company_save':$n=trim($d['name']??'');if(!$n)fail('Nom obligatoire');$v=[$n];
 foreach(['sector','website','city','notes'] as $k)$v[]=trim($d[$k]??'')?:null;
 if($v[2]&&!preg_match('#^https?://#i',$v[2]))fail('Le site doit commencer par http:// ou https://');
 try{if(!empty($d['id'])){$id=(int)$d['id'];$o=q('SELECT logo FROM companies WHERE id=? AND user_id=?',[$id,$u])->fetch();if(!$o)fail('Introuvable',404);
   q('UPDATE companies SET name=?,sector=?,website=?,city=?,notes=?,logo=? WHERE id=? AND user_id=?',[...$v,logo($o['logo']),$id,$u]);
   q('UPDATE applications SET company=? WHERE company_id=? AND user_id=?',[$n,$id,$u]);}
  else q('INSERT INTO companies(name,sector,website,city,notes,logo,user_id) VALUES(?,?,?,?,?,?,?)',[...$v,logo(),$u]);
 }catch(PDOException $x){fail('Cette entreprise existe déjà');}
 out(['ok'=>1]);
case 'company_delete':$id=(int)$d['id'];$o=q('SELECT logo FROM companies WHERE id=? AND user_id=?',[$id,$u])->fetch();
 if($o){if($o['logo']&&is_file($o['logo']))unlink($o['logo']);q('DELETE FROM companies WHERE id=? AND user_id=?',[$id,$u]);}out(['ok'=>1]);
case 'activities':out(q('SELECT * FROM activities WHERE application_id=? AND user_id=? ORDER BY happened_at DESC',[(int)$_GET['id'],$u])->fetchAll());
case 'activity_add':if(!q('SELECT 1 FROM applications WHERE id=? AND user_id=?',[(int)$d['app_id'],$u])->fetch())fail('Introuvable',404);
 if(!in_array($d['type'],['appel','email','entretien','note','relance'],true))fail('Type invalide');
 q('INSERT INTO activities(application_id,user_id,type,content) VALUES(?,?,?,?)',[(int)$d['app_id'],$u,$d['type'],trim($d['content']??'')]);out(['ok'=>1]);
case 'activity_delete':q('DELETE FROM activities WHERE id=? AND user_id=?',[(int)$d['id'],$u]);out(['ok'=>1]);
default:fail('Action inconnue',404);}
}catch(Throwable $x){fail('Erreur serveur',500);}
