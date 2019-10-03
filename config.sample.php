<?PHP
	// CONFIGURE YOUR DATABASE SETTINGS HERE...
	$dbhost = '';
	$dbuser = '';
	$dbpass = '';
	$dbname = '';

	// SET THE POSSIBLE CHARACTERS A RANDOM SHORT URL CAN USE...
	// WE RECOMMEND USING 'abcdefghjkmnpqrstuvwxyz23456789', WHICH EXCLUDES AMBIGUOUS CHARACTERS SUCH AS O, 0, 1, I, L, etc.
	$random_chars = 'abcdefghjkmnpqrstuvwxyz23456789';
	// SET THE ALLOWED CHARS WHEN PROCESSING URL
	$valid_chars = 'abcdefghijklmnopqrstuvwxyz1234567890-_+~.';

	// LENGTH OF YOUR SHORT URL SLUG...
	$slug_length = 5;

	// A PASSWORD TO ACCESS LINK ACTIVITY STATISTICS (LEAVE BLANK TO ALLOW ANYONE TO ACCESS)
	$pw_stats = '';

	// A PASSWORD TO ALLOW CREATING NEW LINKS (LEAVE BLANK TO ALLOW ANYONE TO ACCESS)
	$pw_create = '';

	// A URL TO REDIRECT TO IF THE RAW INDEX PAGE IS ACCESSED (i.e. not creating a link, visiting a link, or viewing stats)
	$url_blank = '';

	// A URL TO REDIRECT TO IF A "404 NOT FOUND" IS GENERATED. LEAVE BLANK TO JUST SHOW AN ERROR.
	$url_not_found = '';
