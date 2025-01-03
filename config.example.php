<?php
/**
 * Jarchi Bot Configuration File
 * 
 * Rename this file to config.php and update the values
 * Keep config.php in .gitignore to protect sensitive data
 */

// Bot Configuration
define('BOT_TOKEN', 'YOUR_BOT_TOKEN'); // Get this from @BotFather
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

// Storage Files
define('USERS_FILE', 'users.json');
define('PROJECTS_FILE', 'projects.json');

// Project Status Definitions
define('PROJECT_STATUS', [
    'active' => '🟢 Active',
    'in_progress' => '🟡 In Progress',
    'completed' => '🟦 Completed',
    'canceled' => '🔴 Canceled'
]);

// Skill Categories and Fields
define('FIELDS', [
    // Programming & Development
    'web_development' => '🌐 Web Development',
    'mobile_development' => '📱 Mobile Development',
    'wordpress' => '📰 WordPress Development',

    // Design
    'ui_design' => '🎨 UI/UX Design',
    'graphic_design' => '🖼️ Graphic Design',
    'logo_design' => '⭐ Logo Design',

    // Content & Writing
    'content_writing' => '✍️ Content Writing',
    'copywriting' => '📝 Copywriting',
    'translation' => '🌍 Translation',

    // Digital Marketing
    'social_media' => '📱 Social Media Management',
    'seo' => '🔍 SEO',
    'digital_marketing' => '📢 Digital Marketing',

    // Video & Animation
    'video_editing' => '🎥 Video Editing',
    'motion_graphics' => '🎬 Motion Graphics',
    'animation' => '🎮 Animation',

    // Audio & Music
    'voice_over' => '🎤 Voice Over',
    'podcast_production' => '🎧 Podcast Production',
    'sound_editing' => '🔊 Sound Editing',

    // Marketing & Sales
    'lead_generation' => '🎯 Lead Generation',
    'market_research' => '📊 Market Research',
    'email_marketing' => '📧 Email Marketing',

    // Business
    'virtual_assistant' => '👩‍💼 Virtual Assistant',
    'data_entry' => '⌨️ Data Entry',
    'accounting' => '💰 Accounting',

    // Technical
    'backend_development' => '⚙️ Backend Development',
    'frontend_development' => '💻 Frontend Development',
    'database' => '🗄️ Database Management',

    // Security & Networks
    'cyber_security' => '🔒 Cyber Security',
    'network_admin' => '🌐 Network Administration',
    'devops' => '⚡ DevOps'
]);

// Error Reporting (change in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
