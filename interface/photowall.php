<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="styles.css">
	<meta charset=UTF-8>
	<title>TwitStat - Photowall</title>
<head>
<body>

		<form action="login.php" method='post' id="sign">
			<input type='submit' name='signIn' value='Logout' />
		</form>
	
		<div id="banner">
		    twitStats
			<br>
		</div>
		<table border="0">
		<tr>
			<td>
				<form action="index.php" method='post' >
					<input type='submit' name='submit' value="Home" class="buttons"/>
					<br>
				</form>
			</td>	
			<td>	
				<form action="search.php" method='post' >
					<input type='submit' name='submit' value="Search" class="buttons"/>
					<br>
				</form>
			</td>
			<td>
				<form action="photowall.php" method='post' >
					<input type='submit' name='submit' value="Photowall" class="buttons"/>
					<br>
				</form>
			</td>	
			<td>	
				<form action="map.php" method='post' >
					<input type='submit' name='submit' value="Map" class="buttons"/>
					<br>
				</form>
			</td>	
			<td>	
				<form action="pie.php" method='post' >
					<input type='submit' name='submit' value="Pie" class="buttons"/>
					<br>
				</form>
			</td>	
		</table>	
	<center>
<?php
	
	//get page number from url
	$curr_page = $_GET['page'];
	if ($curr_page < 1 || empty($curr_page)) $curr_page = 1;
	
	/*if ($curr_page == 1)
	{
		echo "\t\t<p>Welcome to the Photo Wall!  Browse the tweeters below and click on their profile image to view more info...</p>\n";
	}*/
 	
	//connect to database
	include("../../secure/database.php");
        $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	$user_info = pg_prepare($conn, 'get-user-info', "SELECT usr_id, profile_img_url FROM twitStat.twit_user LIMIT 150 OFFSET $1")
		or die("Failed to prepare query for user info: ". pg_last_error());
	$user_info = pg_execute($conn, 'get-user-info', array(150*($curr_page-1))) or die("Failed to execute user info query");
	
	//upper page navigation links
	echo "\t<table id=\"picwall\" >\n\t\t<tr>\n"
		."\t\t  <td align=\"left\">".(($curr_page >= 2)? "<a href=\"photowall.php?page=" .($curr_page-1). "\"> Previous Page</a><br/><br/>" : " " ) . "</td>".
		"\n\t\t  <td align=\"right\"><a href=\"photowall.php?page=" .($curr_page+1). "\">Next Page </a><br/><br/></td>\n"
		. "\t\t</tr>\n\t</table>\n";

	//table for tweeter pictures
	echo "\t<table id=\"picwall\" >\n\t\t<tr>\n";
	$colCounter = 1;
	$picCounter = 0;
	while ( ($picCounter < 50 )  && $line = pg_fetch_array($user_info, null, PGSQL_ASSOC)){
		if (@getimagesize($line['profile_img_url']) )
		{
			echo "\t\t  <td align=\"center\"><a href= \"tweeter.php?id=" .$line['usr_id']. "&page=".$curr_page. "\">"
				. "<img src=\"" . $line['profile_img_url'] 
				. "\" onerror=\"this.src='https://lh3.ggpht.com/lSLM0xhCA1RZOwaQcjhlwmsvaIQYaP3c5qbDKCgLALhydrgExnaSKZdGa8S3YtRuVA=w300';"
				. "\" height=\"100\" width = \"100\"></a></td>\n";
			if (0 == ($colCounter++ % 10) ) {
				echo "\t\t</tr>\n\t\t<tr>";
			}
			$picCounter++;
		}
	}
	echo "\t\t</tr>\n\t</table>\n";
		
	//lower page navigation links
	echo "\t<table id=\"picwall\">\n\t\t<tr>\n"
		."\t\t  <td align=\"left\">".(($curr_page >= 2)? "<br/><a href=\"photowall.php?page=" .($curr_page-1). "\"> Previous Page</a>" : " " ) . "</td>".
		"\n\t\t  <td align=\"right\"><br/><a href=\"photowall.php?page=" .($curr_page+1). "\">Next Page </a></td>\n"
		. "\t\t</tr>\n\t</table><br/><br/>\n";
		
	pg_free_result($user_info);
	pg_close($conn);
?>
	</center>
</body>
</html>
