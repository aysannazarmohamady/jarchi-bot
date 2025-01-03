<?php
// تنظیمات اولیه
define('BOT_TOKEN', '');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

// مسیر فایل‌های ذخیره‌سازی
$users_file = 'users.json';
$projects_file = 'projects.json';

// تعریف وضعیت‌های مختلف پروژه
define('PROJECT_STATUS', [
    'active' => '🟢 فعال',
    'in_progress' => '🟡 در حال انجام',
    'completed' => '🟦 تکمیل شده',
    'canceled' => '🔴 لغو شده'
]);

// تعریف فیلدهای تخصصی به صورت سه ستونه
define('FIELDS', [
    // برنامه‌نویسی و توسعه
    'web_development' => '🌐 برنامه‌نویسی وب',
    'mobile_development' => '📱 برنامه‌نویسی موبایل',
    'wordpress' => '📰 وردپرس',

    // طراحی
    'ui_design' => '🎨 طراحی UI/UX',
    'graphic_design' => '🖼️ طراحی گرافیک',
    'logo_design' => '⭐ طراحی لوگو',

    // محتوا و نوشتن
    'content_writing' => '✍️ تولید محتوا',
    'copywriting' => '📝 کپی‌رایتینگ',
    'translation' => '🌍 ترجمه',

    // دیجیتال مارکتینگ
    'social_media' => '📱 مدیریت شبکه‌های اجتماعی',
    'seo' => '🔍 سئو',
    'digital_marketing' => '📢 دیجیتال مارکتینگ',

    // ویدیو و انیمیشن
    'video_editing' => '🎥 تدوین ویدیو',
    'motion_graphics' => '🎬 موشن گرافیک',
    'animation' => '🎮 انیمیشن‌سازی',

    // صدا و موسیقی
    'voice_acting' => '🎤 گویندگی',
    'podcast_production' => '🎧 تولید پادکست',
    'sound_editing' => '🔊 تدوین صدا',

    // بازاریابی و فروش
    'lead_generation' => '🎯 جذب مشتری',
    'market_research' => '📊 تحقیقات بازار',
    'email_marketing' => '📧 ایمیل مارکتینگ',

    // مدیریت و کسب‌وکار
    'virtual_assistant' => '👩‍💼 دستیار مجازی',
    'data_entry' => '⌨️ ورود داده',
    'accounting' => '💰 حسابداری',

    // فنی و مهندسی
    'backend_development' => '⚙️ برنامه‌نویسی بک‌اند',
    'frontend_development' => '💻 برنامه‌نویسی فرانت‌اند',
    'database' => '🗄️ پایگاه داده',

    // امنیت و شبکه
    'cyber_security' => '🔒 امنیت سایبری',
    'network_admin' => '🌐 مدیریت شبکه',
    'devops' => '⚡ دواپس'
]);

// ایجاد فایل‌های مورد نیاز
foreach ([$users_file, $projects_file] as $file) {
    if (!file_exists($file)) {
        file_put_contents($file, '[]');
    }
}

// دریافت اطلاعات پیام
$update = json_decode(file_get_contents('php://input'), true);
$message = $update['message'] ?? null;

if ($message) {
    $chat_id = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $username = $message['from']['username'] ?? '';
    
    processMessage($chat_id, $text);
}

function processMessage($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        setUserState($chat_id, 'REGISTERED');
        return;
    }
    
    if ($text === '/start') {
        $user = getUserData($chat_id);
        if (!empty($user['registered'])) {
            showMainMenu($chat_id);
            return;
        }
        askName($chat_id);
        return;
    }

    $state = getUserState($chat_id);
    switch ($state) {
        case 'AWAITING_NAME':
            handleName($chat_id, $text);
            break;
            
        case 'AWAITING_ROLE':
            handleRole($chat_id, $text);
            break;
            
        case 'AWAITING_FREELANCER_FIELDS':
            handleFreelancerFields($chat_id, $text);
            break;
            
        case 'REGISTERED':
            if ($text === '📨 بگیر این پروژه رو') {
                $user = getUserData($chat_id);
                sendMessage($chat_id, "🤝 شما می‌توانید از طریق لینک بالا با کارفرما گفتگو کنید. موفق باشید!");
            } else {
                handleMainMenu($chat_id, $text);
            }
            break;
            
        case 'AWAITING_PROJECT_FIELD_EMPLOYER':
            handleEmployerProjectField($chat_id, $text);
            break;
            
        case 'AWAITING_PROJECT_TITLE_EMPLOYER':
            handleEmployerProjectTitle($chat_id, $text);
            break;
            
        case 'AWAITING_PROJECT_DESC_EMPLOYER':
            handleEmployerProjectDesc($chat_id, $text);
            break;
            
        case 'AWAITING_PROJECT_BUDGET':
            handleEmployerProjectBudget($chat_id, $text);
            break;
    }
}

