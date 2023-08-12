# Distance Measurement
This simple Symfony CLI application will go through 8 addresses and calculate the distance between each address to Adchieve HQ. 

# Prerequisites
- PHP 8.2.8
- Composer 2.X
- PositionStack API access key

# Installation
- Clone this repo
- `composer install`
- `cp .env.example .env`
- Edit PositionStack access key in `.env`
- Grab PositionStack API key and edit it in the .env file (It is also pre-configured)

# Usage
As simple as executing `php bin/console calculate:distance` and you will see the output in your terminal with the progress.

You may also pass path to another file to the command as a first argument.

E.G: `php bin/console calculate:distance files/addresses.json`

### Json File Format
- destination <br>
A key that holds two properties (name and an address)
- addresses <br>
A key that holds an array of addresses (each address should include a name and an address)

Look [in this file](https://github.com/crayon1337/DistanceNavigator/blob/main/files/addresses.json) for reference.

If everything went well you'll see a new `distances.csv` file created in the files directory and includes all the addresses and their distance to the HQ.

# Run tests
`php bin/phpunit`

# Run linter
`./vendor/bin/phpcs --standard=ruleset.xml src/`

# CI - CD
Any commit pushed to this PR will always trigger GitHub actions to run. Those, checks will check for linting and also execute tests.
However, PRs shall NOT be merged into development or production branches before the pipelines turn green.

# Disclaimer
Please note that, this is basic yet powerful implementation where I utilize the usage of DTOs, Service, External Service, Error handling, testing and helper utility classes.

# Results
You can see a sample of results in [this CSV file](https://github.com/crayon1337/DistanceNavigator/blob/main/files/distances.csv)
