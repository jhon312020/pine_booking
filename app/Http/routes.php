<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/', 'HomeController@index');
Route::match(array('GET', 'POST'), 'home/index', 'HomeController@index');
#Route::get('expense/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'expense/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'expense/list', 'ExpenseController@index');
Route::match(array('GET', 'POST'), 'expense/getDateIncome', 'ExpenseController@getDateIncome');
Route::delete('expense/delete/{expense}', 'ExpenseController@delete');
//Route::match(array('GET', 'POST'), 'expense/list', 'ExpenseController@today');

Route::get('expense/today', 'ExpenseController@today');
Route::get('expense/yesterday', 'ExpenseController@today');
Route::get('reports/lastyear', 'ExpenseController@yearly');
Route::get('reports/currentyear', 'ExpenseController@yearly');
Route::get('reports/monthly', 'ExpenseController@monthly');
Route::match(array('GET', 'POST'), 'reports/income', 'ExpenseController@income');
Route::get('reports/month/{month}', 'ExpenseController@monthly');
Route::match(array('GET', 'POST'), 'income/add', 'IncomeController@add');
Route::match(array('GET', 'POST'), 'income/list', 'IncomeController@index');
Route::match(array('GET', 'POST'), 'income', 'IncomeController@listing');
Route::delete('income/delete/{income}', 'IncomeController@delete');

Route::match(array('GET', 'POST'), 'room/add', 'RoomController@add');
Route::match(array('GET', 'POST'), 'room/list', 'RoomController@listing');
Route::match(array('GET', 'POST'), 'room/edit/{id}', 'RoomController@edit');
Route::delete('room/delete/{room}', 'RoomController@delete');
Route::match(array('GET', 'POST'), 'room/update/{id}', 'RoomController@update');

Route::match(array('GET', 'POST'), 'customer/add', 'CustomerController@add');
Route::match(array('GET', 'POST'), 'customer/list', 'CustomerController@listing');
Route::match(array('GET', 'POST'), 'customer/edit/{id}', 'CustomerController@edit');
Route::delete('customer/delete/{customer}', 'CustomerController@delete');

Route::match(array('GET', 'POST'), 'employees/add', 'EmployeeController@add');
Route::match(array('GET', 'POST'), 'employees/list', 'EmployeeController@listing');
Route::match(array('GET', 'POST'), 'employees/edit/{id}', 'EmployeeController@edit');
Route::delete('employees/delete/{employee}', 'EmployeeController@delete');
Route::match(array('GET', 'POST'), 'employees/pay/{id}', 'EmployeeController@pay');
Route::match(array('GET', 'POST'), 'employees/getSalary', 'EmployeeController@getSalary');

Route::match(array('GET', 'POST'), 'food/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'food/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'laundry/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'laundry/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'electricity/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'electricity/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'housekeeping/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'housekeeping/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'internet/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'internet/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'salary/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'salary/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'others/add', 'ExpenseController@add');
Route::match(array('GET', 'POST'), 'others/list', 'ExpenseController@listing');

Route::match(array('GET', 'POST'), 'overall/list', 'ExpenseController@index');
Route::match(array('GET', 'POST'), 'user/changepassword', 'UserController@changepassword');

Route::match(array('GET', 'POST'), 'reservation/index', 'ReservationController@index');
Route::match(array('GET', 'POST'), 'reservation/add', 'ReservationController@add');
Route::match(array('GET', 'POST'), 'reservation/advance/{id}', 'ReservationController@advance');
Route::match(array('GET', 'POST'), 'reservation/edit/{id}', 'ReservationController@edit');
Route::match(array('GET', 'POST'), 'reservation/confirm/{id}', 'ReservationController@confirm');
Route::match(array('GET', 'POST'), 'reservation/completed/{id}', 'ReservationController@completed');
Route::match(array('GET', 'POST'), 'reservation/view_detail/{id}', 'ReservationController@view_detail');
Route::delete('reservation/cancel/{reservation}', 'ReservationController@cancel');
Route::delete('reservation/delete/{reservation}', 'ReservationController@delete');
Route::match(array('GET', 'POST'), 'reservation/autocomplete', 'ReservationController@autocomplete');
Route::match(array('GET', 'POST'), 'reservation/show_customer_detail', 'ReservationController@show_customer_detail');
Route::match(array('GET', 'POST'), 'reservation/show_available_room_list', 'ReservationController@show_available_room_list');
Route::match(array('GET', 'POST'), 'reservation/check_available_rooms', 'ReservationController@check_available_rooms');

Route::match(array('GET', 'POST'), 'room_type/index', 'RoomTypeController@index');
Route::delete('room_type/delete/{room_type}', 'RoomTypeController@delete');