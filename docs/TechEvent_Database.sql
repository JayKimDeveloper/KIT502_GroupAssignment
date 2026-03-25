/**
* Author: YoungHyun Kim
* version: 0.1
**/

/**
* user_TB
* comment: Basic User Table.
*          Considered the admin, organiser, attendee
*/
CREATE TABLE user_TB (
    login_id VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    location VARCHAR(100) NOT NULL,
    role ENUM('admin', 'organiser', 'attendee') NOT NULL,
    email VARCHAR(100) NOT NULL,
    update_date DATETIME NOT NULL,
    register_date DATETIME NOT NULL,
    PRIMARY KEY (login_id)
);

/**
* category_TB
* comment: event category TB
*/
CREATE TABLE category_TB (
    category_id INT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (category_id)
);

/**
* event_TB
* comment: maintain the event info
*/
CREATE TABLE event_TB (
    event_id CHAR(12) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    login_id VARCHAR(100) NOT NULL,
    img_url VARCHAR(100),
    ticket_price INT NOT NULL,
    capacity INT NOT NULL,
    status ENUM('Draft', 'Confirmed', 'Cancelled') NOT NULL,
    category_ID INT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL,
    PRIMARY KEY (event_id),
    FOREIGN KEY (login_id) REFERENCES user_TB(login_id),
    FOREIGN KEY (category_ID) REFERENCES category_TB(category_id)
);

/**
* booking_TB
* comment: maintain the booking
*/
CREATE TABLE booking_TB (
    booking_id CHAR(12) NOT NULL,
    booking_date DATETIME NOT NULL,
    member_cnt INT NOT NULL,
    event_id CHAR(12) NOT NULL,
    login_id VARCHAR(100) NOT NULL,
    PRIMARY KEY (booking_id),
    FOREIGN KEY (event_id) REFERENCES event_TB(event_id),
    FOREIGN KEY (login_id) REFERENCES user_TB(login_id)
);

/**
* notification_TB
* comment: manage the notification
*         - version.0.1, Beta version of Notification.
*/
CREATE TABLE notification_TB (
    notification_id VARCHAR(12) NOT NULL,
    login_id VARCHAR(100) NOT NULL,
    booking_id CHAR(12) NOT NULL,
    type VARCHAR(100) NOT NULL,
    message TEXT,
    is_read TINYINT(1) NOT NULL,
    update_date DATETIME NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (login_id) REFERENCES user_TB(login_id),
    FOREIGN KEY (booking_id) REFERENCES booking_TB(booking_id)
);

/**
* activies_logs_TB
* comment: Log table, might be helpful for tracking trouble and admin page
*/
CREATE TABLE activies_logs_TB (
    log_id VARCHAR(25) NOT NULL,
    login_id VARCHAR(100) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    action_category VARCHAR(50) NOT NULL,
    new_value VARCHAR(255),
    create_date DATETIME NOT NULL,
    PRIMARY KEY (log_id),
    FOREIGN KEY (login_id) REFERENCES user_TB(login_id)
);