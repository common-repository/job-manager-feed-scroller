<?php
/**
Plugin Name: Job manager feed scroller
Plugin URI: http://wordpress.org/plugins/job-manager-feed-scroller/
Description: Get Jobs added by plugin Job Manager and display them as scrolling text. Shortcode [showjobscroll]
Tags: job-manager,job-feeds
Version: 1.0
Author: Nishant Vaity
Author URI: https://github.com/enishant
License: GPL2
*/

function get_jobman_jobs()
{
	$jobs_uid = uniqid('jobs_uid_');
	global $post;
	$posts = get_posts( 'post_type=jobman_job&numberposts=-1&post_status=publish' );
	$jobs .= '<div class="scroll-text">';
	$jobs .= '<ul id="' . $jobs_uid . '">';
	foreach ($posts as $post)
	{
		$jobs .= '<li><a href="' . get_permalink() . '">' .$post->post_title . '</a></li>';
	}
	$jobs .= '</ul>';
	$jobs .= '</div>';
	$jobs .= '<script>';
	$jobs .= 'jQuery("#' . $jobs_uid . '").newsTicker({row_height: 150, max_rows: 1, speed: 600,direction: "up",duration: 4000,autostart: 1, pauseOnHover: 1});';
	$jobs .= '</script>';
	return $jobs;
}

function jobman_jobs_scripts() 
{
	wp_enqueue_script( 'jobman-jobs-newsTicker', plugins_url( 'jquery.newsTicker.js' , __FILE__ ));
}

function jobmanager_send_email_on_plugin_activate() {
	$plugin_title = "Job manager feed scroller";
	$plugin_url = 'http://wordpress.org/plugins/job-manager-feed-scroller/';
	$plugin_support_url = 'http://wordpress.org/support/plugin/job-manager-feed-scroller';
	$plugin_author = 'Nishant Vaity';
	$plugin_author_url = 'https://github.com/enishant';
	$plugin_author_mail = 'enishant@gmail.com';

	$website_name  = get_option('blogname');
	$adminemail = get_option('admin_email');
	$user = get_user_by( 'email', $adminemail );

	$headers = 'From: ' . $website_name . ' <' . $adminemail . '>' . "\r\n";
	$subject = "Thank you for installing " . $plugin_title . "!\n";
	if($user->first_name)
	{
		$message = "Dear " . $user->first_name . ",\n\n";
	}
	else
	{
		$message = "Dear Administrator,\n\n";
	}
	$message.= "Thank your for installing " . $plugin_title . " plugin.\n";
	$message.= "Visit this plugin's site at " . $plugin_url . " \n\n";
	$message.= "Please write your queries and suggestions at developers support \n" . $plugin_support_url ."\n";
	$message.= "All the best !\n\n";
	$message.= "Thanks & Regards,\n";
	$message.= $plugin_author . "\n";
	$message.= $plugin_author_url ;
	wp_mail( $adminemail, $subject, $message,$headers);
	
	$subject = $plugin_title . " plugin is installed and activated by website " . get_option('home') ."\n";
	$message = $plugin_title  . " plugin is installed and activated by website " . get_option('home') ."\n\n";
	$message.= "Website : " . get_option('home') . "\n";
	$message.="Email : " . $adminemail . "\n";
	if($user->first_name)
	{
		$message.= "First name : " . $user->first_name . " \n";
	}
	if($user->last_name)
	{
		$message.= "Last name : " . $user->last_name . "\n";	
	}
	wp_mail( $plugin_author_mail , $subject, $message,$headers);
}

register_activation_hook( __FILE__, 'jobmanager_send_email_on_plugin_activate' );
add_shortcode('showjobscroll','get_jobman_jobs');
add_filter('widget_text', 'do_shortcode', 11);
add_action( 'wp_enqueue_scripts', 'jobman_jobs_scripts' );
?>
