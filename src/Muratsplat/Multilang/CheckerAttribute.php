<?php 

namespace Muratsplat\Multilang;

use Illuminate\Database\Schema\Builder;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Muratsplat\Multilang\Base;

/**
 * The class checks what exist of Eloquent model's attributes
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class CheckerAttribute extends Base {
  
    /**
     * @var \Illuminate\Database\Schema\Builder 
     */
    private $builder;
    
    /**
     * @var \Illuminate\Cache\Repository
     */
    private $cache;
    
    /**
     * Name of key to storing model columns in cache
     * 
     * @var string
     */
    private $cacheKeyName   = 'columns';
    
    /**
     * remember time for storing columns name 
     * 
     * The duration is minute.
     * 
     * @var int
     */
    private $rememberTime;
    
        /**
         * Constructer makes to inject Laravel's Cache and SchemaBuilder instances
         * into this object
         * 
         * @param \Illuminate\Database\Schema\Builder $builder
         * @param \Illuminate\Cache\Repository $cache
         */
        public function __construct(Builder $builder, Cache $cache, Config $config) {
            
            $this->builder  = $builder;
            
            $this->cache    = $cache;
            
            $this->config   = $config;
          
            // getting remember time from the configuration..
            $this->setRememberTime($this->getConfig('rememberTime'));            
        }
        
        /**
         * To select model and check columns
         * 
         * @param \Illuminate\Database\Eloquent\Model $model
         * @name   string
         * @return bool
         */
        public function check(Model $model, $name) {
            
            return $this->search($model, $name);          
        }
        
        
        /**
         * To get all columns on caching
         * 
         * @param \Illuminate\Database\Eloquent\Model $model
         * @return array
         */
        protected function getColumnsOnCache(Model $model) {
             
            /**
             * In normal scenario tables and  columns often is not changed. 
             * Therefore every time it is not need to access the database for knowing 
             * columns name. We don't want make preoccupy/busy to the database. 
             */            
            $fullKey    = $this->getFullName($model);
            
            return $this->cache->remember($fullKey, $this->getRememberTime(), function() use($model) {
                
                return $this->builder->getColumnListing($model->getTable());
                
            });            
        }      
        
        /**
         * to get key name for storing in cache
         * 
         * @param \Illuminate\Database\Eloquent\Model $model name 
         * @return string
         */
        private function getFullName(Model $model) {
            
            $hashed     = md5($model->getTable());
            
            $rootName   = $this->getConfig('cachePrefix');
            
            return $rootName . '/' . $this->cacheKeyName . '/' . $hashed;            
        }
        
        /**
         * To search column name in given model.
         * 
         * It is found  returns true.
         * 
         * @param \Illuminate\Database\Eloquent\Model $model
         * @param string $column
         * @return bool
         */
        private function search(Model $model, $column) {
            
            $columns = $this->getColumnsOnCache($model);
            
            return in_array($column, $columns);                    
        }
        
        /**
         * To set remember time 
         * 
         * @param integer $time  duration is minute
         */
        private function setRememberTime($time) {
            
            $this->rememberTime = is_int($time) && $time > 0 ? $time : 1;
        }
        
        /**
         * TO get remember time
         * 
         * @return int
         */
        private function getRememberTime() {
            
            return (integer) $this->rememberTime;
        }
        
}