function handleName($chat_id, $text) {
    global $message;
    saveUserData($chat_id, [
        'name' => $text,
        'username' => $message['from']['username'] ?? ''
    ]);
    askRole($chat_id, $text);
}

function handleRole($chat_id, $text) {
    if ($text === 'بازگشت به منو' || $text === '/start') {
        $user = getUserData($chat_id);
        if (!empty($user['registered'])) {
            showMainMenu($chat_id);
            return;
        }
    }

    $role = '';
    switch($text) {
        case '💼 پروژه‌بده هستم':
            $role = 'employer';
            saveUserData($chat_id, [
                'role' => $role,
                'registered' => true
            ]);
            showMainMenu($chat_id);
            setUserState($chat_id, 'REGISTERED');
            break;
            
        case '🛠️ پروژه‌بگیر هستم':
            $role = 'freelancer';
            saveUserData($chat_id, [
                'role' => $role,
                'registered' => true
            ]);
            askFreelancerFields($chat_id);
            break;
            
        case '👀 فقط تماشاچی هستم':
            $role = 'viewer';
            saveUserData($chat_id, [
                'role' => $role,
                'registered' => true
            ]);
            showMainMenu($chat_id);
            setUserState($chat_id, 'REGISTERED');
            break;
            
        default:
            askRole($chat_id, getUserData($chat_id)['name']);
            return;
    }
}

function askName($chat_id) {
    $welcome_text = "📢 سلام! من جارچی‌ام! خبر کار و پروژه رو به گوش اهلش می‌رسونم!\n\n";
    $welcome_text .= "🎯 اینجا:\n";
    $welcome_text .= "• صاحب‌کارها پروژه‌هاشون رو جار می‌زنن\n";
    $welcome_text .= "• متخصص‌ها پروژه‌های مورد علاقه‌شون رو می‌قاپن\n";
    $welcome_text .= "• و من این وسط حواسم هست که همه راضی باشن! 😊\n\n";
    $welcome_text .= "✨ اسمت چیه دوست من؟";
    
    sendMessage($chat_id, $welcome_text);
    setUserState($chat_id, 'AWAITING_NAME');
}

function askRole($chat_id, $name) {
    $message = "خوشحالم که با جارچی آشنا شدی {$name} عزیز! 😊\n\n";
    $message .= "لطفاً نقش خودت رو انتخاب کن:";
    
    $keyboard = [
        ['💼 پروژه‌بده هستم'],
        ['🛠️ پروژه‌بگیر هستم'],
        ['👀 فقط تماشاچی هستم']
    ];
    
    sendMessage($chat_id, $message, $keyboard);
    setUserState($chat_id, 'AWAITING_ROLE');
}

function askFreelancerFields($chat_id) {
    $message = "لطفاً حداقل یک و حداکثر سه زمینه تخصصی خودت رو انتخاب کن.\n";
    $message .= "هر بار یکی رو انتخاب کن و در نهایت 'تایید نهایی' رو بزن:";
    
    $keyboard = makeFieldsKeyboard();
    sendMessage($chat_id, $message, $keyboard);
    
    saveUserData($chat_id, ['selected_fields' => []]);
    setUserState($chat_id, 'AWAITING_FREELANCER_FIELDS');
}

