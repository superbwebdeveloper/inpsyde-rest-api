# Inpsyde Rest Api Steps

Steps to create a plugin

- There is no use of install and uninstall function, I didn't add that.
- Created a shortcode, use shortcode on WordPress Editor in Page to show user data
- using wp_remote_get() to get response from 'https://jsonplaceholder.typicode.com/users'
- userdetail_ajax_handler handles ajax by clicking user field in HTML Table

# System Configuration

- WordPress 5.6
- version PHP 7.2+

# Shortcode

- [typicode_data] to display user data

### Resources

- Plain old CSS
- jQuery v3.5.1 that is already in WordPress 5.6

### Installation

- Go to Plugins and Install the zip folder and click on Activate.
- Then go to Pages-> Create Page and Paste the short code [typicode_data] and publish the post.
- View the page and click on any User info field(eg. username etc), It will display the user details.

# Inpsyde Rest Api Explaination
