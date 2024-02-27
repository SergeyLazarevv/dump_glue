# dump_glue
script for creating an index file for many dump files, taking into account their dependence on each other

# usage
1) Put script in folder with dump sql files
2) run ```php dump_glue.php```
3) will be created index.sql include all sql files in directory in order of their dependencies
4) run psql -U <your_user> -d <db_name> -f index.sql