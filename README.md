# Simple Chat Room Based on PHP
This is a simple chat room website using Server-sent Events and Shared Memory Functions.

## Installation
 - Setup a web server like [Nginx](https://www.nginx.com/resources/wiki/start/) or [Apache](https://httpd.apache.org/docs/current/getting-started.html).
 - Clone the project to your local file system.
 - Set the root of the web to the path of the project.
 - Setup PHP-FPM. (For example, edit [nginx.conf](https://www.nginx.com/resources/wiki/start/topics/examples/phpfcgi/#connecting-nginx-to-php-fpm))
 - Start the service. (For example: `sudo service nginx start`)

## Usage
 - Open the url in the browser (For example: `localhost`) to access the chat room.
 - Fill the `User` and `Message` boxes.
 - Press `Enter` or click on `Submit` to submit the message.
 - The message will show to all of the online users.
 - Latest 50 messages are stored, older messages will be cleaned up while new messages comes in.
 - Access `/clean.php` to permanently delete all messages. (For testing purpose, if you don't need this, remove the script or deny access to the endpoint.)
 
## Test Environment
 - WSL Ubuntu 20.04
 - Nginx 1.18
 - PHP-FPM 7.4
 
## References
 - [Server-sent Events](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events): Keep the connection and send new messages clients.
 - [EventSource](https://developer.mozilla.org/en-US/docs/Web/API/EventSource): Keep the connection and receive new messages from the server.
 - [PHP-shmop](https://www.php.net/manual/en/ref.shmop.php): Make different users get same chat room data.
 - [Vue.js](https://vuejs.org/): Frontend render.
 - [Bootstrap](https://getbootstrap.com/): Styling.