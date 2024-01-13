<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$strEventType = strtolower(trim($_GET["ty"]));
$strStartDate = trim($_GET["sd"]);
$strEndDate   = trim($_GET["ed"]);

if ($strStartDate == "" || $strEndDate == "")
{
	$strStartDate = date("m/01/Y");
	$strEndDate   = date("m/d/Y", strtotime("+1 month", strtotime($strStartDate)));
}

if ($strEventType == "current")
{
	$strQuery = <<< QUERY
SELECT FROM_UNIXTIME(e.time_start, '%Y-%m-%d %H:%i:%s') AS time_start
,      e.time_end
,      ROUND(((UNIX_TIMESTAMP() - e.time_start) / 60 / 60), 2) AS duration_hours
,      e.node_name
,      e.cluster_nodes
,      e.reason
,      e.reason_uid
,      e.state
FROM   hpc_cluster_event_table AS e
WHERE  e.time_end = 0
ORDER BY e.time_start DESC;
QUERY;

}
else
{
	$strQuery = <<< QUERY
SELECT FROM_UNIXTIME(e.time_start, '%Y-%m-%d %H:%i:%s') AS time_start
,      FROM_UNIXTIME(e.time_end, '%Y-%m-%d %H:%i:%s') AS time_end
,      ROUND(((e.time_end - e.time_start) / 60 / 60), 2) AS duration_hours
,      e.node_name
,      e.cluster_nodes
,      e.reason
,      e.reason_uid
,      e.state
FROM   hpc_cluster_event_table AS e
WHERE  e.time_end > 0
AND    e.time_start >= UNIX_TIMESTAMP(STR_TO_DATE('$strStartDate', '%m/%d/%Y'))
AND    e.time_start < UNIX_TIMESTAMP(STR_TO_DATE('$strEndDate', '%m/%d/%Y'))
ORDER BY e.time_start DESC;
QUERY;

}

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Events</title>

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

      <h2>SLURM Events</h2>

<?php

if ($strEventType == "current")
{

?>

      <p>A list of current SLURM events.</p>

<?php

}
else
{

?>

      <p>A list of completed SLURM events.</p>

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

<?php

}

?>

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
	SORT_DIRECTION = "desc"

</script>
