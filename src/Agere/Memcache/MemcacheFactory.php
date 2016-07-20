<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 24.07.13 19:05
 */

namespace Agere\Memcache;

use Agere\Memcache\Memcache as MemcachePro;
use Agere\Memcache\MemcacheFake;

class MemcacheFactory {

	/**
	 * Create memcache object
	 *
	 * @param $options Memcache options
	 * @return Memcache|MemcacheFake
	 * @throws \Exception
	 */
	public static function create($options) {
		/*
		'host'		=> 'localhost',
		'port'		=> 11211,
		'prefix'	=> 'ccb',
		'enable'	=> true,
		*/

		if (isset($options['enable']) && $options['enable']) {
			//try {
			
				if(class_exists('Memcache')) {
					$host = isset($options['host']) ? $options['host'] : 'localhost';
					$port = isset($options['port']) ? $options['port'] : 11211;

					$cache = new \Memcache();
					$cache->addServer($host, $port);
					
					// memcache doesn't have opportunity check whether memcache server available and generate notice if it is false
					// @link http://stackoverflow.com/questions/1241728/can-i-try-catch-a-warning
					if (false == $cache->connect($host, $port)) {
						// go on without Memcache
						throw new \Exception('OOps, Memcache server is not available. Please, review Memcache server is started!');
					}
					$instance = new MemcachePro($cache, $options['prefix']);
				} else {
					throw new \Exception('OOps, Memcache extension is not installed on this server. You should make installation Memcache extension or take this in your host provider.');
				}
			/*} catch (\Exception $e) {
				die("WARNING: {$e->getMessage()}"); //@todo write to log
			}*/
		} else {
			$instance = new MemcacheFake();
		}

		//self::$instance->flush();
		return $instance;
	}
}