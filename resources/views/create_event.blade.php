
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Event - TechEvents</title>

    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/create_event.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

    <div class="page">
        <div class="container">
            <h1 class="page-title">Create New Event</h1>

            <div class="event-form-box">
                <h2>Event Details</h2>
                <p id="formMessage" class="form-message"></p>

                <form id="createEventForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter event title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" placeholder="Enter event description" required>
                    </div>

                    <div class="form-group">
                        <label for="categoryId">Category</label>
                        <select id="categoryId" name="category_id">
                            <option value="">No category</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="startDatetime">Start Date and Time</label>
                            <input type="datetime-local" id="startDatetime" name="start_datetime" required>
                        </div>

                        <div class="form-group half">
                            <label for="endDatetime">End Date and Time</label>
                            <input type="datetime-local" id="endDatetime" name="end_datetime" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <select id="location" name="location" required>
                            <option value="">Select event location</option>
                            <option value="Hobart">Hobart Campus</option>
                            <option value="Launceston">Launceston Campus</option>
                            <option value="Melbourne">Melbourne Campus</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <input type="number" id="capacity" name="capacity" placeholder="Enter maximum capacity" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" placeholder="Enter price" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="">Select event status</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image">Image Upload (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" id="cancelBtn">Cancel</button>
                        <button type="reset" class="btn btn-outline" id="resetBtn">Reset</button>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </div>
                </form>
            </div>
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

    <script>window.APP_URL = "{{ url('/') }}";</script>
    <script src="{{ asset('js/create_event.js') }}"></script>
</body>
</html>
