<?php namespace Muratsplat\Multilang\Tests\Model;


use Illuminate\Database\Eloquent\Model;
use Muratsplat\Multilang\MainInterface;
use Muratsplat\Multilang\LangInterface;
/**
 *  Simple Models For tests
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */

/**
 * Simple Main Model
 */
class Content extends Model implements MainInterface {
    //put your code here
}


/**
 * ContantLang  will be Content's multi languages model.
 */
class ContentLang extends Model implements LangInterface {
    //put your code here
}


