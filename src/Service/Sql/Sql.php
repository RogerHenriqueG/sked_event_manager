<?php
namespace App\Service\Sql;

class Sql extends \PDO
{
	private $data = [];

	function __construct()
	{
		$this->data = parse_ini_file(__DIR__ . '/../../../.env');
		parent::__construct("{$this->data['dbengine']}:dbname={$this->data['dbname']};host={$this->data['dbhost']}",$this->data['dbuser'],$this->data['dbpass']);
	}

	public function setParams($stmt, $data = [])
	{

		foreach ($data as $key => $value) {
			$stmt->bindValue($key, $value);
		}

		return $stmt;
	}
}
