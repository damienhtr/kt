<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';


/**
 * Impementation of Zend Cache Backend using the Zend Platform (Output Content Caching)
 *
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_ZendPlatform extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{

    // -----------------
    // --- Constants ---
    // -----------------
    const TAGS_PREFIX = "internal_ZPtag:";
    
    
    // ----------------------
    // --- Public methods ---
    // ----------------------

    /**
     * Constructor
     * Validate that the Zend Platform is loaded and licensed
     *
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!function_exists('accelerator_license_info')) {
            Zend_Cache::throwException('The Zend Platform extension must be loaded for using this backend !');
        }
        if (!function_exists('accelerator_get_configuration')) {
            $licenseInfo = accelerator_license_info();
            Zend_Cache::throwException('The Zend Platform extension is not loaded correctly: '.$licenseInfo['failure_reason']);
        }
        $accConf = accelerator_get_configuration();
        if (@!$accConf['output_cache_licensed']) {
            Zend_Cache::throwException('The Zend Platform extension does not have the proper license to use content caching features');
        }
        if (@!$accConf['output_cache_enabled']) {
            Zend_Cache::throwException('The Zend Platform content caching feature must be enabled for using this backend, set the \'zend_accelerator.output_cache_enabled\' directive to On !');
        }
        if (!is_writable($accConf['output_cache_dir'])) {
            Zend_Cache::throwException('The cache copies directory \''. ini_get('zend_accelerator.output_cache_dir') .'\' must be writable !');
        }
        parent:: __construct($options);       
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached data (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        // doNotTestCacheValidity implemented by giving zero lifetime to the cache
        if ($doNotTestCacheValidity) {
            $lifetime = 0;
        } else {
            $lifetime = $this->_directives['lifetime'];
        }
        $res = output_cache_get($id, $lifetime);
        if($res) {
            return $res[0];
        } else {
            return false;
        }
    }


    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $result = output_cache_get($id, $this->_directives['lifetime']);
        if ($result) {
            return $result[1];
        }
        return false;
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param string $data data to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        if (!($specificLifetime === false)) {
            if ($this->_directives['logging']) {
                Zend_Log::log("Zend_Cache_Backend_ZendPlatform::save() : non false specifc lifetime is unsuported for this backend", Zend_Log::LEVEL_WARNING);
            }
        }
        $lifetime = $this->_directives['lifetime'];
        $result1 = output_cache_put($id, array($data, time()));
        foreach($tags as $tag) {
            $tagid = self::TAGS_PREFIX.$tag;
            $old_tags = output_cache_get($tagid, $lifetime);
            if ($old_tags === false) {
                $old_tags = array();
            }
            $old_tags[$id] = $id;
            $result2 = output_cache_put($tagid, $old_tags);
        }
        return $result1 && $result2;
    }


    /**
     * Remove a cache record
     *
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        return output_cache_remove_key($id);
    }


    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     *                                               This mode is not supported in this backend
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                                               ($tags can be an array of strings or a single string)
     *                                               This mode is not supported in this backend
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not matching one of the given tags
     *                                               ($tags can be an array of strings or a single string)
     *                                               This mode is not supported in this backend
     *
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            $idlist = null;
            foreach ($tags as $tag) {
                $next_idlist = output_cache_get(self::TAGS_PREFIX.$tag, $this->_directives['lifetime']);
                if ($idlist) {
                    $idlist = array_intersect_assoc($idlist, $next_idlist);
                } else {
                    $idlist = $next_idlist;
                }
                if (count($idlist) == 0) {
                    // if ID list is already empty - we may skip checking other IDs
                    $idlist = null;
                    break;
                }
            }
            if ($idlist) {
                foreach ($idlist as $id) {
                    output_cache_remove_key($id);
                }
            }
            return true;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            if ($this->_directives['logging']) {
                Zend_Log::log("Zend_Cache_Backend_ZendPlatform::clean() : CLEANING_MODE_NOT_MATCHING_TAG is not supported by the Zend Platform backend", Zend_Log::LEVEL_WARNING);
            }
        }
        $cacheDir = ini_get('zend_accelerator.output_cache_dir');
        if (!$cacheDir) {
            return false;
        }
        $cacheDir .= '/.php_cache_api/';
        return $this->_clean($cacheDir, $mode);
    }


    // -----------------------
    // --- Private methods ---
    // -----------------------


    /**
     * Clean a directory and recursivly go over it's subdirectories
     *
     * Remove all the cached files that need to be cleaned (according to mode and files mtime)
     *
     * @param string $dir Path of directory ot clean
     * @param string $mode The same parameter as in Zend_Cache_Backend_ZendPlatform::clean()
     * @return boolean true if ok
     */
    private function _clean($dir, $mode)
    {
        $d = @dir($dir);
        if (!$d) {
            return false;
        }
        $result = true;
        while (false !== ($file = $d->read())) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $d->path . $file;
            if (is_dir($file)) {
                $result = ($this->_clean($file .'/', $mode)) && ($result);
            } else {
                if ($mode == Zend_Cache::CLEANING_MODE_ALL) {
                    $result = ($this->_remove($file)) && ($result);
                } else if ($mode == Zend_Cache::CLEANING_MODE_OLD) {
                    // Files older than lifetime get deleted from cache
                    if (!is_null($this->_directives['lifetime'])) {
                        if ((time() - @filemtime($file)) > $this->_directives['lifetime']) {
                            $result = ($this->_remove($file)) && ($result);
                        }
                    }
                }
            }
        }
        $d->close();
        return $result;
    }

    /**
     * Remove a file
     *
     * If we can't remove the file (because of locks or any problem), we will touch
     * the file to invalidate it
     *
     * @param string $file complete file path
     * @return boolean true if ok
     */
    private function _remove($file)
    {
        if (!@unlink($file)) {
            # If we can't remove the file (because of locks or any problem), we will touch
            # the file to invalidate it
            if ($this->_directives['logging']) {
                Zend_Log::log("Zend_Cache_Backend_ZendPlatform::_remove() : we can't remove $file => we are going to try to invalidate it", Zend_Log::LEVEL_WARNING);
            }
            if (is_null($this->_directives['lifetime'])) {
                return false;
            }
            if (!file_exists($file)) {
                return false;
            }
            return @touch($file, time() - 2*abs($this->_directives['lifetime']));
        }
        return true;
    }

}
