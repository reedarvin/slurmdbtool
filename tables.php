<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$fCustomQuery = FALSE;

$strQuery = trim($_GET["q"]);

if ($strQuery == "")
{
	$strQuery = <<< QUERY
SELECT table_schema
,      table_name
FROM   information_schema.tables
WHERE  LOWER(table_schema) = 'slurm_acct_db'
ORDER BY table_schema ASC, table_name ASC;
QUERY;
}
else
{
	$fCustomQuery = TRUE;

	if (substr($strQuery, -1) != ";")
	{
		$strQuery = $strQuery . ";";
	}
}

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool</title>

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

      <h2>SLURM Tables</h2>

      <p>A list of tables in the SLURM database.</p>

      <form name="f" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

      <p class="options"><span class="label">Query Database:</span><br />
      <input class="query" name="q" type="text" value="<?php if ($fCustomQuery === TRUE ) { echo $strQuery; } ?>">
      <input class="button" type="submit" value="Go!">
      </p>

      </form>

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
		$j = 0;

		foreach ($arrResultSet[$i] as $strValue)
		{
			if ($fCustomQuery === FALSE && $j == 1)
			{

?>

            <td><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?q=SELECT * FROM <?php echo $strValue; ?> LIMIT 0, 1000"><?php echo $strValue; ?></a></td>

<?php

			}
			else
			{

?>

            <td><?php echo $strValue; ?></td>

<?php

			}

			$j++;
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
