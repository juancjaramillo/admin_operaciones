<?php
dl("oci8.so");
/*
	OracleEditor.php
	$Revision: 1.20 $
	by Tim Strehle <tim@strehle.de>

	http://oracleeditor.sourceforge.net/

	OracleEditor.php is a standalone PHP script which allows you to browse
	your Oracle database tables and insert, update and delete rows in any
	table. It requires no installation and no configuration.

	I wrote it for situations where you quickly need to do some small data
	manipulation and don't have a better tool available. OracleEditor.php is
	free and Open Source.
	Feel free to contact me at tim@strehle.de with questions/comments.

	Disclaimer:
	Use this software at your own risk. I won't guarantee that it works, and
	I won't be held liable for any damage caused by it!

	Please make sure to protect this script with a .htaccess password or the
	like - never ever allow public access to it. Anyone capable of guessing
	your database login will be able to cause severe damage to your database.

	$Id: OracleEditor.php,v 1.20 2006/05/12 21:30:40 tim Exp $

	$Log: OracleEditor.php,v $
	Revision 1.20  2006/05/12 21:30:40  tim
	Allow slash and colon in service name for Oracle Instant Client connection
	syntax. Allow dollar sign in user name.
	Now works with php.ini setting "short_open_tag = Off".
	
	Revision 1.19  2005/06/28 09:11:54  tim
	For UTF-8 detection, use getenv('NLS_LANG') instead of $_SERVER[ 'NLS_LANG' ].

	Revision 1.18  2005/06/10 14:49:09  tim
	Added PHP version and required module check.
	Added "online help" via the HTML title attribute.
	Selecting a new table or modifying the SQL statement now cancels export mode.
	Make the browser scroll to the row to insert/update/delete row.
	When switching from popup to manual SQL entry mode, the current SELECT
	statement is used to pre-fill the SQL statement textarea.

	Revision 1.17  2004/11/17 11:04:22  tim
	Now working with the php.ini setting "magic_quotes_gpc = On" as well.
	Added accesskeys to buttons and links and automatically focusing form
	fields on page load for fast keyboard navigation.
	Added set size "1000".
	Displaying some Oracle-related environment variables at the bottom.

	Revision 1.16  2004/04/27 12:33:02  tim
	Beautified the displayed version string.

	Revision 1.15  2004/04/27 12:19:31  tim
	Added export format "HTML table".
	The table popup now shows other Oracle user's tables if the current user
	is allowed to access them.
	Use CVS revision number for the version number.

	Revision 1.14  2004/01/07 09:59:00  tim
	Allow dollar sign in table names.

	Revision 1.13  2003/11/28 13:40:39  tim
	Fixed empty columns not being exported in CSV format.

	Revision 1.12  2003/11/28 13:07:44  tim
	Added XML and CSV export.
	Session ID is now passed along with GET/POST requests, allowing you to
	run OracleEditor in multiple browser windows (which wasn't possible with
	the cookie-based session ID).
	Fixed password validation bug, which filtered special characters from the
	password entered (you couldn't log in if your password was "$pecial").
	Added ocisetprefetch(), which might improve performance.

	Revision 1.11  2003/10/20 11:10:01  tim
	Set HTML character set to UTF-8 when the Oracle connection runs in UTF-8
	mode (as defined in the NLS_LANG environment variable).

	Revision 1.10  2003/10/10 09:24:39  tim
	Cosmetics - fixed "Insert new row" line missing one column.

	Revision 1.9  2003/09/26 11:31:02  tim
	Fixed rownumber INSERT bug.

	Revision 1.8  2003/09/24 08:38:30  tim
	Added row number display.

	Revision 1.7  2003/08/21 06:45:47  tim
	Added "Switch to manual SQL entry" option, allowing you to run arbitrary
	SQL including DML/DDL statements.

	Revision 1.6  2003/08/05 10:31:00  tim
	Display views as well.

	Revision 1.5  2003/05/26 09:45:17  tim
	Now rows from all tables can be edited, not only from tables which have a primary
	key defined. And the primary key need not be in the SELECT clause anymore to
	be able to edit rows.
	Debug mode logs all SQL statements in the PHP error_log.
	Fixed not moving to the first set after changes to the WHERE clause.


	Requirements:
	PHP 4 (version 4.1 or greater) with Session and OCI8 support.
	Works fine with the php.ini setting "register_globals = off",
	and regardless of the "magic_quotes_gpc" setting.
*/

// Don't write PHP warnings into HTML. Watch your PHP error_log file!

ini_set('display_errors', 0);

// Format version string

$version = trim(substr('$Revision: 1.20 $', 10, -1));

// Fix magic_quotes_gpc garbage

if (get_magic_quotes_gpc())
  { function stripslashes_deep($value)
	  { return (is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value));
	  }

	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
  }

// To allow multiple independent OracleEditor sessions,
// propagate session ID in the URL instead of a cookie.

ini_set('session.use_cookies', '0');

// We'll add the session ID to URLs ourselves - disable trans_sid

ini_set('url_rewriter.tags', '');

// Initialize session ID

$sid = '';

if (isset($_REQUEST[ 'sid' ]))
  $sid = substr(trim(preg_replace('/[^a-f0-9]/', '', $_REQUEST[ 'sid' ])), 0, 13);

if ($sid == '')
  $sid = uniqid('');

// Start PHP session

session_id($sid);
session_name('OracleEditor');
session_start();

$setsizes = array( 10, 25, 50, 100, 1000 );

$exportformats = array(
	'xml'  => array( 'XML', 'text/xml' ),
	'csv'  => array( 'CSV', 'text/comma-separated-values' ),
	'html' => array( 'HTML table', 'text/html' ),
	);

if (! isset($_SESSION[ 'exportformat' ]))
  $_SESSION[ 'exportformat' ] = 'xml';

// Initialize database connection parameters

if ((! isset($_SESSION[ 'connection' ])) || isset($_REQUEST[ 'disconnect' ]))
  pof_blanksession();

if (isset($_REQUEST[ 'connection' ]))
  if (is_array($_REQUEST[ 'connection' ]))
	{ pof_blanksession();

	  if (isset($_REQUEST[ 'connection' ][ 'user' ]))
		$_SESSION[ 'connection' ][ 'user' ] = substr(trim(preg_replace('/[^a-zA-Z0-9$_-]/', '', $_REQUEST[ 'connection' ][ 'user' ])), 0, 30);

	  if (isset($_REQUEST[ 'connection' ][ 'password' ]))
		$_SESSION[ 'connection' ][ 'password' ] = substr(trim($_REQUEST[ 'connection' ][ 'password' ]), 0, 30);

	  if (isset($_REQUEST[ 'connection' ][ 'service' ]))
		$_SESSION[ 'connection' ][ 'service' ] = substr(trim(preg_replace('|[^a-zA-Z0-9:.() =/_-]|', '', $_REQUEST[ 'connection' ][ 'service' ])), 0, 2000);
	}

// Rather dumb character set detection:
// Try switching to UTF-8 automagically on stuff like "NLS_LANG=american_america.UTF8"

$charset = 'ISO-8859-1';

if (getenv('NLS_LANG'))
  if (strtoupper(substr(getenv('NLS_LANG'), -5)) == '.UTF8')
	$charset = 'UTF-8';

// Initialize debug mode

if (! isset($_SESSION[ 'debug' ])) $_SESSION[ 'debug' ] = false;
if (isset($_REQUEST[ 'debug' ])) $_SESSION[ 'debug' ] = ($_REQUEST[ 'debug' ] == 1);

// Initialize / drop DDL cache

if (! isset($_SESSION[ 'cache' ])) $_SESSION[ 'cache' ] = array();
if (isset($_REQUEST[ 'dropcache' ])) $_SESSION[ 'cache' ] = array();

// Initialize entry mode

if (! isset($_SESSION[ 'entrymode' ])) $_SESSION[ 'entrymode' ] = 'popups';

// Initialize SQL filter fields

if (! isset($_SESSION[ 'sql'     ])) $_SESSION[ 'sql'     ] = '';
if (! isset($_SESSION[ 'table'   ])) $_SESSION[ 'table'   ] = '';
if (! isset($_SESSION[ 'select'  ])) $_SESSION[ 'select'  ] = '*';
if (! isset($_SESSION[ 'where'   ])) $_SESSION[ 'where'   ] = '';
if (! isset($_SESSION[ 'set'     ])) $_SESSION[ 'set'     ] = 1;
if (! isset($_SESSION[ 'setsize' ])) $_SESSION[ 'setsize' ] = $setsizes[ 0 ];

