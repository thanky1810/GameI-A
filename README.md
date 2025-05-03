# Online Game Project Game A

## Description

This project is an online multiplayer game platform built with PHP. It uses Composer for dependency management and integrates several games using WebSocket technology via the `cboden/ratchet` library for real-time communication. It also utilizes `vlucas/phpdotenv` for managing environment variables.

## Features

- Real-time multiplayer gameplay.
- Multiple games available within the platform.
- WebSocket support for communication between players.
- Environment configuration using `.env` files.

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/online-game-project.git
   ```

2. Navigate to the project directory:

   ```bash
   cd online-game-project
   ```

3. Install Composer dependencies:

   ```bash
   composer install
   ```

4. Set up the environment configuration:

   - Create a `.env` file based on `.env.example`:
     ```bash
     cp .env.example .env
     ```

5. Configure your environment variables in the `.env` file (e.g., database settings, WebSocket server configuration).

6. Run the WebSocket server for real-time communication:

   ```bash
   php bin/server.php
   ```

7. Start the web server (e.g., using XAMPP or PHP's built-in server):

   ```bash
   php -S localhost:8000 -t public
   ```

8. Open your browser and go to `http://localhost:8000` to play the game.

## Usage

Once the server is running, players can join and play the game through the web interface. WebSocket communication will handle real-time interactions during gameplay.

## Acknowledgments

- Thanks to [Ratchet](http://socketo.me/) for providing WebSocket support.
- Thanks to [PHP Dotenv](https://github.com/vlucas/phpdotenv) for environment variable management.
- Inspiration from other real-time multiplayer game projects.
