function insert_csrf_token()
{
  /* insert_csrf_token()
    * Description:
    *   Inserts a hashed token and time of request into the form to protect against
    *    CSRF.
    * Parameters:
    *   None.
    * Sample Usage:
    *   <form action"">
	*	<?php insert_csrf_token();?>
	*		do stuff
	*	</form>
    * Return Argument:
    *   Two hidden form inputs with the time and hashed token.
    */
    $time = time();
    printf('<input type="hidden" value="%s" name="csrf_time">', $time);
    printf('<input type="hidden" value="%s" name="csrf_token">'
    		, create_csrf_token($time));
}
function create_csrf_token($time)
{
	/* create_csrf_token($time)
    * Description:
    *   Creates a hashed token using the time provided, shibboleth's
    *	persistent id, and a salt defined outside of the public domain 
    *   (home/csis/.csis-config.php).
    * Parameters:
    *   $time - either the current time from insert_csrf_token, or the time
    *		from the posted form from check_csrf_token.
    * Sample Usage:
    *   printf('<input type="hidden" value="%s" name="csrf_token">'
    *		, create_csrf_token(time()));
    * Return Argument:
    *   The sha1 hashed token using the time provided.
    */
    global $csrf_salt;
    $token = sha1( $time . $_SERVER['persistent_id'] . $csrf_salt );

    return $token;
}
function check_csrf_token()
{
	/* check_csrf_token()
    * Description:
    *   Checks a posted token by creating a new token using the post time provided
    *	and matching that value against the token that was passed with the form.
    * Parameters:
    *   None.
    * Sample Usage:
    *  if ($_POST['submit']) {
	*		if (!check_csrf_token()) {
	*			exit();
	*		}
	*	}
    * Return Argument:
    *   Returns true if the check passed, false if the check failed.
    */
    try {
        $max_life_s = 5*60;
        if (isset($_POST['csrf_token'])) {
            $their_token = $_POST['csrf_token'];
            $their_time  = $_POST['csrf_time'];
        } elseif (isset($_GET['csrf_token'])) {
            $their_token = $_GET['csrf_token'];
            $their_time  = $_GET['csrf_time'];
        }
        $our_token = create_csrf_token( $their_time );
        if ( $their_token == $our_token && $their_time - time() <= $max_life_s ) {
            return true;
        }
    } catch ( Exception $e ) {
        error_log( "CSRF Validation failed: $e \n", 3, PATH_ERROR_LOG);
    }

    return false;
}