function handleFreelancerFields($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        setUserState($chat_id, 'REGISTERED');
        return;
    }

    $user = getUserData($chat_id);
    $selected_fields = $user['selected_fields'] ?? [];
    
    if ($text === '✅ تایید نهایی') {
        if (count($selected_fields) >= 1 && count($selected_fields) <= 3) {
            saveUserData($chat_id, [
                'fields' => $selected_fields,
                'registered' => true
            ]);
            setUserState($chat_id, 'REGISTERED');
            showMainMenu($chat_id);
            return;
        } else {
            sendMessage($chat_id, "⚠️ لطفاً حداقل یک و حداکثر سه زمینه انتخاب کنید.");
            return;
        }
    }
    
    if (count($selected_fields) >= 3 && !in_array($text, $selected_fields)) {
        sendMessage($chat_id, "⚠️ شما قبلاً سه زمینه را انتخاب کرده‌اید. لطفاً 'تایید نهایی' را بزنید.");
        return;
    }
    
    foreach (FIELDS as $key => $value) {
        if ($text === $value) {
            if (!in_array($value, $selected_fields)) {
                $selected_fields[] = $value;
                saveUserData($chat_id, ['selected_fields' => $selected_fields]);
                
                $message = "✅ {$value} اضافه شد.\n\n";
                $message .= "زمینه‌های انتخاب شده:\n";
                foreach ($selected_fields as $field) {
                    $message .= "• {$field}\n";
                }
                $message .= "\nتعداد انتخاب شده: " . count($selected_fields) . "/3";
                
                sendMessage($chat_id, $message, makeFieldsKeyboard($selected_fields));
            } else {
                sendMessage($chat_id, "⚠️ این زمینه قبلاً انتخاب شده است.");
            }
            return;
        }
    }
}

function showMainMenu($chat_id) {
    $user = getUserData($chat_id);
    $message = "خوش اومدی {$user['name']} عزیز! 🌟\n\n";
    $message .= "چطور می‌تونم کمکت کنم؟";
    
    $keyboard = [
        ['ℹ️ درباره جارچی'],
        ['👤 پروفایل من'],
        ['📬 صندوق جارچی']
    ];
    
    if ($user['role'] === 'employer') {
        array_unshift($keyboard, 
            ['📝 ثبت پروژه جدید'],
            ['📋 پروژه‌های من']
        );
    } elseif ($user['role'] === 'freelancer') {
        array_unshift($keyboard, 
            ['🔍 مشاهده پروژه‌های مرتبط'],
            ['📋 پروژه‌های من']
        );
    }
    
    sendMessage($chat_id, $message, $keyboard);
    setUserState($chat_id, 'REGISTERED');
}

function handleMainMenu($chat_id, $text) {
    $user = getUserData($chat_id);
    
    // بررسی دکمه‌های مدیریت وضعیت پروژه
    if (strpos($text, '❌ لغو پروژه') === 0) {
        $project_title = str_replace('❌ لغو پروژه ', '', $text);
        $projects = getAllProjects();
        foreach ($projects as $project) {
            if ($project['employer_id'] == $chat_id && $project['title'] === $project_title) {
                updateProjectStatus($project['employer_id'], 'canceled');
                sendMessage($chat_id, "❌ پروژه '{$project_title}' لغو شد.");
                
                // اطلاع‌رسانی به فریلنسر اگر پروژه در حال انجام بود
                if ($project['freelancer_id']) {
                    sendMessage($project['freelancer_id'], 
                        "⚠️ پروژه '{$project_title}' توسط کارفرما لغو شد.");
                }
                
                showEmployerProjects($chat_id);
                return;
            }
        }
    } elseif (strpos($text, '✅ تکمیل پروژه') === 0) {
        $project_title = str_replace('✅ تکمیل پروژه ', '', $text);
        $projects = getAllProjects();
        foreach ($projects as $project) {
            if ($project['employer_id'] == $chat_id && $project['title'] === $project_title) {
                updateProjectStatus($project['employer_id'], 'completed');
                sendMessage($chat_id, "✅ پروژه '{$project_title}' با موفقیت تکمیل شد.");
                
                // اطلاع‌رسانی به فریلنسر
                if ($project['freelancer_id']) {
                    sendMessage($project['freelancer_id'], 
                        "🎉 تبریک! پروژه '{$project_title}' با موفقیت تکمیل شد.");
                }
                
                showEmployerProjects($chat_id);
                return;
            }
        }
    }
    
    // منوی اصلی
    switch ($text) {
        case '📝 ثبت پروژه جدید':
            if ($user['role'] === 'employer') {
                startNewProjectEmployer($chat_id);
            }
            break;
            
        case '📋 پروژه‌های من':
            if ($user['role'] === 'employer') {
                showEmployerProjects($chat_id);
            } elseif ($user['role'] === 'freelancer') {
                showFreelancerProjects($chat_id);
            }
            break;
            
        case '🔍 مشاهده پروژه‌های مرتبط':
            if ($user['role'] === 'freelancer') {
                showRelevantProjects($chat_id);
            }
            break;
            
        case 'ℹ️ درباره جارچی':
            showAbout($chat_id);
            break;
            
        case '👤 پروفایل من':
            showProfile($chat_id);
            break;
            
        case '📬 صندوق جارچی':
            showContactInfo($chat_id);
            break;
    }
}

