#!/bin/bash
echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Publicando assets de Livewire/Flux..."
php artisan vendor:publish --tag=livewire:assets --force

echo "Release completado"