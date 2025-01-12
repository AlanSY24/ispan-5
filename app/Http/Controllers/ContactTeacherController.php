<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactTeacher;
use App\Models\BeTeacher;
use App\Models\TeacherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TeacherProfile;


class ContactTeacherController extends Controller
{
    //
    public function contactTeacher(Request $request)
    {
        // 驗證請求資料
        $validated = $request->validate([
            'be_teacher_id' => 'required|exists:be_teachers,id',
        ]);
        // 獲取已登入使用者的 ID
        $userId = Auth::id();

            // 檢查是否已經存在聯絡記錄
        $existingContact = ContactTeacher::where('be_teacher_id', $validated['be_teacher_id'])
        ->where('user_id', $userId)
        ->first();

        if ($existingContact) {
        return response()->json(['message' => '您已經聯絡過這位老師了。'], 400);
        }


        // 建立新的聯絡紀錄
        $contact = ContactTeacher::create([
            'be_teacher_id' => $validated['be_teacher_id'],
            'user_id' => $userId, // 使用已登入使用者的 ID
        ]);

        return response()->json(['message' => '聯絡成功', 'contact' => $contact], 201);
    }

    public function checkContactStatus(Request $request)
    {
        $validated = $request->validate([
            'be_teacher_id' => 'required|exists:be_teachers,id',
        ]);
    
        $userId = Auth::id();
    
        $exists = ContactTeacher::where('be_teacher_id', $validated['be_teacher_id'])
                                ->where('user_id', $userId)
                                ->exists();
    
        return response()->json(['exists' => $exists]);
    }

        /**
     * 獲取使用者聯絡過的所有教師
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyContacts()
    {
        // 獲取已登入使用者的 ID
        $userId = Auth::id();

        // 獲取使用者聯絡過的所有教師
        $contacts = ContactTeacher::where('user_id', $userId)->get();

        return response()->json($contacts);
    }

    // 顯示特定使用者的所有 TeacherRequests 及其相關的 ContactStudents
    public function showUserBeTeacherWithContacts()
    {

        // 獲取已登入使用者的 ID
        $userId = Auth::id();
        // 查找指定使用者的 TeacherRequests 並加載相關的 ContactStudents
        $teacherRequests = TeacherRequest::where('CaseReceiver', $userId)
            ->where('status', 'published')
            ->with('contactStudents.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 TeacherRequests 並加載相關的 ContactStudents
        $teacherRequestsIn_progress = TeacherRequest::where('CaseReceiver', $userId)
            ->where('status', 'in_progress')
            ->with('contactStudents.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 TeacherRequests 並加載相關的 ContactStudents
        $teacherRequestsCompleted = TeacherRequest::where('CaseReceiver', $userId)
            ->where('status', 'completed')
            ->with('contactStudents.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 TeacherRequests 並加載相關的 ContactStudents
        $teacherRequestsCancelled = TeacherRequest::where('CaseReceiver', $userId)
            ->where('status', 'cancelled')
            ->with('contactStudents.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 BeTeacher 並加載相關的 ContactStudents
        $beteacher = BeTeacher::where('user_id', $userId)
            ->where('status', 'published')
            ->with('contactTeacher.user', 'subject', 'city')
            ->get();
             // 查找指定使用者的 BeTeacher 並加載相關的 ContactStudents
        $beteacherIn_progress = BeTeacher::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->with('contactTeacher.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 BeTeacher 並加載相關的 ContactStudents
        $beteacherCompleted = BeTeacher::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('contactTeacher.user', 'subject', 'city')
            ->get();
        // 查找指定使用者的 BeTeacher 並加載相關的 ContactStudents
        $beteacherCancelled = BeTeacher::where('user_id', $userId)
            ->where('status', 'cancelled')
            ->with('contactTeacher.user', 'subject', 'city')
            ->get();
            

        return response()->json(['beteacher' => $beteacher, 'beteacherIn_progress' => $beteacherIn_progress, 'beteacherCancelled' => $beteacherCancelled, 'beteacherCompleted' => $beteacherCompleted, 'teacherRequests' => $teacherRequests, 'teacherRequestsIn_progress' => $teacherRequestsIn_progress, 'teacherRequestsCancelled' => $teacherRequestsCancelled, 'teacherRequestsCompleted' => $teacherRequestsCompleted, 'beteacher' => $beteacher, 'beteacherIn_progress' => $beteacherIn_progress, 'beteacherCancelled' => $beteacherCancelled, 'beteacherCompleted' => $beteacherCompleted]);
    }

    public function remove(Request $request)
    {
        $userId = $request->input('user_id');
        $teacherRequestId = $request->input('teacher_request_id');

        // 刪除 ContactStudent 資料
        ContactTeacher::where('user_id', $userId)
            ->where('be_teacher_id', $teacherRequestId)
            ->delete();

        return response()->json(['success' => true]);
    }
    public function keepSelected(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'beTeacher_Id' => 'required|exists:be_teachers,id',
        ]);

        $beTeacherId = $request->beTeacher_Id;
        $userId = $request->user_id;

        // 获取所有与该 TeacherRequest 相关的学生联系记录
        $contactTeachers = ContactTeacher::where('be_teacher_id', $beTeacherId)->get();

        foreach ($contactTeachers as $contactTeacher) {
            // 如果学生ID不等于选中的学生ID，则删除该记录
            if ($contactTeacher->user_id != $userId) {
                $contactTeacher->delete();
            }
        }

        return response()->json(['message' => 'Only the selected teacher was kept']);
    }
    public function updateStatus(Request $request)
    {
        \Log::info('Request Data: ', $request->all());


        // 驗證請求
        $request->validate([
            'id' => 'required|exists:be_teachers,id',
            'status' => 'required|in:completed,cancelled',
        ]);

        // 查找並更新 TeacherRequest
        $teacherRequest = BeTeacher::findOrFail($request->id);
        $teacherRequest->status = $request->status;
        $teacherRequest->save();

        // 返回更新後的數據作為響應
        return response()->json([
            'title' => $teacherRequest->title,
            'status' => $teacherRequest->status,
        ]);
    }

    
    public function store(Request $request)
    {
        $userId = Auth::id(); // 获取已登录用户的 ID
        $teacherId = $request->input('teacher_id'); // 从请求中获取教师 ID

        // 验证请求数据
        $validated = $request->validate([
            'teacher_id' => 'required|exists:be_teachers,id', // 确保教师 ID 存在
        ]);

        // 检查是否已经存在联系记录
        $existingContact = ContactTeacher::where('user_id', $userId)
            ->where('be_teacher_id', $teacherId)
            ->first();

        if ($existingContact) {
            return response()->json(['success' => false, 'message' => '您已經聯繫過這位老師了。'], 400);
        }

        // 创建新的联系记录
        $contact = ContactTeacher::create([
            'user_id' => $userId,
            'be_teacher_id' => $teacherId,
        ]);

        // 返回 JSON 响应
        return response()->json(['success' => true, 'message' => '聯繫請求已記錄。'], 201);
    }
    

}
