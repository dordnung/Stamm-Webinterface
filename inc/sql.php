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
		$this->db = mysqli_connect($host, $user, $pass, $dbName) or die("Couldn't make connection.");
	}
	
	// Escapes a string
	public function escape($string)
	{
		return mysqli_real_escape_string($this->db, $string);
	}
	
	// Do a query
	public function query($query)
	{
		$result = mysqli_query($this->db, $query) or die(mysqli_error($this->db));
		
		return $result;
	}
	
	// Check if we found Data
	public function foundData($result)
	{
		return mysqli_num_rows($result);
	}
	
	// Get Rows
	public function getRows($result)
	{
		return mysqli_fetch_row($result);
	}
	
	// Get Array
	public function getArray($result)
	{
		return mysqli_fetch_assoc($result);
	}
}
