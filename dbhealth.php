<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$strQuery = <<< QUERY
SELECT engine
,      CONCAT(table_schema, '.', table_name) AS table_name
,      table_rows
,      ROUND(data_length / 1024 / 1024, 2) AS data_size_gb
,      ROUND(index_length / 1024 / 1024, 2) AS index_size_gb
,      ROUND(data_free / 1024 / 1024, 2) AS data_free_gb
,      (data_free / (index_length + data_length)) AS frag_ratio
FROM   information_schema.tables
WHERE  table_schema LIKE '%slurm%'
ORDER BY table_name ASC;
QUERY;

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Database Health</title>

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

      <h2>SLURM Database Health</h2>

      <p>Data fragmentation metrics on the SLURM database.</p>

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

	SORT_COLUMN    = 1;
	SORT_DIRECTION = "asc"

</script>
