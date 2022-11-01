# Composer dependency validator

## Purpose
A simple tool what can be used to check if all the used packages are required explicitly in the composer.json file.

## Reason
Trying to explain the problem by an example: 
There is a library called **libraryA** what uses another library **libraryB**,
and there is an application called **applicationA** what uses **libraryA** and **libraryB** as well.

When a developer upgrades the **libraryB** package in **libraryA** via composer, 
he doesn't have to bump the major version of **libraryA**.

After **libraryA** is updated in **applicationA** the **libraryB** package will be updated as well automatically
what can break the functionality of the application.

## The solution
To avoid the situation explained above, all the used packages should be required explicitly in the application's composer.json,
both **libraryA** and **libraryB**.

This tool helps to identify the packages to require.

## Usage
Simply call `vendor/bin/dependency-validator` from the root of the project.

## Config
A configuration can be defined in the root of the project. The filename has to be `dependency-validator.json`.

Can contain the following settings:
### excludedNamespaces
Contains an array of strings with the namespaces what should be excluded from the validation process.


