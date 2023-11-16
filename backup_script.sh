#!/bin/bash

backup_dir="/mnt/backuptest"

mkdir -p "$backup_dir"

source_dirs=("/var/www/applicant" "/var/www/test")

backup_filename="backup_$(date +\%Y\%m\%d).tar.bz2"

tar -cjf "$backup_dir/$backup_filename" "${source_dirs[@]}"

# Limit the number of backup files to 5
num_backups=$(ls -t "$backup_dir" | wc -l)
if [ "$num_backups" -gt 5 ]; then
    # Delete the oldest backup file
    oldest_backup=$(ls -t "$backup_dir" | tail -1)
    rm "$backup_dir/$oldest_backup"
fi
