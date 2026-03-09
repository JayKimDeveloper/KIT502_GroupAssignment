# shared.css Guide

Base stylesheet for all pages in the TechEvents UTAS project.  
Every team member must link this file before writing any page-specific styles.

---

## Setup

Add this inside `<head>` on every HTML page:

```html
<link rel="stylesheet" href="shared.css">
```

If you have page-specific styles, add them **after** shared.css:

```html
<link rel="stylesheet" href="shared.css">
<link rel="stylesheet" href="register.css">
```

---

## Color Variables

Defined in `:root` — use these instead of raw hex values in your own CSS.

```css
var(--primary)    /* #E76F51 — warm orange */
var(--secondary)  /* #7A1F2B — burgundy */
var(--accent)     /* #F4A261 — golden */
var(--bg)         /* #FFF7ED — cream background */
var(--surface)    /* #FFE8D6 — ivory */
var(--text)       /* #3A1F1F — dark brown */
```

Example:
```css
.my-element {
  background-color: var(--primary);
  color: var(--text);
}
```

---

## Layout

### `.container`
Centers content horizontally with a max width of 1100px.  
Wrap all page content inside this.

```html
<div class="container">
  <!-- your content -->
</div>
```

### `.page`
Wraps the main content area. Adds top padding so content isn't hidden behind the fixed navbar.

```html
<div class="page">
  <div class="container">
    <!-- your content -->
  </div>
</div>
```

---

## Navbar

Copy this into every page. Add `class="active"` to the link that matches the current page.

```html
<nav class="navbar">
  <div class="container">
    <a href="index.html" class="nav-brand">Tech<span>Events</span></a>
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="events.html" class="active">Events</a></li>
      <li><a href="create.html">Create Event</a></li>
    </ul>
    <div class="nav-actions">
      <a href="login.html" class="btn btn-outline">Log in</a>
      <a href="register.html" class="btn btn-primary">Sign up</a>
    </div>
  </div>
</nav>
```

> Change `class="active"` to whichever page you are currently on.

---

## Buttons

Always use `.btn` together with a variant. Using a variant alone won't work.

| Class | Description |
|-------|-------------|
| `.btn .btn-primary` | Filled orange button |
| `.btn .btn-outline` | Transparent button with orange border |
| `.btn-block` | Makes the button full width |

```html
<!-- filled -->
<button class="btn btn-primary">Register</button>

<!-- outline -->
<button class="btn btn-outline">Cancel</button>

<!-- full width -->
<button class="btn btn-primary btn-block">Submit</button>

<!-- link styled as button -->
<a href="register.html" class="btn btn-primary">Sign up</a>
```

> `.btn-primary` alone won't apply the full button style — always pair it with `.btn`.

---

## Footer

Copy this to the bottom of every page.

```html
<footer class="footer">
  <div class="container">
    <p>TechEvents UTAS — connecting students with the tech community.</p>
  </div>
  <div class="footer-bottom">
    <div class="container">
      © 2025 TechEvents UTAS. All rights reserved.
    </div>
  </div>
</footer>
```

---

## Full Page Template

Use this as a starting point for every new page.

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Title — TechEvents</title>
  <link rel="stylesheet" href="shared.css">
  <style>
    /* page-specific styles here */
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="container">
      <a href="index.html" class="nav-brand">Tech<span>Events</span></a>
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="events.html">Events</a></li>
        <li><a href="create.html">Create Event</a></li>
      </ul>
      <div class="nav-actions">
        <a href="login.html" class="btn btn-outline">Log in</a>
        <a href="register.html" class="btn btn-primary">Sign up</a>
      </div>
    </div>
  </nav>

  <div class="page">
    <div class="container">

      <!-- your content here -->

    </div>
  </div>

  <footer class="footer">
    <div class="container">
      <p>TechEvents UTAS — connecting students with the tech community.</p>
    </div>
    <div class="footer-bottom">
      <div class="container">
        © 2025 TechEvents UTAS. All rights reserved.
      </div>
    </div>
  </footer>

</body>
</html>
```

---
