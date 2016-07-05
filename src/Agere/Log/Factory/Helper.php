<?php
namespace Agere\Log\Factory;

class Helper {
	
	/**
	 * ����������� ���� ��������, ���� �� ������ �� �������� ��� ������ ��������� ����� �����
	 */
	private function __construct() {}

	
	/**
	 * ������� ���� ���������� ����� getFactory, ��� ������ ��� �� � ����,
	 * �� ��������� ������� ���� �������������. todo
	 * 
	 * � ������� ������� ����� ������ ������ ��� ������� ��������.
	 * ���������: $typestr = 'news', ������� ����� "Service_News"
	 * 
	 * @param str $typestr 	����� ������ ������ ��� ������� ��������.
	 */
	static function getFinder($typestr) {
		$type = self::handleType($typestr);
		$logger = "Agere\\Log\\Logger\\" . $type;
		return new $logger();
	}

	
	/**
	 * ��������� ����� ���������� ���������. 
	 * �������� ����� �� ������� � ������� � �����.
	 * �������� ������ ������� ������.
	 * 
	 * @return string $type
	 */
	static function handleType($typestr) {
		$typestr = trim($typestr);
		$explode = explode('_', $typestr);
		$type = array_pop($explode);
		return ucfirst($type);
	}
}