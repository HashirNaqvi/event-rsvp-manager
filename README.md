Event RSVP Manager Plugin
Description
The Event RSVP Manager is a simple and intuitive WordPress plugin that allows event organizers to manage event registrations with admin approval. It provides a complete solution for creating events, registering users for those events, and allowing administrators to approve or reject registration requests. Only logged-in users can register for events, ensuring a secure and authenticated process.

Features
Event Creation and Management: Admins can create and manage events using a custom post type, adding all necessary details.
User Registration: Only logged-in users can register for events through a styled registration form.
Admin Approval: Registrations are saved with a "pending" status until approved or rejected by an admin.
Pending Requests Management: Admins can view, approve, or delete pending registrations via an easy-to-use admin panel.
Approved Guests List: Once approved, the users are moved to an "Approved Guests" list, which can be displayed in the admin panel or via a shortcode.
Email Notifications: Optional confirmation emails can be sent upon registration submission.
User Manual
1. Installation Instructions
Download the plugin code and upload it to your WordPress site's wp-content/plugins directory.
Activate the plugin via the Plugins menu in the WordPress admin dashboard.
Once activated, the plugin will create a custom post type called Events and a database table for RSVP management.
2. How to Use
Creating an Event
Go to the Events menu in the WordPress admin panel.
Click Add New to create a new event.
Fill in the event title and details.
Publish the event to make it available for user registration.
Displaying the Event Registration Form
To allow users to register for an event, use the shortcode [erm_rsvp_form] on any page or post where you want to display the event registration form.
Only logged-in users will see the registration form. Others will be prompted to log in or register for an account.
Managing Pending RSVPs
Go to the RSVP Manager menu in the WordPress admin panel.
The Pending RSVPs page will display all users who have registered but are waiting for approval.
Admins can Approve or Reject these requests by clicking the appropriate button.
Approved Guests
Once a user’s registration is approved, they are moved to the "Approved Guests" list.
You can display this list in the admin panel under the Approved Guests submenu.
You can also display the list of approved guests on the frontend using the [erm_approved_guests] shortcode.
3. Admin Features
Add Event: Admins can add a new event from the Events section in the admin panel.
Pending RSVP Requests: Admins can manage pending registrations from the RSVP Manager page.
Approve or Reject Registrations: Admins can approve or reject registrations with one click. Approved registrations are moved to the approved list, and rejected registrations are deleted.
Approved Guests List: Admins can view all approved guests from the Approved Guests page.
4. Shortcodes
[erm_rsvp_form]: Displays the event registration form for logged-in users. If the user is not logged in, they will be prompted to log in or register.
[erm_approved_guests]: Displays the list of approved guests for events.
Example Usage
Create an Event:

Add a new event by navigating to the Events menu and clicking Add New. Once the event is created, users will be able to register for it.
RSVP Form on a Page:

Create a new page (or edit an existing one) and add the following shortcode:
plaintext
Copy code
[erm_rsvp_form]
This will display the event registration form on the page.
Approved Guests:

To display the list of approved guests on a page, use the following shortcode:
plaintext
Copy code
[erm_approved_guests]
Installation Requirements
WordPress Version: 5.0 or higher
PHP Version: 7.2 or higher
Frequently Asked Questions
Can I create multiple events? Yes, you can create as many events as you like. Each event will be available for user registration separately.

Do I need to approve every registration manually? Yes, registrations are stored with a pending status until an admin approves or deletes them.

Can I display a list of approved users? Yes, you can display the approved users list using the [erm_approved_guests] shortcode.

Is user login required to register for events? Yes, only logged-in users can register for events. If a user is not logged in, they will be prompted to log in or register an account.

