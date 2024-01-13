<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$strQuery = <<< QUERY
SHOW GLOBAL VARIABLES;
QUERY;

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Database Settings</title>

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

      <h2>SLURM Database Settings</h2>

      <p>Global variables for all MariaDB databases.</p>

      <div class="hr"></div>

<?php

if (is_array($arrResultSet[0]))
{
	$arrColumns = array_keys($arrResultSet[0]);

?>

      <table id="grid" class="grid">
        <thead>
          <tr>

<?php

	foreach ($arrColumns as $strColumn)
	{

?>

            <th><?php echo $strColumn; ?></th>

<?php

	}

?>

          </tr>

        </thead>
        <tbody>

<?php

	$intRows = count($arrResultSet);

	for ($i = 0; $i < $intRows; $i++)
	{

?>

          <tr>

<?php

		foreach ($arrResultSet[$i] as $strValue)
		{

?>

            <td><?php echo $strValue; ?></td>

<?php

		}

?>

          </tr>

<?php

	}

?>

        </tbody>
      </table>

<?php

}
else
{

?>

      <p>Oops! Query didn't return any results.</p>

<?php

}

?>

    </div>
  </div>

</html>

<script>

	SORT_COLUMN    = 0;
	SORT_DIRECTION = "asc"

</script>
