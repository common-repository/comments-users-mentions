<?php
/*
Plugin Name: Comments Users Mentions
Plugin URI: http://plugins.svn.wordpress.org/comments-users-mentions/
Description: This plugin allows to mention Wordpress users in a comment by writing usernames preceded by a character (default @), e.g. @guguweb.
The mentioned users will receive a notification email with a link to the relevant post.
Version: 0.2
Author: Augusto Destrero
Author URI: http://www.guguweb.com
License: GPLv2 or later
*/

function longer_username_first($user1, $user2) {
    // used to reverse sort by user_login length
    $len1 = strlen($user1->user_login);
    $len2 = strlen($user2->user_login);
    if ($len1 == $len2) {
        return 0;
    }
    return ($len1 < $len2) ? 1 : -1;
}

// load languages
function cum_lang_init() {
    load_plugin_textdomain( 'cum', false, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'init', 'cum_lang_init' );

/**
* this function send email to mentioned users
* @uses cum_email_mentioned_users FUNCTION to send emails. It based on comment_post ACTION HOOK
* @uses cum-mention-char FILTER HOOK to set the character used fot mentions (default \@)
* @uses cum-email-subject FILTER HOOK to alter email subject
* @uses cum-email-message FILTER HOOK to alter email content
*/
function cum_email_mentioned_users( $comment_id ) {
    $comment = get_comment( $comment_id );

    // get the character used for citations (default is @, but you can set e.g. +)
    $mention_char = apply_filters('cum-mention-char', '@');
    
    // look for mentions in comment text
    $pattern = "/\\$mention_char(\S+?)(?:$|\s|\.|,)/";
    preg_match_all( $pattern, $comment->comment_content, $matches );

    // used to send just one email to users mentioned multiple times in the comment
    $already_notified = array();
    
    foreach ($matches[1] as $m) {
        // search for users whose user_login begins with the match
        $args = array (
                'search'         => "$m*",
                'search_columns' => array( 'user_login' ),
                'fields'         => array( 'user_login', 'user_email' ),
        );
        $user_query = new WP_User_Query( $args );
        $users = $user_query->get_results();
        
        // reverse sort by user_login length
        usort($users, "longer_username_first");
        
        foreach ($users as $user) {
            // search for the character used for citations followed by the complete user_login
            $pattern = "/\\" . $mention_char . $user->user_login . "/";
            if (preg_match($pattern, $comment->comment_content) &&
                    !in_array($user->user_login, $already_notified)) {
                $name = $user->user_login;
                $mail = $user->user_email;
                $title = get_the_title( $comment->comment_post_ID );

                $subject = wp_sprintf( __( '%s mentioned you in a comment to the article "%s"' , 'cum' ), $comment->comment_author, $title );
                $subject = apply_filters( 'cum-email-subject', $subject, $comment, $name, $mail, $title );

                $message = '<div><h1>' . $subject . '</h1><div style="Border:5px solid grey;padding:1em;">' . apply_filters( 'the_content', wp_trim_words( $comment->comment_content, 25 ) ) . "</div></div><p>" . __( 'Read post', 'cum' ) . ' : <a href="' . get_permalink( $comment->comment_post_ID ) . '">' . $title . '</a> ' . __( 'on', 'cum' ) . ' <a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a></p>';
                $message = apply_filters( 'cum-email-message', $message, $comment, $name, $mail, $title );

                add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );
                wp_mail( $mail, $subject, $message );
                
                $already_notified[] = $user->user_login;
                // exit loop from the users whose user_login begins with the original regex match
                break;
            }
        }   
    }
}
add_action( 'comment_post', 'cum_email_mentioned_users', 90 ); // Launching after spam test
