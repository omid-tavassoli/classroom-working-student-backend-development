# PHP Basics Challenge – User & Role Management System

## Overview

This project is a simple user and role management system I built to practice and demonstrate core PHP concepts. The goal wasn’t to create a full production-ready system, but to show a clean structure and proper use of PHP fundamentals like OOP, namespaces, traits, interfaces, and exception handling.

The system models two user types:

* **AdminUser**
* **CustomerUser**

Both share common behavior through an abstract base class while extending functionality where appropriate.

---

## Architecture & Design Decisions

### 1. Project Structure

```
src/
  Interfaces/
    Resettable.php
  Models/
    UserBase.php
    AdminUser.php
    CustomerUser.php
  Traits/
    CanLogin.php
  Utils/
tests/
demo.php
composer.json
README.md
```

The structure follows common PHP conventions:

* `src/` contains all application logic.
* `Models/` holds domain entities.
* `Interfaces/` contains contracts that define behavior.
* `Traits/` contains reusable functionality shared between classes.
* `tests/` contains unit and integration tests.
* `demo.php` demonstrates runtime behavior and serves as a simple manual test.
* Composer is used for PSR-4 autoloading.

This separation improves maintainability and readability while keeping the architecture simple and appropriate for the project size.

---

### 2. Core Design Principles

I tried to keep the design simple while still showing some OOP concepts.

`UserBase` is an abstract class that holds shared things like name, email, role, validation, and a static counter.
`AdminUser` and `CustomerUser` extend it so they can reuse that logic.

I used an interface (`Resettable`) only for the admin to show how specific behavior can be applied to certain classes.

The `CanLogin` trait is just a reusable login feature shared between users without making the class hierarchy more complicated.

---

### 3. Visibility & Encapsulation

The project intentionally demonstrates different visibility levels:

* `private` → internal state (e.g., name)
* `protected` → accessible to subclasses (e.g., email)
* `public` → exposed properties (role, for demonstration)

Getters and setters are implemented to enforce validation and encapsulation.

---

### 4. Constants & Static Members

Role identifiers are implemented using constants:

```php
UserBase::ROLE_ADMIN
UserBase::ROLE_CUSTOMER
```

A static instance counter tracks how many user objects have been created.
This demonstrates static properties and methods in PHP.

---

### 5. Validation & Exception Handling

Email validation is performed using `filter_var`.
Invalid input results in an `InvalidArgumentException`.

This demonstrates defensive programming and error handling best practices.

---

### 6. Magic Methods

The following magic methods are implemented:

* `__construct` → initialization
* `__toString` → readable object representation
* `__get` / `__set` → controlled dynamic access (whitelisted)

Magic methods are restricted intentionally to avoid unsafe behavior while still demonstrating their use.

---

### 7. Arrays & Data Handling

The project demonstrates both array types:

* **Numeric arrays** → ordered collections of users
* **Associative arrays** → lookup tables keyed by email

Array functions used:

* `array_map`
* `array_filter`
* `count`

The choice between numeric and associative arrays is documented in the demo script.

---

### 8. Functions & Closures

The project includes:

* A regular named function for formatting users
* Anonymous functions (closures) for filtering collections

Closures are particularly useful for concise logic applied to arrays.

---

### 9. Control Structures

Multiple control structures are demonstrated:

* `if`
* `switch`
* `foreach`
* `while`
* Logical operators (`&&`, `||`, `!==`)

These are integrated into realistic scenarios rather than isolated examples.

---

### 10. Testing Strategy

Two levels of testing are provided:

#### Unit Tests

#### Validation points :

* Role assignment
* Validation exceptions
* Login logic
* Static counters

#### Integration Test

This test validate a full user workflow combining:

* Models
* Traits
* Interfaces
* Arrays
* Control structures

---

## Demo Script

The `demo.php` file demonstrates:

* Object creation
* Trait usage (login)
* Interface usage (password reset)
* Array operations
* Control structures
* Static counters
* Magic methods

---

## Autoloading

Composer is used with PSR-4 autoloading:

```json
"App\\": "src/"
```

This maps namespaces directly to directory structure and eliminates manual `require` statements.

---

## Thought Process & Approach

* I wanted to show a good range of PHP fundamentals without making the project overly complex.
* I focused on keeping the structure clean and easy to understand.
* I tried to keep the architecture simple but still realistic enough to reflect real-world usage.
* I paid attention to readability, naming, and using proper type hints where it made sense.
* I avoided over-engineering and kept the implementation aligned with the scope of the challenge.
* I used traits for shared behavior instead of creating deep inheritance chains.
* I used interfaces to represent specific capabilities (like password reset).
* I made sure validation happens early when objects are created.
* I followed modern PHP practices like strict types, namespaces, and PSR-4 autoloading.

---

## Author

Omid Tavassoli
