<?php
use Illuminate\Support\Facades\Route;

//Review
Route::group(['middleware' => ['auth']],function(){
    Route::get('/rooms',function (){ return redirect('/'); });
    
   

});
Route::group(['prefix'=>'room','middleware' => ['auth','verified']],function(){

    Route::get('room','RoomController@index')->name("user.room.index");
    Route::match(['get'],'/create','RoomController@createroom')->name('room.user.create');
    Route::post('/store/{id}','RoomController@store')->name('room.store');
    Route::match(['get'],'/edit/{id}','RoomController@edit')->name('room.edit');
    Route::match(['get'],'/vacancyupdate/{id}','RoomController@vacancyupdate')->name('room.vacancyupdate');
    Route::post('/roomavailability','RoomController@availabilityUpdate')->name('rooms.availabiltyupdate');
    Route::post('/availabiltybulkupdate','RoomController@availabiltybulkupdate')->name('rooms.availabiltybulkupdate');

});

