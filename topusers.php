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
SELECT q.name
,      q.acct
,      q.shares
,      q.admin_level
,      q.alloc_hours
,      q.type FROM ( SELECT u.name
                     ,      a.acct
                     ,      a.shares
                     ,      u.admin_level
                     ,      ROUND(SUM(auh.alloc_secs) / 60 / 60, 2) AS alloc_hours
                     ,      tt.type
                     FROM   user_table u
                     INNER JOIN hpc_cluster_assoc_table AS a ON (u.name = a.user)
                     INNER JOIN hpc_cluster_assoc_usage_hour_table AS auh ON (a.id_assoc = auh.id)
                     INNER JOIN tres_table AS tt ON (auh.id_tres = tt.id)
                     WHERE  auh.time_start >= UNIX_TIMESTAMP(STR_TO_DATE('$strStartDate', '%m/%d/%Y'))
                     AND    auh.time_start < UNIX_TIMESTAMP(STR_TO_DATE('$strEndDate', '%m/%d/%Y'))
                     GROUP BY u.name, a.acct, u.admin_level, tt.type ) AS q
WHERE  q.name IS NOT NULL
ORDER BY q.alloc_hours DESC;
QUERY;

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Top Users</title>

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

      <h2>Top Users</h2>

      <p>A list of SLURM users by CPU usage.</p>

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

	SORT_COLUMN    = 4;
	SORT_DIRECTION = "desc"

</script>
