<?php
//on ne peut pas test  àcause de Validator::make(), User::create() et Hash::make()
//car ils dependent du container laravel, des facases, de Eloquent, de la DB, du service preovider Hash , etc