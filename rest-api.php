<?php

/**
 * Plugin Name: Inpsyde Rest Api
 * Description: Custom endpoint for jsonplaceholder user details
 * Version: 1.0.0
 * Author: Balbir Kaur
 * Author URI: http://balbirkaur.com
 * License: GPL2+
 *
 * @package Rest_Api
 */

// If this file is access directly, abort!
defined('ABSPATH') or die('Unauthorized Access');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}
// Define constants.
define('INSPSYDE_REST_API_PLUGIN_URL', plugin_dir_url(__FILE__));

add_action('wp_enqueue_scripts', 'jsonplaceholder_rest_api_init');

function jsonplaceholder_rest_api_init()
{
  //  wp_enqueue_script('jquery-version', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', array('jquery'));
  ///  wp_enqueue_script('jsonplaceholder_ajax-js', plugins_url('/js/jsonplaceholder_ajax.js', __FILE__));
  //  wp_enqueue_script('jquery');
  // register our main script but do not enqueue it yet
  wp_register_script('jsonplaceholder-ajax-js', INSPSYDE_REST_API_PLUGIN_URL . 'js/jsonplaceholder_ajax.js', array('jquery'), '1.0.0', true);

  // you can define variables directly in your HTML but I decided that the most proper way is wp_localize_script()
  wp_localize_script('jsonplaceholder-ajax-js', 'jsonplaceholder_params', array(
    'ajaxurl' => admin_url('admin-ajax.php') // WordPress AJAX
  ));

  wp_enqueue_script('jsonplaceholder-ajax-js');
  wp_enqueue_style('jsonplaceholder-css', INSPSYDE_REST_API_PLUGIN_URL . 'css/jsonplaceholder.css');
}

add_shortcode('typicode_data', 'jsonplaceholder_data');


/**
 * Get a list of email subscribers.
 *
 * @return object The HTTP response that comes as a result of a wp_remote_get().
 */
function cache_t_users()
{
  // Do we have this information in our transients already?
  $transient = get_transient('cache_t_users');

  // Yep!  Just return it and we're done.
  if (!empty($transient)) {

    // The function will return here every time after the first time it is run, until the transient expires.
    return $transient;

    // Nope!  We gotta make a call.
  } else {

    // We got this url from the documentation for the remote API.
    $url = 'https://jsonplaceholder.typicode.com/users';

    // We are structuring these arguments based on the API docs as well.
    $arguments = array(
      'method' => 'GET'
    );

    // Call the API.
    $response = wp_remote_get($url, $arguments);

    // Save the API response so we don't have to call again until tomorrow.
    set_transient('cache_t_users', $response, DAY_IN_SECONDS);

    // Return the list of users.  The function will return here the first time it is run, and then once again, each time the transient expires.
    return $response;
  }
}

function jsonplaceholder_data()
{

  $output = cache_t_users();
  if (is_wp_error($output)) {
    $error_message = $output->get_error_message();
    return "Oops: $error_message";
  } else {
    $data_result = json_decode(wp_remote_retrieve_body($output));
    $html = "";
    $html .= '<table class="table">';
    $html .= '<thead class="thead-dark">
          <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
          </tr>
        </thead>';
    $html .= '<tbody>';
    foreach ($data_result as $userinfo) {
      $html .= '<tr>
            <th scope="row"><a rel="' . $userinfo->id . '">' . $userinfo->id . '</a></th>
            <td><a class="userbtn" rel="' . $userinfo->id . '">' . $userinfo->name . '</a></td>
            <td><a class="userbtn" rel="' . $userinfo->id . '">' . $userinfo->username . '</a></td>
            <td><a class="userbtn" rel="' . $userinfo->id . '">' . $userinfo->email . '</a></td>
          </tr>';
    }

    $html .= '</tbody>
      </table>';
    return $html;
  }
}


function userdetail_ajax_handler()
{

  if (!empty($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $url = 'https://jsonplaceholder.typicode.com/users/' . $user_id;
  } else {
    $url = 'https://jsonplaceholder.typicode.com/users';
  }


  $arguments = array(
    'method' => 'GET',
    'headers'   => array(
      'Content-Type' => 'application/json; charset=UTF-8'
    )
  );
  $response = wp_remote_get($url, $arguments);
  if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
    return "Oops: $error_message";
  } else {
    $data_result = json_decode(wp_remote_retrieve_body($response));
    $html = "";
    $html .= '<table class="table">';
    $html .= '<tr>
          <th scope="col">Id</th>
          <th scope="row">' . $data_result->id .
      '</th>  
          </tr>';
    $html .= '<tr>
          <td>Name</td>
          <td>' . $data_result->name . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Username</td>
          <td>' . $data_result->username . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Email</td>
          <td>' . $data_result->email . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Address: </td>
          <td> Street: ' . $data_result->address->street . '<br>  
          Suite: ' . $data_result->address->suite . '<br>  
          City: ' . $data_result->address->city . '<br>   
          ZipCode: ' . $data_result->address->zipcode . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Phone: </td>
          <td>' . $data_result->phone . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Website: </td>
          <td>' . $data_result->website . '</td>  
          </tr>';
    $html .= '<tr>
          <td>Company: </td>
          <td> Name: ' . $data_result->company->name . '<br>   
          Catch Phrase: ' . $data_result->company->catchPhrase . '<br>  
          BS: ' . $data_result->company->bs . '</td>  
          </tr>';
    $html .= '<tr>
          <th scope="row" col="4"><a class="userbtn">BACK</a></th>     
          </tr>';
    $html .= '</tbody>
          </table>';
    echo  $html;
    wp_die(); // this is required to terminate immediately and return a proper response
  }
}

add_action('wp_ajax_userdetail', 'userdetail_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_userdetail', 'userdetail_ajax_handler'); // wp_ajax_nopriv_{action}
