--SKIPIF--
<?php
if (getenv('DB') != 'PostgreSQL') {
	exit('skip only running for database PostgreSQL engine');
}
?>
--FILE--
<?php
include __DIR__.'/../../unit.php';
$db = new \cs\DB\PostgreSQL('travis', 'postgres', '', '127.0.0.1', uniqid('xyz_', false));
if (!$db->connected()) {
	die('Connection failed:(');
}

$db->q('CREATE TABLE `[prefix]test` (id SERIAL PRIMARY KEY, title VARCHAR(1024), description TEXT, value FLOAT);');

$query  = "INSERT INTO `[prefix]test` (`title`, `description`, `value`) VALUES ('%s', '%s', '%f')";
$result = $db->insert(
	$query,
	[
		[
			'Title 1',
			'Description 1',
			10.5
		]
	]
);
if ($result) {
	var_dump('single insert id', $db->id(), $db->affected());
}
$result = $db->insert(
	$query,
	[
		[
			'Title 2',
			'Description 2',
			11.5
		],
		[
			'Title 3',
			'Description 3',
			12.5
		]
	]
);
if ($result) {
	var_dump('multiple insert id', $db->affected());
}
$result = $db->insert(
	$query,
	[
		[
			'Title 4',
			'Description 2',
			11.5
		],
		[
			'Title 5',
			'Description 3',
			12.5
		]
	],
	false
);
if ($result) {
	var_dump('multiple insert id without join', $db->affected());
}

$result = $db->q('SELECT `id`, `title` FROM `[prefix]test`');
if (!is_resource($result)) {
	die('Simple query failed');
}
if (!$db->q(
	[
		'SELECT `id`, `title` FROM `[prefix]test` ORDER BY `id` ASC',
		'SELECT `id`, `title` FROM `[prefix]test` ORDER BY `id` ASC'
	]
)
) {
	die('Multi query failed');
}
$u = $db->f($result);
var_dump('single row', $u);
$u = $db->f($result, true);
var_dump('single row single column', $u);

$result = $db->q('SELECT `id`, `title` FROM `[prefix]test`');
var_dump('multiple rows', $db->f($result, false, true));

$result = $db->q('SELECT `id`, `title` FROM `[prefix]test`');
$u      = $db->f($result, true, true);
var_dump('multiple rows single column', $u);

$result = $db->q('SELECT `id`, `title` FROM `[prefix]test`');
$u      = $db->f($result, false, true, true);
var_dump('multiple rows indexed array', $u);

$result = $db->q('SELECT `id`, `title` FROM `[prefix]test`');
$u      = $db->f($result, true, true, true);
var_dump('multiple rows indexed array single column', $u);

var_dump('->qf()', $db->qf("SELECT * FROM `[prefix]test`"));
var_dump('->qf(..., 2)', $db->qf("SELECT * FROM `[prefix]test` WHERE `id` = '%d'", 2));
var_dump('->qfs()', $db->qfs("SELECT * FROM `[prefix]test`"));
var_dump('->qfa()', $db->qfa("SELECT * FROM `[prefix]test`"));
var_dump('->qfas()', $db->qfas("SELECT * FROM `[prefix]test`"));
var_dump('columns list', $db->columns('[prefix]test'));
var_dump('columns list like title', $db->columns('[prefix]test', 'title'));
var_dump('columns list like titl%', $db->columns('[prefix]test', 'titl%'));
var_dump('tables list', $db->tables());
var_dump('tables list like [prefix]test', $db->tables('[prefix]test'));
var_dump('tables list like [prefix]test%', $db->tables('[prefix]test%'));
$db->transaction(
	function ($db) {
		/**
		 * @var \cs\DB\PostgreSQL $db
		 */
		$db->q('DELETE FROM `[prefix]test` WHERE `id` = 2');
		return false;
	}
);
var_dump('transaction for deletion: rollback #1', $db->qfs("SELECT `id` FROM `[prefix]test` WHERE `id` = 2"));
try {
	$db->transaction(
		function ($db) {
			/**
			 * @var \cs\DB\PostgreSQL $db
			 */
			$db->q('DELETE FROM `[prefix]test` WHERE `id` = 2');
			throw new Exception;
		}
	);
} catch (Exception $e) {
	var_dump('thrown exception '.get_class($e));
}
var_dump('transaction for deletion: rollback #2', $db->qfs("SELECT `id` FROM `[prefix]test` WHERE `id` = 2"));
try {
	$db->transaction(
		function ($db) {
			/**
			 * @var \cs\DB\PostgreSQL $db
			 */
			$db->q('DELETE FROM `[prefix]test` WHERE `id` = 2');
			$db->transaction(
				function () {
					throw new Exception;
				}
			);
		}
	);
} catch (Exception $e) {
	var_dump('thrown exception '.get_class($e));
}
var_dump('transaction for deletion: rollback #3 (nested transaction)', $db->qfs("SELECT `id` FROM `[prefix]test` WHERE `id` = 2"));
try {
	$db->transaction(
		function ($db) {
			/**
			 * @var \cs\DB\PostgreSQL $db
			 */
			$db->transaction(
				function ($db) {
					/**
					 * @var \cs\DB\PostgreSQL $db
					 */
					$db->q('DELETE FROM `[prefix]test` WHERE `id` = 2');
				}
			);
			throw new Exception;
		}
	);
} catch (Exception $e) {
	var_dump('thrown exception '.get_class($e));
}
var_dump('transaction for deletion: rollback #4 (nested transaction)', $db->qfs("SELECT `id` FROM `[prefix]test` WHERE `id` = 2"));
$db->transaction(
	function ($db) {
		/**
		 * @var \cs\DB\PostgreSQL $db
		 */
		$db->q('DELETE FROM `[prefix]test` WHERE `id` = 2');
	}
);
var_dump('transaction for deletion: commit', $db->qfs("SELECT `id` FROM `[prefix]test` WHERE `id` = 2"));

