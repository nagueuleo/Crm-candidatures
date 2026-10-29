<?php
session_start();
const DB=['host'=>'localhost','name'=>'crm_candidatures','user'=>'root','pass'=>'Unique@20262027'];
function db(){static $p;return $p??=new PDO('mysql:host='.DB['host'].';dbname='.DB['name'].';charset=utf8mb4',DB['user'],DB['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
function csrf(){return $_SESSION['csrf']??=bin2hex(random_bytes(16));}
function auth(){if(empty($_SESSION['uid'])){header('Location: login.php');exit;}}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES);}