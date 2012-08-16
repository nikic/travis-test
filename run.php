<?php

$db = new PDO('mysql:host=localhost;dbname=information_schema', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//var_dump($db->query('SHOW COLLATION')->fetchAll());


require_once("mysqli_connect.php");

	$tmp    = NULL;
	$link   = NULL;

	// Note: no SQL type tests, internally the same function gets used as for mysqli_fetch_array() which does a lot of SQL type test
	if (!is_null($tmp = @mysqli_fetch_field()))
		printf("[001] Expecting NULL, got %s/%s\n", gettype($tmp), $tmp);

	if (!is_null($tmp = @mysqli_fetch_field($link)))
		printf("[002] Expecting NULL, got %s/%s\n", gettype($tmp), $tmp);

	require('mysqli_table.php');

	$charsets = my_get_charsets($link);
	if (!$res = mysqli_query($link, "SELECT id AS ID, label FROM test AS TEST ORDER BY id LIMIT 1")) {
		printf("[003] [%d] %s\n", mysqli_errno($link), mysqli_error($link));
	}

	/* ID column, binary charset */
	$tmp = mysqli_fetch_field($res);
	var_dump($tmp);

	/* label column, result set charset */
	$tmp = mysqli_fetch_field($res);
	var_dump($tmp);
	if ($tmp->charsetnr != $charsets['results']['nr']) {
		printf("[004] Expecting charset %s/%d got %d\n",
			$charsets['results']['charset'],
			$charsets['results']['nr'], $tmp->charsetnr);
	}
	if ($tmp->length != (1 * $charsets['results']['maxlen'])) {
		printf("[005] Expecting length %d got %d\n",
			$charsets['results']['maxlen'],
			$tmp->max_length);
	}
	if ($tmp->db != $db) {
		printf("011] Expecting database '%s' got '%s'\n",
		  $db, $tmp->db);
	}

	var_dump(mysqli_fetch_field($res));

	mysqli_free_result($res);

	// Read http://bugs.php.net/bug.php?id=42344 on defaults!
	if (NULL !== ($tmp = mysqli_fetch_field($res)))
		printf("[006] Expecting NULL, got %s/%s\n", gettype($tmp), $tmp);

	if (!mysqli_query($link, "DROP TABLE IF EXISTS test"))
		printf("[007] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	if (!mysqli_query($link, "CREATE TABLE test(id INT NOT NULL DEFAULT 1)"))
		printf("[008] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	if (!mysqli_query($link, "INSERT INTO test(id) VALUES (2)"))
		printf("[009] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	if (!$res = mysqli_query($link, "SELECT id as _default_test FROM test")) {
		printf("[010] [%d] %s\n", mysqli_errno($link), mysqli_error($link));
	}
	var_dump(mysqli_fetch_assoc($res));
	/* binary */
	var_dump(mysqli_fetch_field($res));
	mysqli_free_result($res);

	mysqli_close($link);

	print "done!";
