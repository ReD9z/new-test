<?php

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

Route::group(['middleware' => ['json.response']], function () {
    
    /******* Public routes *******/
    Route::post('login', 'Api\AuthController@login');
    
    
    /******* Private routes *******/
    Route::middleware('auth:api')->group(function () {
        Route::get('logout', 'Api\AuthController@logout');
        Route::post('files', 'Api\FilesUploadController@add');
        Route::post('files/remove', 'Api\FilesUploadController@remove');
    
        // Article
        Route::post('article', 'Api\ArticleController@store'); // Add one item
        Route::get('article', 'Api\ArticleController@index'); // Show list
        Route::get('article/{id}', 'Api\ArticleController@show'); // Show one item
        Route::put('article', 'Api\ArticleController@store');  // Edit one item
        Route::delete('article', 'Api\ArticleController@store');  // Edit one item
        Route::delete('article/{id}', 'Api\ArticleController@destroy'); // Delete one item
        Route::post('article/excel', 'Api\ArticleController@addExcelData'); // Delete one item

        // Address
        Route::post('address', 'Api\AddressController@store'); // Add one item
        Route::get('address', 'Api\AddressController@index'); // Show list
        Route::get('address/{id}', 'Api\AddressController@show'); // Show one item
        Route::put('address', 'Api\AddressController@store');  // Edit one item
        Route::delete('address/{id}', 'Api\AddressController@destroy'); // Delete one item
        Route::post('address/excel', 'Api\AddressController@addExcelData'); 
        Route::post('address/excelExport', 'Api\AddressController@exportExcelData');

        // Users
        Route::post('users', 'Api\UsersController@store'); // Add one item
        Route::get('users', 'Api\UsersController@index'); // Show list
        Route::get('users/{id}', 'Api\UsersController@show'); // Show one item
        Route::put('users', 'Api\UsersController@store');  // Edit one item
        Route::delete('users/{id}', 'Api\UsersController@destroy'); // Delete one item

        // Admin
        Route::post('admins', 'Api\AdminsController@store'); // Add one item
        Route::get('admins', 'Api\AdminsController@index'); // Show list
        Route::get('admins/{id}', 'Api\AdminsController@show'); // Show one item
        Route::put('admins', 'Api\AdminsController@store');  // Edit one item
        Route::delete('admins/{id}', 'Api\AdminsController@destroy'); // Delete one item

        // Moderators
        Route::post('moderators', 'Api\ModeratorsController@store'); // Add one item
        Route::get('moderators', 'Api\ModeratorsController@show'); // Show one item
        Route::put('moderators', 'Api\ModeratorsController@store');  // Edit one item
        Route::delete('moderators/{id}', 'Api\ModeratorsController@destroy'); // Delete one item
        Route::get('moderators', 'Api\ModeratorsController@index'); // Show list

        // TypesToWorks
        Route::post('types_to_work', 'Api\TypesToWorksController@store'); // Add one item
        Route::get('types_to_work', 'Api\TypesToWorksController@show'); // Show one item
        Route::put('types_to_work', 'Api\TypesToWorksController@store');  // Edit one item
        Route::delete('types_to_work/{id}', 'Api\TypesToWorksController@destroy'); // Delete one item
        Route::get('types_to_work', 'Api\TypesToWorksController@index'); // Show list

        // Areas
        Route::post('areas', 'Api\AreasController@store'); // Add one item
        Route::get('areas', 'Api\AreasController@show'); // Show one item
        Route::put('areas', 'Api\AreasController@store');  // Edit one item
        Route::delete('areas/{id}', 'Api\AreasController@destroy'); // Delete one item
        Route::get('areas', 'Api\AreasController@index'); // Show list

        // AddressToOrders 
        Route::post('address_to_orders', 'Api\AddressToOrdersController@store'); // Add one item
        Route::get('address_to_orders', 'Api\AddressToOrdersController@show'); // Show one item
        Route::put('address_to_orders', 'Api\AddressToOrdersController@store');  // Edit one item
        Route::delete('address_to_orders/{id}', 'Api\AddressToOrdersController@destroy'); // Delete one item
        Route::get('address_to_orders', 'Api\AddressToOrdersController@index'); // Show list
        Route::get('address_to_orders_one/{id}', 'Api\AddressToOrdersController@indexOne'); // Show list

        // CitiesToWorks
        Route::post('cities_to_works', 'Api\CitiesToWorksController@store'); // Add one item
        Route::get('cities_to_works/{id}', 'Api\CitiesToWorksController@show'); // Show one item
        Route::put('cities_to_works', 'Api\CitiesToWorksController@store');  // Edit one item
        Route::delete('cities_to_works/{id}', 'Api\CitiesToWorksController@destroy'); // Delete one item
        Route::get('cities_to_works', 'Api\CitiesToWorksController@index'); // Show list

        // Clients 
        Route::post('clients', 'Api\ClientsController@store'); // Add one item  
        Route::get('clients', 'Api\ClientsController@show'); // Show one item
        Route::put('clients', 'Api\ClientsController@store');  // Edit one item
        Route::delete('clients/{id}', 'Api\ClientsController@destroy'); // Delete one item
        Route::get('clients', 'Api\ClientsController@index'); // Show list
        Route::get('managersAddress/{id}', 'Api\ClientsController@managersAddress'); // Show managers address list
        Route::put('clientsFiles', 'Api\ClientsController@loadFiles'); // Load files
        Route::delete('clientsFiles', 'Api\ClientsController@loadFiles'); // Delete files
        Route::post('clientsComment', 'Api\ClientsController@addComment'); // add Comment
        Route::delete('deleteComment', 'Api\ClientsController@addComment'); // add Comment
        Route::put('editComment', 'Api\ClientsController@addComment'); // add Comment
       
        // Installers
        Route::post('installers', 'Api\InstallersController@store');  // Add one item
        Route::get('installers', 'Api\InstallersController@show'); // Show one item
        Route::put('installers', 'Api\InstallersController@store');  // Edit one item
        Route::delete('installers/{id}', 'Api\InstallersController@destroy'); // Delete one item
        Route::get('installers', 'Api\InstallersController@index'); // Show list

        // Managers   
        Route::post('managers', 'Api\ManagersController@store');  // Add one item
        Route::get('managers', 'Api\ManagersController@show'); // Show one item
        Route::put('managers', 'Api\ManagersController@store');  // Edit one item
        Route::delete('managers/{id}', 'Api\ManagersController@destroy'); // Delete one item
        Route::get('managers', 'Api\ManagersController@index'); // Show list

        // Orders 
        Route::post('orders', 'Api\OrdersController@store');  // Add one item  
        Route::get('orders/{id}', 'Api\OrdersController@show'); // Show one item
        Route::put('orders', 'Api\OrdersController@store');  // Edit one item
        Route::delete('orders/{id}', 'Api\OrdersController@destroy'); // Delete one item
        Route::get('orders', 'Api\OrdersController@index'); // Show list

        // Roles
        Route::post('roles', 'Api\OrdersController@store');  // Add one item   
        Route::get('roles', 'Api\RolesController@show'); // Show one item
        Route::put('roles', 'Api\RolesController@store');  // Edit one item
        Route::delete('roles/{id}', 'Api\RolesController@destroy'); // Delete one item
        Route::get('roles', 'Api\RolesController@index'); // Show list

        // Tasks   
        Route::post('tasks', 'Api\TasksController@store'); // Add one item
        Route::get('tasks', 'Api\TasksController@show'); // Show one item
        Route::put('tasks', 'Api\TasksController@store');  // Edit one item
        Route::delete('tasks/{id}', 'Api\TasksController@destroy'); // Delete one item
        Route::get('tasks', 'Api\TasksController@index'); // Show list

        // TasksManager   
        Route::post('managerTask', 'Api\ManagerTaskController@store'); // Add one item
        Route::get('managerTask', 'Api\ManagerTaskController@show'); // Show one item
        Route::put('managerTask', 'Api\ManagerTaskController@store');  // Edit one item
        Route::delete('managerTask/{id}', 'Api\ManagerTaskController@destroy'); // Delete one item
        Route::get('managerTask', 'Api\ManagerTaskController@index'); // Show list
        Route::post('addExcelTask', 'Api\ManagerTaskController@addExcelTask'); // Show list

        // EntrancesManager   
        Route::put('entrancesSave', 'Api\EntrancesController@store'); // Save one item
    });
});
