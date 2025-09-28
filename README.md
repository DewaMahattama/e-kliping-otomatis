# 🚀 Aplikasi Laravel - [Kliping Berta Otomatis]

[![Laravel](https://img.shields.io/badge/Laravel-Framework-red)](https://laravel.com/)
[![HuggingFace](https://img.shields.io/badge/HuggingFace-IndoBERT-yellow)](https://huggingface.co/)
[![Python](https://img.shields.io/badge/Python-3.9-blue)](https://www.python.org/)

## 🧠 Model

Model yang digunakan adalah IndoBERT yang telah di-fine-tune untuk **analisis sentimen berita**.  
Model ini dapat diunduh langsung dari **Hugging Face Hub**.  

---

## 📚 Library & Framework
 
- [Python](https://www.python.org/)  
- [Transformers (Hugging Face)](https://huggingface.co/transformers/)  
- [Torch](https://pytorch.org/)  
- [Pandas, NumPy, scikit-learn, matplotlib](https://scikit-learn.org/stable/)  

---

## ⚙️ Cara Menjalankan Sistem (Lokal)

1. **Clone repo ini:**
   ```bash
   git clone https://github.com/DewaMahattama/sentiment-api
   cd sentiment-api
2. **Install Depedensi Laravel:** 
   ```bash
    composer install
3. **Install Dependency Frontend (Vite/NPM)**
   ```bash
   npm install
   npm run dev
2. **Copy dan Konfigurasi File Enviorment:**
   ```bash
   cp .env.example .env
   Lalu edit file .env dan sesuaikan konfigurasi database dan lainnya:
4. **Generate Key Aplikasi:**
   ```bash
   php artisan key:generate
3. **Jalankan Server:**
   ```bash
   php artisan serve

