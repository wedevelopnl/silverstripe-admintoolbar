# SilverStripe Admin Toolbar

## Introduction

Display a toolbar with admin options on the frontend

## Requirements

* SilverStripe CMS ^4.0

## Installation

```
composer require "thewebmen/silverstripe-admintoolbar"
```

## How to use
Place `$AdminToolbar` in your Page.ss to display the admin toolbar.

## Options
Disable buttons using the config:

```
TheWebmen\AdminToolbar\AdminToolbar:
  hide_cache_button: true
  hide_stage_button: false
  hide_edit_button: false
````

Add an extensions on `TheWebmen\AdminToolbar\AdminToolbar` with the method `addExtraButtonsHTML(&$extraButtonsHTML)` to add exta buttons


