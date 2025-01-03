# jarchi-bot
# 🗣️ Jarchi - Freelance Project Management Telegram Bot

Jarchi (meaning "Town Crier" in Persian) is a Telegram bot that serves as a bridge between employers and freelancers, facilitating:
- Project posting and management by employers
- Project discovery and acceptance by freelancers
- Project status tracking
- Direct communication between parties

## ⚙️ Installation

1. Clone the repository:
```bash
git clone https://github.com/YOUR_USERNAME/jarchi-bot.git
cd jarchi-bot
```

2. Configure the `config.php` file with your bot information:
```php
define('BOT_TOKEN', 'YOUR_BOT_TOKEN');
```

3. Create necessary data files:
```bash
touch users.json
touch projects.json
```

4. Set up domain and webhook:
```
https://api.telegram.org/bot<BOT_TOKEN>/setWebhook?url=https://your-domain.com/bot.php
```

## 📂 Project Structure

```
jarchi-bot/
├── bot.php           # Main bot file
├── config.php        # Bot configuration
├── users.json        # Users database
├── projects.json     # Projects database
└── README.md         # Documentation
```

## 🌟 Features

- 🔄 Simple and intuitive user interface
- 👥 User registration with role differentiation
- 📝 Project management capabilities
- 🏷️ Specialized project categorization
- 📊 Statistics and reporting
- 🔔 Automated notifications

### User Roles
- 👨‍💼 Employers: Can post and manage projects
- 👨‍💻 Freelancers: Can view and accept relevant projects
- 👀 Viewers: Can browse available projects

### Project Categories
- 🌐 Web Development
- 📱 Mobile Development
- 💻 Software Development
- 🎨 UI/UX Design
- ✍️ Content Writing
- 🎥 Video Editing
- And many more...

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/something-new`)
3. Commit your changes (`git commit -am 'Add something new'`)
4. Push to the branch (`git push origin feature/something-new`)
5. Create a Pull Request

## 🐞 Bug Reports

Found a bug? Please open an issue and include:
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)


## 👥 Contact

For issues, suggestions, or contributions:
- Create an issue in this repository
- Contact us on Telegram: [@Aysan_dev](https://t.me/Aysan_dev)

## 🌟 Acknowledgments

- All contributors who help improve this project
- The Telegram Bot API for making this possible
- The freelance community for their feedback and support

---

Made with ❤️ for the freelance community
