<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Schema\Builder;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;

use Muratsplat\Multilang\Base;
use Carbon\Carbon;

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
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;
    
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
        public function __construct(Builder $builder, CacheManager $cache, Config $config) {
            
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
                       
            $fullKey    = $this->getFullName($model); 
            /**
             * In normal scenario tables and  columns often is not changed. 
             * Therefore every time it is not need to access the database for knowing 
             * columns name. We don't want make the database to preoccupy. 
             */
            if (!$this->cache->has($fullKey)) {
                
                $this->putColumns($model);
            }
            
            return $this->search($this->cache->get($fullKey), $name);          
        }
        
        /**
         * to put model's columns list on cache
         * 
         * @param \Illuminate\Database\Eloquent\Model $model
         */
        protected function putColumns(Model $model) {
            
            $fullKey    = $this->getFullName($model);
            
            $columns    = $this->builder->getColumnListing($model->getTable());
            
            $time       = Carbon::now()->addMinutes($this->getRememberTime());
            
            // storing all columns of given model using cache driver
            $this->cache->put($fullKey, $columns, $time);           
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
         * To search column name in gven array.
         * 
         * It is found  returns true. 
         * 
         * @param array $array
         * @param string $column
         * @return bool
         */
        private function search($array, $column) {
            
            return in_array($column, $array);                    
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
