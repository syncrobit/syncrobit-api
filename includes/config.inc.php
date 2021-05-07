<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

define("SB_CORE", dirname(dirname(__FILE__))."/");
define("SB_LIBS", SB_CORE."libs/");
define("SB_MODULES", SB_CORE."modules/");
define("SB_RESOURCES", SB_CORE."resources/");

/** MySQL Credentials */
define("SB_DB_HOST", "mysql.local");
define("SB_DB_USER", "syncrobit");
define("SB_DB_PASSWORD", "m3rt3c123");
define("SB_DB_DATABASE", "syncrobit");
define("SB_DB_UNITS", "chameleon");

/** Postgres Credentials */
define("SB_PG_HOST", "pgsql.local");
define("SB_PG_USER", "etl");
define("SB_PG_PASSWORD", "m3rt3c123");
define("SB_PG_DATABASE", "etl");

/** Memcached Credentials */
define("SB_MEMCACHED", "192.168.198.141");
define("MEMCACHED_SHORT",   3600);
define("MEMCACHED_MEDIUM",  95200);
define("MEMCACHED_LONG",    1005200);

//DB Calls
$pg_db      = new PDO("pgsql:host=".SB_PG_HOST.";port=5432;dbname=".SB_PG_DATABASE.";user=".SB_PG_USER.";password=".SB_PG_PASSWORD);
$msql_db    = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_DATABASE, SB_DB_USER, SB_DB_PASSWORD);
$msqlu_db    = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);