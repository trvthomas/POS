# TRV Solutions POS System 🎉
## About the Project
Hey there! 👋 Meet TRV Solutions POS, a project I started back in 2018 when I was just a 14-year-old curious coder. It’s a **complete point-of-sale system** built with simplicity in mind using plain PHP, HTML, CSS (Bulma.io), and JavaScript.

This POS system was made for **small businesses** that need something free-to-start, offline, reliable, and hassle-free. No internet? No problem. No fancy cloud servers or website? You don’t need one. Just a computer, and you’re good to go!

For Spanish speakers (and to avoid translating the whole repo), check out the [Spanish version here](https://github.com/trvthomas/POS-es).

## Features 🌟
### General
- Works completely offline—no cloud, no subscription fees.
- Easy setup with XAMPP for a permanent, reliable solution.
- Simple, intuitive interface tailored for small businesses and non-tech-savvy users.
- Lightweight and efficient—designed to get the job done.
>![Mockup 1](https://github.com/user-attachments/assets/21f1180f-61a3-4725-8df8-adfdc89b2342)

### Functional
- User permissions (seller, inventory staff, administrator)
- Advanced inventory control
    - Receive stock
    - Remove stock
    - Inventory adjustment/count
    - Changes history
- Unlimited products and categories
- Custom payment method
- Automatic printing
- Detailed sales statistics
- Administrator dashboard
    - Daily & monthly statistics
        - Sales stats
        - User stats
        - Products stats
        - Coupons stats
    - Barcode generator
    - Discount coupons
    - Custom receipts design
    - Bulk product import/edition
    - Daily and monthly email reports[^1]
    - Low stock email reports[^1]
    - Gift/exchange tickets
    - Discount limits
- Many more!
>![Mockup 2](https://github.com/user-attachments/assets/f7b7d59c-1166-4fc2-b318-c3d44cafbc3c)

## History 📖
This project holds a special place in my journey as a developer. It started as a way to learn and experiment, and over the years, it became something much bigger.

From 2018 to 2023, I dedicated a lot of time and effort to this project, constantly improving it until I decided to shift my focus to new adventures. While I’m no longer updating it, you’re more than welcome to use it, fork it, improve it, and keep it alive.

![Mockup 3](https://github.com/user-attachments/assets/ce089eab-4709-4c75-8842-f3bfc9c97cdf)

## Installation & Setup ⚙️
Getting started is super simple! Here are two ways to run TRV Solutions POS:

### 1️⃣ Permanent Setup with XAMPP:
1. Download and install [XAMPP](https://www.apachefriends.org/download.html).
2. Place the **/trv** folder inside the **htdocs** directory created by XAMPP (the full path should look like this: **/xampp/htdocs/trv/**).
3. Start XAMPP and access the POS at **[http://localhost/trv](http://localhost/trv)** in your browser.
> [!TIP]
> You can set Apache and MySQL to start automatically with your computer. Open XAMPP's Control Panel, and in the Service column, check the box next to each module (run XAMPP as administrator first).

### 2️⃣ Quick Test with VS Code’s PHP Server Extension:
1. Install the (PHP Server)[https://marketplace.visualstudio.com/items?itemName=brapifra.phpserver] extension in VS Code.
2. Open the **/trv** folder and start the PHP server.
3. Access the system via the provided localhost link.

### Setup
The system will guide you through the setup steps the first time you open it.

### Emails and Timezone
To enable email functionality, you’ll need to configure the email server settings. This can be done by editing the **DBData.php** file (located inside the **include** folder) and updating the constants for your email server.
Additionally, you can set the correct timezone in the same file to ensure accurate timestamps for transactions and reports.

> [!NOTE]
> While TRV Solutions POS has been extensively tested on Windows systems, there might be slight errors or edge cases. Additionally, it has not been tested on other operating systems, so compatibility may vary.

## Contributing 💡
I’m no longer actively maintaining this project, but I’d love to see what others can do with it! Feel free to:
- Fork this project and make it your own.
- Submit pull requests with improvements or bug fixes.
- Share your thoughts, ideas, or use cases in the Issues tab.

## Want More Fun? 🛠️
Here are a few challenges you can try with TRV Solutions POS System:
- Add a dark mode to the UI (*PS* Bulma now released its 1.x version with automatic dark mode support).
- Create additional features like customer database or gift cards.
- Migrate it to a modern framework for a fun tech refresh.

[^1]: Requires your own mail server
Images disclaimer: You might be thinking, *"Wait, didn’t they say this hasn’t been tested on anything other than Windows? So why are there Mac mockups in the images?"* Well, the truth is, I just couldn’t find decent Windows mockups. So here we are—enjoy the aesthetic! 😄