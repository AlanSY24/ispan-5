<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學生自我介紹</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input[type="file"] {
            padding: 0.5rem;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="file"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert-success {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .profile-info {
            margin-bottom: 1rem;
        }

        .profile-info img {
            max-width: 200px;
            border-radius: 4px;
            display: block;
            margin-bottom: 1rem;
        }

        .profile-info a {
            display: inline-block;
            margin-top: 0.5rem;
            color: #007bff;
            text-decoration: none;
        }

        .profile-info a:hover {
            text-decoration: underline;
        }
        #ifr {
            display: none; /* 初始隱藏 iframe */
        }
    </style>
</head>
<body>
    <div class="container-SP">
        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h1>學生自我介紹與學歷</h1>

        <form action="{{ route('studentprofile.store') }}" method="POST">
            @csrf

            <div class="form-group-SP">
                <label for="education">學歷</label><br>
                <textarea id="education" name="education" rows="4" required>{{ old('education', $profile->education ?? '') }}</textarea>
            </div>

            <div class="form-group-SP">
                <label for="introduction">自我介紹</label><br>
                <textarea id="introduction" name="introduction" rows="4" required>{{ old('introduction', $profile->introduction ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn-SP">儲存</button>
        </form>
    </div>
</body>
</html>