var_dump('Empty query string');
var_dump($db->q(''));
var_dump($db->q(['']));

var_dump('Empty insert query string');
var_dump($db->insert('', []));

var_dump('Bad fetch object');
var_dump($db->f(false));

var_dump('Call free on wrong argument');
var_dump($db->free(false));

var_dump('Bad columns table');
var_dump($db->columns(''));

var_dump('Server info');
var_dump($db->server());

var_dump('DB type', $db->db_type());
var_dump('Database name', $db->database());
var_dump('Queries', $db->queries());
var_dump('Last query', $db->query());
var_dump('Time', $db->time());
var_dump('Connecting time', $db->connecting_time());

$db->q('DROP TABLE `[prefix]test`');
unset($db, $e);

var_dump('Bad connection settings');
var_dump(@(new \cs\DB\PostgreSQL('fail'))->connected());

class PostgreSQL_test extends \cs\DB\PostgreSQL {
	function __construct ($database, $user = '', $password = '', $host = 'localhost', $prefix = '') { }
	static function test () {
		$db = new self('');
		var_dump($db->get_host_port_and_persistent('somehost'));
		var_dump($db->get_host_port_and_persistent('somehost:5433'));
		var_dump($db->get_host_port_and_persistent('p:somehost'));
		var_dump($db->get_host_port_and_persistent('p:somehost:5433'));
	}
}
var_dump('Host string parsing');
PostgreSQL_test::test();
?>
--EXPECTF--
string(16) "single insert id"
int(1)
int(1)
string(18) "multiple insert id"
int(2)
string(31) "multiple insert id without join"
int(1)
string(10) "single row"
array(2) {
  ["id"]=>
  string(1) "1"
  ["title"]=>
  string(7) "Title 1"
}
string(24) "single row single column"
string(1) "2"
string(13) "multiple rows"
array(5) {
  [0]=>
  array(2) {
    ["id"]=>
    string(1) "1"
    ["title"]=>
    string(7) "Title 1"
  }
  [1]=>
  array(2) {
    ["id"]=>
    string(1) "2"
    ["title"]=>
    string(7) "Title 2"
  }
  [2]=>
  array(2) {
    ["id"]=>
    string(1) "3"
    ["title"]=>
    string(7) "Title 3"
  }
  [3]=>
  array(2) {
    ["id"]=>
    string(1) "4"
    ["title"]=>
    string(7) "Title 4"
  }
  [4]=>
  array(2) {
    ["id"]=>
    string(1) "5"
    ["title"]=>
    string(7) "Title 5"
  }
}
string(27) "multiple rows single column"
array(5) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
  [2]=>
  string(1) "3"
  [3]=>
  string(1) "4"
  [4]=>
  string(1) "5"
}
string(27) "multiple rows indexed array"
array(5) {
  [0]=>
  array(2) {
    [0]=>
    string(1) "1"
    [1]=>
    string(7) "Title 1"
  }
  [1]=>
  array(2) {
    [0]=>
    string(1) "2"
    [1]=>
    string(7) "Title 2"
  }
  [2]=>
  array(2) {
    [0]=>
    string(1) "3"
    [1]=>
    string(7) "Title 3"
  }
  [3]=>
  array(2) {
    [0]=>
    string(1) "4"
    [1]=>
    string(7) "Title 4"
  }
  [4]=>
  array(2) {
    [0]=>
    string(1) "5"
    [1]=>
    string(7) "Title 5"
  }
}
string(41) "multiple rows indexed array single column"
array(5) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
  [2]=>
  string(1) "3"
  [3]=>
  string(1) "4"
  [4]=>
  string(1) "5"
}
string(6) "->qf()"
array(4) {
  ["id"]=>
  string(1) "1"
  ["title"]=>
  string(7) "Title 1"
  ["description"]=>
  string(13) "Description 1"
  ["value"]=>
  string(4) "10.5"
}
string(12) "->qf(..., 2)"
array(4) {
  ["id"]=>
  string(1) "2"
  ["title"]=>
  string(7) "Title 2"
  ["description"]=>
  string(13) "Description 2"
  ["value"]=>
  string(4) "11.5"
}
string(7) "->qfs()"
string(1) "1"
string(7) "->qfa()"
array(5) {
  [0]=>
  array(4) {
    ["id"]=>
    string(1) "1"
    ["title"]=>
    string(7) "Title 1"
    ["description"]=>
    string(13) "Description 1"
    ["value"]=>
    string(4) "10.5"
  }
  [1]=>
  array(4) {
    ["id"]=>
    string(1) "2"
    ["title"]=>
    string(7) "Title 2"
    ["description"]=>
    string(13) "Description 2"
    ["value"]=>
    string(4) "11.5"
  }
  [2]=>
  array(4) {
    ["id"]=>
    string(1) "3"
    ["title"]=>
    string(7) "Title 3"
    ["description"]=>
    string(13) "Description 3"
    ["value"]=>
    string(4) "12.5"
  }
  [3]=>
  array(4) {
    ["id"]=>
    string(1) "4"
    ["title"]=>
    string(7) "Title 4"
    ["description"]=>
    string(13) "Description 2"
    ["value"]=>
    string(4) "11.5"
  }
  [4]=>
  array(4) {
    ["id"]=>
    string(1) "5"
    ["title"]=>
    string(7) "Title 5"
    ["description"]=>
    string(13) "Description 3"
    ["value"]=>
    string(4) "12.5"
  }
}
string(8) "->qfas()"
array(5) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
  [2]=>
  string(1) "3"
  [3]=>
  string(1) "4"
  [4]=>
  string(1) "5"
}
string(12) "columns list"
array(4) {
  [0]=>
  string(2) "id"
  [1]=>
  string(5) "title"
  [2]=>
  string(11) "description"
  [3]=>
  string(5) "value"
}
string(23) "columns list like title"
array(1) {
  [0]=>
  string(5) "title"
}
string(23) "columns list like titl%"
array(1) {
  [0]=>
  string(5) "title"
}
string(11) "tables list"
array(1) {
  [0]=>
  string(%d) "xyz_%stest"
}
string(29) "tables list like [prefix]test"
array(1) {
  [0]=>
  string(%d) "xyz_%stest"
}
string(30) "tables list like [prefix]test%"
array(1) {
  [0]=>
  string(%d) "xyz_%stest"
}
string(37) "transaction for deletion: rollback #1"
string(1) "2"
string(26) "thrown exception Exception"
string(37) "transaction for deletion: rollback #2"
string(1) "2"
string(26) "thrown exception Exception"
string(58) "transaction for deletion: rollback #3 (nested transaction)"
string(1) "2"
string(26) "thrown exception Exception"
string(58) "transaction for deletion: rollback #4 (nested transaction)"
string(1) "2"
string(32) "transaction for deletion: commit"
bool(false)
string(18) "Empty query string"
bool(false)
bool(false)
string(25) "Empty insert query string"
bool(false)
string(16) "Bad fetch object"
bool(false)
string(27) "Call free on wrong argument"
bool(true)
string(17) "Bad columns table"
bool(false)
string(11) "Server info"
string(5) "%d.%d.%d"
string(7) "DB type"
string(10) "postgresql"
string(13) "Database name"
string(6) "travis"
string(7) "Queries"
array(3) {
  ["num"]=>
  int(35)
  ["time"]=>
  array(0) {
  }
  ["text"]=>
  array(0) {
  }
}
string(10) "Last query"
array(2) {
  ["time"]=>
  float(%f)
  ["text"]=>
  string(%d) "%s"
}
string(4) "Time"
float(%f)
string(15) "Connecting time"
float(%f)
string(23) "Bad connection settings"
bool(false)
string(19) "Host string parsing"
array(3) {
  [0]=>
  string(8) "somehost"
  [1]=>
  int(5432)
  [2]=>
  bool(false)
}
array(3) {
  [0]=>
  string(8) "somehost"
  [1]=>
  string(4) "5433"
  [2]=>
  bool(false)
}
array(3) {
  [0]=>
  string(8) "somehost"
  [1]=>
  int(5432)
  [2]=>
  bool(true)
}
array(3) {
  [0]=>
  string(8) "somehost"
  [1]=>
  string(4) "5433"
  [2]=>
  bool(true)
}
