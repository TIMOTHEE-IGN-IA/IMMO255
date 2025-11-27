Skip to content
Navigation Menu
TIMOTHEE-IGN-IA
IMMO255

Type / to search
Code
Issues
Pull requests
Actions
Projects
Wiki
Security
Insights
Settings
Files
Go to file
t
admin
config
config.php
database.php
public
.htaccess
annonce-detail.php
annonce.php
annonce_detail.php
auth.php
config.php
delete_annonce.php
edit_annonce.php
envoyer_demande.php
get_notifications_count.php
index.php
load.php
login.php
logout.php
marquer_lu.php
notifications.php
photo.php
photo_cover.php
profil.php
publish.php
register.php
routes.php
send_interest.php
update_annonce.php
update_profile.php
IMMO255/config
/
database.php
in
main

Edit

Preview
 @@ -1,19 +1,25 @@
 <?php
 <?php
// config/database.php
// config/database.php → Version Railway ULTRA FIABLE (2025)
$host = 'localhost';

$db   = 'immo225';
$host     = $_ENV['MYSQLHOST'] ?? 'trolley.proxy.rlwy.net';
$user = 'root';
$port     = $_ENV['MYSQLPORT'] ?? '46405';
$pass = '';
$db       = $_ENV['MYSQLDATABASE'] ?? 'railway';
$charset = 'utf8mb4';
$user     = $_ENV['MYSQLUSER'] ?? 'root';
$pass     = $_ENV['MYSQLPASSWORD'] ?? 'NyHYVYPPDtlyWDsgKKUHUtXEjioNMQvR';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
 
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
 $options = [
 $options = [
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
 ];
 ];
 
 
 try {
 try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $pdo = new PDO($dsn, $user, $pass, $options);
    // die("Connexion OK ! Base de données connectée !"); // ← décommente pour tester
 } catch (\Throwable $e) {
 } catch (\Throwable $e) {
    die('Connexion échouée');
    die('Connexion échouée : ' . $e->getMessage());
}
}
?>
While the code is focused, press Alt+F1 for a menu of operations.
