<?php
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'],'/','RoomController@index')->name('room.admin.index');

Route::match(['get'],'/create','RoomController@createroom')->name('room.admin.create');
Route::match(['get'],'/edit/{id}','RoomController@edit')->name('room.admin.edit');
Route::match(['get'],'/vacancyupdate/{id}','RoomController@vacancyupdate')->name('room.admin.vacancyupdate');

Route::post('/store/{id}','RoomController@store')->name('room.admin.store');
Route::post('/roomavailability','RoomController@availabilityUpdate')->name('room.availabiltyupdate');

Route::get('/getForSelect2','RoomController@getForSelect2')->name('room.admin.getForSelect2');
Route::post('/bulkEdit','RoomController@bulkEdit')->name('room.admin.bulkEdit');
