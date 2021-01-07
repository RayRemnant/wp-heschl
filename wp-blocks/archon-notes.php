<?php

function archon_notes_init()
{
    //Check if Gutenberg is active
    
    if (!function_exists('register_block_type'))
    return;

    //Register the block editor script
  
    wp_register_script(
        'archon-notes-js',
        PLUGIN_URL . 'wp-blocks/archon-notes.js',
        array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
    );

  //Register the block, and define the attributes
   
   register_block_type( 'rayremnant/archon-notes', array(
       
       'attributes' => [
            'header' => [
               'default'    => 'Notes | ',
               'type'       => 'string'
            ],
            
            'title' => [
               'default'    => 'Title',
               'type'       => 'string'
            ],
            
            'text' => [
                'default' => 'write your notes',
                'type'    => 'string'
            ],
        
        ],  
    
        'icon' => 'format-aside',

        //Define the category for your block 
        'category' => 'common',

        //The script name we gave in the wp_register_script() call
        'editor_script'   => 'archon-notes-js'
    ));

}
add_action( 'init', 'archon_notes_init' );