function startNewProjectEmployer($chat_id) {
    $keyboard = array_map(function($field) {
        return [$field];
    }, array_values(FIELDS));
    array_unshift($keyboard, ['بازگشت به منو']);
    
    sendMessage($chat_id, "🏷️ لطفاً زمینه پروژه رو انتخاب کن:", $keyboard);
    setUserState($chat_id, 'AWAITING_PROJECT_FIELD_EMPLOYER');
}

function handleEmployerProjectField($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        return;
    }

    foreach (FIELDS as $key => $value) {
        if ($text === $value) {
            saveProjectData($chat_id, ['field' => $value]);
            sendMessage($chat_id, "📋 حالا عنوان پروژه رو وارد کن:", [['بازگشت به منو']]);
            setUserState($chat_id, 'AWAITING_PROJECT_TITLE_EMPLOYER');
            return;
        }
    }
    sendMessage($chat_id, "⚠️ لطفاً یک زمینه معتبر انتخاب کنید.");
}

function handleEmployerProjectTitle($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        return;
    }
    
    saveProjectData($chat_id, ['title' => $text]);
    sendMessage($chat_id, "✍️ حالا توضیحات پروژه رو بنویس:", [['بازگشت به منو']]);
    setUserState($chat_id, 'AWAITING_PROJECT_DESC_EMPLOYER');
}

function handleEmployerProjectDesc($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        return;
    }
    
    saveProjectData($chat_id, ['description' => $text]);
    sendMessage($chat_id, "💰 بودجه پروژه چقدره؟", [['بازگشت به منو']]);
    setUserState($chat_id, 'AWAITING_PROJECT_BUDGET');
}

function handleEmployerProjectBudget($chat_id, $text) {
    if ($text === 'بازگشت به منو') {
        showMainMenu($chat_id);
        return;
    }
    
    $project_data = getProjectData($chat_id);
    $project_data['budget'] = $text;
    $project_data['employer_id'] = $chat_id;
    $project_data['employer_username'] = getUserData($chat_id)['username'] ?? '';
    $project_data['status'] = 'active';
    $project_data['created_at'] = date('Y-m-d H:i:s');
    $project_data['freelancer_id'] = null;
    $project_data['accepted_at'] = null;
    $project_data['completed_at'] = null;
    
    saveProject($project_data);
    
    $summary = "✅ پروژه با موفقیت ثبت شد!\n\n";
    $summary .= "📌 عنوان: {$project_data['title']}\n";
    $summary .= "📝 توضیحات: {$project_data['description']}\n";
    $summary .= "🏷️ زمینه: {$project_data['field']}\n";
    $summary .= "💰 بودجه: {$project_data['budget']}";
    
    sendMessage($chat_id, $summary);
    showMainMenu($chat_id);
    setUserState($chat_id, 'REGISTERED');
    clearProjectData($chat_id);
}

