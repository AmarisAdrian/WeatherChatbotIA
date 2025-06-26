# Weather Chatbot IA

## Descripción del Proyecto

Chatbot inteligente que consulta información meteorológica y responde a preguntas sobre el clima utilizando IA. El proyecto está dockerizado y separado en:

- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js
- **Base de datos**: MySQL

## Requisitos Previos

- Docker
- Docker Compose
- Git

## Instalación

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/AmarisAdrian/WeatherChatbotIA.git
   cd WeatherChatbotIA
2. Configurar variables de entorno en el entorno de docker 
  Archivo 1: .env (raíz del proyecto)
    # Configuración MySQL
    - MYSQL_ROOT_PASSWORD=root
    - MYSQL_DATABASE=weather_chatbot
    - MYSQL_USER=chatbot_user
    - MYSQL_PASSWORD=root
    
    # Configuración Laravel
    - DB_HOST=mysql
    - DB_PORT=3306
    - DB_DATABASE=weather_chatbot
    - DB_USERNAME=chatbot_user
    - DB_PASSWORD=root
   
    Archivo 2: backend/.env (Copiar de backend/.env.example y modificar):
      - OPENAI_API_KEY=sk-tu-key-de-openai-aqui
      - APP_NAME="Weather Chatbot"
      - APP_URL=http://localhost:8000
3. Iniciar la aplicación
   
   🌐 Acceso a la Aplicación
      - Frontend	http://localhost:5173
      - Backend (API)	http://localhost:8000
      - MySQL	mysql:3307	
