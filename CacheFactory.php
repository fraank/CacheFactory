<?php
/**
 * a really simple singleton class to communicate with memcached
 * I don't need it anymore, but maybe its useful some somebody else.
 */

final class CacheFactory
{

  const memcached_host = '127.0.0.1';
  const memcached_port = 11211;
  const expiration_time = 360;
  
  /**
  * initialite Memcached
  */
  public static function init()
  {
    static $m = null;
    if ($m === null) {
      $m = new Memcached();
      $m->addServer(CacheFactory::memcached_host, CacheFactory::memcached_port);
    }
    return $m;
  }

  /**
  * get from cache or call that function
  */
  public static function getOrCallFunction($key, $function)
  {
    $m = CacheFactory::init();
    if(!($res = CacheFactory::get($key)))
    {
      $res = $function();
      CacheFactory::set($key, $res);
    }
    return $res;
  }

  /**
  * get key
  */
  public static function get($key)
  {
    $m = CacheFactory::init();
    if (!($res = $m->get($key)))
    {
      if ($m->getResultCode() == Memcached::RES_NOTFOUND)
      {
        return false;
      }
    }
    return $res;
  }

  /**
  * set key
  */
  public static function set($key, $value)
  {
    $m = CacheFactory::init();
    return $m->set($key, $value, time() + CacheFactory::expiration_time);
  }

  /**
  * delete key
  */
  public static function delete($key)
  {
    $m = CacheFactory::init();
    return $m->delete($key);
  }

  /**
  * flush all content
  */
  public static function flush()
  {
    $m = CacheFactory::init();
    return $m->flush();
  }

  /**
  * Private constructor so none can instance it
  */
  private function __construct() { }

}