if (isset($_REQUEST[ 'select' ])) $_SESSION[ 'select' ] = trim($_REQUEST[ 'select' ]);

// Initialize export mode

$exportmode = false;

if (isset($_REQUEST[ 'export' ]))
  $exportmode = true;

// Switch back from export mode

if ($exportmode)
  { // SQL input fields changed?

	$check_fields = array( 'sql', 'table', 'select', 'where' );

	foreach ($check_fields as $field)
	  if (isset($_REQUEST[ $field ]))
		if ($_REQUEST[ $field ] != $_SESSION[ $field ])
		  { $exportmode = false;
			break;
		  }

	// History item selected?

	if (isset($_REQUEST[ 'history' ]))
	  if ($_REQUEST[ 'history' ] != '')
		$exportmode = false;
  }

// Action + record set?

$action = '';

if (isset($_REQUEST[ 'action' ]))
  if (($_REQUEST[ 'action' ] == 'edit') || ($_REQUEST[ 'action' ] == 'delete'))
	$action = $_REQUEST[ 'action' ];

$actionrecord = false;

if ($action != '')
  if (isset($_REQUEST[ 'record' ]))
	if (is_array($_REQUEST[ 'record' ]))
	  if (isset($_REQUEST[ 'record' ][ 'table' ]) && isset($_REQUEST[ 'record' ][ 'rowid' ]))
		$actionrecord = $_REQUEST[ 'record' ];

if (! is_array($actionrecord))
  $action = '';

// edit or delete cancelled?

if (isset($_REQUEST[ 'editcancel' ]) || isset($_REQUEST[ 'deletecancel' ]))
  { $action = '';
	$actionrecord = false;
  }

// set changed?

if (isset($_REQUEST[ 'set' ]))
  if ($_REQUEST[ 'set' ] != $_SESSION[ 'set' ])
	{ $val = intval($_REQUEST[ 'set' ]);
	  if ($val > 0)
		$_SESSION[ 'set' ] = $val;
	}

// setsize changed?

if (isset($_REQUEST[ 'setsize' ]))
  if ($_REQUEST[ 'setsize' ] != $_SESSION[ 'setsize' ])
	if (in_array($_REQUEST[ 'setsize' ], $setsizes))
	  { $_SESSION[ 'setsize' ] = $_REQUEST[ 'setsize' ];
		$_SESSION[ 'set'     ] = 1;
	  }

// empty column list means *

if ($_SESSION[ 'select' ] == '') $_SESSION[ 'select' ] = '*';

// entry mode changed?

if (isset($_REQUEST[ 'entrymode' ]))
  if (($_REQUEST[ 'entrymode' ] == 'popups') || ($_REQUEST[ 'entrymode' ] == 'manual'))
	{ $_SESSION[ 'sql'    ] = '';

	  // Switch from "popups" to "manual"? Prefill SQL statement...
	  if (($_SESSION[ 'entrymode' ] == 'popups') && ($_REQUEST[ 'entrymode' ] == 'manual') && ($_SESSION[ 'table' ] != '') && ($_SESSION[ 'select' ] != ''))
		$_SESSION[ 'sql' ] = 'SELECT ' . $_SESSION[ 'select' ] . ' from ' . $_SESSION[ 'table' ] . ' ' . $_SESSION[ 'where' ];

	  $_SESSION[ 'table'  ] = '';
	  $_SESSION[ 'select' ] = '*';
	  $_SESSION[ 'where'  ] = '';
	  $_SESSION[ 'set'    ] = 1;

	  $_SESSION[ 'entrymode' ] = $_REQUEST[ 'entrymode' ];
	}

// sql changed? (entrymode=manual)

if (isset($_REQUEST[ 'sql' ]))
  if ($_REQUEST[ 'sql' ] != $_SESSION[ 'sql' ])
	{ $_SESSION[ 'sql' ] = trim($_REQUEST[ 'sql' ]);
	  $_SESSION[ 'set' ] = 1;
	}

// where changed? (entrymode=popups)

if (isset($_REQUEST[ 'where' ]))
  if ($_REQUEST[ 'where' ] != $_SESSION[ 'where' ])
	{ $_SESSION[ 'where' ] = trim($_REQUEST[ 'where' ]);
	  $_SESSION[ 'set'   ] = 1;
	}

// table changed? (entrymode=popups)

if (isset($_REQUEST[ 'table' ]))
  if ($_REQUEST[ 'table' ] != $_SESSION[ 'table' ])
	{ $newtable = substr(trim(preg_replace('/[^a-zA-Z0-9$#_.-]/', '', $_REQUEST[ 'table' ])), 0, 61);

	  if ($newtable != $_SESSION[ 'table' ])
		{ $_SESSION[ 'table'  ] = $newtable;
		  $_SESSION[ 'select' ] = '*';
		  $_SESSION[ 'where'  ] = '';
		  $_SESSION[ 'set'    ] = 1;
		}

	  // We need a way to set both table + where in HREFs
	  if (isset($_REQUEST[ 'keepwhere' ]))
		$_SESSION[ 'where' ] = $_REQUEST[ 'keepwhere' ];
	}

// history item selected?

if (! isset($_SESSION[ 'history' ])) $_SESSION[ 'history' ] = array();

$dont_execute = false;

if (isset($_REQUEST[ 'history' ]))
  if ($_REQUEST[ 'history' ] != '')
	{ $tmp = intval($_REQUEST[ 'history' ]);
	  if ($tmp >= 0)
		if (isset($_SESSION[ 'history' ][ $tmp ]))
		  { $_SESSION[ 'entrymode' ] = $_SESSION[ 'history' ][ $tmp ][ 'entrymode' ];
			$_SESSION[ 'set'       ] = $_SESSION[ 'history' ][ $tmp ][ 'set'     ];
			$_SESSION[ 'setsize'   ] = $_SESSION[ 'history' ][ $tmp ][ 'setsize' ];

			if ($_SESSION[ 'history' ][ $tmp ][ 'entrymode' ] == 'popups')
			  { $_SESSION[ 'table'   ] = $_SESSION[ 'history' ][ $tmp ][ 'table'   ];
				$_SESSION[ 'select'  ] = $_SESSION[ 'history' ][ $tmp ][ 'select'  ];
				$_SESSION[ 'where'   ] = $_SESSION[ 'history' ][ $tmp ][ 'where'   ];
				$_SESSION[ 'sql'     ] = '';
			  }
			else
			  { $_SESSION[ 'sql'     ] = $_SESSION[ 'history' ][ $tmp ][ 'sql' ];
				$_SESSION[ 'table'   ] = '';
				$_SESSION[ 'select'  ] = '';
				$_SESSION[ 'where'   ] = '';
			  }

			// Non-SELECT statements should only be shown, not automatically executed
			// when switching to them (to avoid unwanted DELETEs etc.)

			if ($_SESSION[ 'history' ][ $tmp ][ 'type' ] != 'SELECT')
			  $dont_execute = true;
		  }
	}

// Build main SQL statement

$main_sql = '';

if ((($_SESSION[ 'table' ] != '') || ($_SESSION[ 'sql' ] != '')) && (! $dont_execute))
  {	if ($_SESSION[ 'entrymode' ] == 'popups')
	  { // Always select the ROWID - we're using this for "Actions" support instead of the primary key

		$main_sql = 'select ';

		// Prevent "ORA-00936: missing expression":
		//   "select *, ROWID" is incorrect, we have to use "select tablename.*, ROWID" instead

		if (trim($_SESSION[ 'select' ]) == '*')
		  $main_sql .= $_SESSION[ 'table' ] . '.';

		$rowidsql = ', rowidtochar(ROWID) as ROWID_';

		$main_sql .= trim($_SESSION[ 'select' ] . $rowidsql . ' from ' . $_SESSION[ 'table' ] . ' ' . $_SESSION[ 'where' ]);
	  }
	else
	  $main_sql = $_SESSION[ 'sql' ];
  }

// Initialize connection

$conn = false;

if (($_SESSION[ 'connection' ][ 'user' ] != '') && ($_SESSION[ 'connection' ][ 'password' ] != ''))
  pof_connect();

// Do export?

$doexport = false;
$export_errormsg = '';

