<?php

namespace App\Http\Controllers;

use App\Models\GetStudent;
use App\Models\Subject;
use Illuminate\Http\Request;

class GetStudentController extends Controller
{
    /**
     * 顯示所有學生案件
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 取得所有科目
        $subjects = Subject::select('id', 'name')->get();

        // 取得所有學生資料
        $students = GetStudent::with('subject', 'city', 'user')->get();

        // 使用 compact 將變量名改為 'students' 和 'subjects'
        return view('student_cases', compact('students', 'subjects'));
    }
}
