<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('guest')->group(function () {
    Route::post('login', 'Api\v1\AuthController@login')->name('login');
    Route::post('refresh-token', 'Api\v1\AuthController@refreshToken')->name('refreshToken');
});

Route::middleware('auth:api')->group(function () {
    Route::group(['middleware' => ['auth:api']], function (){
        Route::post('logout', 'Api\v1\AuthController@logout')->name('logout');
        Route::get('get-platform','Api\v1\RiotController@getPlatform');
    });
    
    /*Route::get('user', 'Api\v1\AuthController@user')->name('logout');
    Route::post('logout', 'Api\v1\AuthController@logout')->name('logout');

    Route::group(['middleware' => ['permissions'], 'permissions' => ['admin', 'operationnel']], function ()
    {
        Route::prefix('/commandes')->group( function () {
            Route::get('/', 'Api\v1\CommandsController@filter');
            Route::post('/fileupload', 'Api\v1\CommandsController@commandeInfosSendScreen');

            // ACCES
            Route::get('/get_step_status/acces/{id}', 'Api\v1\CommandsController@commandAccesStepStatus');
            Route::get('/get_step_details/acces/{step}/{id}', 'Api\v1\CommandsController@commandAccesStepDetails');
            Route::get('/infos/acces/{id}', 'Api\v1\CommandsController@commandAccesInfos');

            // NROPM
            Route::get('/get_step_status/nropm/{id}', 'Api\v1\NropmCommandsController@commandNropmStepStatus');
            Route::get('/get_step_details/nropm/{step}/{id}', 'Api\v1\NropmCommandsController@commandNropmStepDetails');
            Route::get('/infos/nropm/{id}', 'Api\v1\NropmCommandsController@commandNropmInfos');

            // ANNUL NROPM
            Route::get('/get_step_status/annul_nropm/{id}', 'Api\v1\NropmCommandsController@commandAnnulNropmStepStatus');
            Route::get('/get_step_details/annul_nropm/{step}/{id}', 'Api\v1\NropmCommandsController@commandNropmStepDetails');
            Route::get('/infos/annul_nropm/{id}', 'Api\v1\NropmCommandsController@commandAnnulNropmInfos');

            // RES NROPM
            Route::get('/get_step_status/res_nropm/{id}', 'Api\v1\NropmCommandsController@commandResNropmStepStatus');
            Route::get('/get_step_details/res_nropm/{step}/{id}', 'Api\v1\NropmCommandsController@commandNropmStepDetails');
            Route::get('/infos/res_nropm/{id}', 'Api\v1\NropmCommandsController@commandResNropmInfos');

            // GET REFERENCES
            Route::get('/get-references-acces/{id}', 'Api\v1\CommandsController@getReferences');
        });

        // TABLE LOG FLUX
        Route::prefix('/errors')->group( function () {
            Route::get('/', 'Api\v1\CommandsErrorsController@filter');
            Route::get('/get_id_by_ref_iftr/{refIftr}', 'Api\v1\CommandsErrorsController@getErrorId');
            Route::post('/acquit', 'Api\v1\CommandsErrorsController@acquit');
            Route::get('/get_file/{id}', 'Api\v1\CommandsErrorsController@getFile');
        });

        Route::prefix('/files')->group( function () {
            Route::get('/', 'Api\v1\DashboardFilesController@filter');
            Route::get('/get_file/{id}', 'Api\v1\DashboardFilesController@getFile');
            Route::get('/list_files_from_command/{type}/{id}', 'Api\v1\DashboardFilesController@listFilesFromCommand');
        });

        // TABLE EXPORT
        Route::prefix('/exports')->group( function () {
            Route::get('/', 'Api\v1\ExportsController@filter');
            Route::get('/get_file/{id}', 'Api\v1\ExportsController@getFile');
            Route::get('/relaunch/{id}', 'Api\v1\ExportsController@relaunchExport');
            Route::get('/infos/{id}', 'Api\v1\ExportsController@getExportInfos');
            Route::post('/add-custom', 'Api\v1\ExportsController@addCustomExport');
            Route::post('/update', 'Api\v1\ExportsController@updateExportJobs');
            Route::post('/delete/{id}', 'Api\v1\ExportsController@deleteExportJobs');
        });

        // SAV
        Route::prefix('/sav')->group( function () {
            Route::get('/', 'Api\v1\SavController@filter');
        });

        // Change Last Step
        Route::prefix('/change-last-step')->group( function () {
            Route::post('/', 'Api\v1\ChangelaststepController@change');
        });
    });

    Route::group(['middleware' => ['permissions'], 'permissions' => ['SI']], function ()
    {
        Route::prefix('/accounts')->group( function () {
            Route::get('/', 'Api\v1\AccountsController@filter');
            Route::get('/infos/client/{id}', 'Api\v1\AccountsController@accountClientInfos');
            Route::get('/infos/operateur/{id}', 'Api\v1\AccountsController@accountOperateurInfos');
            Route::post('/add/client', 'Api\v1\AccountsController@createAccountClient');
            Route::post('/add/service', 'Api\v1\AccountsController@addService');
            Route::post('/add/ip-by-auth-id', 'Api\v1\AccountsController@addIpByAuthId');
            Route::post('/add/operateur', 'Api\v1\AccountsController@createAccountOperateur');
            Route::post('/add/interco-oi', 'Api\v1\AccountsController@accountAddIntercoOi');
            Route::post('/add/interco-attribut', 'Api\v1\AccountsController@accountAddAttribut');
            Route::post('/add/reference', 'Api\v1\AccountsController@accountAddReference');
            Route::get('/get-auth-ip/{id}', 'Api\v1\AccountsController@accountAuthIp');
            Route::get('/get-operateur-exploitant', 'Api\v1\AccountsController@accountOpExploitant');
            Route::get('/get-service/{type}', 'Api\v1\AccountsController@accountService');
            Route::get('/get-interco-config/{id}', 'Api\v1\AccountsController@loadIntercoConfig');
            Route::get('/get-reference-operateur/{id}', 'Api\v1\AccountsController@accountOpReferences');
            Route::get('/get-interco-oi/{id}', 'Api\v1\AccountsController@accountIntercoOi');
            Route::get('/get-one-interco-oi/{id}', 'Api\v1\AccountsController@accountOneIntercoOi');
            Route::get('/get-interco-attributes/{id}', 'Api\v1\AccountsController@accountIntercoAttributes');
            Route::post('/update/interco-oi', 'Api\v1\AccountsController@accountIntercoOiUpdate');
            Route::post('/update/interco-attributes', 'Api\v1\AccountsController@accountIntercoAttributeUpdate');
            Route::post('/update/reference', 'Api\v1\AccountsController@accountReferenceUpdate');
            Route::post('/update/operateur', 'Api\v1\AccountsController@accountOperateurUpdate');
            Route::post('/update/auth', 'Api\v1\AccountsController@accountAuthUpdate');
            Route::post('/update/client', 'Api\v1\AccountsController@accountClientUpdate');
            Route::delete('/delete/attribut/{id}', 'Api\v1\AccountsController@accountDeleteAttribut');
        });
    });*/
});
