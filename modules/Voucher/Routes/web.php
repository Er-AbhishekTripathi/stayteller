<?php
use Illuminate\Support\Facades\Route;

//Review
Route::group(['middleware' => ['auth']],function(){
    Route::get('/voucher',function (){ return redirect('/'); });
    Route::post('/voucher','VoucherController@addReview')->name('voucher.store');
});
