# WEB-SDT
### Temp info for the teacher
### Я там налажал с коммитами и бренчами, каюсь. Много коммитов отправлял без указания какая лаба, т.к. много раз фиксил стили, функции, БД запросы которые относятся к разным лабам + обновлял readme и т.д.</br>
### For launching the app, .env files must be filled. Their description can be found below. I uploaded some env (without dot) files, with examples, change what must be changed, and it will be almost ready to roll<br/>
### Sometimes, the installation of image viewing script is needed for correct work. If images are not showing (happened to me twice, tried it on the absolutely new linux system - everything was fine) then go to the localhost:8001/slir/install <br/>This will automatically install some additional files in the project folder, which will make images working
### Main Info
- __Website__ is avaiable on(at?) the localhost:8001/<br/>
- __DB__ is avaiable on(at?) the localhost:5050<br/>
- __Docker__ is avaiable on(at?) the https://hub.docker.com/r/kirylmi/web-php-pgsql
### Main parts:
PHP,
Apache,
PostgreSQL
### Additional software:
PGAdmin4,
Composer
### Libraries
PHPMailer - https://github.com/PHPMailer/PHPMailer,<br/>
Dotenv (for PHP) - https://github.com/vlucas/phpdotenv,<br/>
GD (PHP ext)<br/>
SLIR - https://github.com/lencioni/SLIR<br/>
AWS SDK for PHP (S3) - https://github.com/aws/aws-sdk-php<br/>

## Important Notes
### Dotenv
Program requiers .env files:
- .env in the main directory (with the docker-compose variables) like:
    > DB_PASSWORD<br/>
    > DB_NAME<br/>
    > DB_USER
- .env in the ./www/ directory (with the Mailer variables + DB) like:
    > DB_HOST<br/>
    > DB_PORT<br/>
    > DB_USER<br/>
    > DB_NAME<br/>
    > DB_PASSWORD<br/>
    > MAIL_USER<br/>
    > MAIL_EMAIL<br/>
    > MAIL_PASS<br/>
    > MAIL_FROM<br/>
    > MAIL_SSL<br/>
    > MAIL_PORT<br/>
    > S3_BUCKET<br/>
    > S3_KEY<br/>
    > S3_SECRET_K<br/>

## Tasks:
- :heavy_check_mark: Docker+PHP<br/>
    - :white_check_mark: Docker + docker-compose <br/>
    - :white_check_mark: Apache with PHP on(in?) Docker<br/>
    - :white_check_mark: PostgreSQL on(in?) Docker<br/>
    - :white_check_mark: PGAdmin4 on(in?) Docker<br/>
    - :white_check_mark: Composer on(in?) Docker<br/>
    - :white_check_mark: Migrations in PostgreSQL(Dockerised)<br/>
    - :checkered_flag: Tests<br/>
- :heavy_check_mark: Apache Rerouting<br/>
    - :white_check_mark: .htaccess file<br/>
    - :checkered_flag: Tests<br/>
- :heavy_check_mark: Registration,Sign in and activation through email<br/>
    - :white_check_mark: Interface<br/>
    - :white_check_mark: PHPMailer<br/>
    - :white_check_mark: DB queries<br/>
    - :black_square_button: Three tries*<br/>
    - :checkered_flag: Tests<br/>
- :heavy_check_mark: Users list + Profile<br/>
    - :white_check_mark: ProfilePage<br/>
    - :white_check_mark: NavPanel<br/>
    - :white_check_mark: UsersPage<br/>
    - :white_check_mark: ProfilePage with photos<br/>
    - :white_check_mark: Tests <br/>
- :heavy_check_mark: Profile edit<br/>
    - :white_check_mark: Change email with reactivation<br/>
    - :white_check_mark: Change password, name<br/>
    - :white_check_mark: Tests <br/>
    - :black_square_button: Change privilege (Lab N required)<br/>
    - :white_check_mark: Tests <br/>
- :heavy_check_mark: Images<br/>
    - :white_check_mark: Amazon S3<br/>
    - :white_check_mark: Amazon S3 put <br/>
    - :white_check_mark: Amazon S3 get <br/>
    - :white_check_mark: Amazon S3 delete <br/>
    - :white_check_mark: DB queries<br/>
    - :white_check_mark: GD Tests<br/>
    - :white_check_mark: Show and Delete interface<br/>
    - :white_check_mark: Size control<br/>
    - :white_check_mark: Fake images tests<br/>
- :heavy_check_mark: Subscriptions<br/>
    - :white_check_mark: Idea<br/>
    - :white_check_mark: DB (Table, functions, queries)<br/>
    - :white_check_mark: Button:)<br/>
    - :white_check_mark: Showing images in the correct order<br/>
    - :white_check_mark: Styles fixes <br/>
    - :white_check_mark: Tests <br/>
    - :black_square_button: list of subscribers. Easy one, but  should i add it?<br/>
- :heavy_check_mark: Comments<br/>
    - :white_check_mark: Idea<br/>
    - :white_check_mark: DB (Table, functions, queries)<br/>
    - :white_check_mark: Buttons:)<br/>
    - :white_check_mark: Showing them and synchronysing<br/>
    - :black_square_button: Styles fixes. Will have to change a lot. Better stay as it is <br/>
    - :white_check_mark: Tests <br/>

## P.S.
........



### For me:
- :white_check_mark: Not ERRORS+header, create a php file with redirection, or add function "Rerout($errormsg,$location)"
- :white_check_mark::black_square_button: Stop using direct db.php access. There must be such hierarchy: Pages->(if needed)Functions->(if needed)Db queries
- :white_check_mark::black_square_button: Delete useless comments
