# Survey Full Stack Application

Built with these technologies
<table>
    <tr>
        <td>
            <a href="https://laravel.com"><img src="https://i.imgur.com/pBNT1yy.png" /></a>
        </td>
        <td>
            <a href="https://vuejs.org/"><img src="https://i.imgur.com/BxQe48y.png" /></a>
        </td>
        <td>
            <a href="https://tailwindcss.com/"><img src="https://i.imgur.com/wdYXsgR.png" /></a>
        </td>
    </tr>
</table> 


## Requirements
You need to have PHP version **8.0** or above. Node.js version **12.0** or above.

## Demo


## Installation

#### Backend
1. Clone the project
2. Go to the project root directory
3. Run `composer install` or `composer update`
4. Create database
5. Copy `.env.example` into `.env` file and adjust parameters
6. Update your DataBase parameters. If you want to use Mysql, make sure you have mysql server up and running. If you want to use sqlite:

    you can just delete all DataBase parameters except DB_CONNECTION and set its value to sqlite
    Then create file database/database.sqlite

7. Run `php artisan key:generate --ansi`
8. Run `php artisan migrate`
7. Run `php artisan serve` to start the project at http://localhost:8000

#### Frontend
1. Navigate to `vue` folder using terminal
2. Run `npm install` to install vue.js project dependencies
4. Start frontend by running `npm run dev`
5. Open http://localhost:3000


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

