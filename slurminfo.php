<?php

include("includes/db.php");
include("includes/functions.php");

exec("/usr/bin/munge -V", $arrMungeVersion, $intReturn);
exec("/usr/bin/sinfo -V", $arrSLURMVersion, $intReturn);
exec("/usr/bin/mysql -V", $arrMySQLVersion, $intReturn);
exec("/usr/bin/sinfo -O ALL", $arrSinfoResults, $intReturn);

$strMungeVersion = "";
$strSLURMVersion = "";
$strMySQLVersion = "";

if (is_array($arrMungeVersion))
{
	foreach ($arrMungeVersion as $strLine)
	{
		if (trim($strLine) != "")
		{
			$strMungeVersion = $strMungeVersion . " " . $strLine;
		}
	}
}

if (is_array($arrSLURMVersion))
{
	foreach ($arrSLURMVersion as $strLine)
	{
		if (trim($strLine) != "")
		{
			$strSLURMVersion = $strSLURMVersion . " " . $strLine;
		}
	}
}

if (is_array($arrMySQLVersion))
{
	foreach ($arrMySQLVersion as $strLine)
	{
		if (trim($strLine) != "")
		{
			$strMySQLVersion = $strMySQLVersion . " " . $strLine;
		}
	}
}

$strMungeVersion = trim($strMungeVersion);
$strSLURMVersion = trim($strSLURMVersion);
$strMySQLVersion = trim($strMySQLVersion);

?>

<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SLURM Database Tool - SLURM Info</title>

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

      <h2>SLURM Information</h2>

      <p>SLURM component versions and node information.</p>

      <div class="hr"></div>

      <p><span class="bold">SLURM:</span> <?php echo $strSLURMVersion; ?>, <span class="bold">Munge:</span> <?php echo $strMungeVersion; ?>, <span class="bold">MariaDB:</span> <?php echo $strMySQLVersion; ?></p>

      <table id="grid" class="grid">

<?php

if (is_array($arrSinfoResults))
{
	$i = 0;

	foreach ($arrSinfoResults as $strLine)
	{
		if (stristr($strLine, "|") != FALSE)
		{
			$arrLineSplit = preg_split("/\|/", $strLine);

			if ($i == 0)
			{

?>

        <thead>
          <tr>

<?php

				foreach ($arrLineSplit as $strLinePart)
				{

?>

            <th><?php echo $strLinePart; ?></th>

<?php

				}

?>

          </tr>
        </thead>
        <tbody>

<?php

			}
			else
			{

?>

          <tr>

<?php

				foreach ($arrLineSplit as $strLinePart)
				{

?>

            <td><?php echo $strLinePart; ?></td>

<?php

				}

?>

          </tr>

<?php

			}
		}

		$i++;
	}

?>

        </tbody>

<?php

}

?>

      </table>

    </div>
  </div>

</html>


<script>

	SORT_COLUMN    = 0;
	SORT_DIRECTION = "asc"

</script>
