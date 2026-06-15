<?php
session_start();
$file = "profile.json";
$hobbies = json_decode(file_get_contents($file), true);
function save($file,$data){
   file_put_contents($file, json_encode($data));
}
function exists($arr,$value){
   foreach($arr as $a){
       if(strtolower($a) == strtolower($value)){
           return true;
       }
   }
   return false;
}
if($_SERVER["REQUEST_METHOD"] === "POST"){
   $action = $_POST["action"];
   if($action == "add"){
       $hobby = trim($_POST["hobby"]);
       if($hobby == ""){
           $_SESSION["msg"] = "Pole nesmí být prázdné.";
       }
       elseif(exists($hobbies,$hobby)){
           $_SESSION["msg"] = "Tento zájem už existuje.";
       }
       else{
           $hobbies[] = $hobby;
           save($file,$hobbies);
           $_SESSION["msg"] = "Zájem byl přidán.";
       }
   }
   if($action == "delete"){
       $id = $_POST["id"];
       unset($hobbies[$id]);
       $hobbies = array_values($hobbies);
       save($file,$hobbies);
       $_SESSION["msg"] = "Zájem byl odstraněn.";
   }
   if($action == "edit"){
       $id = $_POST["id"];
       $new = trim($_POST["new"]);
       if($new == ""){
           $_SESSION["msg"] = "Pole nesmí být prázdné.";
       }
       elseif(exists($hobbies,$new)){
           $_SESSION["msg"] = "Tento zájem už existuje.";
       }
       else{
           $hobbies[$id] = $new;
           save($file,$hobbies);
           $_SESSION["msg"] = "Zájem byl upraven.";
       }
   }
   header("Location: index.php");
   exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Zájmy</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Moje zájmy</h1>
<?php
if(isset($_SESSION["msg"])){
   echo "<p class='msg'>".htmlspecialchars($_SESSION["msg"])."</p>";
   unset($_SESSION["msg"]);
}
?>
<ul>
<?php foreach($hobbies as $i=>$h): ?>
<li>
<span><?= htmlspecialchars($h) ?></span>
<form method="post">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $i ?>">
<button>Smazat</button>
</form>
<form method="post">
<input type="text" name="new" value="<?= htmlspecialchars($h) ?>">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?= $i ?>">
<button>Upravit</button>
</form>
</li>
<?php endforeach; ?>
</ul>
<h2>Přidat zájem</h2>
<form method="post">
<input type="text" name="hobby">
<input type="hidden" name="action" value="add">
<button>Přidat</button>
</form>
</body>
</html>
