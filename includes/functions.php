<?php

$HASH_KEY = "The quick brown fox jumped over the lazy dog.";

function DoEncrypt($strPlainText, $strHashKey)
{
	$strSecretKey = md5($strHashKey);

	$strIV = substr(hash("sha256", $HASH_KEY), 0, 16);

	$strEncryptedText = openssl_encrypt($strPlainText, "AES-128-CBC", $strSecretKey, OPENSSL_RAW_DATA, $strIV);

	return base64_encode($strEncryptedText);
}

function DoDecrypt($strEncryptedText, $strHashKey)
{
	$strHashKey = md5($strHashKey);

	$strIV = substr(hash("sha256", $HASH_KEY), 0, 16 );

	$strDecryptedText = openssl_decrypt(base64_decode($strEncryptedText), "AES-128-CBC", $strHashKey, OPENSSL_RAW_DATA, $strIV);

	return $strDecryptedText;
}

function DoQuery($strDBHost, $strDBUser, $strDBPass, $strQuery)
{
	$arrResultSet = array();

	$objConn = mysqli_connect($strDBHost, $strDBUser, $strDBPass);

	if ($objConn != FALSE)
	{
		mysqli_select_db($objConn, "slurm_acct_db");

		$objQuery = mysqli_query($objConn, $strQuery);

		$arrResultSet = array();

		if ($objQuery != FALSE)
		{
			if (mysqli_num_rows($objQuery) > 0)
			{
				while ($arrRow = mysqli_fetch_assoc($objQuery))
				{
					array_push($arrResultSet, $arrRow);
				}
			}
		}
		else
		{
			echo "Oops! mysqli_query: " . mysqli_error($objConn) . "\n";
		}

		mysqli_close($objConn);
	}
	else
	{
		echo "Oops! mysqli_connect: " . mysqli_connect_error() . "\n";
	}

	return $arrResultSet;
}

?>
