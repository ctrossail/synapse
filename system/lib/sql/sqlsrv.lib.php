<?php

class mysql extends sql
{
	var $link;

	
        
	function sql_connect($host, $login, $password)
	{
		$this->link = mysql_connect($host, $login, $password);
		
		//mysql_set_charset('utf8',$this->link); 
		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'latin1'", $this->link);
		mysql_query("SET NAMES 'utf8'");
		
	}

	function sql_select_db($db)
	{
		return mysql_select_db($db, $this->link) or die (mysql_error());
	}
	
	function _query($sql)
	{
		return mysql_query($sql, $this->link);	
	}

	function sql_num_rows($res)
	{
		return mysql_num_rows($res);
	}
	
	function sql_close()
	{
		$this->link = mysql_close($this->link);
	}
	
	function sql_affected_rows()
	{
		return mysql_affected_rows($this->link);
	}

	
	function sql_real_escape_string($data)
	{
		return mysql_real_escape_string($data);
	}
	
	function sql_insert_id()
	{
		return $this->last_id;
	}
	
	function _insert_id()
	{
		return mysql_insert_id();
	}
	
	function _error()
	{
		return mysql_error();
	}
	
	function sql_fetch_array($res)
	{
		return mysql_fetch_array($res);
	}
	
	function sql_to_array($res)
	{
		$rep = array();
		
		while($tab = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			
			$rep[] = $tab;
		}
		
		return $rep;
	}
	
	function sql_fetch_object($res)
	{
		return mysql_fetch_object($res);
	}
	
	function sql_fetch_row($res)
	{
		return mysql_fetch_row($res);
	}
}




?>