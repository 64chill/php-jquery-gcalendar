# Simple Application Working with Google Calendar
![](https://i.ibb.co/vqkcqvh/a-UIhhkj-HY12-BNbbzz.png)
- **index.html**
    - starting file
    - Contains the form, 2 modals that are used as a message to the user if the calendar event was created or not.
    - Used: bootstrap, bootstrap-datepicker, clockpicker, jquery
- **style.css** 
    - additional styling of the page
- **script.js**
    - has the frontend logic.
    - Communicate with the backend via AJAX requests
    - Has implementation of ReCaptcha 3.0
    - Validates all fields to match the correct regex (fields that are required), if there is some error it will show the appropriate errors above the form and mark fields in red
- **form.handler.php**
    - backend code
    -  Checks received fields by validating them
    -  Validates ReCpatcha
    -  Creates calendar event on the google calendar if all fields good. Summary contains Name and Phone number received from the frontend form