if (isset($_REQUEST[ 'export' ]))
  if (is_array($_REQUEST[ 'export' ]))
	if (isset($_REQUEST[ 'export' ][ 'doit' ]) && isset($_REQUEST[ 'export' ][ 'format' ]) && isset($_REQUEST[ 'export' ][ 'limit' ]))
	  $doexport = true;

if ($doexport)
  { // Do the export
	// Exporting may take a while

	set_time_limit(0);

	// Initialize export settings

	$exportlimit = abs(intval($_REQUEST[ 'export' ][ 'limit' ]));

	$_SESSION[ 'exportformat' ] = $_REQUEST[ 'export' ][ 'format' ];

	if (! isset($exportformats[ $_SESSION[ 'exportformat' ] ]))
	  $_SESSION[ 'exportformat' ] = 'xml';

	// Send Content-type header

	header(sprintf('Content-Type: %s; name="dbexport.%s"', $exportformats[ $_SESSION[ 'exportformat' ] ][ 1 ], $_SESSION[ 'exportformat' ]));
	header(sprintf('Content-disposition: attachment; filename="dbexport.%s"', $_SESSION[ 'exportformat' ]));

	// Loop through results

	$ok = false;

	$cursor = pof_opencursor($main_sql);

	if ($cursor)
	  if (ocistatementtype($cursor) == 'SELECT')
		$ok = true;

	if ($ok)
	  { // Get column list

		$columns = array();
		$numcols = ocinumcols($cursor);

		for ($j = 1; $j <= $numcols; $j++)
		  if (ocicolumnname($cursor, $j) != 'ROWID_')
			$columns[ (ocicolumnname($cursor, $j)) ] = array(
				'type' => ocicolumntype($cursor, $j),
				'size' => ocicolumnsize($cursor, $j)
				);

		// Header

		if ($_SESSION[ 'exportformat' ] == 'xml')
		  { echo sprintf('<' . '?xml version="1.0" encoding="%s"?' . '>', $charset) . "\n";
			echo "<!-- Generated by OracleEditor.php (http://oracleeditor.sourceforge.net/) -->\n";

			$userstr = $_SESSION[ 'connection' ][ 'user' ];
			if ($_SESSION[ 'connection' ][ 'service' ] != '')
			  $userstr .= '@' . $_SESSION[ 'connection' ][ 'service' ];

			echo sprintf('<rowset exported="%s" user="%s" server="%s">', date('Y-m-d\TH:i:s'), $userstr, $_SERVER[ 'SERVER_NAME' ]) . "\n";
			echo sprintf("\t<sql>%s</sql>\n", htmlspecialchars($main_sql));

			// Column aliases: We can use column names as tag names only if
			// they're valid XML names - <count(MYFIELD)> won't work.

			$i = 0;
			foreach ($columns as $name => $column)
			  { $i++;

				if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $name) == 0)
				  $columns[ $name ][ 'alias' ] = 'ALIAS' . $i;
			  }

			echo "\t<columns>\n";
			foreach ($columns as $name => $column)
			  echo sprintf("\t\t" . '<column name="%s" type="%s" size="%s"%s/>' . "\n",
				htmlspecialchars($name),
				$column[ 'type' ],
				$column[ 'size' ],
				(isset($column[ 'alias' ]) ? ' alias="' . $column[ 'alias' ] . '"' : '')
				);
			echo "\t</columns>\n";
		  }
		elseif ($_SESSION[ 'exportformat' ] == 'csv')
		  { $first = true;

			foreach ($columns as $name => $column)
			  if ($name != 'ROWID_')
				{ if (! $first) echo ', ';
				  echo sprintf('"%s"', str_replace('"', '""', $name));
				  $first = false;
				}

			echo "\n";
		  }
		elseif ($_SESSION[ 'exportformat' ] == 'html')
		  { ?>

			<html>
			<head>
			<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>">
			<meta name="date" content="<?php echo date('Y-m-d\TH:i:s'); ?>">
			<meta name="generator" content="OracleEditor.php (http://oracleeditor.sourceforge.net/)">
			<title>Exported Oracle data (by OracleEditor.php)</title>
			</head>
			<body>

			<h1>Exported Oracle data</h1>

			<?php
			$userstr = $_SESSION[ 'connection' ][ 'user' ];
			if ($_SESSION[ 'connection' ][ 'service' ] != '')
			  $userstr .= '@' . $_SESSION[ 'connection' ][ 'service' ];
			?>

			<p>The Oracle user <em><?php echo htmlspecialchars($userstr); ?></em> exported this data on <em><?php echo date('r'); ?></em>
			by running the following SQL statement in <a href="http://<?php echo $_SERVER[ 'HTTP_HOST' ]; ?><?php echo $_SERVER[ 'PHP_SELF' ]; ?>">a local copy of OracleEditor.php</a> on <em><?php echo $_SERVER[ 'SERVER_NAME' ]; ?></em>:<br />
			<pre><?php echo htmlspecialchars($main_sql); ?></pre></p>

			<table border="1">
			<tr>

			<?php

			foreach ($columns as $name => $column)
			  echo sprintf('<th>%s<br />(%s, %s)</th>' . "\n",
				htmlspecialchars($name),
				$column[ 'type' ],
				$column[ 'size' ]
				);

			?>

			</tr>

			<?php
		  }

		// Rows

		$i = 1;

		while (true)
		  { if (! ocifetchinto($cursor, $row, OCI_ASSOC | OCI_RETURN_LOBS))
			  break;

			if ($_SESSION[ 'exportformat' ] == 'xml')
			  { echo sprintf("\t<row%s>\n", (isset($row[ 'ROWID_' ]) ? (' id="' . htmlspecialchars($row[ 'ROWID_' ]) . '"') : ''));

				foreach ($row as $fieldname => $value)
				  if ($fieldname != 'ROWID_')
					echo sprintf("\t\t<%1\$s>%2\$s</%1\$s>\n",
						(isset($columns[ $fieldname ][ 'alias' ]) ? $columns[ $fieldname ][ 'alias' ] : $fieldname ),
						htmlspecialchars($value));

				echo "\t</row>\n";
			  }
			elseif ($_SESSION[ 'exportformat' ] == 'csv')
			  { $first = true;

				foreach ($columns as $fieldname => $column)
				  if ($fieldname != 'ROWID_')
					{ if (! $first) echo ', ';
					  if (isset($row[ $fieldname ]))
						echo sprintf('"%s"', str_replace('"', '""', $row[ $fieldname ]));
					  else
						echo '""';
					  $first = false;
					}

				echo "\n";
			  }
			elseif ($_SESSION[ 'exportformat' ] == 'html')
			  { echo "<tr>\n";

				foreach ($columns as $fieldname => $column)
				  if ($fieldname != 'ROWID_')
					{ echo "\t<td>";
					  if (isset($row[ $fieldname ]))
						echo htmlspecialchars($row[ $fieldname ]);
					  echo "</td>\n";
					}

				echo "</tr>\n";
			  }

			if (($exportlimit > 0) && ($exportlimit <= ++$i))
			  break;
		  }

		// Footer

		if ($_SESSION[ 'exportformat' ] == 'xml')
		  { echo "</rowset>\n";
		  }
		elseif ($_SESSION[ 'exportformat' ] == 'html')
		  { ?>

			</table>
			<p>HTML generated by <a href="http://oracleeditor.sourceforge.net/">OracleEditor.php</a> <?php echo $version; ?> &copy; 2006 by <a href="http://tim.digicol.de/">Tim Strehle</a> &lt;<a href="mailto:tim@strehle.de">tim@strehle.de</a>&gt;</p>
			</body>
			</html>

			<?php
		  }

		pof_closecursor($cursor);

		session_write_close();
		exit;
	  }
	else
	  $export_errormsg = 'Unable to export';
  }


function pof_blanksession()
{ global $setsizes;

  $_SESSION[ 'connection' ] = array(
		'user'     => '',
		'password' => '',
		'service'  => ''
		);

  $_SESSION[ 'cache'   ] = array();
  $_SESSION[ 'debug'   ] = false;
  $_SESSION[ 'sql'     ] = '';
  $_SESSION[ 'table'   ] = '';
  $_SESSION[ 'select'  ] = '*';
  $_SESSION[ 'where'   ] = '';
  $_SESSION[ 'set'     ] = 1;
  $_SESSION[ 'setsize' ] = $setsizes[ 0 ];
  $_SESSION[ 'history' ] = array();
}


