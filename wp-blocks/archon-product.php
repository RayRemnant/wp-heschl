<?php

function archon_product_init()
{
    //Check if Gutenberg is active
    
    if (!function_exists('register_block_type'))
    return;

    //Register the block editor script
  
    wp_register_script(
        'archon-product-js',
        PLUGIN_URL . 'wp-blocks/archonProduct.js',
        array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
    );

  //Register the block, and define the attributes

  $serverHost = getenv("SERVER_HOST");

if(!isset($serverHost) || empty($serverHost)){
	$serverHost="http://localhost:3003";
}

  $serverAuth = getenv("SERVER_AUTH");
   
   register_block_type( 'rayremnant/archon-product', array(
       
       'attributes' => [
           
            '_id' => [
            'default' => '',
            'type'    => 'string'
            ], 
            'collection' => [
            'default' => 'best',
            'type'    => 'string'
            ],
            'name' => [
                'default' => 'name',
                'type'    => 'string'
            ],
            'text' => [
                'default' => '',
                'type'    => 'string'
            ],
		  'serverHost' => [
			'default' => $serverHost,
			'type'    => 'string'
		],
		'serverAuth' => [
			'default' => $serverAuth,
			'type'    => 'string'
		],
        ],
    
        'icon' => 'editor-customchar',

        //Define the category for your block 
        'category' => 'common',

        //The script name we gave in the wp_register_script() call
        'editor_script'   => 'archon-product-js',
      
        ///The callback called by the javascript file to render the block
        'render_callback' => 'archon_product_render',
    ));

}
add_action( 'init', 'archon_product_init' );


function archon_product_render( $attributes ) {
    
    foreach($attributes as $key => $attribute){
        if(empty($attribute)){
            $attributes[$key] = "null";
        }
        else {
            $attributes[$key] = '"' . $attributes[$key] . '"';
        }
    }

    
    
  ob_start();
  echo '<script>CONTENT_BLOCK_SEPARATOR{"query": { "textQuery":' . $attributes['name'] . ',"collection":' . $attributes['collection'] . '},"text":'. $attributes['text']  .',"blockType":"postProduct1"}CONTENT_BLOCK_SEPARATOR</script>';
  return ob_get_clean();
  }