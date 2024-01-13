<?php

include("includes/functions.php");

$strUsername = trim($_POST["un"]);
$strPassword = trim($_POST["pw"]);
$strHashKey  = trim($_POST["hk"]);

if ($strUsername != "" && $strPassword != "" && $strHashKey != "")
{
	$strPasswordHash = DoEncrypt($strPassword, $strHashKey);
	$strTextDecrypt  = DoDecrypt($strPasswordHash, $strHashKey);
}

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Generate Hashes</title>

<?php

include("includes/scripts.php");

?>

</head>
<body>

<?php

include("includes/header.php");

?>

  <div class="hr"></div>

  <div class="main">
    <div class="wrapper">

      <h2>Generate Hashes</h2>

      <p>Generate a new password hash via a hash key.</p>

      <form name="f" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

      <p class="options"><span class="label">Username:</span><br />
      <input class="text" name="un" type="text" value="<?php echo $strUsername; ?>"><br />
      <span class="label">Password:</span><br />
      <input class="text" name="pw" type="text" value="<?php echo $strPassword; ?>"><br />
      <span class="label">Hash Key:</span><br />
      <input class="text" name="hk" type="text" value="<?php echo $strHashKey; ?>"><br />
      <input class="button" type="submit" value="Go!">
      </p>

      </form>

      <div class="hr"></div>

<?php

if ($strUsername != "" && $strPassword != "" && $strHashKey != "" && ($strPassword == $strTextDecrypt))
{

?>

      <p>$DB_USER = &quot;<?php echo $strUsername; ?>&quot;;<br/>
      $DB_PASS_HASH = &quot;<?php echo $strPasswordHash; ?>&quot;;<br/>
      $DB_HASH_KEY = &quot;<?php echo $strHashKey; ?>&quot;;</p>

<?php

}

?>

    </div>
  </div>

</html>