function showEmployerProjects($chat_id) {
    $all_projects = getAllProjects();
    $employer_projects = array_filter($all_projects, function($project) use ($chat_id) {
        return $project['employer_id'] == $chat_id;
    });
    
    if (empty($employer_projects)) {
        $message = "😕 شما هنوز پروژه‌ای ثبت نکرده‌اید!\n";
        $message .= "برای ثبت پروژه جدید می‌توانید از گزینه 'ثبت پروژه جدید' در منوی اصلی استفاده کنید.";
        sendMessage($chat_id, $message, [['بازگشت به منو']]);
        return;
    }
    
    usort($employer_projects, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    foreach ($employer_projects as $project) {
        $message = "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️\n\n";
        $message .= "🔸 عنوان: {$project['title']}\n";
        $message .= "📝 توضیحات: {$project['description']}\n";
        $message .= "🏷️ زمینه: {$project['field']}\n";
        $message .= "💰 بودجه: {$project['budget']}\n";
        $message .= "📅 تاریخ ثبت: " . formatDate($project['created_at']) . "\n";
        $message .= "🔄 وضعیت: " . PROJECT_STATUS[$project['status']] . "\n";
        
        if ($project['freelancer_id']) {
            $freelancer = getUserData($project['freelancer_id']);
            $message .= "👨‍💻 فریلنسر: " . ($freelancer['name'] ?? 'ناشناس') . "\n";
            if (!empty($freelancer['username'])) {
                $message .= "🔗 لینک فریلنسر: @" . $freelancer['username'] . "\n";
            }
        }
        
        $keyboard = [['بازگشت به منو']];
        if ($project['status'] === 'active') {
            $keyboard[] = ["❌ لغو پروژه {$project['title']}"];
        } elseif ($project['status'] === 'in_progress') {
            $keyboard[] = ["✅ تکمیل پروژه {$project['title']}"];
            $keyboard[] = ["❌ لغو پروژه {$project['title']}"];
        }
        
        sendMessage($chat_id, $message, $keyboard);
    }
}

function showFreelancerProjects($chat_id) {
    $all_projects = getAllProjects();
    $freelancer_projects = array_filter($all_projects, function($project) use ($chat_id) {
        return $project['freelancer_id'] == $chat_id;
    });
    
    if (empty($freelancer_projects)) {
        $message = "😕 شما هنوز پروژه‌ای نگرفته‌اید!\n";
        $message .= "می‌توانید از بخش 'مشاهده پروژه‌های مرتبط' پروژه‌های متناسب با تخصص خود را ببینید.";
        sendMessage($chat_id, $message, [['بازگشت به منو']]);
        return;
    }
    
    usort($freelancer_projects, function($a, $b) {
        return strtotime($b['accepted_at']) - strtotime($a['accepted_at']);
    });
    
    foreach ($freelancer_projects as $project) {
        $message = "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️\n\n";
        $message .= "🔸 عنوان: {$project['title']}\n";
        $message .= "📝 توضیحات: {$project['description']}\n";
        $message .= "🏷️ زمینه: {$project['field']}\n";
        $message .= "💰 بودجه: {$project['budget']}\n";
        $message .= "📅 تاریخ گرفتن پروژه: " . formatDate($project['accepted_at']) . "\n";
        $message .= "🔄 وضعیت: " . PROJECT_STATUS[$project['status']] . "\n";
        
        if (!empty($project['employer_username'])) {
            $message .= "👤 کارفرما: @" . $project['employer_username'] . "\n";
        }
        
        sendMessage($chat_id, $message, [['بازگشت به منو']]);
    }
}

function showRelevantProjects($chat_id) {
    $user = getUserData($chat_id);
    
    if (empty($user['fields'])) {
        $message = "⚠️ شما هنوز تخصصی انتخاب نکرده‌اید!\n";
        $message .= "لطفا به منوی اصلی برگردید و از بخش پروفایل، تخصص‌های خود را مشخص کنید.";
        sendMessage($chat_id, $message, [['بازگشت به منو']]);
        return;
    }

    $all_projects = getAllProjects();
    
    if (empty($all_projects)) {
        sendMessage($chat_id, "😕 در حال حاضر هیچ پروژه‌ای ثبت نشده است.", [['بازگشت به منو']]);
        return;
    }

    $relevant_projects = array_filter($all_projects, function($project) use ($user) {
        return in_array($project['field'], $user['fields']) && 
               $project['status'] === 'active' &&
               $project['freelancer_id'] === null;
    });

    if (empty($relevant_projects)) {
        $message = "😕 فعلاً پروژه‌ای در زمینه‌های تخصصی شما وجود نداره.\n\n";
        $message .= "💡 تخصص‌های شما:\n";
        foreach ($user['fields'] as $field) {
            $message .= "• {$field}\n";
        }
        sendMessage($chat_id, $message, [['بازگشت به منو']]);
        return;
    }

    foreach ($relevant_projects as $project) {
        $project_text = "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️\n\n";
        $project_text .= "🔸 عنوان: {$project['title']}\n";
        $project_text .= "📝 توضیحات: {$project['description']}\n";
        $project_text .= "🏷️ زمینه: {$project['field']}\n";
        $project_text .= "💰 بودجه: {$project['budget']}\n";
        $project_text .= "📅 تاریخ: " . formatDate($project['created_at']) . "\n\n";

        $keyboard = [['بازگشت به منو']];
        
        if (!empty($project['employer_username'])) {
            $keyboard[] = ['📨 بگیر این پروژه رو'];
            $project_text .= "برای گرفتن پروژه روی دکمه زیر کلیک کنید 👇";
        }

        sendMessage($chat_id, $project_text, $keyboard);
        
        if (!empty($project['employer_username'])) {
            $chat_link = "https://t.me/{$project['employer_username']}";
            sendMessage($chat_id, "🔗 لینک گفتگو با کارفرما:\n{$chat_link}");
        }
    }
}

function showProfile($chat_id) {
    $user = getUserData($chat_id);
    
    $message = "👤 پروفایل شما:\n\n";
    $message .= "👋 نام: {$user['name']}\n";
    
    switch ($user['role']) {
        case 'employer':
            $message .= "💼 نقش: کارفرما\n";
            $employer_projects = array_filter(getAllProjects(), function($project) use ($chat_id) {
                return $project['employer_id'] == $chat_id;
            });
            $message .= "📊 تعداد پروژه‌های ثبت شده: " . count($employer_projects) . "\n";
            break;
            
        case 'freelancer':
            $message .= "🛠️ نقش: فریلنسر\n";
            $freelancer_projects = array_filter(getAllProjects(), function($project) use ($chat_id) {
                return $project['freelancer_id'] == $chat_id;
            });
            $message .= "📊 تعداد پروژه‌های گرفته شده: " . count($freelancer_projects) . "\n\n";
            $message .= "📚 تخصص‌های شما:\n";
            if (!empty($user['fields'])) {
                foreach ($user['fields'] as $field) {
                    $message .= "• {$field}\n";
                }
            }
            break;
            
        case 'viewer':
            $message .= "👀 نقش: تماشاچی\n";
            break;
    }
    
    $keyboard = [
        ['✏️ ویرایش نام'],
        ['بازگشت به منو']
    ];
    
    if ($user['role'] === 'freelancer') {
        array_unshift($keyboard, ['📚 ویرایش تخصص‌ها']);
    }
    
    sendMessage($chat_id, $message, $keyboard);
}

function showAbout($chat_id) {
    $about_text = "📢 جارچی چیه؟\n\n";
    $about_text .= "🤝 جارچی یه پل ارتباطی بین پروژه‌بده‌ها و پروژه‌بگیرهاست.\n\n";
    $about_text .= "✨ مزیت‌های جارچی:\n";
    $about_text .= "• کاملاً رایگان\n";
    $about_text .= "• بدون واسطه\n";
    $about_text .= "• شخصی‌سازی شده\n";
    $about_text .= "• تعامل دو طرفه\n";
    $about_text .= "• دسته‌بندی هوشمند پروژه‌ها\n\n";
    $about_text .= "به جارچی بسپارش! 📣";

    sendMessage($chat_id, $about_text, [['بازگشت به منو']]);
}

function showContactInfo($chat_id) {
    $message = "📬 صندوق جارچی\n\n";
    $message .= "با جارچی در ارتباط باشید!\n\n";
    $message .= "✨ برای:\n";
    $message .= "• گزارش مشکلات\n";
    $message .= "• پیشنهادات\n";
    $message .= "• انتقادات\n";
    $message .= "• همکاری\n\n";
    $message .= "🔗 می‌تونید با من در ارتباط باشید:\n";
    $message .= "@Aysan_dev\n\n";
    $message .= "💌 خوشحال میشم نظراتتون رو بشنوم!";
    
    sendMessage($chat_id, $message, [['بازگشت به منو']]);
}

function makeFieldsKeyboard($selected_fields = []) {
    $keyboard = [
        ['بازگشت به منو']
    ];
    
    foreach (FIELDS as $key => $value) {
        if (!in_array($value, $selected_fields)) {
            $keyboard[] = [$value];
        }
    }
    
    if (count($selected_fields) >= 1) {
        $keyboard[] = ['✅ تایید نهایی'];
    }
    
    return $keyboard;
}

function formatDate($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return "چند لحظه پیش";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " دقیقه پیش";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " ساعت پیش";
    } else {
        $days = floor($diff / 86400);
        return $days . " روز پیش";
    }
}

