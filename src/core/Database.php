<?php

namespace MNV\Core;

use PDO;

class Database{
	
	protected PDO $pdo;
	protected string $tableName;
	
	/**
	 * Connect to the database
	 */
	public function __construct(string $tableName)
	{
		$db = env('DB_CONNECTION') . ':host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE');
		$userName = env('DB_USERNAME');
		$userPassword = env('DB_PASSWORD');
		
		$this->pdo = new \PDO($db, $userName, $userPassword);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		if(env('APP_ENV')=='testing')
			$this->pdo->beginTransaction();
		
		$this->tableName = $tableName;
	}
	
	/**
	 * Return the pdo connection
	 */
	public function getPdo()
	{
		return $this->pdo;
	}
	
	/**
	 * Returns the ID of the last inserted row or sequence value
	 *
	 * @param  string $param Name of the sequence object from which the ID should be returned.
	 * @return string representing the row ID of the last row that was inserted into the database.
	 */
	public function lastInsertId($param = null)
	{
		return $this->pdo->lastInsertId($param);
	}
	
	/**
	 * handler for dynamic CRUD methods
	 *
	 * Format for dynamic methods names -
	 * Create:  insertTableName($arrData)
	 * Retrieve: getTableNameByFieldName($value)
	 * Update: updateTableNameByFieldName($value, $arrUpdate)
	 * Delete: deleteTableNameByFieldName($value)
	 *
	 * @param  string     $function
	 * @param  array      $arrParams
	 * @return array|bool
	 */
	public function __call($function, array $params = array())
	{
		if (! preg_match('/^(get|update|insert|delete)(.*)$/', $function, $matches)) {
			throw new \BadMethodCallException($function.' is an invalid method Call');
		}
		
		if ('insert' == $matches[1]) {
			if (! is_array($params[0]) || count($params[0]) < 1) {
				throw new \InvalidArgumentException('insert values must be an array');
			}
			return $this->insert($this->camelCaseToUnderscore($matches[2]), $params[0]);
		}
		
		list($this->tableName, $fieldName) = explode('By', $matches[2], 2);
		if (! isset($this->tableName, $fieldName)) {
			throw new \BadMethodCallException($function.' is an invalid method Call');
		}
		
		if ('update' == $matches[1]) {
			if (! is_array($params[1]) || count($params[1]) < 1) {
				throw new \InvalidArgumentException('update fields must be an array');
			}
			return $this->update(
				$this->camelCaseToUnderscore($this->tableName),
				$params[1],
				array($this->camelCaseToUnderscore($fieldName) => $params[0])
			);
		}
		
		//select and delete method
		return $this->{$matches[1]}(
			$this->camelCaseToUnderscore($this->tableName),
			array($this->camelCaseToUnderscore($fieldName) => $params[0])
		);
	}
	
	/**
	 * Record retrieval method
	 *
	 * @param  array      $where     (key is field name)
	 * @return array|bool (associative array for single records, multidim array for multiple records)
	 */
	public function get($whereAnd = array(), $whereOr = array(), $whereLike = array())
	{
		$cond   =   '';
		$s=1;
		$params =   array();
		foreach($whereAnd as $key => $val)
		{
			$cond   .=  " And ".$key." = :a".$s;
			$params['a'.$s] = $val;
			$s++;
		}
		foreach($whereOr as $key => $val)
		{
			$cond   .=  " OR ".$key." = :a".$s;
			$params['a'.$s] = $val;
			$s++;
		}
		foreach($whereLike as $key => $val)
		{
			$cond   .=  " OR ".$key." like '% :a".$s."%'";
			$params['a'.$s] = $val;
			$s++;
		}
		$stmt = $this->pdo->prepare("SELECT  {$this->tableName}.* FROM {$this->tableName} WHERE 1 ".$cond);
		try {
			$stmt->execute($params);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if (! $res || count($res) != 1) {
				return $res;
			}
			return $res;
		} catch (\PDOException $e) {
			throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
		}
	}
	
	public function getAllRecords($fields='*', $cond='', $orderBy='', $limit='')
	{
		$stmt = $this->pdo->prepare("SELECT $fields FROM {$this->tableName} WHERE 1 ".$cond." ".$orderBy." ".$limit);
		
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	public function getRecFrmQry($query)
	{
		//echo $query;
		$stmt = $this->pdo->prepare($query);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	public function getRecFrmQryStr($query)
	{
		//echo $query;
		$stmt = $this->pdo->prepare($query);
		$stmt->execute();
		return array();
	}
	public function getQueryCount($field, $cond='')
	{
		$stmt = $this->pdo->prepare("SELECT count($field) as total FROM {$this->tableName} WHERE 1 ".$cond);
		try {
			$stmt->execute();
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if (! $res || count($res) != 1) {
				return $res;
			}
			return $res;
		} catch (\PDOException $e) {
			throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
		}
	}
	
	/**
	 * Update Method
	 *
	 * @param  array  $set       (associative where key is field name)
	 * @param  array  $where     (associative where key is field name)
	 * @return array    data that been updated
	 */
	public function update(array $set, array $where)
	{
		$arrSet = array_map(
			function($value) {
				return $value . '=:' . $value;
			},
			array_keys($set)
		);
		
		$stmt = $this->pdo->prepare(
			"UPDATE {$this->tableName} SET ". implode(',', $arrSet).' WHERE '. key($where). '=:'. key($where) . 'Field'
		);
		
		foreach ($set as $field => $value) {
			$stmt->bindValue(':'.$field, $value);
		}
		$stmt->bindValue(':'.key($where) . 'Field', current($where));
		try {
			$stmt->execute();
			return $this->get($where)[0];
		} catch (\PDOException $e) {
			throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
		}
	}
	
	/**
	 * Delete Method
	 *
	 * @param  array  $where     (associative where key is field name)
	 * @return int    number of affected rows
	 */
	public function delete(array $where)
	{
		$stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE ".key($where) . ' = ?');
		try {
			$stmt->execute(array(current($where)));
			
			return $stmt->rowCount();
		} catch (\PDOException $e) {
			throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
		}
	}
	
	
	public function deleteFrmQry($query)
	{
		$stmt = $this->pdo->prepare($query);
		$stmt->execute();
	}
	
	
	/**
	 * Insert Method
	 *
	 * @param  array  $arrData   (data to insert, associative where key is field name)
	 * @return array    data that been inserted
	 */
	public function insert(array $data)
	{
		$stmt = $this->pdo->prepare("INSERT INTO {$this->tableName} (".implode(',', array_keys($data)).")
            VALUES (".implode(',', array_fill(0, count($data), '?')).")"
		);
		try{
			$stmt->execute(array_values($data));
			$lastInsertId = $this->lastInsertId();
			return $this->get(['id' => $lastInsertId])[0];
		} catch (\PDOException $e) {
			throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
		}
	}
	/**
	 * Print array Method
	 *
	 * @param  array
	 */
	public function arprint($array){
		print"<pre>";
		print_r($array);
		print"</pre>";
	}
	
	/**
	 * Cache Method
	 *
	 * @param  string QUERY
	 * @param  Int Time default 0 set
	 */
	public function getCache($sql,$filePath,$cache_min=0) {
		$f = $filePath.md5($sql);
		if ( $cache_min!=0 and file_exists($f) and ( (time()-filemtime($f))/60 < $cache_min ) ) {
			$arr = unserialize(file_get_contents($f));
		}
		else {
			unlink($f);
			$arr = self::getRecFrmQry($sql);
			if ($cache_min!=0) {
				$fp = fopen($f,'w');
				fwrite($fp,serialize($arr));
				fclose($fp);
			}
		}
		return $arr;
	}
	
	
}