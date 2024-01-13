<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$strStartDate = trim($_GET["sd"]);
$strEndDate   = trim($_GET["ed"]);

if ($strStartDate == "" || $strEndDate == "")
{
	$strStartDate = date("m/01/Y");
	$strEndDate   = date("m/d/Y", strtotime("+1 month", strtotime($strStartDate)));
}

$strQuery = <<< QUERY
SELECT q.alloc_hours
,      q.down_hours
,      q.idle_hours
,      q.plan_hours
,      (q.alloc_hours + q.down_hours + q.idle_hours + q.plan_hours) as rep_hours
,      q.type FROM ( SELECT ROUND(SUM(uh.alloc_secs) / 60 / 60, 0) AS alloc_hours
                     ,      ROUND(SUM(uh.down_secs) / 60 / 60, 0) AS down_hours
                     ,      ROUND(SUM(uh.idle_secs) / 60 / 60, 0) AS idle_hours
                     ,      ROUND(SUM(uh.plan_secs) / 60 / 60, 0) AS plan_hours
                     ,      tt.type
                     FROM   hpc_cluster_usage_hour_table uh
                     INNER JOIN tres_table AS tt ON (uh.id_tres = tt.id)
                     WHERE  uh.time_start >= UNIX_TIMESTAMP(STR_TO_DATE('$strStartDate', '%m/%d/%Y'))
                     AND    uh.time_start < UNIX_TIMESTAMP(STR_TO_DATE('$strEndDate', '%m/%d/%Y'))
                     GROUP BY tt.type ) AS q
WHERE  alloc_hours IS NOT NULL
ORDER BY q.type ASC;
QUERY;

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Usage Metrics</title>

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

      <h2>Usage Metrics</h2>

      <p>SLURM hourly usage metrics.</p>

      <form name="f" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

      <p class="options"><a href="<?php echo $_SERVER["PHP_SELF"] . "?sd=" . date("m/01/Y", strtotime("-1 month", strtotime(date("m/01/Y")))) . "&ed=" . date("m/01/Y"); ?>">[Show last month]</a><br/>
      <br/>
      <span class="label">Start Date:</span><br />
      <input class="date" name="sd" type="text" value="<?php echo $strStartDate; ?>"><br />
      <span class="label">End Date:</span><br />
      <input class="date" name="ed" type="text" value="<?php echo $strEndDate; ?>"><br />
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

	SORT_COLUMN    = 5;
	SORT_DIRECTION = "asc"

</script>