function updateProjectStatus($project_id, $new_status) {
    $projects = getAllProjects();
    foreach ($projects as &$project) {
        if ($project['employer_id'] == $project_id) {
            $project['status'] = $new_status;
            if ($new_status === 'completed') {
                $project['completed_at'] = date('Y-m-d H:i:s');
            }
            file_put_contents('projects.json', json_encode($projects));
            return true;
        }
    }
    return false;
}

function acceptProject($chat_id, $project_id) {
    $projects = getAllProjects();
    foreach ($projects as &$project) {
        if ($project['employer_id'] == $project_id) {
            if ($project['status'] === 'active' && $project['freelancer_id'] === null) {
                $project['freelancer_id'] = $chat_id;
                $project['accepted_at'] = date('Y-m-d H:i:s');
                $project['status'] = 'in_progress';
                file_put_contents('projects.json', json_encode($projects));
                
                // اطلاع‌رسانی به کارفرما
                $employer_message = "🎉 پروژه '{$project['title']}' توسط یک فریلنسر پذیرفته شد!\n\n";
                $freelancer = getUserData($chat_id);
                $employer_message .= "👨‍💻 نام فریلنسر: {$freelancer['name']}\n";
                if (!empty($freelancer['username'])) {
                    $employer_message .= "🔗 لینک فریلنسر: @{$freelancer['username']}\n";
                }
                sendMessage($project['employer_id'], $employer_message);
                
                return true;
            }
        }
    }
    return false;
}

