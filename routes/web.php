<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FindTeacherController;
use App\Http\Controllers\StudentPageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;

Route::get('/', function () {
    return view('welcome');
});

// http://localhost/LearnLink/public/

// 首頁↓↓↓↓↓↓↓↓↓↓↓  http://127.0.0.1:8000/homePage
Route::view('/homePage','homepage');

// 登入頁面↓↓↓↓↓↓↓↓↓↓    http://127.0.0.1:8000/login
Route::view('/login','login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
// 登入功能↑↑↑↑↑↑↑↑↑

// 註冊↓↓↓↓↓↓↓↓↓↓    
Route::post('/register', [AuthController::class, 'register']);

// 登入測試   http://127.0.0.1:8000/auth_status
Route::view('/auth_status', 'auth_status')->name('auth.status');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');




Route::view('/findteacher','findteacher');
// Route::match(['get', 'post'], '/findteacher', function () {
//     return view('findteacher');
// });
// Route::post('/findteacher', function () {
//     return view('welcome');
// });
Route::post('/findteacher', [FindTeacherController::class, 'findteacher'])->name('findteacher');


Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');



Route::get('/studentpage', [StudentPageController::class, 'index'])->name('studentpage');
Route::post('/studentpage/store', [StudentPageController::class, 'store'])->name('studentpage.store');


// 獲取城市列表
Route::get('/cities', [CityController::class, 'index']);
// 獲取區域列表
Route::get('/districts/{cityId}', [DistrictController::class, 'getDistricts']);

Route::get('/teacher_lists', [SubjectController::class, 'index'])->name('teacher_lists');
Route::get('/student_cases', [SubjectController::class, 'index'])->name('student_cases');



// 老師列表搜尋
Route::get('/search', [TeacherController::class, 'search']);