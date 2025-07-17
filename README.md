# 📰 DeepSeek News Fetcher

## 📌 About
The **DeepSeek Politics News Fetcher** is a PHP script that automatically fetches **fresh and unique Politics news articles** from the DeepSeek API and stores them into a MySQL database (`gonews` table).  
It is designed for news websites or blogs that want to automate political news publishing.

---

## ✨ Features
✔ Fetches **3 unique political news articles** per request  
✔ Automatically **checks for duplicate titles** before inserting  
✔ **Dynamic slug generation** for SEO-friendly URLs  
✔ Uses **Bootstrap 5** for a clean, simple admin interface  
✔ Allows manual triggering via a button  
✔ Displays inserted vs skipped articles count  
✔ Ready to be integrated with **CRON jobs** for automation

---

## ⚙️ Adjustables
You can customize the following easily in the PHP file:

| Variable | Description | Example |
|----------|-------------|---------|
| `$category` | News category to insert into DB | `"Politics"` (change to `"Technology"`, `"Medicine"`, etc.) |
| `$featured_image` | Default image for articles | `"assets/card.jpeg"` |
| `$is_top` | Random or fixed flag for top news | `rand(0,1)` or `1` for always top |
| `$status` | Article status | `"published"` or `"draft"` |
| `max_tokens` | Length of generated news | `800` |
| `temperature` | Creativity level of AI | `0.7 – 1.0` (higher = more creative) |
| `3 unique articles` | Change number of requested articles | Change prompt to request 5 or 10 articles |

---

## 🚀 Usage
1. Clone this repository or copy the PHP file into your project.  
2. Update your database credentials inside the PHP script:
    ```php
    $conn = new mysqli("localhost", "username", "password", "database_name");
    ```
3. Replace with your **DeepSeek API Key**:
    ```php
    $api_key = "your-deepseek-api-key";
    ```
4. Open the page in a browser and click **"Fetch & Insert Politics News"**.  
5. New articles will be inserted into your `gonews` table.

---

## 📸 Screenshot (Optional)
_Add a screenshot of the admin panel here if you want._

---

## ⏳ Future Enhancements
- ✅ Automatic fetching via CRON jobs  
- ✅ Multi-category support (Politics, Medicine, Technology, etc.)  
- ✅ Automatic featured image generation  

---

## 📝 License
This project is open-source. Feel free to modify and integrate it into your news platform.

---

## 👤 Author
Developed by **GoHybrid Ltd** – Powered by Technology.
