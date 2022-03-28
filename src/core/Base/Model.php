<?php

namespace MNV\Core\Base;

use MNV\Core\Database;

abstract class Model {
	
	protected string $tableName;
	protected array $columns;
	protected Database $db;
	
	public function __construct()
	{
		$this->tableName = $this->getTableName();
		$this->db = new Database($this->tableName);
	}
	
	public function getDB(): Database{
		return $this->db;
	}
	
	private function getTableName(): string{
		$class = (new \ReflectionClass($this))->getShortName();
		$tableSingular = strtolower(trim(preg_replace('/[A-Z]/', '_$0', $class), '_'));
		if(ends_with($tableSingular, 'y')){
			return substr($tableSingular, 0, -1) . 'ies';
		} else {
			return $tableSingular . 's';
		}
	}
	
	
}