function pof_sqlline($msg, $error = false)
{ if ($error)
	$class = 'sqllineerr';
  else
	$class = 'sqlline';

  $html = '<table class="' . $class . '"><tr><td>' . htmlspecialchars($msg) . '</td></tr></table>' . "\n";

  return $html;
}


function pof_connect()
{ global $conn;

  $conn = ocilogon($_SESSION[ 'connection' ][ 'user' ], $_SESSION[ 'connection' ][ 'password' ], $_SESSION[ 'connection' ][ 'service' ]);

  $err = ocierror();

  if (is_array($err))
	echo htmlspecialchars('Logon failed: ' . $err[ 'message' ]) . '<br />' . "\n";
}


function pof_disconnect()
{ global $conn;

  if ($conn)
	ocilogoff($conn);
}


function pof_opencursor($sql, $bind = false)
{ global $conn;

  $cursor = ociparse($conn, $sql);

  if (! $cursor)
	{ $err = ocierror($conn);
	  if (is_array($err))
		echo pof_sqlline('Parse failed: ' . $err[ 'message' ], true);
	}
  else
	{ // This might improve performance?
	  ocisetprefetch($cursor, $_SESSION[ 'setsize' ]);

	  if (is_array($bind))
		foreach ($bind as $fieldname => $value)
		  ocibindbyname($cursor, ':' . $fieldname, $bind[ $fieldname ], -1);

	  $ok = ociexecute($cursor);

	  if (! $ok)
		{ $err = ocierror($cursor);

		  if (is_array($err))
			echo pof_sqlline('Execute failed: ' . $err[ 'message' ], true);

		  pof_closecursor($cursor);

		  $cursor = false;
		}
	}

  return $cursor;
}


function pof_closecursor($cursor)
{ if ($cursor)
	ocifreestatement($cursor);
}


function pof_gettables()
{ if (! isset($_SESSION[ 'cache' ][ '_alltables' ]))
	{ $_SESSION[ 'cache' ][ '_alltables' ] = array();

	  $sql = sprintf(
		"select ' ' as OWNER, TABLE_NAME from USER_TABLES " .
		"union " .
		"select OWNER, TABLE_NAME from USER_TAB_PRIVS where PRIVILEGE = 'SELECT' and GRANTEE = '%1\$s' " .
		"order by OWNER, TABLE_NAME",
		strtoupper($_SESSION[ 'connection' ][ 'user' ])
		);

	  if ($_SESSION[ 'debug' ]) error_log($sql);

	  $cursor = pof_opencursor($sql);

	  if ($cursor)
		{ while (true)
			{ if (! ocifetchinto($cursor, $row, OCI_ASSOC | OCI_RETURN_LOBS))
				break;

			  if (trim($row[ 'OWNER' ]) == '')
				$_SESSION[ 'cache' ][ '_alltables' ][ ] = $row[ 'TABLE_NAME' ];
			  else
				$_SESSION[ 'cache' ][ '_alltables' ][ ] = $row[ 'OWNER' ] . '.' . $row[ 'TABLE_NAME' ];
			}

		  pof_closecursor($cursor);
		}
	}

  return $_SESSION[ 'cache' ][ '_alltables' ];
}


function pof_getviews()
{ if (! isset($_SESSION[ 'cache' ][ '_allviews' ]))
	{ $_SESSION[ 'cache' ][ '_allviews' ] = array();
	  $sql = 'select VIEW_NAME from USER_VIEWS order by VIEW_NAME';
	  if ($_SESSION[ 'debug' ]) error_log($sql);

	  $cursor = pof_opencursor($sql);

	  if ($cursor)
		{ while (true)
			{ if (! ocifetchinto($cursor, $row, OCI_ASSOC | OCI_RETURN_LOBS))
				break;

			  $_SESSION[ 'cache' ][ '_allviews' ][ ] = $row[ 'VIEW_NAME' ];
			}

		  pof_closecursor($cursor);
		}
	}

  return $_SESSION[ 'cache' ][ '_allviews' ];
}


function pof_getpk($table)
{ if (! isset($_SESSION[ 'cache' ][ $table ])) $_SESSION[ 'cache' ][ $table ] = array();

  if (! isset($_SESSION[ 'cache' ][ $table ][ 'pk' ]))
	{ $_SESSION[ 'cache' ][ $table ][ 'pk' ] = '';

	  $sql = "select COLUMN_NAME from USER_CONS_COLUMNS col, USER_CONSTRAINTS con where con.TABLE_NAME=:TABLE_NAME and con.CONSTRAINT_TYPE='P' and col.CONSTRAINT_NAME=con.CONSTRAINT_NAME";
	  $bind = array( 'TABLE_NAME' => $table );
	  if ($_SESSION[ 'debug' ]) error_log($sql);

	  $cursor = pof_opencursor($sql, $bind);

	  if ($cursor)
		{ if (ocifetchinto($cursor, $row, OCI_NUM))
			$_SESSION[ 'cache' ][ $table ][ 'pk' ] = $row[ 0 ];
		  pof_closecursor($cursor);
		}
	}

  return $_SESSION[ 'cache' ][ $table ][ 'pk' ];
}


function pof_getcoldefs($table)
{ if (! isset($_SESSION[ 'cache' ][ $table ])) $_SESSION[ 'cache' ][ $table ] = array();

  if (! isset($_SESSION[ 'cache' ][ $table ][ 'coldefs' ]))
	{ $_SESSION[ 'cache' ][ $table ][ 'coldefs' ] = array();

	  $sql = "select COLUMN_NAME, NULLABLE, DATA_DEFAULT from USER_TAB_COLUMNS where TABLE_NAME=:TABLE_NAME";
	  $bind = array( 'TABLE_NAME' => $table );
	  if ($_SESSION[ 'debug' ]) error_log($sql);

	  $cursor = pof_opencursor($sql, $bind);

	  if ($cursor)
		{ while (true)
			{ if (! ocifetchinto($cursor, $row, OCI_ASSOC))
				break;

			  $_SESSION[ 'cache' ][ $table ][ 'coldefs' ][ $row[ 'COLUMN_NAME' ] ] = array(
				'nullable' => true,
				'default'  => ''
				);

			  if (isset($row[ 'NULLABLE' ]))
				if ($row[ 'NULLABLE' ] == 'N')
				  $_SESSION[ 'cache' ][ $table ][ 'coldefs' ][ $row[ 'COLUMN_NAME' ] ][ 'nullable' ] = false;

			  if (isset($row[ 'DATA_DEFAULT' ]))
				$_SESSION[ 'cache' ][ $table ][ 'coldefs' ][ $row[ 'COLUMN_NAME' ] ][ 'default' ] = trim(strtr($row[ 'DATA_DEFAULT' ], '()', '  '));
			}

		  pof_closecursor($cursor);
		}
	}

  return $_SESSION[ 'cache' ][ $table ][ 'coldefs' ];
}


