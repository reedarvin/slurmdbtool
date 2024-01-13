<?php

include("includes/db.php");
include("includes/functions.php");

$DB_PASS = DoDecrypt($DB_PASS_HASH, $DB_HASH_KEY);

$strJobType   = strtolower(trim($_GET["ty"]));
$strStartDate = trim($_GET["sd"]);
$strEndDate   = trim($_GET["ed"]);
$strUsername  = trim($_GET["un"]);
$strJobID     = trim($_GET["jid"]);

if ($strStartDate == "" || $strEndDate == "")
{
	$strEndDate   = date("m/d/Y");
	$strStartDate = date("m/d/Y", strtotime("-1 day", strtotime($strEndDate)));
}

if ($strJobType == "current")
{
	$strQuery = <<< QUERY
SELECT j.job_db_inx
,      CASE
            WHEN (j.time_start = 0 AND j.time_end = 0) THEN 'queued'
            WHEN (j.time_start = 0 AND j.time_end > 0) THEN 'cancelled'
            WHEN (j.time_start > 0 AND j.time_end = 0) THEN 'running'
            ELSE 'completed'
       END AS job_status
,      FROM_UNIXTIME(j.time_submit, '%Y-%m-%d %H:%i:%s') AS time_submit
,      CASE
            WHEN j.time_start = 0 THEN 0
            ELSE FROM_UNIXTIME(j.time_start, '%Y-%m-%d %H:%i:%s')
       END AS time_start
,      j.time_end
,      ROUND(((UNIX_TIMESTAMP() - j.time_submit) / 60 / 60), 2) AS duration_hours
,      j.account
,      (SELECT user FROM hpc_cluster_assoc_table WHERE id_assoc = j.id_assoc) AS user
,      j.job_name
,      j.work_dir
,      j.cpus_req
,      j.partition
,      j.nodelist
,      j.timelimit
,      j.kill_requid
FROM   hpc_cluster_job_table AS j
WHERE  j.time_end = 0
ORDER BY j.job_db_inx DESC;
QUERY;

}
elseif ($strJobType != "current" && $strUsername == "" && $strJobID == "")
{
	$strQuery = <<< QUERY
SELECT a.user
,      COUNT(j.job_db_inx) AS job_count
FROM   hpc_cluster_job_table AS j
INNER JOIN hpc_cluster_assoc_table AS a ON (j.id_assoc = a.id_assoc)
WHERE  j.time_end >= UNIX_TIMESTAMP(STR_TO_DATE('$strStartDate', '%m/%d/%Y'))
AND    j.time_end < UNIX_TIMESTAMP(STR_TO_DATE('$strEndDate', '%m/%d/%Y'))
GROUP BY a.user
ORDER BY a.user ASC
QUERY;

}
elseif ($strJobType != "current" && $strUsername != "" && $strJobID == "")
{
	$strQuery = <<< QUERY
SELECT j.job_db_inx
,      CASE
            WHEN (j.time_start = 0 AND j.time_end = 0) THEN 'queued'
            WHEN (j.time_start = 0 AND j.time_end > 0) THEN 'cancelled'
            WHEN (j.time_start > 0 AND j.time_end = 0) THEN 'running'
            ELSE 'completed'
       END AS job_status
,      FROM_UNIXTIME(j.time_submit, '%Y-%m-%d %H:%i:%s') AS time_submit
,      CASE
            WHEN j.time_start = 0 THEN 0
            ELSE FROM_UNIXTIME(j.time_start, '%Y-%m-%d %H:%i:%s')
       END AS time_start
,      FROM_UNIXTIME(j.time_end, '%Y-%m-%d %H:%i:%s') AS time_end
,      CASE
            WHEN j.time_start = 0 THEN 0
            ELSE ROUND(((j.time_end - j.time_start) / 60 / 60), 2)
       END AS duration_hours
,      j.account
,      (SELECT user FROM hpc_cluster_assoc_table WHERE id_assoc = j.id_assoc) AS user
,      j.job_name
,      j.work_dir
,      j.cpus_req
,      j.partition
,      j.nodelist
,      j.timelimit
,      j.kill_requid
FROM   hpc_cluster_job_table AS j
WHERE  (SELECT user FROM hpc_cluster_assoc_table WHERE id_assoc = j.id_assoc) = '$strUsername'
AND    j.time_end > 0
AND    j.time_end >= UNIX_TIMESTAMP(STR_TO_DATE('$strStartDate', '%m/%d/%Y'))
AND    j.time_end < UNIX_TIMESTAMP(STR_TO_DATE('$strEndDate', '%m/%d/%Y'))
ORDER BY j.job_db_inx DESC;
QUERY;

}
else
{
	$strQuery = <<< QUERY
SELECT j.job_db_inx
,      CASE
            WHEN s.time_start = 0 THEN 0
            ELSE FROM_UNIXTIME(s.time_start, '%Y-%m-%d %H:%i:%s')
       END AS time_start
,      CASE
            WHEN s.time_end = 0 THEN 0
            ELSE FROM_UNIXTIME(s.time_end, '%Y-%m-%d %H:%i:%s')
       END AS time_end
,      CASE
            WHEN s.time_suspended = 0 THEN 0
            ELSE FROM_UNIXTIME(s.time_suspended, '%Y-%m-%d %H:%i:%s')
       END AS time_suspended
,      CASE
            WHEN s.time_end = 0 THEN 0
            WHEN s.time_start = 0 THEN 0
            ELSE ROUND(((s.time_end - s.time_start) / 60 / 60), 2)
       END AS duration_hours
,      j.account
,      a.user
,      j.job_name
,      s.id_step
,      s.step_name
,      s.exit_code
,      j.work_dir
,      j.cpus_req
,      j.partition
,      j.nodelist
,      j.timelimit
,      j.kill_requid
FROM   hpc_cluster_job_table AS j
INNER JOIN hpc_cluster_assoc_table AS a ON (j.id_assoc = a.id_assoc)
INNER JOIN hpc_cluster_step_table AS s ON (j.job_db_inx = s.job_db_inx)
WHERE  s.job_db_inx = '$strJobID'
ORDER BY s.id_step ASC;
QUERY;

}

