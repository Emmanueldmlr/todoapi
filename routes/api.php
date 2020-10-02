    <?php

    use API\Todo\TodoController;
    use Illuminate\Http\Request;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
    */

    Route::middleware('auth:api')->group(function(){
        Route::resource('/todos', TodoController::class);
        Route::get('/logout', 'API\Auth\AuthenticationController@logout');
    });

    Route::post('/user/register', [
        'as' => 'api.register',
        'uses' =>  'API\Auth\AuthenticationController@register'
    ]);

    Route::post('/user/login', [
        'as' => 'api.user.login',
        'uses' =>  'API\Auth\AuthenticationController@userLogin'
    ]);

    Route::get('/login', [
        'as' => 'api.login',
        'uses' =>  'API\Auth\AuthenticationController@login'
    ]);