function pof_getforeignkeys($table)
{ if (! isset($_SESSION[ 'cache' ][ $table ])) $_SESSION[ 'cache' ][ $table ] = array();

  if (! isset($_SESSION[ 'cache' ][ $table ][ 'constraints' ]))
	{ $_SESSION[ 'cache' ][ $table ][ 'constraints' ] = array( 'from' => array(), 'to' => array() );

	  // Find own + remote foreign key constraint names
	  // XXX foreign tables might belong to a different user! take R_OWNER into account!

	  $sql =
		"select CONSTRAINT_NAME, R_CONSTRAINT_NAME from USER_CONSTRAINTS where TABLE_NAME=:TABLE_NAME and CONSTRAINT_TYPE='R' and STATUS='ENABLED' " .
		"union " .
		"select CONSTRAINT_NAME, R_CONSTRAINT_NAME from USER_CONSTRAINTS where R_CONSTRAINT_NAME in " .
		"(select CONSTRAINT_NAME from USER_CONSTRAINTS where TABLE_NAME=:TABLE_NAME) ".
		"and CONSTRAINT_TYPE='R' and STATUS='ENABLED'";
	  $bind = array( 'TABLE_NAME' => $table );
	  if ($_SESSION[ 'debug' ]) error_log($sql);

	  $cursor = pof_opencursor($sql, $bind);

	  $names = array();
	  $constraints = array();

	  if ($cursor)
		{ while (true)
			{ if (! ocifetchinto($cursor, $row, OCI_ASSOC))
				break;

			  $names[ ] = $row[ 'CONSTRAINT_NAME'   ];

			  if (isset($row[ 'R_CONSTRAINT_NAME' ]))
				if ($row[ 'R_CONSTRAINT_NAME' ] != '')
				  $names[ ] = $row[ 'R_CONSTRAINT_NAME' ];
			}

		  pof_closecursor($cursor);
		}

	  if (count($names) > 0)
		{ $sql = "select CONSTRAINT_NAME, TABLE_NAME, R_CONSTRAINT_NAME from USER_CONSTRAINTS where CONSTRAINT_NAME in ('" . implode("','", $names) . "')";
		  if ($_SESSION[ 'debug' ]) error_log($sql);

		  $cursor = pof_opencursor($sql);

		  if ($cursor)
			{ while (true)
				{ if (! ocifetchinto($cursor, $row, OCI_ASSOC))
					break;

				  $constraints[ $row[ 'CONSTRAINT_NAME' ] ] = $row;
				}

			  pof_closecursor($cursor);
			}

		  $sql = "select CONSTRAINT_NAME, COLUMN_NAME from USER_CONS_COLUMNS where CONSTRAINT_NAME in ('" . implode("','", $names) . "')";
		  if ($_SESSION[ 'debug' ]) error_log($sql);

		  $cursor = pof_opencursor($sql);

		  if ($cursor)
			{ while (true)
				{ if (! ocifetchinto($cursor, $row, OCI_ASSOC))
					break;

				  $constraints[ $row[ 'CONSTRAINT_NAME' ] ][ 'COLUMN_NAME'  ] = $row[ 'COLUMN_NAME' ];
				}

			  pof_closecursor($cursor);
			}
		}

	  if (count($constraints) > 0)
		{ foreach ($constraints as $key => $item)
			{ if (! isset($item[ 'R_CONSTRAINT_NAME' ]))
				continue;

			  if ($item[ 'TABLE_NAME' ] == $table)
				$_SESSION[ 'cache' ][ $table ][ 'constraints' ][ 'to' ][ $item[ 'COLUMN_NAME' ] ] = array(
					'table'  => $constraints[ $item[ 'R_CONSTRAINT_NAME' ] ][ 'TABLE_NAME'  ],
					'column' => $constraints[ $item[ 'R_CONSTRAINT_NAME' ] ][ 'COLUMN_NAME' ]
					);
			  else
				{ $col = $constraints[ $item[ 'R_CONSTRAINT_NAME' ] ][ 'COLUMN_NAME' ];

				  if (! isset($_SESSION[ 'cache' ][ $table ][ 'constraints' ][ 'from' ][ $col ]))
					$_SESSION[ 'cache' ][ $table ][ 'constraints' ][ 'from' ][ $col ] = array();

				  $_SESSION[ 'cache' ][ $table ][ 'constraints' ][ 'from' ][ $col ][ ] = array(
					'table'  => $item[ 'TABLE_NAME'  ],
					'column' => $item[ 'COLUMN_NAME' ]
					);
				}
			}
		}
	}

  return $_SESSION[ 'cache' ][ $table ][ 'constraints' ];
}


// Charset header

header('Content-Type: text/html; charset=' . $charset);

?>
<html>
<head>
<title>OracleEditor.php<?php
if ($_SESSION[ 'connection' ][ 'user' ] != '')
  { if ($_SESSION[ 'table' ] != '')
	  echo ': ' . $_SESSION[ 'table' ];

	echo ' (' . $_SESSION[ 'connection' ][ 'user' ];
    if ($_SESSION[ 'connection' ][ 'service' ] != '')
      echo '@' . $_SESSION[ 'connection' ][ 'service' ];
	echo ')';
  }
?></title>
<style type="text/css">

body,a,p,span,td,th,input,select,textarea {
	font-family:verdana,arial,helvetica,geneva,sans-serif,serif;
	font-size:12px;
}

a:link,a:visited,a:active {
	color:darkgray;
}

.logo {
	color:yellow;
	background:black;
	font-weight:bold;
	font-size:14px;
}

.headerline {
	border-style:none;
	padding:5px;
	background:black;
	color:white;
	width:100%;
}

.selectform {
	border-width:1px;
	border-color:#FF9999;
	border-style:dashed;
	padding:5px;
	width:100%;
}

.sqlline {
	font-family:courier;
	border-style:none;
	padding:5px;
	width:100%;
	color:yellow;
	background:black;
}

.sqllineerr {
	font-family:courier;
	border-style:none;
	padding:5px;
	width:100%;
	color:red;
	background:black;
}

.resultgrid {
	border-width:1px;
	border-color:#FF9999;
	border-style:dashed;
	padding:5px;
}

.gridheader {
	background:#EEEEEE;
	color:darkgray;
}

.gridline {
	background:white;
}

.gridlinealt {
	background:#EEEEEE;
}

.gridfooter {
	border-style:none;
	background:black;
	color:white;
	width:100%;
}

</style>
</head>
<body>
<form name="form1" method="post" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

<input type="hidden" name="sid" value="<?php echo $sid; ?>" />

<?php

if ($conn == false)
  { ?>

	<table class="headerline">
	<tr>
		<td colspan="2"><span class="logo">OracleEditor.php</span> Browse and edit your Oracle database records ...</td>
	</tr>
	</table>

	<?php

	// Check requirements

	$requirements_ok = true;
	$required_version = '4.1.0';

	if (version_compare(phpversion(), $required_version) < 0)
	  { printf("<strong>PHP too old</strong>: You're running PHP %s, but <strong>PHP %s is required</strong> to run OracleEditor.php!<br />\n", phpversion(), $required_version);
		$requirements_ok = false;
	  }

	if (! function_exists('ocilogon'))
	  { echo "<strong>PHP has no Oracle OCI support</strong>: Your PHP installation doesn't have the <a href=\"http://www.php.net/manual/en/ref.oci8.php\">OCI8 module</a> installed which is required to run OracleEditor.php!<br />\n";
		$requirements_ok = false;
	  }

	if (! function_exists('session_start'))
	  { echo "<strong>PHP has no session support</strong>: Your PHP installation doesn't have the <a href=\"http://www.php.net/manual/en/ref.session.php\">Session module</a> installed which is required to run OracleEditor.php!<br />\n";
		$requirements_ok = false;
	  }

	// Login form

	if ($requirements_ok)
	  {	?>

		<table class="selectform">
		<tr>
			<td>User: </td>
			<td><input type="text" name="connection[user]" value="<?php echo $_SESSION[ 'connection' ][ 'user' ]; ?>" title="Enter the Oracle user name" /></td>

			<script type="text/javascript">
			document.forms[ 'form1' ].elements[ 'connection[user]' ].focus();
			</script>

		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="connection[password]" value="" title="Enter the Oracle user's password" /></td>
		</tr>
		<tr>
			<td>Service name: </td>
			<td><input type="text" name="connection[service]" value="<?php echo htmlspecialchars($_SESSION[ 'connection' ][ 'service' ]); ?>" title="Enter a tnsnames.ora identifier, or leave blank for local databases" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Connect to Oracle" accesskey="c" title="Click to log in [c]" /></td>
		</tr>
		</table>

		<?php
	  }
  }
