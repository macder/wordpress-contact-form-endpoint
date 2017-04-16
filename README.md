# WFV - WordPress Form Validation

#### Declarative input validation API for WordPress

Intended for developers who want to build forms in a theme using custom markup and validate the input in a declarative way.

WFV gives you the ability to declare form validation constraints in a similar way found in MVC frameworks such as [Laravel](https://laravel.com/).

Markup a form in a template and define its constraints in `functions.php`, a plugin, or wherever. Everything is up to you, the developer.

You get a simple declarative api that helps you work with forms and input in an elegant way.

WFV uses [Valitron](https://github.com/vlucas/valitron), a powerful lightweight library that has ZERO dependencies, to validate input constraints.


# Table of Contents
* [Basic Example](#basic-example)
* [Features](#features)
* [TODO](#todo)
* [Install](#install)
* [Usage](#usage)
  * [Rules](#configure-validation-rules)
  * [Custom Rules](#custom-validation-rules)
  * [Custom Error Messages](#custom-error-messages)
  * [Validation Action](#callback-for-successful-validation)
  * [Markup a Form](#create-a-form-somewhere-in-your-theme)
  * [Validation Instance](#create-the-validation-instance)
  * [User Input](#user-input)
    * [Retrieve](#retrieve)
    * [Has](#has-input)
    * [Contains](#input-contains)
  * [Auto Populate](#auto-populate)
    * [Text Fields](#text-input)
    * [Checkboxes and Radio](#checkboxes-and-radio)
    * [Select and Multi-Select](#select-and-multi-select)
    * [Pre Populate](#pre-populate)
  * [Errors](#validation-errors)
    * [Has](#has-error)
    * [Bag](#get-field-errors)
    * [First Error](#get-fields-first-error)


## Basic example

`functions.php` or in some plugin:
```php
<?php

// declare the rules
$my_form = array(
  'action'  => 'contact_form',
  'rules'   => array(
    'first_name' => ['required'],
    'email'      => ['required', 'email']
  )
);

// hook for validation pass
add_action( 'contact_form_pass', 'my_form_valid' );
function my_form_valid( $form ) {
  // form input valid, do something...
}

// hook for validation fail
add_action( 'contact_form_fail', 'my_form_invalid' );
function my_form_invalid( $form ) {
  // form input invalid, do something...
}

// activate the form
wfv_create( $my_form );

```
Theme template:
```html
<form method="post">
  <input name="first_name" type="text">
  <input name="email" type="text">
  <?php $my_form->get_token_fields(); ?>
  <input type="submit" value="Send">
</form>
```

## Features
Just an API for input validation with WordPress.

...nothing more, nothing less

* 32 built-in validation rules from [Valitron](https://github.com/vlucas/valitron#built-in-validation-rules)
* Custom rules
* Custom error messages
* Sanitized input data
* Auto populate fields, including [checkboxes, radio](#checkboxes-and-radio) and [multi-selects](#select-and-multi-select)
* Action hooks for validation pass and fail
* Self POST - no redirects, no GET vars, no sessions, no cookies
* Declarative and object oriented API
* Lightweight
* NO RENDERED MARKUP
* Developer freedom

## TODO
- API endpoint for front end - support single configuration.

# Install

Currently there is no release available.

Under active development

If you can't wait, install as development.

`$ git clone` inside `./wp-content/plugins`

`$ composer install`

Once a release is packaged, install will be the usual WordPress way.

# Usage

## Configure validation rules

```php
<?php
$my_form = array(
  'action'  => 'contact_form', // unique identifier
  'rules'   => array(
    'first_name' => ['required'],
    'email'      => ['required', 'email'],
  )
);
```

For available validation rules, reference the [Valitron](https://github.com/vlucas/valitron#built-in-validation-rules) doc.

## Custom validation rules

Prepend `custom:` to rule, name of rule is the callback.
```php
<?php
$my_form = array(
  'action'  => 'contact_form', // unique identifier
  'rules'   => array(
    'phone'      => ['required', 'custom:phone'],
  )
);
```
Create the callback:
```php
<?php // phone field will validate only if the input is 'hi' ...how cruel

function wfv__phone( $value ) {
  return ( 'hi' === $value ) ? true : false;
}
```

## Custom error messages

```php
<?php
$my_form = array(
  'action'  => 'contact_form', // unique identifier
  'rules'   => array(
    'email'     => ['required', 'email']
  ),

  // override an error msg
  'messages' => [
    'email' => array(
      'required' => 'No email, no reply... get it?'
    )
  ]
);
```

## Callback for successful validation

When the input validates, i.e. passes all the constraints, the action hook defined in `$my_form['action']` is triggered.

Hook into it, do some logic in the callback:

```php
<?php

add_action( 'contact_form', 'contact_form_valid' );
function contact_form_valid( $form ) {
  // form validated, do something...
  echo $form->input->name;
  echo $form->input->email;
}
// that was better than using conditionals...
```

## Create a form somewhere in your theme

```html
<form name="contact_form" method="post">
  <input name="name" type="text">
  <input name="email" type="text">
  <textarea name="msg"></textarea>

  <?php $my_form->get_token_fields(); ?>
  <input type="submit" value="Submit">
</form>
```
The form must have the required token tag:
```php
<?php $my_form->get_token_fields(); ?>
```
This adds 2 hidden fields, nonce and action. The generated action field identifies the form to a validation instance.

## Create the validation instance
### `wfv_create( array $form )`
Send `array $form` to the `WFV\Factory\ValidationFactory` to create an instance of `WFV\Validator`

The instance is assigned by reference:
```php
<?php
// $my_form becomes an instance of WFV\Validator
wfv_create( $my_form );
```

`$my_form` can now access properties and methods available to `WFV\Validator`

```php
<?php
$my_form->input;     // Instance of WFV\Input
$my_form->errors;    // Instance of WFV\Errors
$my_form->rules;     // Instance of WFV\Rules
$my_form->messages;  // Instance of WFV\Messages
```
**Get and Set:**

All property instances on `WFV\Validator` share the same accessor and mutator traits.

Examine [`AccessorTrait.php`](https://github.com/macder/wp-form-validation/blob/master/src/AccessorTrait.php) and [`MutatorTrait.php`](https://github.com/macder/wp-form-validation/blob/master/src/MutatorTrait.php) for available methods to get and set properties.

## User input
### `WFV\Input`
Class instance that holds the form input data as properties.

The `input` property on `WFV\Validator` is an instance of `WFV\Input`

```php
<?php // $input becomes instance of WFV\Input

$input = $my_form->input;
```

### Retrieve

Get the input value of a field:
```php
<?php // output the value the user entered into the email field

echo $my_form->input->email; // foo@bar.com

```

Get input as an array:
```php
<?php // get users input as an associative array

$input = $my_form->input->get_array();
echo $input['email']; // foo@bar.com
```

### Has input
#### `has( string $field )`
Check if `$field` has value, return `bool`

```php
<?php // was something entered into the email field?

$my_form->input->has('email');  // true
```

### Input contains
#### `contains( string $field, string $value )`
Check if `$field` contains `$value`, return `bool`

```php
<?php // Did the user enter foo@bar.com into the email field?

$my_form->input->contains( 'email', 'foo@bar.com');  // true
```
```php
<?php // Did the user enter bar@foo.com into the email field?

$my_form->input->contains( 'email', 'bar@foo.com');  // false
```

## Auto Populate

### Text input

If validation fails, these fields would populate using the submitted values:
```html
<input name="email" type="text" value="<?= $my_form->input->email ?>">
```

```html
<textarea name="msg"><?= $my_form->input->msg ?></textarea>
```

### Checkboxes and Radio
#### `checked_if( string $field, string $value )`
Return string `'checked'` when `$field` has input `$value`.

```php
<?php // will echo 'checked' if user checked 'green' checkbox

echo $my_form->checked_if('color', 'green'); // checked

```

Checkbox:
```php
<input name="color[]" type="checkbox" value="green" <?= $my_form->checked_if('color', 'green'); ?>>
<input name="color[]" type="checkbox" value="blue" <?= $my_form->checked_if('color', 'blue'); ?>>
<input name="color[]" type="checkbox" value="red" <?= $my_form->checked_if('color', 'red'); ?>>
```

Radio:
```php
<input name="agree" type="radio" value="yes" <?= $my_form->checked_if('agree', 'yes'); ?>>
<input name="agree" type="radio" value="no" <?= $my_form->checked_if('agree', 'no'); ?>>
```

### Select and multi-select
#### `selected_if( string $field, string $value )`
Return string `'selected'` when `$field` has input `$value`.

```php
<?php // will echo 'selected' if user selected 'green' in select input

echo $my_form->selected_if('color', 'green'); // selected
```

Select:
```php
<select name="title">
  <option value="">Select...</option>
  <option value="Mr" <?= $my_form->selected_if('title', 'Mr'); ?>>Mr</option>
  <option value="Dr" <?= $my_form->selected_if('title', 'Dr'); ?>>Dr</option>
  <option value="Miss" <?= $my_form->selected_if('title', 'Miss'); ?>>Miss</option>
  <option value="Mrs" <?= $my_form->selected_if('title', 'Mrs'); ?>>Mrs</option>
</select>
```

Multi-select:
```php
<select name="color[]" multiple>
  <option value="red"<?= $my_form->selected_if('color', 'red'); ?>>Red</option>
  <option value="blue"<?= $my_form->selected_if('color', 'blue'); ?>>Blue</option>
  <option value="green"<?= $my_form->selected_if('color', 'green'); ?>>Green</option>
</select>
```

### Pre populate
#### `put( string $field, string $value )`
Pre-populate a field before `$_POST`

```php
<?php // the email field will always be pre populated with foo@bar.com

$my_form->input->put('email', 'foo@bar.com');
```
```html
<input name="email" type="text" value="<?= $my_form->input->email ?>">
```

## Validation Errors
### `WFV\Errors`
Class instance that holds validation errors.

The `errors` property on `WFV\Validator` is an instance of `WFV\Errors`
```php
<?php // $errors becomes instance of WFV\Errors

$errors = $my_form->errors;
```
### Has error
#### `has( string $field )`
Check if `$field` has error, return `bool`
```php
<?php // does the email field have an error?

$my_form->errors->has('email'); // true or false
```

### Get field errors:
```php
<?php // get the error bag for a field

$email_errors = $my_form->errors->email;

foreach( $email_errors as $error ) {
  echo $error;
}
```

### Get field's first error
#### `error( string $field )`
Convienience method to get first error on field.

```php
<?php // get the first email error message

echo $my_form->error('email'); // Email is required
```
First error message is the first declared rule.

For example: `required` is the first error if rules are declared as `['required', 'email']` and both validations fail.


## Development

`$ git clone` inside `./wp-content/plugins`

`$ composer install`

Checkout the `develop` branch

`$ git checkout develop`

Create new feature branch

`$ git checkout -b feature/[name]`

`$ git push origin feature/[name]`

When your feature is ready and tested

`$ git fetch origin develop`

`$ git merge origin/develop`

`$ git push origin feature/[name]`

Now test to ensure your code works on `develop`

Create a pull request to merge your feature branch into `develop`