// توابع کمکی
function sendMessage($chat_id, $text, $keyboard = null) {
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($keyboard !== null) {
        $data['reply_markup'] = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true
        ]);
    }
    
    file_get_contents(API_URL . 'sendMessage?' . http_build_query($data));
}

function setUserState($chat_id, $state) {
    $data = getUserData($chat_id);
    $data['state'] = $state;
    saveUserData($chat_id, $data);
}

function getUserState($chat_id) {
    $data = getUserData($chat_id);
    return $data['state'] ?? 'AWAITING_NAME';
}

function saveUserData($chat_id, $new_data) {
    global $users_file;
    $users = json_decode(file_get_contents($users_file), true);
    $users[$chat_id] = array_merge($users[$chat_id] ?? [], $new_data);
    file_put_contents($users_file, json_encode($users));
}

function getUserData($chat_id) {
    global $users_file;
    $users = json_decode(file_get_contents($users_file), true);
    return $users[$chat_id] ?? [];
}

function saveProjectData($chat_id, $data) {
    $temp_file = "project_temp_{$chat_id}.json";
    $current = file_exists($temp_file) ? json_decode(file_get_contents($temp_file), true) : [];
    $updated = array_merge($current, $data);
    file_put_contents($temp_file, json_encode($updated));
}

function getProjectData($chat_id) {
    $temp_file = "project_temp_{$chat_id}.json";
    return json_decode(file_get_contents($temp_file), true);
}

function clearProjectData($chat_id) {
    $temp_file = "project_temp_{$chat_id}.json";
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
}

function saveProject($project_data) {
    global $projects_file;
    $projects = json_decode(file_get_contents($projects_file), true);
    $projects[] = $project_data;
    file_put_contents($projects_file, json_encode($projects));
}

function getAllProjects() {
    global $projects_file;
    return json_decode(file_get_contents($projects_file), true) ?? [];
}
