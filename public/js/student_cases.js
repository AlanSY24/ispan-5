document.addEventListener('DOMContentLoaded', function() {
    var hearts = document.querySelectorAll('.heart-icon');

    hearts.forEach(function(heartIcon) {
        var isFavorite = heartIcon.classList.contains('fas');
        var teacherRequestId  = heartIcon.getAttribute('data-teacher-id');

        // 加载收藏状态
        checkFavoriteStatus(teacherRequestId, function(isFavorited) {
            if (isFavorited) {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
            }
            heartIcon.style.color = '#ed1212'; // 红色
        });

        heartIcon.addEventListener('click', function() {
            var isFavorite = heartIcon.classList.contains('fas');
            console.log('Click detected on teacher ID:', teacherRequestId);
            
            if (!isFavorite) {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
                toggleFavorite(teacherRequestId, 'add');
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
                toggleFavorite(teacherRequestId, 'remove');
            }
            heartIcon.style.color = '#ed1212'; // 红色
        });
    });
});

function checkFavoriteStatus(teacherRequestId, callback) {
    $.ajax({
        url: '/LearnLink/public/student_cases/favorites/status/' + teacherRequestId,
        method: 'GET',
        success: function(response) {
            callback(response.isFavorited);
        },
        error: function(xhr) {
            console.log('Error:', 'Failed to fetch favorite status');
            console.log('XHR:', xhr);
        }
    });
}

function toggleFavorite(teacherRequestId, action) {
    var url = action === 'add'
        ? '/LearnLink/public/student_cases/favorites/' + teacherRequestId
        : '/LearnLink/public/student_cases/favorites/' + teacherRequestId;

    var method = action === 'add' ? 'POST' : 'DELETE';

    console.log('URL:', url);
    console.log('Method:', method);
    console.log('User ID:', getUserId());

    $.ajax({
        url: url,
        method: method,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            user_id: getUserId()  // Optional: Send user ID if needed
        },
        success: function(response) {
            console.log('Success:', action === 'add' ? 'Added to favorites' : 'Removed from favorites');
            console.log('Response:', response);
        },
        error: function(xhr) {
            console.log('Error:', action === 'add' ? 'Failed to add favorite' : 'Failed to remove favorite');
            console.log('XHR:', xhr);
        }
    });
}


function getUserId() {
    return document.querySelector('meta[name="user-id"]').getAttribute('content');
}


$(document).ready(function() {
    // 獲取城市列表
    $.ajax({
        url: '/LearnLink/public/cities',
        type: 'GET',
        success: function(data) {
            $('#city').empty();
            $('#city').append('<option value="">請選擇縣市</option>');
            $.each(data, function(index, city) {
                $('#city').append('<option value="' + city.id + '">' + city.city + '</option>');
            });
        }
    });

    // 當選擇縣市後，獲取對應的區域列表
    $('#city').change(function() {
        var selectedCityId = $(this).val();
        if (selectedCityId) {
            $.ajax({
                url: '/LearnLink/public/districts/' + selectedCityId,
                type: 'GET',
                success: function(data) {
                    $('#districts').empty();
                    $.each(data, function(index, district) {
                        $('#districts').append(
                            '<label><input type="checkbox" name="districts[]" value="' + district.id + '"> ' + district.district_name + '</label><br>'
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Districts AJAX Error:', error);
                }
            });
        } else {
            $('#districts').empty(); // 如果未選擇縣市，清空區域選擇
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchButtons').addEventListener('click', async function() {
        const subject = document.getElementById('subject').value;
        const city = document.getElementById('city').value;
        const selectedDistricts = Array.from(document.querySelectorAll('#districts input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);
        const minBudget = document.querySelector('.min-input').value;
        const maxBudget = document.querySelector('.max-input').value;
        const selectedTimes = Array.from(document.querySelectorAll('.t_search_time input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);

        if ((minBudget && minBudget < 100) || (maxBudget && maxBudget > 100000) || (minBudget && maxBudget && minBudget >= maxBudget)) {
            alert('請檢查預算輸入是否正確');
            return;
        }


        const queryParams = new URLSearchParams();

        if (subject && subject !== '0') {
            queryParams.append('subject', subject);
        }
        if (city) {
            queryParams.append('city', city);
        }
        if (selectedDistricts.length > 0) {
            queryParams.append('districts', selectedDistricts.join(','));
        }
        if (minBudget) {
            queryParams.append('minBudget', minBudget);
        }
        if (maxBudget) {
            queryParams.append('maxBudget', maxBudget);
        }
        if (selectedTimes.length > 0) {
            queryParams.append('time', selectedTimes.join(','));
        }

        try {
            const response = await fetch(`http://localhost/LearnLink/public/student_cases?${queryParams.toString()}`);
            
            if (response.status === 404) {
                const result = await response.json();
                alert(result.message); // Display the specific message from the server
            } else {
                // Redirect to the results page if data is found
                window.location.href = `http://localhost/LearnLink/public/student_cases?${queryParams.toString()}`;
            }
        } catch (error) {
            console.error('Error during fetch:', error);
            alert('發生錯誤，請稍後再試');
        }
    });


    
   // 选择所有的“联系”按钮
    document.querySelectorAll('.contact-button').forEach(function(button) {
        button.addEventListener('click', function() {
            // 获取按钮的 data 属性
            var teacherRequestId = this.getAttribute('data-student-id');
            var userId = document.querySelector('meta[name="user-id"]').getAttribute('content');
            var studentName = this.getAttribute('data-name'); // 获取 data-name

            // 日志输出调试信息
            console.log('Teacher Request ID:', teacherRequestId);
            console.log('User ID:', userId);
            console.log('Student Name:', studentName);

            // 检查 ID 是否有效
            if (!teacherRequestId || !userId) {
                console.error('Teacher Request ID 或 User ID 未定义。');
                alert('無法獲取學生ID或用戶ID，請確認是否已登入，或是重新嘗試謝謝。');
                return;
            }

            // 发起 AJAX 请求
            $.ajax({
                url: '/LearnLink/public/contact_student', // 确保 URL 匹配 Laravel 路由
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    teacher_requests_id: teacherRequestId, // 发送 teacher_requests_id
                    user_id: userId,
                },
                success: function(response) {
                    console.log('Response:', response); // 输出响应用于调试
                    if (response.message === '聯繫成功') {
                        alert(`已聯絡 ${studentName} 學生，請耐心等候回復，謝謝`);
                    } else {
                        alert(response.message); // 显示服务器返回的错误消息
                    }
                },
                error: function(xhr) {
                    // 获取并显示服务器返回的错误消息
                    var errorMessage = '聯絡時發生錯誤，請稍後再試';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                    }
                    console.log('Error:', xhr);
                    alert(errorMessage);
                }
            });
        });
    });



});