$arrResultSet = DoQuery($DB_HOST, $DB_USER, $DB_PASS, $strQuery);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - Jobs</title>

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

      <h2>SLURM Jobs</h2>

<?php

if ($strJobType == "current")
{

?>

      <p>A list of current jobs submitted to SLURM.</p>

<?php

}
elseif ($strJobType != "current" && $strJobID == "")
{

?>

      <p>A list of completed jobs submitted to SLURM.</p>

      <form name="f" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

      <p class="options"><span class="label">Start Date:</span><br />
      <input class="date" name="sd" type="text" value="<?php echo $strStartDate; ?>"><br />
      <span class="label">End Date:</span><br />
      <input class="date" name="ed" type="text" value="<?php echo $strEndDate; ?>"><br />
      <span class="label">Username:</span><br />
      <input class="text" name="un" type="text" value="<?php echo $strUsername; ?>"><br />
      <input class="button" type="submit" value="Go!">
      </p>

      </form>

<?php

}
else
{

?>

      <p>Step details for SLURM job: <?php echo $strJobID; ?></p>

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

		$j = 0;

		foreach ($arrResultSet[$i] as $strValue)
		{
			if ($strJobType != "current" && $strUsername == "" && $strJobID == "" && $j == 0)
			{

?>

            <td><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?un=<?php echo $strValue; ?>&sd=<?php echo $strStartDate; ?>&ed=<?php echo $strEndDate; ?>"><?php echo $strValue; ?></a></td>

<?php

			}
			elseif ($strJobType != "current" && $strUsername != "" && $strJobID == "" && $j == 0)
			{

?>

            <td><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?jid=<?php echo $strValue; ?>"><?php echo $strValue; ?></a></td>

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
