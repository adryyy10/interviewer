# frolo-server

- PHP 8.2
- Symfony 6.2
- API Platform 3.1

REST API for the Interviewer.

## Local setup

```bash
# Start the DB (MySQL), SMTP server and client inside of Docker
$ docker-compose up -d

# Run Symfony locally
$ composer install
$ symfony server:start

# Prepare the database
$ ./reset_dev_db.sh
```

## Testing

Setup:

```bash
$ ./reset_test_db.sh
```

Unit tests (PHPUnit):

```bash
$ ./vendor/bin/phpunit ./tests
```