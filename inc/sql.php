<?php
/**
 * -----------------------------------------------------
 * File        sql.php
 * Authors     David <popoklopsi> Ordnung
 * Version     1.2
 * License     GPLv3
 * Web         http://popoklopsi.de
 * -----------------------------------------------------
 * 
 * Stamm Webscript
 * Copyright (C) 2012-2013 David <popoklopsi> Ordnung
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */


// SQL class
class SQL
{
	// link identifier
	private $db = NULL;
	
	// Constructor
	function __construct($host, $user, $pass, $dbName)
	{
		// Connect to MySQL
		$this->db = mysql_connect($host, $user, $pass) or die("Couldn't make connection.");
		mysql_select_db($dbName, $this->db) or die("Couldn't select database");
	}
	
	// Escapes a string
	public function escape($string)
	{
		return mysql_escape_string($string);
	}
	
	// Do a query
	public function query($query)
	{
		$result = mysql_query($query, $this->db) or die(mysql_error($this->db));
		
		return $result;
	}
	
	// Check if we found Data
	public function foundData($result)
	{
		return mysql_num_rows($result);
	}
	
	// Get Rows
	public function getRows($result)
	{
		return mysql_fetch_row($result);
	}
	
	// Get Array
	public function getArray($result)
	{
		return mysql_fetch_assoc($result);
	}
}