else
  { // Display connection header

	echo '<table class="headerline"><tr><td>';
	echo '<span class="logo">OracleEditor.php</span> ';
	echo 'Connected to Oracle as ' . $_SESSION[ 'connection' ][ 'user' ];
	if ($_SESSION[ 'connection' ][ 'service' ] != '')
	  echo '@' . $_SESSION[ 'connection' ][ 'service' ];

	echo ' - <a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&disconnect=1" accesskey="d" title="Click here to log out [d]">Disconnect</a>';
	echo '</table>' . "\n";

	echo '<table class="selectform"><tr><td>' . "\n";

	if ($_SESSION[ 'entrymode' ] == 'popups')
	  { // Popup-aided SQL query entry

		echo 'SELECT ';

		// "select" (column list) input field

		echo '<input type="text" name="select" value="' . htmlspecialchars($_SESSION[ 'select' ]) . '" size="20" title="Enter column names (comma-separated), or * for all columns" />';

		// "table" selection popup

		$alltables = pof_gettables();
		$allviews = pof_getviews();

		echo ' FROM <select name="table" onChange="javascript:document.forms[0].submit()" title="Select a table/view to display or edit">' . "\n";

		$found = false;

		echo '<option value="">[Select a table]</option>' . "\n";

		foreach ($alltables as $tablename)
		  { echo '<option value="' . $tablename . '"';

			if (! $found)
			  if ($tablename == $_SESSION[ 'table' ])
				{ echo ' selected="selected"';
				  $found = true;
				}

			echo '>' . $tablename . '</option>' . "\n";
		  }

		echo '<option value=""></option>' . "\n";
		echo '<option value="">[Select a view]</option>' . "\n";

		foreach ($allviews as $viewname)
		  { echo '<option value="' . $viewname . '"';

			if (! $found)
			  if ($viewname == $_SESSION[ 'table' ])
				{ echo ' selected="selected"';
				  $found = true;
				}

			echo '>' . $viewname . '</option>' . "\n";
		  }

		if (! $found)
		  echo '<option value="" selected="selected">[Select a table/view]</option>' . "\n";

		echo '</select>' . "\n";

		// "where" input field for WHERE, ORDER BY, GROUP BY, ...

		echo ' <input type="text" name="where" value="' . htmlspecialchars($_SESSION[ 'where' ]) . '" size="40" title="Enter GROUP BY or ORDER BY clauses here" />;';
	  }
	else
	  { // Manual SQL query/command entry

		?>

		SQL: [Warning: Be careful with UPDATE, DELETE, DROP etc. - there's no chance to rollback!]<br />

		<textarea name="sql" rows="5" cols="80" title="Enter any SQL statement here: SELECT, INSERT, UPDATE, DELETE, ALTER, DROP..."><?php echo htmlspecialchars($_SESSION[ 'sql' ]); ?></textarea>

		<script type="text/javascript">
		document.forms[ 'form1' ].elements[ 'sql' ].focus();
		</script>

		<?php
	  }

	// "setsize" selection popup

	echo '<br /> Display <select name="setsize" onChange="javascript:document.forms[0].submit()" title="Select the number of rows to display per page">' . "\n";

	foreach ($setsizes as $size)
	  { echo '<option value="' . $size . '"';
		if ($size == $_SESSION[ 'setsize' ])
		  echo ' selected="selected"';
		echo '>' . $size . '</option>' . "\n";
	  }

	echo '</select> records per page.' . "\n";

	// Submit button

	echo '<input type="submit" accesskey="e" value="' . ($_SESSION[ 'entrymode' ] == 'popups' ? 'Refresh' : 'Execute') . '" title="Click here to execute the SQL statement [e]" />' . "\n";
	echo '<input type="submit" accesskey="x" name="export" value="Export" title="Click here to export rows as text, XML or CSV [x]" />' . "\n";

	echo str_repeat('&nbsp;', 6);
	echo '<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&entrymode=' . ($_SESSION[ 'entrymode' ] == 'popups' ? 'manual' : 'popups') . '" accesskey="s" title="Click here to switch between manual SQL entry and the table/view popup [s]">';
	echo ($_SESSION[ 'entrymode' ] == 'popups' ? 'Switch to manual SQL entry' : 'Switch to popup-aided SQL entry') . '</a>' . "\n";

	echo '</td></tr></table>' . "\n";


	// Update record if requested

	if (($action == 'edit') && isset($_REQUEST[ 'editsave' ]) && is_array($actionrecord) && isset($_REQUEST[ 'edit' ]))
	  if (is_array($_REQUEST[ 'edit' ]))
		if (count($_REQUEST[ 'edit' ]) > 0)
		  { $sql = 'update ' . $actionrecord[ 'table' ] . ' set ';
			$i = 0;
			$bind = array();

			foreach ($_REQUEST[ 'edit' ] as $fieldname => $field)
			  { if (! (isset($field[ 'mode' ]) && isset($field[ 'value' ]) && isset($field[ 'function' ])))
				  continue;

				if ($i > 0)
				  $sql .= ', ';

				$sql .= $fieldname . '=';

				if ($field[ 'mode' ] == 'function')
				  $sql .= $field[ 'function' ];
				else
				  { $sql .= ':' . $fieldname;
					$bind[ $fieldname ] = $field[ 'value' ];
				  }

				$i++;
			  }

			$sql .= ' where ROWID=chartorowid(:rowid_)';
			if ($_SESSION[ 'debug' ]) error_log($sql);

			$bind[ 'rowid_' ] = $actionrecord[ 'rowid' ];

			echo pof_sqlline($sql . ';');

			$updcursor = ociparse($conn, $sql);

			if (! $updcursor)
			  { $err = ocierror($conn);
				if (is_array($err))
				  echo pof_sqlline('Parse failed: ' . $err[ 'message' ], true);
			  }
			else
			  { foreach ($bind as $fieldname => $value)
				  ocibindbyname($updcursor, ':' . $fieldname, $bind[ $fieldname ], -1);

				$ok = ociexecute($updcursor);

				if (! $ok)
				  { $err = ocierror($updcursor);
					if (is_array($err))
					  echo pof_sqlline('Execute failed: ' . $err[ 'message' ], true);
				  }

				ocifreestatement($updcursor);
			  }
		  }


	// Delete record if requested

	if (($action == 'delete') && isset($_REQUEST[ 'deleteconfirm' ]) && is_array($actionrecord))
	  { $sql = 'delete from ' . $actionrecord[ 'table' ] . ' where ROWID=chartorowid(:rowid_)';
		if ($_SESSION[ 'debug' ]) error_log($sql);

		echo pof_sqlline($sql . ';');

		$delcursor = ociparse($conn, $sql);

		if (! $delcursor)
		  { $err = ocierror($conn);
			if (is_array($err))
			  echo pof_sqlline('Parse failed: ' . $err[ 'message' ], true);
		  }
		else
		  { ocibindbyname($delcursor, ':rowid_', $actionrecord[ 'rowid' ], -1);

			$ok = ociexecute($delcursor);

			if (! $ok)
			  { $err = ocierror($delcursor);
				if (is_array($err))
				  echo pof_sqlline('Execute failed: ' . $err[ 'message' ], true);
			  }

			ocifreestatement($delcursor);
		  }

		$action = '';
		$actionrecord = false;
	  }


	// Insert record if requested

	if (isset($_REQUEST[ 'insertsave' ]) && isset($_REQUEST[ 'insert' ]))
	  if (is_array($_REQUEST[ 'insert' ]))
		if (count($_REQUEST[ 'insert' ]) > 0)
		  { $fieldnames = array();
			$fieldvalues = array();
			$bind = array();

			foreach ($_REQUEST[ 'insert' ] as $fieldname => $field)
			  { if (! (isset($field[ 'mode' ]) && isset($field[ 'value' ]) && isset($field[ 'function' ])))
				  continue;

				$fieldnames[ ] = $fieldname;

				if ($field[ 'mode' ] == 'function')
				  $fieldvalues[ ] = $field[ 'function' ];
				else
				  { $fieldvalues[ ] = ':' . $fieldname;
					$bind[ $fieldname ] = $field[ 'value' ];
				  }
			  }

			$sql = 'insert into ' . $_SESSION[ 'table' ] . ' (' . implode(', ', $fieldnames) . ') values (' . implode(', ', $fieldvalues) . ')';
			if ($_SESSION[ 'debug' ]) error_log($sql);

			echo pof_sqlline($sql . ';');

			$inscursor = ociparse($conn, $sql);

			if (! $inscursor)
			  { $err = ocierror($conn);
				if (is_array($err))
				  echo pof_sqlline('Parse failed: ' . $err[ 'message' ], true);
			  }
			else
			  { foreach ($bind as $fieldname => $value)
				  ocibindbyname($inscursor, ':' . $fieldname, $bind[ $fieldname ], -1);

				$ok = ociexecute($inscursor);

				if (! $ok)
				  { $err = ocierror($inscursor);
					if (is_array($err))
					  echo pof_sqlline('Execute failed: ' . $err[ 'message' ], true);
				  }

				ocifreestatement($inscursor);
			  }
		  }


	// Run SELECT statement, display results

	if ((($_SESSION[ 'table' ] != '') || ($_SESSION[ 'sql' ] != '')) && (! $dont_execute))
	  {	echo pof_sqlline($main_sql . ';');
		if ($_SESSION[ 'debug' ]) error_log($main_sql);

		if ($_SESSION[ 'entrymode' ] == 'popups')
		  $pk = pof_getpk($_SESSION[ 'table' ]);
		else
		  $pk = '';

		$cursor = pof_opencursor($main_sql);
		$statementtype = '';

		if ($cursor)
		  { // Add to history
			// Remove ROWID select string from the SQL string displayed in the history - it's just ugly

			if ($_SESSION[ 'entrymode' ] == 'popups')
			  $histsql = str_replace($rowidsql, '', $main_sql);
			else
			  $histsql = $main_sql;

			foreach ($_SESSION[ 'history' ] as $key => $item)
			  if ($item[ 'sql' ] == $histsql)
				unset($_SESSION[ 'history' ][ $key ]);

			$statementtype = ocistatementtype($cursor);

			$historyitem = array(
				'sql'       => $histsql,
				'set'       => $_SESSION[ 'set'       ],
				'setsize'   => $_SESSION[ 'setsize'   ],
				'entrymode' => $_SESSION[ 'entrymode' ],
				'type'      => $statementtype
				);

			if ($_SESSION[ 'entrymode' ] == 'popups')
			  { $historyitem[ 'table'   ] = $_SESSION[ 'table'   ];
				$historyitem[ 'select'  ] = $_SESSION[ 'select'  ];
				$historyitem[ 'where'   ] = $_SESSION[ 'where'   ];
			  }

			array_unshift($_SESSION[ 'history' ], $historyitem);

			if (count($_SESSION[ 'history' ]) > 25)
			  array_pop($_SESSION[ 'history' ]);
		  }

		if ($statementtype == 'SELECT')
		  {	// Get column list

			$columns = array();
			$numcols = ocinumcols($cursor);

			for ($j = 1; $j <= $numcols; $j++)
			  if (ocicolumnname($cursor, $j) != 'ROWID_')
				$columns[ (ocicolumnname($cursor, $j)) ] = array(
					'type' => ocicolumntype($cursor, $j),
					'size' => ocicolumnsize($cursor, $j)
					);

			// Display main table

			if ($exportmode)
			  { // Display export settings form

				?>

				<table class="selectform">
				<tr>
					<td align="left">
						Export format:
						<?php $i = 0; foreach ($exportformats as $value => $config) { $i++; ?>
						<label><input type="radio" name="export[format]" value="<?php echo $value; ?>" <?php echo ($value == $_SESSION[ 'exportformat' ] ? 'checked="checked" ' : ''); ?>/><?php echo htmlspecialchars($config[ 0 ]); ?></label>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td align="left">
						Record limit:
						<select name="export[limit]" title="Select the maximum number of rows to be exported">
							<option value="100" selected="selected">100</option>
							<option value="1000">1000</option>
							<option value="0">Unlimited (Are you sure?)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="left">
						<input type="submit" name="export[doit]" value="Export now" accesskey="n" title="Click here to download the export file now [n]" />
						<input type="button" value="Cancel" accesskey="c" onClick="location.href='<?php echo $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid; ?>'" title="Click here to go back, cancel exporting [c]" />
						<?php echo htmlspecialchars($export_errormsg); ?>
					</td>
				</tr>
				</table>

				<?php
			  }
			else
			  {	// Display table header

				echo '<table class="resultgrid">' . "\n";
				echo '<tr class="gridheader">' . "\n";
				echo '<th>Row</th>' . "\n";

				if ($_SESSION[ 'entrymode' ] == 'popups')
				  echo '<th>Actions</th>' . "\n";

				foreach ($columns as $columnname => $column)
				  echo '<th>' . $columnname . '<br />(' . $column[ 'type' ] . ', ' . $column[ 'size' ] . ')</th>' . "\n";

				echo '</tr>' . "\n";

				// Skip previous sets

				$offset = 0;

				if ($_SESSION[ 'set' ] > 1)
				  { $offset = ($_SESSION[ 'set' ] - 1) * $_SESSION[ 'setsize' ];
					for ($j = 1; $j <= $offset; $j++)
					  if (! ocifetch($cursor))
						break;
				  }

				$morerows = false;
				$foundactionrecord = false;

				$foreign = pof_getforeignkeys($_SESSION[ 'table' ]);

				// Display records

				$i = 0;

				while (true)
				  { if (! ocifetchinto($cursor, $row, OCI_ASSOC | OCI_RETURN_LOBS))
					  break;

					$i++;

					echo '<tr class="' . ($i % 2 ? 'gridline' : 'gridlinealt') . '">' . "\n";
					echo '<td>' . ($i + $offset) . '</td>' . "\n";

					// Is this record to be edited?

					$mode = 'show';

					if ($action != '')
					  if (($actionrecord[ 'table' ] == $_SESSION[ 'table' ]) && ($actionrecord[ 'rowid' ] == $row[ 'ROWID_' ]))
						{ $mode = $action;
						  $foundactionrecord = true;
						}

					// Display Actions column (entrymode=popups)

					if ($_SESSION[ 'entrymode' ] == 'popups')
					  { echo '<td>';

						if ($mode == 'edit')
						  { echo '<a name="actionrecord"></a>';
							echo '<input type="submit" value="Update" name="editsave" title="Click here to save your changes now" /><br />';
							echo '<input type="submit" value="Cancel" name="editcancel" title="Click here to dismiss your changes and go back" />';
						  }
						elseif ($mode == 'delete')
						  { echo '<a name="actionrecord"></a>';
							echo '<input type="submit" value="Delete" name="deleteconfirm" title="Click here to delete this record now" /><br />';
							echo '<input type="submit" value="Cancel" name="deletecancel" title="Click here to go back" />';
						  }
						else
						  {	$qs = 'record[table]=' . urlencode($_SESSION[ 'table' ]) . '&' .
								'record[rowid]=' . urlencode($row[ 'ROWID_' ]);

							echo '<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&action=edit&' . $qs . '#actionrecord" title="Click here to change this record">Update</a><br />';
							echo '<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&action=delete&' . $qs . '#actionrecord" title="Click here to delete this record">Delete</a>';
						  }

						echo '</td>' . "\n";
					  }

					// Display values

					if ($mode == 'edit')
					  { foreach ($columns as $columnname => $column)
						  { $value = '';
							$nul = false;

							if (isset($row[ $columnname ]))
							  $value = $row[ $columnname ];
							else
							  $nul = true;

							echo '<td>';

							if ($columnname == $pk)
							  echo '<pre>' . htmlspecialchars($value) . '</pre>';
							else
							  { echo '<nobr>Original value: <nobr>' . htmlspecialchars(substr($value, 0, 50)) . (strlen($value) > 50 ? '...' : '') . '</nobr><br />';

								$inputsize = $column[ 'size' ];
								if ($inputsize < 4)
								  $inputsize = 4;
								elseif ($inputsize > 48)
								  $inputsize = 48;

								echo '<nobr><input type="radio" name="edit[' . $columnname . '][mode]" value="value" ' . ($nul ? '' : 'checked="checked" ') . '/>' . "\n";

								if (($column[ 'type' ] == 'LONG') || ($column[ 'type' ] == 'CLOB'))
								  echo '<textarea name="edit[' . $columnname . '][value]" rows="10" cols="48" wrap="virtual">' . htmlspecialchars($value) . '</textarea>' . "\n";
								else
								  { echo '<input type="text" name="edit[' . $columnname . '][value]" value="' . htmlspecialchars($value) .'" size="' . $inputsize . '" ';
									if (($column[ 'size' ] <= 256) && (($column[ 'type' ] == 'VARCHAR') || ($column[ 'type' ] == 'VARCHAR2')))
									  echo 'maxlength="' . $column[ 'size' ] . '" ';
									echo '/>';
								  }

								echo '</nobr><br />' . "\n";

								echo '<nobr><input type="radio" name="edit[' . $columnname . '][mode]" value="function" ' . ($nul ? 'checked="checked" ' : '') . '/> ' . "\n";
								echo 'Function: <input type="text" name="edit[' . $columnname . '][function]" value="' . ($nul ? 'NULL' : '') .'" size="10" /></nobr>' . "\n";
							  }

							echo '</td>' . "\n";
						  }
					  }
					else
					  foreach ($columns as $columnname => $column)
						{ echo '<td>';

						  if (isset($row[ $columnname ]))
							{ echo '<pre>';

							  if (isset($foreign[ 'to' ][ $columnname ]))
								echo
									'<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid .
									'&table=' . urlencode($foreign[ 'to' ][ $columnname ][ 'table' ]) .
									'&keepwhere=' . urlencode("where " . $foreign[ 'to' ][ $columnname ][ 'column' ] . "='" . ereg_replace("'", "''", $row[ $columnname ]) . "'") .
									'" title="Click here to display the referenced ' . htmlspecialchars($foreign[ 'to' ][ $columnname ][ 'table' ]) . ' record">';

							  echo htmlspecialchars($row[ $columnname ]);

							  if (isset($foreign[ 'to' ][ $columnname ]))
								echo '</a>';

							  echo '</pre>';

							  if (isset($foreign[ 'from' ][ $columnname ]))
								foreach ($foreign[ 'from' ][ $columnname ] as $key => $item)
								  { if ($key > 0)
									  echo '<br />';
									echo
										'<nobr><a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid .
										'&table=' . urlencode($item[ 'table' ]) .
										'&keepwhere=' . urlencode("where " . $item[ 'column' ] . "='" . ereg_replace("'", "''", $row[ $columnname ]) . "'") .
										'" title="Click here to display references to this record in ' . htmlspecialchars($item[ 'table' ] . '.' . $item[ 'column' ]) . '">-&gt; ' .
										nl2br(htmlspecialchars(wordwrap($item[ 'table' ] . '.' . $item[ 'column' ], 30, "-\n", true))) . '</a></nobr>' . "\n";
								  }
							}

						  echo '</td>' . "\n";
						}

					echo '</tr>' . "\n";

					// Check whether there's a next result set

					if ($i >= $_SESSION[ 'setsize' ])
					  { if (ocifetch($cursor))
						  $morerows = true;
						break;
					  }
				  }

				if (! $foundactionrecord)
				  { $action = '';
					$actionrecord = false;
				  }


				// New record row

				if ($action == '')
				  { echo '<tr class="' . ($i % 2 ? 'gridlinealt' : 'gridline') . '">' . "\n";

					if (isset($_REQUEST[ 'showinsert' ]))
					  { // Find default values + NOT NULL restrictions

						$coldefs = pof_getcoldefs($_SESSION[ 'table' ]);

						// Paint cells

						echo '<td><a name="insertrow"></a>&nbsp;</td>' . "\n";
						echo '<td><input type="submit" value="Insert" name="insertsave" /></td>' . "\n";

						foreach ($columns as $columnname => $column)
						  { $value = '';
							$nul   = false;

							if (isset($coldefs[ $columnname ]))
							  { $value = $coldefs[ $columnname ][ 'default'  ];
								$nul   = $coldefs[ $columnname ][ 'nullable' ];
							  }

							echo '<td>';

							$inputsize = $column[ 'size' ];
							if ($inputsize < 4)
							  $inputsize = 4;
							elseif ($inputsize > 48)
							  $inputsize = 48;

							echo '<nobr><input type="radio" name="insert[' . $columnname . '][mode]" value="value" ' . ($nul ? '' : 'checked="checked" ') . '/>' . "\n";
							echo '<input type="text" name="insert[' . $columnname . '][value]" value="' . htmlspecialchars($value) .'" size="' . $inputsize . '" ';
							if (($column[ 'size' ] <= 256) && (($column[ 'type' ] == 'VARCHAR') || ($column[ 'type' ] == 'VARCHAR2')))
							  echo 'maxlength="' . $column[ 'size' ] . '" ';
							echo '/></nobr><br />' . "\n";

							echo '<nobr><input type="radio" name="insert[' . $columnname . '][mode]" value="function" ' . ($nul ? 'checked="checked" ' : '') . '/> ' . "\n";
							echo 'Function: <input type="text" name="insert[' . $columnname . '][function]" value="' . ($nul ? 'NULL' : '') .'" size="10" /></nobr>' . "\n";

							echo '</td>' . "\n";
						  }
					  }
					elseif ($_SESSION[ 'entrymode' ] == 'popups')
					  echo '<td colspan="' . (count($columns) + 2) . '"><a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&showinsert=1#insertrow" title="Click here to create a new record in ' . htmlspecialchars($_SESSION[ 'table' ]) . '">Insert new row</a></td>';

					echo '</tr>' . "\n";
				  }

				echo '</table>' . "\n";

				echo '<table class="gridfooter"><tr><td>' . "\n";

				if ($_SESSION[ 'set' ] > 1)
				  { echo '<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&set=1" accesskey="f" title="Click here to go to the first page [f]">|&lt;</a> ';
					echo '<a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&set=' . ($_SESSION[ 'set' ] - 1) . '" accesskey="p" title="Click here to go to the previous page [p]">&lt;&lt;</a> ';
				  }

				echo 'Page ' . $_SESSION[ 'set' ];

				if ($morerows)
				  echo ' <a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&set=' . ($_SESSION[ 'set' ] + 1) . '" accesskey="n" title="Click here to go to the next page [n]">&gt;&gt;</a>';

				echo '</td></tr></table>' . "\n";
			  }
		  }
		elseif ($statementtype != '')
		  { // Non-SELECT statements

			$rowcount = ocirowcount($cursor);

			$words = array(
				'UPDATE' => 'updated',
				'DELETE' => 'deleted',
				'INSERT' => 'inserted'
				);

			$msg = $rowcount . ' row' . ($rowcount == 1 ? '' : 's') . ' ';

			if (isset($words[ $statementtype ]))
			  $msg .= $words[ $statementtype ] . '.';
			else
			  $msg = $statementtype . ' affected ' . $msg . '.';

			echo pof_sqlline($msg);
		  }

		pof_closecursor($cursor);
	  }


	// History popup

	echo '<table class="selectform"><tr><td>' . "\n";
	echo 'History: <select name="history" onChange="javascript:document.forms[0].submit()" title="Select a previous SQL statement">' . "\n";
	echo '<option value="" selected="selected"> </option>' . "\n";
	foreach ($_SESSION[ 'history' ] as $key => $item)
	  echo '<option value="' . $key . '">' . htmlspecialchars(substr($item[ 'sql' ], 0, 100)) . '</option>' . "\n";
	echo '</select>' . "\n";
	echo '</td></tr></table>' . "\n";

	// Hidden fields for the currently edited record

	if (is_array($actionrecord))
	  { echo '<input type="hidden" name="record[table]" value="' . htmlspecialchars($actionrecord[ 'table' ]) . '" />' . "\n";
		echo '<input type="hidden" name="record[rowid]" value="' . htmlspecialchars($actionrecord[ 'rowid' ]) . '" />' . "\n";
		if ($action != '')
		  echo '<input type="hidden" name="action" value="' . $action . '" />' . "\n";
	  }

	// Footer

	echo '<table class="selectform"><tr>' . "\n";

	// "Drop cache" link

	echo '<td valign="top"><a href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&dropcache=1" title="After altering tables, click here to force a re-read of table definitions">Drop DDL cache</a></td>' . "\n";

	// "Debug" link

	echo '<td valign="top"><a title="Click here to switch SQL statement logging on or off" href="' . $_SERVER[ 'PHP_SELF' ] . '?sid=' . $sid . '&debug=';

	if ($_SESSION[ 'debug' ])
	  echo '0">Turn debug mode off';
	else
	  echo '1">Turn debug mode on';

	echo '</a><br />(Logs all SQL statements in ' . ini_get('error_log') . ')</td>' . "\n";

	// Oracle environment variables display

	echo '<td valign="top">Oracle environment variables:<br />';

	$env_vars = array( 'ORACLE_SID', 'NLS_LANG', 'NLS_DATE_FORMAT' );

	$first = true;

	foreach ($env_vars as $env_var)
	  { $val = getenv($env_var);

		if ($val === false)
		  continue;

		if (! $first) echo '<br />';
		echo sprintf("%s=%s\n", $env_var, $val);
		$first = false;
		}

	echo '</td>';

	echo '</tr></table>';
  }

pof_disconnect();

?>

</form>

<a href="http://oracleeditor.sourceforge.net/" title="OracleEditor.php homepage">OracleEditor.php</a> <?php echo $version; ?> &copy; 2006 by <a href="http://tim.digicol.de/" title="Tim Strehle's homepage on tim.digicol.de">Tim Strehle</a> &lt;<a href="mailto:tim@strehle.de" title="Send e-mail to Tim Strehle">tim@strehle.de</a>&gt;

</body>
</html>
