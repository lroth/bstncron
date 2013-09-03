<?php
	/* FETCHES INSTAGRAM -feed and writes it into database. Marks the winners */

    // Get class for Instagram
    // More examples here: https://github.com/cosenary/Instagram-PHP-API

    require_once 'instagram.class.php';
	require_once "db.inc.php";

	//should be 18 in production
	define("NTH_WINNER", 18);

    // Initialize class with client_id
    $instagram = new Instagram('9a4352feb59a4634991c881f37d1d33c');

	// Set keyword for should be (bostons1800) in production
	$tag = 'bostons1800';

    try {

		$dbh = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8", $db_user, $db_pass );
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->exec("SET NAMES 'utf8'");

	    // Get latest photos according to #hashtag keyword
	    $media = $instagram->getTagMedia($tag);

		// populare counter
		$sql = $dbh->prepare("SELECT count(*) FROM instagram_1800");
		$sql->execute();

		$counter = $sql->fetchColumn();
	    foreach( $media->data as $data)
	    {

			if ($data->type == 'image') {
		        $image_id = ($data->id);
				$image_url = ($data->images->low_resolution->url);

				$username = strip_symbols($data->user->username);
				$full_name = strip_symbols($data->user->full_name);
				$instagram_link = ($data->link);

				$comments = ($data->comments->count);
				$likes = ($data->likes->count);

				$created_time = ($data->created_time);


				try {
			    	$winner = ( $counter % NTH_WINNER ) ? 0 : 1;

					$sql = $dbh->prepare("INSERT INTO instagram_1800 (image_id, image_url, instagram_link, username, full_name, likes, comments, winner, instagram_created) VALUES (?,?,?,?,?,?,?,?,?)");
					$sql->execute( array( $image_id, $image_url, $instagram_link, $username, $full_name, $likes, $comments, $winner, $created_time ) );

					//if insert succeeds ++
					$counter++;

				// image_id is primary key -> catch if image already exists or any other PDO -error
		        } catch (PDOException $e) {

					try {
			        	// try to update comments
						$sql = $dbh->prepare("UPDATE instagram_1800 SET likes = ?, comments = ? WHERE image_url = ?");
						$sql->execute( array( $likes, $comments , $image_url));

					} catch (PDOException $e) {
						print ($e->getMessage() );
					}

					print ($e->getMessage() );
				}
			}

            $dbh = null;
	    }

    }  catch (PDOException $e) {
    	print ($e->getMessage() );
	}


	/**
 * Strip symbols from text.
 */
function strip_symbols( $text )
{
    $plus   = '\+\x{FE62}\x{FF0B}\x{208A}\x{207A}';
    $minus  = '\x{2012}\x{208B}\x{207B}';

    $units  = '\\x{00B0}\x{2103}\x{2109}\\x{23CD}';
    $units .= '\\x{32CC}-\\x{32CE}';
    $units .= '\\x{3300}-\\x{3357}';
    $units .= '\\x{3371}-\\x{33DF}';
    $units .= '\\x{33FF}';

    $ideo   = '\\x{2E80}-\\x{2EF3}';
    $ideo  .= '\\x{2F00}-\\x{2FD5}';
    $ideo  .= '\\x{2FF0}-\\x{2FFB}';
    $ideo  .= '\\x{3037}-\\x{303F}';
    $ideo  .= '\\x{3190}-\\x{319F}';
    $ideo  .= '\\x{31C0}-\\x{31CF}';
    $ideo  .= '\\x{32C0}-\\x{32CB}';
    $ideo  .= '\\x{3358}-\\x{3370}';
    $ideo  .= '\\x{33E0}-\\x{33FE}';
    $ideo  .= '\\x{A490}-\\x{A4C6}';

    return preg_replace(
        array(
        // Remove modifier and private use symbols.
            '/[\p{Sk}\p{Co}]/u',
        // Remove mathematics symbols except + - = ~ and fraction slash
            '/\p{Sm}(?<![' . $plus . $minus . '=~\x{2044}])/u',
        // Remove + - if space before, no number or currency after
            '/((?<= )|^)[' . $plus . $minus . ']+((?![\p{N}\p{Sc}])|$)/u',
        // Remove = if space before
            '/((?<= )|^)=+/u',
        // Remove + - = ~ if space after
            '/[' . $plus . $minus . '=~]+((?= )|$)/u',
        // Remove other symbols except units and ideograph parts
            '/\p{So}(?<![' . $units . $ideo . '])/u',
        // Remove consecutive white space
            '/ +/',
        ),
        ' ',
        $text );
}