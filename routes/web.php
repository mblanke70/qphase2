<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/schwerpunkt');

Route::get ('schwerpunkt', 'KurswahlenController@zeigeSchwerpunkte');
Route::post('schwerpunkt', 'KurswahlenController@verarbeiteSchwerpunkte');
    
Route::get ('p1', 'KurswahlenController@zeigeP1');
Route::post('p1', 'KurswahlenController@verarbeiteP1');

Route::get ('p2', 'KurswahlenController@zeigeP2');
Route::post('p2', 'KurswahlenController@verarbeiteP2');

Route::get ('p3', 'KurswahlenController@zeigeP3');
Route::post('p3', 'KurswahlenController@verarbeiteP3');

Route::get ('p4', 'KurswahlenController@zeigeP4');
Route::post('p4', 'KurswahlenController@verarbeiteP4');

Route::get ('p5', 'KurswahlenController@zeigeP5');
Route::post('p5', 'KurswahlenController@verarbeiteP5');

Route::get ('k1', 'KurswahlenController@verarbeiteK1');	// Deutsch

Route::get ('k2', 'KurswahlenController@zeigeK2');		// Fremdsrpache
Route::post('k2', 'KurswahlenController@verarbeiteK2');

Route::get ('k3', 'KurswahlenController@verarbeiteK3');	// Mathematik

Route::get ('e1', 'KurswahlenController@zeigeE1');
Route::post('e1', 'KurswahlenController@verarbeiteE1');

Route::get ('e2', 'KurswahlenController@zeigeE2');
Route::post('e2', 'KurswahlenController@verarbeiteE2');

Route::get ('e3', 'KurswahlenController@zeigeE3');
Route::post('e3', 'KurswahlenController@verarbeiteE3');

Route::get ('e4', 'KurswahlenController@zeigeE4');
Route::post('e4', 'KurswahlenController@verarbeiteE4');

Route::get ('e5678', 'KurswahlenController@verarbeiteE5678');

Route::get ('wf', 'KurswahlenController@zeigeWahlfach');
Route::post('wf', 'KurswahlenController@verarbeiteWahlfach');
Route::post('wf/{fach}', 'KurswahlenController@aendereHalbjahre');
Route::delete('wf', 'KurswahlenController@loescheWahlfach');
