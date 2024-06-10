#!/bin/bash

# Navigate to the Laravel project directory
# cd /path/to/your/laravel/project

while true
do
  # Run the Artisan command
  php artisan app:export-transactions

  # Wait for 1 second
  sleep 1
done
