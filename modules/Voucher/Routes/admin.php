<?php
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'],'/','VoucherController@index')->name('voucher.admin.index');

Route::match(['get'],'/create','VoucherController@create')->name('voucher.admin.create');
Route::match(['get'],'/edit/{id}','VoucherController@edit')->name('voucher.admin.edit');

Route::post('/store/{id}','VoucherController@store')->name('voucher.admin.store');

Route::get('/getForSelect2','VoucherController@getForSelect2')->name('voucher.admin.getForSelect2');
Route::post('/bulkEdit','VoucherController@bulkEdit')->name('voucher.admin.bulkEdit');
