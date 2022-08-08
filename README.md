Steps to follow fresh project setup in local system:

- git clone https://github.com/shivrajsinh/my_project_laravel_vue.git
- composer install
- copy .env.example and rename to .env
- change all the settings in .env file as per your local system requirements
- php artisan key:generate 
- php artisan migrate
- php artisan db:seed
- npm install [To install JS dependencies] 
- npm run dev/watch [To compile css, js]
- php artisan optimize
- php artisan serve


Super admin login credentials
User name - superadmin@gmail.com
Paasword - superadmin@123
