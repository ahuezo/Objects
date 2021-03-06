<?php

namespace TypiCMS\Modules\Objects\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'TypiCMS\Modules\Objects\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return null
     */
    public function map()
    {
        Route::group(['namespace' => $this->namespace], function (Router $router) {

            /*
             * Front office routes
             */
            if ($page = TypiCMS::getPageLinkedToModule('objects')) {
                $options = $page->private ? ['middleware' => 'auth'] : [];
                foreach (config('translatable-bootforms.locales') as $lang) {
                    if ($page->translate('status', $lang) && $uri = $page->uri($lang)) {
                        $router->get($uri, $options + ['as' => $lang.'.objects', 'uses' => 'PublicController@index']);
                        $router->get($uri.'/{slug}', $options + ['as' => $lang.'.objects.slug', 'uses' => 'PublicController@show']);
                    }
                }
            }

            /*
             * Admin routes
             */
            $router->group(['middleware' => 'admin', 'prefix' => 'admin'], function (Router $router) {
                $router->get('objects', 'AdminController@index')->name('admin::index-objects');
                $router->get('objects/create', 'AdminController@create')->name('admin::create-object');
                $router->get('objects/{object}/edit', 'AdminController@edit')->name('admin::edit-object');
                $router->post('objects', 'AdminController@store')->name('admin::store-object');
                $router->put('objects/{object}', 'AdminController@update')->name('admin::update-object');
                $router->patch('objects/{object}', 'AdminController@ajaxUpdate');
                $router->delete('objects/{object}', 'AdminController@destroy')->name('admin::destroy-object');
            });
        });
    }
}
